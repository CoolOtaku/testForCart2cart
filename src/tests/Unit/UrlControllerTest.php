<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

class UrlControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_shows_shorten_view()
    {
        $response = $this->get(route('url.index'));

        $response->assertStatus(200);
        $response->assertViewIs('shorten');
    }

    /** @test */
    public function it_shortens_a_url()
    {
        $data = ['original_url' => 'https://rozetka.com.ua/ua/'];

        $response = $this->post(route('url.shorten'), $data);

        $this->assertDatabaseHas('urls', ['original_url' => 'https://rozetka.com.ua/ua/']);

        $url = Url::where('original_url', 'https://rozetka.com.ua/ua/')->first();
        $response->assertSessionHas('short_url', url($url->short_url));

        $response->assertRedirect();
    }

    /** @test */
    public function it_redirects_to_original_url()
    {
        $url = Url::factory()->create([
            'original_url' => 'https://rozetka.com.ua/ua/',
            'short_url' => 'abcdef'
        ]);

        $response = $this->get(route('url.redirect', $url->short_url));

        $response->assertRedirect('https://rozetka.com.ua/ua/');
    }

    /** @test */
    public function it_returns_404_if_short_url_not_found()
    {
        $response = $this->get(route('url.redirect', 'nonexistent'));

        $response->assertStatus(404);
    }
}
