<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService{
    protected $client;

    public function __construct(){
        $this->client = new Client();
        
        // GANTI dengan path file service account Anda
        $this->client->setAuthConfig(storage_path('app/service-account-key.json'));
        $this->client->addScope(Calendar::CALENDAR_READONLY);
        
        // Untuk development, non-aktifkan SSL verification
        $httpClient = new \GuzzleHttp\Client([
            'verify' => false,
            'timeout' => 30,
        ]);
        $this->client->setHttpClient($httpClient);
    }
    
    public function getFreeBusy($calendarId, $timeMin, $timeMax, $timeZone = 'Asia/Jakarta'){
        try {
            // Suppress output buffer untuk mencegah output dari Google library
            ob_start();
            
            $service = new Calendar($this->client);

            $request = new \Google\Service\Calendar\FreeBusyRequest();
            $request->setTimeMin($timeMin);
            $request->setTimeMax($timeMax);
            $request->setTimeZone($timeZone);
            
            $item = new \Google\Service\Calendar\FreeBusyRequestItem();
            $item->setId($calendarId);
            $request->setItems([$item]);

            $response = $service->freebusy->query($request);
            
            // Clean any output buffer
            ob_end_clean();
            
            Log::info('FreeBusy fetched successfully', [
                'calendar_id' => $calendarId,
                'time_range' => $timeMin . ' to ' . $timeMax,
                'busy_slots' => count($response->getCalendars()[$calendarId]->getBusy() ?? [])
            ]);

            return $response;

        } catch (\Exception $e) {
            // Clean buffer even on error
            ob_end_clean();
            
            Log::error('FreeBusy Error', [
                'calendar_id' => $calendarId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    public function getClient()
    {
        return $this->client;
    }
}