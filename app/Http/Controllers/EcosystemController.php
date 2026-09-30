<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\Startup;
use Illuminate\Http\Request;

class EcosystemController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::upcoming()->get();
        $going = EventAttendance::where('device_id', $request->attributes->get('device'))->pluck('event_id')->all();

        return view('pages.ecosystem', [
            'events' => $events,
            'startups' => Startup::orderByDesc('hiring')->orderBy('name')->get(),
            'communities' => Community::orderByDesc('members')->get(),
            'going' => $going,
        ]);
    }

    /** « Je participe » : bascule la participation de cet appareil. */
    public function attend(Request $request, Event $event)
    {
        $device = $request->attributes->get('device');
        $row = EventAttendance::where('event_id', $event->id)->where('device_id', $device)->first();
        $row ? $row->delete() : EventAttendance::create(['event_id' => $event->id, 'device_id' => $device]);

        return $request->expectsJson()
            ? response()->json(['going' => ! $row, 'count' => $event->attendances()->count()])
            : back();
    }
}
