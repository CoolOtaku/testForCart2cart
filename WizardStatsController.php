<?php
/*-----------------------------------------------------------------------------+
| MagneticOne                                                                  |
| Copyright (c) 2008 MagneticOne.com <contact@magneticone.com>                 |
| All rights reserved                                                          |
+------------------------------------------------------------------------------+
| PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE "license.txt"|
| FILE PROVIDED WITH THIS DISTRIBUTION. THE AGREEMENT TEXT IS ALSO AVAILABLE   |
| AT THE FOLLOWING URL: http://www.magneticone.com/store/license.php           |
|                                                                              |
| THIS  AGREEMENT  EXPRESSES  THE  TERMS  AND CONDITIONS ON WHICH YOU MAY USE  |
| THIS SOFTWARE   PROGRAM   AND  ASSOCIATED  DOCUMENTATION   THAT  MAGNETICONE |
| (hereinafter  referred to as "THE AUTHOR") IS FURNISHING  OR MAKING          |
| AVAILABLE TO YOU WITH  THIS  AGREEMENT  (COLLECTIVELY,  THE  "SOFTWARE").    |
| PLEASE   REVIEW   THE  TERMS  AND   CONDITIONS  OF  THIS  LICENSE AGREEMENT  |
| CAREFULLY   BEFORE   INSTALLING   OR  USING  THE  SOFTWARE.  BY INSTALLING,  |
| COPYING   OR   OTHERWISE   USING   THE   SOFTWARE,  YOU  AND  YOUR  COMPANY  |
| (COLLECTIVELY,  "YOU")  ARE  ACCEPTING  AND AGREEING  TO  THE TERMS OF THIS  |
| LICENSE   AGREEMENT.   IF  YOU    ARE  NOT  WILLING   TO  BE  BOUND BY THIS  |
| AGREEMENT, DO  NOT INSTALL OR USE THE SOFTWARE.  VARIOUS   COPYRIGHTS   AND  |
| OTHER   INTELLECTUAL   PROPERTY   RIGHTS    PROTECT   THE   SOFTWARE.  THIS  |
| AGREEMENT IS A LICENSE AGREEMENT THAT GIVES  YOU  LIMITED  RIGHTS   TO  USE  |
| THE  SOFTWARE   AND  NOT  AN  AGREEMENT  FOR SALE OR FOR  TRANSFER OF TITLE. |
| THE AUTHOR RETAINS ALL RIGHTS NOT EXPRESSLY GRANTED BY THIS AGREEMENT.       |
|                                                                              |
| The Developer of the Code is MagneticOne,                                    |
| Copyright (C) 2006 - 2008 All Rights Reserved.                               |
+-----------------------------------------------------------------------------*/

class Reports_WizardStatsController extends \Cart2cart\Controller\Adminbackend
{
  /**
   * @var Zend_Db_Adapter_Abstract
   */
  protected $_db = null;

  public function init()
  {
    $this->_db = Zend_Registry::get('db');

    if ($this->_request->isXmlHttpRequest()) {
      $this->_helper->layout()->disableLayout();
      $this->_helper->viewRenderer->setNoRender(true);
    }

    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext->addActionContext('migrations', 'json')
      ->initContext('json');
  }

  public function indexAction()
  {
  }

  public function migrationsAction()
  {
    $sql = $this->_getMigrationsQuery();
    $sqlCount = $this->_getMigrationsQuery(true);

    $migrations = [];

    $res = $this->_db->query($sql);

    while ($row = $res->fetch()) {
      $row['storesSetupTime'] = $row['storesSetupTime'] ? $row['storesSetupTime'] + 2 : 0;
      $migrations[] = [
        'migrationId' => $row['migrationId'],
        'wizardCreated' => $row['wizardCreated'],
        'demoCompleted' => $row['demoCompleted'],
        'fullCompleted' => $row['fullCompleted'],
        'sourceId' => $row['sourceId'],
        'sourceUsedPlugin' => $this->_prepareTime($row['sourceUsedPlugin']),
        'targetId' => $row['targetId'],
        'targetUsedPlugin' => $this->_prepareTime($row['targetUsedPlugin']),
        'price' => $row['price'],
        'estimatorPrice' => $row['estimatorPrice'],
        'lastLoginDate' => $row['lastLoginDate'],
        'demoRate' => $row['demoRate'],
        'demoResultsChecked' => $row['demoResultsChecked'],
        'storesSetupTime' => (int)($row['storesSetupTime'] / 60) . 'm. ' . ($row['storesSetupTime'] % 60) . 's',
        'qualityProcessTime' => $this->_prepareTime($row['qualityProcessTime']),
      ];
    }

    $this->view->migrations = $migrations;
    $this->view->migrationsCount = $this->_db->query($sqlCount)->fetchColumn();
  }

