<?php

namespace App\Services;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use App\Models\GoogleOAuthToken;
use Carbon\Carbon;
use Google_Service_Calendar_EventDateTime;
use Google_Service_Calendar_ConferenceData;
use Google_Service_Calendar_CreateConferenceRequest;
class GoogleCalendarService
{
    protected $client;
    public function __construct() {
        $client = new Google_Client();
//local client id '227227998403-60lvijob8t44t8dp2ukp4jnh7aif06jm.apps.googleusercontent.com'        
        $client->setClientId(config("app.google_client_id"));
        //local secret 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
        $client->setClientSecret(config("app.google_secret"));
        $client->setRedirectUri(config("app.url")."/api/v1/google/callback");
        $client->setAccessType('offline'); // Gets us our refresh token
        $client->setApprovalPrompt('force');
        $client->setScopes([Google_Service_Calendar::CALENDAR]);

        $this->client = $client;
        $this->initializeToken();
    }
    private function initializeToken() {
        $tokenModel = GoogleOAuthToken::first();

        if (!$tokenModel) {
            return; // No token available, redirect user to authorization flow
        }

        $this->client->setAccessToken([
            'access_token' => $tokenModel->access_token,
            'refresh_token' => $tokenModel->refresh_token,
            'expires_in' => Carbon::parse($tokenModel->token_expires_at)->diffInSeconds(Carbon::now()),
        ]);

        if ($this->client->isAccessTokenExpired()) {
            $this->refreshAccessToken();
        }
    }

    private function refreshAccessToken() {
        $refreshToken = $this->client->getRefreshToken();
        if ($refreshToken) {
            $newAccessToken = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
            $this->saveTokens($newAccessToken);
        }
    }
    public function createGoogleMeetEvent($meetingDetails)
    {
        $client = $this->getClient(); // Ensure this getClient method initializes and authenticates your Google_Client
        // dd($client);
        $calendarService = new Google_Service_Calendar($client);
    
        $event = new Google_Service_Calendar_Event([
            'summary' => $meetingDetails['summary'],
    'description' => $meetingDetails['description'],
    'start' => new Google_Service_Calendar_EventDateTime([
        'dateTime' => $meetingDetails['startDateTime'], // e.g., "2024-01-01T10:00:00"
        'timeZone' => 'America/New_York', // Ensure this is a valid IANA time zone
    ]),
    'end' => new Google_Service_Calendar_EventDateTime([
        'dateTime' => $meetingDetails['endDateTime'], // e.g., "2024-01-01T11:00:00"
        'timeZone' => 'America/New_York', // Ensure this is a valid IANA time zone
    ]),
            'conferenceData' => new Google_Service_Calendar_ConferenceData([
                'createRequest' => new Google_Service_Calendar_CreateConferenceRequest([
                    'requestId' => 'random-string-' . time(), // Unique ID to avoid conflict in concurrent requests
                    'conferenceSolutionKey' => ['type' => 'hangoutsMeet']
                ])
            ]),
        ]);
    
        try {
            // Pass conferenceDataVersion as an option to the insert method
            $createdEvent = $calendarService->events->insert('primary', $event, ['conferenceDataVersion' => 1]);
             return $createdEvent;
        } catch (Exception $e) {
            // Handle exception if something goes wrong
            // Log::error("Failed to create Google Meet event: " . $e->getMessage());
            // throw new \Exception("Failed to create Google Meet event.", 0, $e);
        }
    }
    public function saveTokens(array $token) {
        if($token && isset($token['expires_in'])){
        $expiresAt = Carbon::now()->addSeconds($token['expires_in']);

        GoogleOAuthToken::updateOrCreate(
            ['id' => 1], // Assuming you're using a single token row.
            [
                'access_token' => $token['access_token'],
                'refresh_token' => $token['refresh_token'] ?? null,
                'token_expires_at' => $expiresAt,
            ]
        );

        // Update the Google_Client with the new tokens
        $this->client->setAccessToken($token);
    }
    }

    public function getClient()
    {
        $tokenModel = GoogleOAuthToken::first(); // Fetch the tokens from the database
    
        if ($tokenModel) {
            // Manually convert to Carbon instance if not already
            $expiresAt = Carbon::parse($tokenModel->token_expires_at);
    
            $this->client->setAccessToken([
                'access_token' => $tokenModel->access_token,
                'refresh_token' => $tokenModel->refresh_token,
                'expires_in' => $expiresAt->getTimestamp() - time(),
            ]);
    
            if ($this->client->isAccessTokenExpired()) {
                // Logic to refresh the token
            }
        }
    
        return $this->client;
    }


    // Other methods...
}