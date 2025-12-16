<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $conversations = DB::select("
            SELECT DISTINCT
                CASE 
                    WHEN messages.sender_id = ? THEN messages.receiver_id
                    ELSE messages.sender_id
                END as user_id,
                users.name,
                (SELECT message FROM messages 
                WHERE (sender_id = ? AND receiver_id = users.id) 
                    OR (sender_id = users.id AND receiver_id = ?)
                ORDER BY created_at DESC LIMIT 1) as last_message,
                (SELECT created_at FROM messages 
                WHERE (sender_id = ? AND receiver_id = users.id) 
                    OR (sender_id = users.id AND receiver_id = ?)
                ORDER BY created_at DESC LIMIT 1) as last_message_time
            FROM messages
            INNER JOIN users ON users.id = CASE 
                WHEN messages.sender_id = ? THEN messages.receiver_id
                ELSE messages.sender_id
            END
            WHERE messages.sender_id = ? OR messages.receiver_id = ?
            ORDER BY last_message_time DESC
        ", [$user->id, $user->id, $user->id, $user->id, $user->id, $user->id, $user->id, $user->id]);
        
        return view('messages.index', compact('conversations'));
    }

    public function show($userId)
    {
        $user = auth()->user();
        $otherUser = User::findOrFail($userId);
        $messages = Message::where(function($q) use ($user, $userId) {
            $q->where('sender_id', $user->id)->where('receiver_id', $userId);
        })->orWhere(function($q) use ($user, $userId) {
            $q->where('sender_id', $userId)->where('receiver_id', $user->id);
        })->orderBy('created_at', 'asc')->get();
        
        Message::where('sender_id', $userId)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        return view('messages.show', compact('messages', 'otherUser'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $validated['receiver_id'],
            'message' => $validated['message'],
        ]);

        return response()->json(['success' => true, 'message' => $message]);
    }

    public function getMessages($userId)
    {
        $user = auth()->user();
        $messages = Message::where(function($q) use ($user, $userId) {
            $q->where('sender_id', $user->id)->where('receiver_id', $userId);
        })->orWhere(function($q) use ($user, $userId) {
            $q->where('sender_id', $userId)->where('receiver_id', $user->id);
        })->orderBy('created_at', 'asc')->get();
        
        Message::where('sender_id', $userId)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        return response()->json(['messages' => $messages]);
    }

}