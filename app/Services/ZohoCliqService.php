<?php
// app/Services/ZohoCliqService.php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class ZohoCliqService
{
    protected $client;
    
    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://accounts.zoho.com/oauth/v2/']);
    }

    public function getAccessToken()
    {
        $accessToken = Cache::get('zoho_access_token');
// dd(env('ZOHO_REFRESH_TOKEN'));
        if (!$accessToken) {
            $response = $this->client->post('token', [
                'form_params' => [
                    'refresh_token' => "1000.f2d84912064a5e0b6d3964591f093457.231ca1e1197557290a2b12d2e1d9312f",
                    'client_id' => "1000.f2d84912064a5e0b6d3964591f093457.231ca1e1197557290a2b12d2e1d9312f",
                    'client_secret' => "3b51422582ec7837d8ac929bd582f6d9956c22e104",
                    'grant_type' => 'refresh_token',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            $accessToken = $data['access_token'];

            Cache::put('zoho_access_token', $accessToken, $data['expires_in'] - 60);
        }

        return $accessToken;
    }

    public function createMeeting($params)
    {
        $accessToken = $this->getAccessToken();
        $response = $this->client->post('https://cliq.zoho.com/api/v2/meetings', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ],
            'json' => $params,
        ]);

        return json_decode($response->getBody(), true);
    }
}
