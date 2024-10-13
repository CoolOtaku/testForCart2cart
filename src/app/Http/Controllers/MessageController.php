<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::all();
        return view('messages', compact('messages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        Message::create([
            'message' => $request->message,
            'status' => Message::STATUS_PENDING,
        ]);

        return redirect()->route('messages.index');
    }

    public function process()
    {
        $message = Message::where('status', Message::STATUS_PENDING)
            ->lockForUpdate()
            ->first();

        if ($message) {
            $message->status = Message::STATUS_PROCESSING;
            $message->save();

            sleep(3);

            $message->status = Message::STATUS_COMPLETED;
            $message->save();
        }

        return redirect()->route('messages.index');
    }
}
