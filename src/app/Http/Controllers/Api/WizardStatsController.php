<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class WizardStatsController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Welcome to the Wizard Stats API']);
    }

    public function getMigrations(Request $request)
    {
        $start = (int)$request->get('start', 0);
        $limit = (int)$request->get('limit', 15);

        $filterData = $this->getFilterData($request);

        $migrationsQuery = DB::table('migrations')
            ->join('accounts', 'migrations.account_id', '=', 'accounts.id')
            ->join('migrations_stores as mss', 'migrations.source_store_id', '=', 'mss.id')
            ->join('migrations_stores as mst', 'migrations.target_store_id', '=', 'mst.id')
            ->leftJoin('migrations_status_history as mshc', function($join) {
                $join->on('migrations.id', '=', 'mshc.migration_id')
                    ->where('mshc.status', 'created');
            })
            ->leftJoin('migrations_status_history as mshd', function($join) {
                $join->on('migrations.id', '=', 'mshd.migration_id')
                    ->where('mshd.status', 'demo_completed');
            })
            ->leftJoin('migrations_status_history as mshf', function($join) {
                $join->on('migrations.id', '=', 'mshf.migration_id')
                    ->where('mshf.status', 'completed');
            })
            ->leftJoin('migrations_rates as mr', 'migrations.id', '=', 'mr.migration_id')
            ->leftJoin('migrations_data as mdl', function($join) {
                $join->on('migrations.id', '=', 'mdl.migration_id')
                    ->where('mdl.key', 'demo_result_links_checked');
            })
            ->leftJoin('migrations_data as mdsp', function($join) {
                $join->on('migrations.id', '=', 'mdsp.migration_id')
                    ->where('mdsp.key', 'clear_last_demo_instructions');
            })
            ->leftJoin('migrations_data as mdtp', function($join) {
                $join->on('migrations.id', '=', 'mdtp.migration_id')
                    ->where('mdtp.key', 'entities_history');
            })
            ->leftJoin('migrations_data as mdqp', function($join) {
                $join->on('migrations.id', '=', 'mdqp.migration_id')
                    ->where('mdqp.key', 'some_quality_process_time_key');
            })
            ->leftJoin('migrations_data as mdwt', function($join) {
                $join->on('migrations.id', '=', 'mdwt.migration_id')
                    ->where('mdwt.key', 'wizard_stores_setup_wait_time');
            })
            ->where('migrations.deleted', 0);

        if ($filterData) {
            foreach ($filterData as $filter) {
                if ($filter['field'] == 'migrationId') {
                    $migrationsQuery->where('migrations.id', $filter['value']);
                }
            }
        }

        $migrations = $migrationsQuery
            ->select([
                'migrations.id as migrationId',
                'mshc.date as wizardCreated',
                'mshd.date as demoCompleted',
                'mshf.date as fullCompleted',
                'mss.cart_id as sourceId',
                'mdsp.value as sourceUsedPlugin',
                'mst.cart_id as targetId',
                'mdtp.value as targetUsedPlugin',
                'migrations.price_in_dollars_with_discount as price',
                'migrations.price_in_dollars as estimatorPrice',
                'accounts.last_visit as lastLoginDate',
                'mr.rate as demoRate',
                'mdl.value as demoResultsChecked',
                DB::raw('IFNULL(mdwt.value, 0) as storesSetupTime'),
                'mdqp.value as qualityProcessTime',
            ])
            ->orderBy('migrationId', 'DESC')
            ->offset($start)
            ->limit($limit)
            ->get();

        return response()->json([
            'migrations' => $migrations,
            'count' => $migrations->count(),
        ]);
    }

    public function save(Request $request)
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
            $migrations = $this->getMigrationsData($start, $limit);

            if ($migrations->isEmpty()) {
                break;
            }

            foreach ($migrations as $row) {
                fputcsv($handle, [
                    'migrationId' => $row->migrationId,
                    'wizardCreated' => $row->wizardCreated,
                    'demoCompleted' => $row->demoCompleted,
                    'fullCompleted' => $row->fullCompleted,
                    'sourceId' => $row->sourceId,
                    'sourceUsedPlugin' => $this->prepareTime($row->sourceUsedPlugin),
                    'targetId' => $row->targetId,
                    'targetUsedPlugin' => $this->prepareTime($row->targetUsedPlugin),
                    'price' => $row->price,
                    'estimatorPrice' => $row->estimatorPrice,
                    'lastLoginDate' => $row->lastLoginDate,
                    'demoRate' => $row->demoRate,
                    'demoResultsChecked' => $row->demoResultsChecked,
                    'storesSetupTime' => $row->storesSetupTime ? $row->storesSetupTime + 2 : 0,
                    'qualityProcessTime' => $this->prepareTime($row->qualityProcessTime),
                ]);
            }

            $start += $limit;
        }

        rewind($handle);

        return response()->stream(function () use ($handle) {
            fpassthru($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=migrations.csv',
        ]);
    }

    private function getMigrationsData($start, $limit)
    {
        return DB::table('migrations')
            ->join('accounts', 'migrations.account_id', '=', 'accounts.id')
            ->join('migrations_stores as mss', 'migrations.source_store_id', '=', 'mss.id')
            ->join('migrations_stores as mst', 'migrations.target_store_id', '=', 'mst.id')
            ->leftJoin('migrations_status_history as mshc', function($join) {
                $join->on('migrations.id', '=', 'mshc.migration_id')
                    ->where('mshc.status', 'created');
            })
            ->select([
                'migrations.id as migrationId',
                'mshc.date as wizardCreated',
                'migrations.price_in_dollars_with_discount as price',
                'migrations.price_in_dollars as estimatorPrice',
                'mss.cart_id as sourceId',
                'mst.cart_id as targetId',
            ])
            ->where('migrations.deleted', '=', 0)
            ->offset($start)
            ->limit($limit)
            ->get();
    }

    private function getFilterData(Request $request)
    {
        $filters = json_decode($request->get('filter', ''), true);
        return $filters ?: [];
    }

    private function prepareTime($time)
    {
        return $time ? gmdate('i:s', $time) : '00:00';
    }
}
