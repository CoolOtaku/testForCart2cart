<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class WizardStatsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_welcome_message_in_index()
    {
        $response = $this->getJson('/api/wizard-stats');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Welcome to the Wizard Stats API',
            ]);
    }

    /** @test */
    public function it_returns_migrations_with_default_limit()
    {
        DB::table('migrations')->insert([
            [
                'account_id' => 1,
                'source_store_id' => 1,
                'target_store_id' => 2,
                'price_in_dollars' => 100,
                'price_in_dollars_with_discount' => 80,
                'deleted' => 0,
            ],
        ]);

        DB::table('accounts')->insert([
            ['id' => 1, 'last_visit' => now()],
        ]);

        DB::table('migrations_stores')->insert([
            ['id' => 1, 'cart_id' => 'source'],
            ['id' => 2, 'cart_id' => 'target'],
        ]);

        $response = $this->json('GET', '/api/wizard-stats/migrations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'migrations' => [
                    '*' => [
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
                    ],
                ],
                'count',
            ]);
    }
}
