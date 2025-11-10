<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\contact;
use Illuminate\Http\Request;
use App\Services\GoogleCalendarService;

class CalendarController extends Controller{
    protected $calendarService;

    public function __construct(GoogleCalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    private function getWhatsAppNumber(){
        $contact = contact::latest()->get(); // Ambil data contact pertama
            return $contact->no_telp; // Fallback nomor default
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

            // Batasi range
            $dateRangeInDays = $start->diffInDays($end);
            if ($dateRangeInDays > 30) {
                $end = $start->copy()->addDays(31);
            }

            $calendarId = 'aa81d34788905de369f3fa9ecf55d216fdf18ddd069e868b4372ebeb359d2858@group.calendar.google.com';

            $timeMin = $start->toIso8601String();
            $timeMax = $end->toIso8601String();

            // UBAH: Gunakan events list bukan freebusy untuk mengecek ada/tidaknya event
            $service = new \Google\Service\Calendar($this->calendarService->getClient());
        
            $optParams = array(
                'timeMin' => $timeMin,
                'timeMax' => $timeMax,
                'singleEvents' => true,
                'orderBy' => 'startTime',
            );

            $results = $service->events->listEvents($calendarId, $optParams);
            $events = $results->getItems();

            // UBAH: Kelompokkan events per hari
            $busyDays = [];
            
            foreach ($events as $event) {
                $eventStart = $event->getStart()->dateTime;
                if (!$eventStart) {
                    $eventStart = $event->getStart()->date; // All-day events
                }
                
                $eventDate = Carbon::parse($eventStart)->format('Y-m-d');
                $busyDays[$eventDate] = true;
            }

            // UBAH: Buat event busy untuk setiap hari yang memiliki event
            $formattedEvents = [];
            foreach ($busyDays as $date => $isBusy) {
                $formattedEvents[] = [
                    'id' => 'busy-' . $date,
                    'title' => '🟥 SIBUK',
                    'start' => $date, // Tanggal saja (all-day event)
                    'allDay' => true, // Tandai sebagai all-day event
                    'color' => '#ff4444',
                    'textColor' => '#ffffff',
                    'borderColor' => '#cc0000',
                ];
            }

            \Log::info('Returning busy days', [
                'busy_days_count' => count($busyDays),
                'total_events_found' => count($events)
            ]);
            
            return response()->json($formattedEvents);

        } catch (\Exception $e) {
            \Log::error('Calendar Events Error', [
                'error' => $e->getMessage()
            ]);
        
            return response()->json([
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    // public function getEvents(Request $request)
    // {
    //     \Log::info('=== CALENDAR EVENTS REQUEST START ===');
    //     \Log::info('Request params:', $request->all());

    //     try {
    //         $request->validate([
    //             'start' => 'required|date',
    //             'end' => 'required|date',
    //        ]);

    //         $start = Carbon::parse($request->start);
    //         $end = Carbon::parse($request->end);

    //         // Batasi range
    //         $dateRangeInDays = $start->diffInDays($end);
    //         if ($dateRangeInDays > 30) {
    //             $end = $start->copy()->addDays(31);
    //         }

    //         $calendarId = 'aa81d34788905de369f3fa9ecf55d216fdf18ddd069e868b4372ebeb359d2858@group.calendar.google.com';

    //         $timeMin = $start->toIso8601String();
    //         $timeMax = $end->toIso8601String();

    //         $response = $this->calendarService->getFreeBusy($calendarId, $timeMin, $timeMax);
    //         $calendars = $response->getCalendars();
        
    //         $events = [];
        
    //         if (isset($calendars[$calendarId])) {
    //             $busySlots = $calendars[$calendarId]->getBusy() ?? [];
            
    //             foreach ($busySlots as $index => $slot) {
    //                 $events[] = [
    //                     'id' => 'busy-' . $index,
    //                     'title' => '🟥 SIBUK',
    //                     'start' => $slot->getStart(),
    //                     'end' => $slot->getEnd(),
    //                     'color' => '#ff4444', // Merah
    //                     'textColor' => '#ffffff',
    //                     'borderColor' => '#cc0000',
    //                     // HAPUS display: 'background' agar muncul di semua view
    //                 ];
    //             }
    //         }

    //         \Log::info('Returning events', ['events_count' => count($events)]);
    //         return response()->json($events);

    //     } catch (\Exception $e) {
    //         \Log::error('Calendar Events Error', [
    //             'error' => $e->getMessage()
    //         ]);
        
    //         return response()->json([
    //             'error' => 'Server error: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function getEventDetails(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        $calendarId = 'aa81d34788905de369f3fa9ecf55d216fdf18ddd069e868b4372ebeb359d2858@group.calendar.google.com';

        try {
            // Use Google Calendar API to get actual event details
            $service = new \Google\Service\Calendar($this->calendarService->getClient());
        
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
                    'location' => $event->getLocation(),  // <- Data lokasi sudah ada di sini
                ];
            }

            return response()->json($formattedEvents);

        } catch (\Exception $e) {
            \Log::error('Event Details Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch event details: ' . $e->getMessage()], 500);
        }
    }
}