<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Availability;
use App\Models\Friend;
use App\Models\User;
use Carbon\Carbon;

class AvailabilityController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
    
        $selectedFriendIds = array_map('intval', (array)$request->get('friends', []));
        $selectedPublicUserIds = array_map('intval', (array)$request->get('public_users', []));
        $selectedUserIds = array_merge($selectedFriendIds, $selectedPublicUserIds);
        
        $query = Availability::with('user');
        $query->where(function($q) use ($user, $selectedUserIds) {
            $q->where('user_id', $user->id);
            if (!empty($selectedUserIds)) {
                $q->orWhere(function($subQ) use ($selectedUserIds) {
                    $subQ->whereIn('user_id', $selectedUserIds)->where('visibility', 'public');
                });
            }
        });
        $availabilities = $query->get();
        
        $timeSlots = [];
        
        foreach ($availabilities as $avail) {
            $dateStr = $avail->date instanceof \Carbon\Carbon ? $avail->date->format('Y-m-d') : $avail->date;
            $startTime = strlen($avail->start_time) === 5 ? $avail->start_time . ':00' : $avail->start_time;
            $endTime = strlen($avail->end_time) === 5 ? $avail->end_time . ':00' : $avail->end_time;
            
            $start = Carbon::parse($dateStr . ' ' . $startTime);
            $end = Carbon::parse($dateStr . ' ' . $endTime);
            
            $current = $start->copy();
            while ($current < $end) {
                $slotKey = $dateStr . '|' . $current->format('H:i:s');
                
                if (!isset($timeSlots[$slotKey])) {
                    $timeSlots[$slotKey] = [
                        'users' => [],
                        'userNames' => [],
                        'availabilityIds' => []
                    ];
                }
                
                if (!in_array($avail->user_id, $timeSlots[$slotKey]['users'])) {
                    $timeSlots[$slotKey]['users'][] = $avail->user_id;
                    $timeSlots[$slotKey]['userNames'][] = $avail->user->name;
                    if ($avail->user_id === $user->id) {
                        $timeSlots[$slotKey]['availabilityIds'][] = $avail->id;
                    }
                }
                
                $current->addMinutes(30);
            }
        }
        
        $maxPeopleCount = 1;
        foreach ($timeSlots as $slot) {
            $maxPeopleCount = max($maxPeopleCount, count($slot['users']));
        }
        
        $calendarEvents = [];
        foreach ($timeSlots as $slotKey => $slotData) {
            [$date, $time] = explode('|', $slotKey);
            $start = Carbon::parse($date . ' ' . $time);
            $end = $start->copy()->addMinutes(30);
            
            $count = count($slotData['users']);
            $intensity = $count / max($maxPeopleCount, 2);
            
            
            $count = min($count, 6); 
            $colors = [
                1 => ["rgba(134, 239, 172, 0.5)", "rgba(134, 239, 172, 0.7)"],
                2 => ["rgba(74, 222, 128, 0.7)", "rgba(74, 222, 128, 0.9)"],
                3 => ["rgba(34, 197, 94, 0.85)", "rgba(34, 197, 94, 1)"],
                4 => ["rgba(22, 163, 74, 0.95)", "rgba(22, 163, 74, 1)"],
                5 => ["rgba(11, 121, 51, 0.95)", "rgba(11, 121, 51, 1)"],
                6 => ["rgba(3, 62, 25, 0.7)", "rgba(3, 62, 25, 1)"]
            ];
            [$color, $borderColor] = $colors[$count];
            
            $calendarEvents[] = [
                'id' => 'slot_' . md5($slotKey),
                'title' => '',
                'start' => $start->format('Y-m-d\TH:i:s'),
                'end' => $end->format('Y-m-d\TH:i:s'),
                'backgroundColor' => $color,
                'borderColor' => $borderColor,
                'borderWidth' => '2px',
                'display' => 'background', 
                'extendedProps' => [
                    'userIds' => $slotData['users'],
                    'userNames' => $slotData['userNames'],
                    'count' => $count,
                    'intensity' => $intensity,
                    'availabilityIds' => $slotData['availabilityIds']
                ],
                'editable' => in_array($user->id, $slotData['users']),
            ];
        }
        
        $friends = Friend::where('user_id', $user->id)->with('friendUser')->get()->pluck('friendUser');
        
        return view('availabilities.index', [
            'calendarEvents' => $calendarEvents,
            'friends' => $friends,
            'selectedFriendIds' => $selectedFriendIds,
            'selectedPublicUserIds' => $selectedPublicUserIds,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'visibility' => 'required|in:public,friends,private',
        ]);

        $startTime = strlen($validated['start_time']) === 5 ? $validated['start_time'] . ':00' : $validated['start_time'];
        $endTime = strlen($validated['end_time']) === 5 ? $validated['end_time'] . ':00' : $validated['end_time'];
        $date = \Carbon\Carbon::parse($validated['date'])->format('Y-m-d');

        $availability = Availability::create([
            'user_id' => auth()->id(),
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'visibility' => $validated['visibility'],
        ]);

        return response()->json([
            'success' => true,
            'availability' => $availability
        ]);
    }

    public function updateDrag(Request $request, $id)
    {
        $availability = Availability::findOrFail($id);

        if ($availability->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $start = \Carbon\Carbon::parse($request->start);
        $end = \Carbon\Carbon::parse($request->end);

        $availability->update([
            'date' => $start->format('Y-m-d'),
            'start_time' => $start->format('H:i:s'),
            'end_time' => $end->format('H:i:s'),
        ]);

        return response()->json(['success' => true]);
    }

    public function updateVisibility(Request $request, $id)
    {
        $availability = Availability::findOrFail($id);

        if ($availability->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'visibility' => 'required|in:public,friends,private',
        ]);

        $availability->update(['visibility' => $validated['visibility']]);
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $availability = Availability::findOrFail($id);
        
        if ($availability->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $availability->delete();
        return response()->json(['success' => true]);
    }

    public function getPublicUsers(Request $request)
    {
        $users = User::whereHas('availabilities', function($query) {
            $query->where('visibility', 'public');
        })
        ->where('id', '!=', auth()->id())
        ->select('id', 'name', 'email')
        ->get();

        return response()->json(['success' => true, 'users' => $users]);
    }
}