<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hostel\Event;
use Carbon\Carbon;

class EventsApiController extends Controller
{
    /**
     * ALL EVENTS
     */
    public function index()
    {
        $events = Event::latest('event_date')
            ->get()
            ->map(function ($event) {

                return $this->formatEvent($event);
            });

        return response()->json([

            'status' => true,
            'message' => 'Events fetched successfully',

            'data' => $events
        ]);
    }

    /**
     * TODAY EVENTS
     */
    public function todayEvents()
    {
        $events = Event::whereDate('event_date', today())
            ->get()
            ->map(function ($event) {

                return $this->formatEvent($event);
            });

        return response()->json([

            'status' => true,
            'message' => 'Today events fetched successfully',

            'data' => $events
        ]);
    }

    /**
     * UPCOMING EVENTS
     */
    public function upcomingEvents()
    {
        $events = Event::whereDate('event_date', '>=', today())
            ->orderBy('event_date')
            ->get()
            ->map(function ($event) {

                return $this->formatEvent($event);
            });

        return response()->json([

            'status' => true,
            'message' => 'Upcoming events fetched successfully',

            'data' => $events
        ]);
    }

    /**
     * SINGLE EVENT
     */
    public function show($id)
    {
        $event = Event::find($id);

        if (!$event) {

            return response()->json([
                'status' => false,
                'message' => 'Event not found'
            ], 404);
        }

        return response()->json([

            'status' => true,
            'message' => 'Event fetched successfully',

            'data' => $this->formatEvent($event)
        ]);
    }

    /**
     * FORMAT EVENT
     */
    private function formatEvent($event)
    {
        return [

            'id' => $event->id,

            'title' => $event->title ?? '',

            'description' => $event->description ?? '',

            'event_date' => $event->event_date
                ? Carbon::parse($event->event_date)->format('d M Y')
                : null,

            'day' => $event->event_date
                ? Carbon::parse($event->event_date)->format('l')
                : null,

            'event_time' => $event->event_time ?? '',

            'location' => $event->location ?? '',

            'status' => ucfirst($event->status ?? 'active'),

            'banner' => $event->banner
                ? asset('storage/' . $event->banner)
                : null,
        ];
    }
}