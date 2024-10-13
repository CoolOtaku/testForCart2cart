<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_message()
    {
        $message = Message::create([
            'message' => 'Test message',
            'status' => Message::STATUS_PENDING,
        ]);

        $this->assertDatabaseHas('message_queue', [
            'message' => 'Test message',
            'status' => Message::STATUS_PENDING,
        ]);
    }

    /** @test */
    public function it_has_default_status_pending()
    {
        $message = Message::create([
            'message' => 'Test message without status',
        ]);

        $this->assertEquals(Message::STATUS_PENDING, $message->status);
    }

    /** @test */
    public function it_can_update_status_of_message()
    {
        $message = Message::create([
            'message' => 'Test message',
            'status' => Message::STATUS_PENDING,
        ]);

        $message->status = Message::STATUS_PROCESSING;
        $message->save();

        $this->assertDatabaseHas('message_queue', [
            'message' => 'Test message',
            'status' => Message::STATUS_PROCESSING,
        ]);
    }

    /** @test */
    public function it_can_delete_a_message()
    {
        $message = Message::create([
            'message' => 'Test message',
            'status' => Message::STATUS_PENDING,
        ]);

        $message->delete();

        $this->assertDatabaseMissing('message_queue', [
            'message' => 'Test message',
        ]);
    }
}
