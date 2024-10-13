<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

class MessageControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_all_messages()
    {
        Message::factory()->create(['message' => 'Test Message 1']);
        Message::factory()->create(['message' => 'Test Message 2']);

        $response = $this->get(route('messages.index'));

        $response->assertStatus(200);
        $response->assertViewHas('messages', function ($messages) {
            return $messages->count() == 2;
        });
    }

    /** @test */
    public function it_stores_a_new_message()
    {
        $data = ['message' => 'New test message'];

        $response = $this->post(route('messages.store'), $data);

        $this->assertDatabaseHas('messages', ['message' => 'New test message', 'status' => Message::STATUS_PENDING]);

        $response->assertRedirect(route('messages.index'));
    }

    /** @test */
    public function it_processes_a_pending_message()
    {
        $message = Message::factory()->create(['status' => Message::STATUS_PENDING]);

        $response = $this->post(route('messages.process'));

        $this->assertDatabaseHas('messages', ['id' => $message->id, 'status' => Message::STATUS_COMPLETED]);

        $response->assertRedirect(route('messages.index'));
    }
}
