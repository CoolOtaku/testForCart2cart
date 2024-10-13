<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\AnimalFactory;
use Mockery;

class AnimalControllerTest extends TestCase
{
    /** @test */
    public function it_displays_the_animal_view_in_index()
    {
        $response = $this->get('/animal');

        $response->assertStatus(200)
            ->assertViewIs('animal');
    }

    /** @test */
    public function it_returns_correct_sound_for_an_animal()
    {
        $response = $this->json('POST', '/animal/sound', ['type' => 'dog']);

        $response->assertStatus(200)
            ->assertJson(['sound' => 'https://www.google.com/logos/fnbx/animal_sounds/dog.mp3']);
    }

    /** @test */
    public function it_returns_error_for_invalid_animal_type()
    {
        $response = $this->json('POST', '/animal/sound', ['type' => 'invalid_animal']);

        $response->assertStatus(400)
            ->assertJson(['error' => 'Invalid animal type.']);
    }
}
