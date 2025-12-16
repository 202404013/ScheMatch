<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\User;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    public function index()
    {
        $friends = Friend::where('user_id', auth()->id())->get();

        return view('friends.index', compact('friends'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'friend_user_id' => 'required|exists:users,id',
        ]);

        if ($validated['friend_user_id'] == auth()->id()) {
            return back()->withErrors(['friend_user_id' => 'You cannot add yourself.']);
        }

        $exists = Friend::where('user_id', auth()->id())
                        ->where('friend_user_id', $validated['friend_user_id'])
                        ->exists();

        if ($exists) {
            return back()->with('message', 'Already added as a friend.');
        }

        Friend::create([
            'user_id' => auth()->id(),
            'friend_user_id' => $validated['friend_user_id']
        ]);

        return redirect()->route('friends.index')->with('success', 'Friend added.');
    }

    public function destroy(Friend $friend)
    {
        if ($friend->user_id !== auth()->id()) {
            abort(403);
        }

        $friend->delete();

        return redirect()->route('friends.index')->with('success', 'Friend removed.');
    }
}
