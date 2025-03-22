<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;

class ChatController extends Controller
{
    public function index()
    {
        $users = User::where('role', '!=', 'admin')
            ->whereHas('messages', function ($query) {
                $query->whereNull('receiver_id')->orWhere('receiver_id', auth()->id());
            })->with(['messages' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])->get()->sortByDesc(function ($user) {
                return $user->messages->first()->created_at;
            });

        return view('admin.chat', compact('users'));
    }

    public function sendMessage(Request $request)
    {
        try {
            $messageContent = $request->input('message');
            $userId = auth()->id();
            $isAdmin = $request->input('is_admin', false);
            $receiverId = $request->input('receiver_id', $isAdmin ? null : 'admin');
    
            if (!$messageContent) {
                return response()->json(['status' => 'error', 'message' => 'Missing message'], 400);
            }
            if ($isAdmin && !$receiverId) {
                return response()->json(['status' => 'error', 'message' => 'Missing receiver_id for admin'], 400);
            }
    
            $message = Message::create([
                'user_id' => $userId,
                'receiver_id' => $receiverId === 'admin' ? null : $receiverId,
                'message' => $messageContent,
                'is_admin' => $isAdmin ? 1 : 0,
            ]);
    
            $userName = $isAdmin ? 'Admin' : auth()->user()->name;
            $broadcastReceiverId = $isAdmin ? $receiverId : 'admin';
        Log::info('Broadcasting message: ', [
            'message' => $messageContent,
            'userId' => $userId,
            'receiverId' => $broadcastReceiverId,
            'userName' => $userName
        ]);
            event(new MessageSent($messageContent, $userId, $receiverId, $userName));
    
            return response()->json(['status' => 'Message sent!']);
        } catch (\Exception $e) {
            Log::error('Error sending message: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getHistory(Request $request)
    {
        $userId = auth()->id();
        $receiverId = $request->query('receiver_id');
        $isAdmin = auth()->user()->role === 'admin';
    
        if ($isAdmin) {
            $messages = Message::where(function ($query) use ($receiverId) {
                $query->where('receiver_id', $receiverId)
                      ->where('is_admin', 1);
            })->orWhere(function ($query) use ($receiverId) {
                $query->where('user_id', $receiverId)
                      ->whereNull('receiver_id');
            })->orderBy('created_at', 'asc')->get();
        } else {
            $messages = Message::where(function ($query) use ($userId) {
                $query->where('user_id', $userId)->whereNull('receiver_id'); 
            })->orWhere(function ($query) use ($userId) {
                $query->where('receiver_id', $userId)->where('is_admin', true);
            })->orderBy('created_at', 'asc')->get();
        }
    
        if ($messages->isEmpty() && !$isAdmin) {
            $messages->push([
                'message' => 'Chào bạn! Bạn có câu hỏi gì không?',
                'is_admin' => true,
                'created_at' => now(),
            ]);
        }
    
        return response()->json(['messages' => $messages]);
    }
}
