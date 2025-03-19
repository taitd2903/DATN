<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;

class ChatController extends Controller
{
    public function index()
    {
        return view('admin.chat');
    }

    public function sendMessage(Request $request)
    {
        $messageContent = $request->input('message');
        $userId = auth()->id();

        $message = Message::create([
            'user_id' => $userId,
            'message' => $messageContent,
            'is_admin' => false,
        ]);

        event(new MessageSent($messageContent, $userId));

        return response()->json(['status' => 'Message sent!']);
    }
}