  private function _getMigrationsQuery($count = false, $start = null, $limit = null, $allData = false)
  {
    $forceLimit = true;
    if ($start === null) {
      $start = (int)$this->_request->getParam('start', 0);
    }

    if ($limit === null) {
      $forceLimit = false;
      $limit = (int)$this->_request->getParam('limit', 15);
    }

    $filterData = $this->_getFilterData();

    $useIds = !$count;
    foreach ($filterData as $filter) {
      if ($filter['field'] == 'migrationId') {
        $useIds = false;
        break;
      }
    }
    
    if (!$allData && $useIds) {
      $migrationsSelect = $this->_db->select()
        ->from('migrations', ['id'])
        ->order('id DESC')
        ->limit($limit, $start);

      $useIds = array_column($this->_db->query($migrationsSelect)->fetchAll(), 'id');
    }

    $filter = new \Cart2cart\Extjs4\Filter([
      'migrationId' => 'm.id',
      'price' => 'm.price_in_dollars_with_discount',
      'estimatorPrice' => 'm.price_in_dollars',
      'wizardCreated' => 'mshc.date',
      'demoCompleted' => 'mshd.date',
      'fullCompleted' => 'mshf.date',
      'sourceId' => 'mss.cart_id',
      'targetId' => 'mst.cart_id',
      'lastLoginDate' => 'a.last_visit',
      'demoRate' => 'mr.rate',
      'demoResultsChecked' => 'mdl.value',
      'qualityProcessTime' => 'mdqp.value',
    ], $this->_request);

    $where = $filter->getWhere();
    if ($where === null) {
      $where = '1';
    }

    $select = $this->_db->select()
      ->from(['a' => 'accounts'], $count ? ['count' => 'COUNT(*)'] : ['email', 'lastLoginDate' => 'last_visit'])
      ->join(['m' => 'migrations'], 'm.account_id = a.id', ['migrationId' => 'id', 'price' => 'price_in_dollars_with_discount', 'estimatorPrice' => 'price_in_dollars'])
      ->join(['mss' => 'migrations_stores'], 'm.source_store_id = mss.id', ['sourceId' => 'mss.cart_id'])
      ->join(['mst' => 'migrations_stores'], 'm.target_store_id = mst.id', ['targetId' => 'mst.cart_id'])
      ->join(['mshc' => 'migrations_status_history'], 'm.id = mshc.migration_id and mshc.status = "created"', ['wizardCreated' => 'mshc.date'])
      ->where($where)
      ->where('m.deleted = 0')
      ->order($filter->getOrder($this->_request));

    $forceLimit and $select->limit($limit, $start);

    if (!$count && is_array($useIds)) {
      $select->where('m.id in(' . implode(',', $useIds) . ')');
    }

    if ($filterData || !$count) {
      $select
        ->joinLeft(['mshd' => 'migrations_status_history'], 'm.id = mshd.migration_id and mshd.status = "demo_completed"', ['demoCompleted' => 'mshd.date'])
        ->joinLeft(['mshf' => 'migrations_status_history'], 'm.id = mshf.migration_id and mshf.status = "completed"', ['fullCompleted' => 'mshf.date'])
        ->joinLeft(['mr' => 'migrations_rates'], 'm.id = mr.migration_id', ['demoRate' => new Zend_Db_Expr('ifnull(mr.rate, 0)')])
        ->joinLeft(
          ['mdl' => 'migrations_data'],
          'm.id = mdl.migration_id and mdl.`key` = "demo_result_links_checked"',
          ['demoResultsChecked' => new Zend_Db_Expr('ifnull(mdl.value, 0)')]
        )
        ->joinLeft(
          ['mdsp' => 'migrations_data'],
          'm.id = mdsp.migration_id and mdsp.`key` = "' . \Cart2cart\Migration\Data::PLUGIN_USED_FOR_SOURCE . '"',
          ['sourceUsedPlugin' => new Zend_Db_Expr('ifnull(mdsp.value, 0)')]
        )
        ->joinLeft(
          ['mdtp' => 'migrations_data'],
          'm.id = mdtp.migration_id and mdtp.`key` = "' . \Cart2cart\Migration\Data::PLUGIN_USED_FOR_TARGET . '"',
          ['targetUsedPlugin' => new Zend_Db_Expr('ifnull(mdtp.value, 0)')]
        )
        ->joinLeft(
          ['mdqp' => 'migrations_data'],
          'm.id = mdqp.migration_id and mdqp.`key` = "' . \Cart2cart\Migration\Data::QUALITY_PROCESS_TIME . '"',
          ['qualityProcessTime' => new Zend_Db_Expr('ifnull(mdqp.value, 0)')]
        )
        ->joinLeft(
          ['mdwt' => 'migrations_data'],
          'm.id = mdwt.migration_id and mdwt.`key` = "wizard_stores_setup_wait_time"',
          ['storesSetupTime' => new Zend_Db_Expr('ifnull(mdwt.value, 0)')]
        )
        ->group('migrationId');
    }
    
    return $select;
  }

