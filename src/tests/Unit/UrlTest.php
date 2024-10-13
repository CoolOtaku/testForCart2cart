<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UrlTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_url()
    {
        $url = Url::create([
            'original_url' => 'https://rozetka.com.ua/ua/',
            'short_url' => 'abc123',
        ]);

        $this->assertDatabaseHas('urls', [
            'original_url' => 'https://rozetka.com.ua/ua/',
            'short_url' => 'abc123',
        ]);
    }

    /** @test */
    public function it_requires_original_url()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Url::create([
            'short_url' => 'abc123',
        ]);
    }

    /** @test */
    public function it_can_update_short_url()
    {
        $url = Url::create([
            'original_url' => 'https://rozetka.com.ua/ua/',
            'short_url' => 'abc123',
        ]);

        $url->short_url = 'xyz789';
        $url->save();

        $this->assertDatabaseHas('urls', [
            'original_url' => 'https://rozetka.com.ua/ua/',
            'short_url' => 'xyz789',
        ]);
    }

    /** @test */
    public function it_can_delete_a_url()
    {
        $url = Url::create([
            'original_url' => 'https://rozetka.com.ua/ua/',
            'short_url' => 'abc123',
        ]);

        $url->delete();

        $this->assertDatabaseMissing('urls', [
            'original_url' => 'https://rozetka.com.ua/ua/',
        ]);
    }
}
