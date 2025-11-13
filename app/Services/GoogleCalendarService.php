<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Illuminate\Support\Facades\Log;
use Google\Service\Calendar\FreeBusyRequest;
use Google\Service\Calendar\FreeBusyRequestItem;

class GoogleCalendarService{
    protected $client;

    public function __construct(){
        $this->client = new Client();
        
        // path file service account
        $this->client->setAuthConfig(storage_path('app/kilau-service-account-key.json'));
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
            ob_start();
            
            $service = new Calendar($this->client);

            $request = new FreeBusyRequest();
            $request->setTimeMin($timeMin);
            $request->setTimeMax($timeMax);
            $request->setTimeZone($timeZone);
            
            $item = new FreeBusyRequestItem();
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