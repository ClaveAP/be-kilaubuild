<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Google\Service\Calendar;
use Illuminate\Http\Request;
use App\Services\GoogleCalendarService;

class CalendarController extends Controller{
    protected $calendarService;

    public function __construct(GoogleCalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

     public function getEvents(Request $request)
    {
        \Log::info('=== CALENDAR EVENTS REQUEST START ===');
        \Log::info('Request params:', $request->all());

        try {
            $request->validate([
                'start' => 'required|date',
                'end' => 'required|date',
            ]);

            $start = Carbon::parse($request->start);
            $end = Carbon::parse($request->end);

            // Batasi range maksimal 31 hari
            $dateRangeInDays = $start->diffInDays($end);
            if ($dateRangeInDays > 31) {
                $end = $start->copy()->addDays(31);
            }

            $calendarId = 'aa81d34788905de369f3fa9ecf55d216fdf18ddd069e868b4372ebeb359d2858@group.calendar.google.com';

            // Ambil events langsung dari Google Calendar
            $service = new Calendar($this->calendarService->getClient());
        
            $optParams = [
                'timeMin' => $start->toIso8601String(),
                'timeMax' => $end->toIso8601String(),
                'singleEvents' => true,
                'orderBy' => 'startTime',
            ];

            $results = $service->events->listEvents($calendarId, $optParams);
            $events = $results->getItems();

            // Format events untuk FullCalendar
            $formattedEvents = [];
            
            foreach ($events as $event) {
                $eventStart = $event->getStart()->dateTime ?: $event->getStart()->date;
                $eventEnd = $event->getEnd()->dateTime ?: $event->getEnd()->date;
                
                // Tentukan apakah event all-day
                $isAllDay = !$event->getStart()->dateTime;

                $formattedEvents[] = [
                    'id' => $event->getId(),
                    'title' => $event->getSummary() ?: '🟥 SIBUK',
                    'start' => $eventStart,
                    'end' => $eventEnd,
                    'allDay' => $isAllDay,
                    'color' => '#ff4444',
                    'textColor' => '#ffffff',
                    'borderColor' => '#cc0000',
                    'extendedProps' => [
                        'description' => $event->getDescription(),
                        'location' => $event->getLocation(),
                    ]
                ];
            }

            \Log::info('Returning events', [
                'events_count' => count($formattedEvents)
            ]);
            
            return response()->json($formattedEvents);

        } catch (\Exception $e) {
            \Log::error('Calendar Events Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        
            return response()->json([
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getEventDetails(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        $calendarId = 'aa81d34788905de369f3fa9ecf55d216fdf18ddd069e868b4372ebeb359d2858@group.calendar.google.com';

        try {
            $service = new Calendar($this->calendarService->getClient());
        
            $optParams = array(
                'timeMin' => $request->start,
                'timeMax' => $request->end,
                'singleEvents' => true,
                'orderBy' => 'startTime',
            );

            $results = $service->events->listEvents($calendarId, $optParams);
            $events = $results->getItems();

            $formattedEvents = [];
            foreach ($events as $event) {
                $start = $event->getStart()->dateTime;
                if (!$start) {
                    $start = $event->getStart()->date;
                }
            
                $end = $event->getEnd()->dateTime;
                if (!$end) {
                    $end = $event->getEnd()->date;
                }

                $formattedEvents[] = [
                    'title' => $event->getSummary(),
                    'start' => $start,
                    'end' => $end,
                    'description' => $event->getDescription(),
                    'location' => $event->getLocation(),  // Data lokasi sudah ada di sini
                ];
            }

            return response()->json($formattedEvents);

        } catch (\Exception $e) {
            \Log::error('Event Details Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch event details: ' . $e->getMessage()], 500);
        }
    }
}