  private function _getFilterData()
  {
    $filters = @json_decode($this->_request->getParam('filter', ''), true);

    return $filters ?: [];
  }

  public function saveAction()
  {
    ini_set('max_execution_time', 0);
    ini_set('memory_limit', '1024M');

    $start = 0;
    $limit = 15;

    $handle = fopen('php://memory', 'rw+');
    fputcsv($handle, [
      'migrationId',
      'wizardCreated',
      'demoCompleted',
      'fullCompleted',
      'sourceId',
      'sourceUsedPlugin',
      'targetId',
      'targetUsedPlugin',
      'price',
      'estimatorPrice',
      'lastLoginDate',
      'demoRate',
      'demoResultsChecked',
      'storesSetupTime',
      'qualityProcessTime',
    ]);

    while (true) {
      $sql = $this->_getMigrationsQuery(false, $start, $limit, true);

      $res = $this->_db->query($sql)->fetchAll();

      if (!$res) {
        break;
      }

      foreach ($res as $row) {
        fputcsv($handle, [
          'migrationId' => $row['migrationId'],
          'wizardCreated' => $row['wizardCreated'],
          'demoCompleted' => $row['demoCompleted'],
          'fullCompleted' => $row['fullCompleted'],
          'sourceId' => $row['sourceId'],
          'sourceUsedPlugin' => $this->_prepareTime($row['sourceUsedPlugin']),
          'targetId' => $row['targetId'],
          'targetUsedPlugin' => $this->_prepareTime($row['targetUsedPlugin']),
          'price' => $row['price'],
          'estimatorPrice' => $row['estimatorPrice'],
          'lastLoginDate' => $row['lastLoginDate'],
          'demoRate' => $row['demoRate'],
          'demoResultsChecked' => $row['demoResultsChecked'],
          'storesSetupTime' => $row['storesSetupTime'] ? $row['storesSetupTime'] + 2 : 0,
          'qualityProcessTime' => $this->_prepareTime($row['qualityProcessTime']),
        ]);
      }

      $start += $limit;
    }

    $this->_helper->layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    rewind($handle);

    $this->_response->setHeader('Pragma', 'public');
    $this->_response->setHeader('Expires', '0');
    $this->_response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
    $this->_response->setHeader('Cache-Control', 'private',false);
    $this->_response->setHeader('Content-Type', 'text/csv');
    $this->_response->setHeader('Content-Disposition', 'attachment; filename=migrations.csv;');
    $this->_response->setBody(stream_get_contents($handle));
  }

  /**
   * @param int $time
   *
   * @return false|string
   */
  private function _prepareTime($time)
  {
    return $time ? gmdate('i:s', $time) : 0;
  }
}