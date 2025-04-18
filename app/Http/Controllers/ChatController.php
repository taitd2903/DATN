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
            })
            ->with(['messages' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->get()
            ->map(function ($user) {
                $user->has_unread = $user->messages->where('is_admin', 0)->where('is_read', false)->isNotEmpty();
                return $user;
            })
            ->sortByDesc(function ($user) {
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

            $user = auth()->user();
            $userName = $isAdmin ? ($user->role === 'admin' ? 'Admin - ' . $user->name : 'Staff - ' . $user->name) : $user->name;
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
        $isAdmin = auth()->user()->role === 'admin' || auth()->user()->role === 'staff';

        if ($isAdmin) {
            $messages = Message::where(function ($query) use ($receiverId) {
                $query->where('receiver_id', $receiverId)
                    ->where('is_admin', 1);
            })->orWhere(function ($query) use ($receiverId) {
                $query->where('user_id', $receiverId)
                    ->whereNull('receiver_id');
            })->orderBy('created_at', 'asc')->get();

            Message::where('user_id', $receiverId)
                ->whereNull('receiver_id')
                ->where('is_read', false)
                ->update(['is_read' => true]);
        } else {
            $messages = Message::where(function ($query) use ($userId) {
                $query->where('user_id', $userId)->whereNull('receiver_id');
            })->orWhere(function ($query) use ($userId) {
                $query->where('receiver_id', $userId)->where('is_admin', true);
            })->orderBy('created_at', 'asc')->get();
        }

        $adminOnline = User::where('role', 'admin')->where('is_online', true)->exists();

        $formattedMessages = $messages->map(function ($message) use ($isAdmin) {
            $senderName = $message->is_admin
                ? ($isAdmin
                    ? ($message->user->role === 'admin' ? 'Admin - ' . $message->user->name : 'Staff - ' . $message->user->name)
                    : 'OceanSport')
                : $message->user->name;

            return [
                'message'   => $message->message,
                'is_admin'  => $message->is_admin,
                'sender'    => $senderName,
                'date'      => optional($message->created_at)->format('d/m/Y'),
                'time'      => optional($message->created_at)->format('H:i')
            ];
        });

        return response()->json([
            'messages' => $formattedMessages,
            'admin_online' => $adminOnline
        ]);
    }
}
