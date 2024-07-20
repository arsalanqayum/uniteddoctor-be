<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Service;
use App\Models\ZohoSetting;
use App\Traits\Zoho\ZohoErrorResponse;
use Carbon\Carbon;
use com\zoho\api\authenticator\OAuthBuilder;
use com\zoho\crm\api\InitializeBuilder;
use com\zoho\crm\api\UserSignature;
use com\zoho\crm\api\dc\USDataCenter;
use com\zoho\crm\api\record\RecordOperations;
use com\zoho\crm\api\HeaderMap;
use com\zoho\crm\api\ParameterMap;
use com\zoho\crm\api\record\GetRecordsHeader;
use com\zoho\crm\api\record\GetRecordsParam;
use com\zoho\crm\api\record\ResponseWrapper;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZohoService
{
    use ZohoErrorResponse;
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }
    public function createProduct($productDetails)
    {
        $accessToken = $this->getAccessToken();
        $response = $this->client->request('POST', 'https://www.zohoapis.com/crm/v2/Products', [
            'headers' => [
                'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'data' => [$productDetails]
            ]
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }
    public function getAccessToken()
    {
        $zohoSetting = ZohoSetting::firstOrFail();
        if (Carbon::parse($zohoSetting->expires)->isPast()) {
            // dd('sss');
            $client = new Client();
            $response = $client->post('https://accounts.zoho.com/oauth/v2/token', [
                'form_params' => [
                    'refresh_token' => $zohoSetting->refresh_token,
                    'client_id' => config('services.zoho.client_id'),
                    'client_secret' => '3b51422582ec7837d8ac929bd582f6d9956c22e104',
                    'grant_type' => 'refresh_token',
                ]
            ]);
            $responseBody = json_decode($response->getBody(), true);
            // dd($responseBody);
            $zohoSetting->update([
                'token' => $responseBody['access_token'],
                'expires' =>  Carbon::now()->addDays(30)->timestamp,
            ]);
        }
        return $zohoSetting->token;
    }
    public function authorization()
    {
        $zoho_setting = ZohoSetting::first(); // Ensure there's a first entry, or handle potential nulls
        if (!$zoho_setting) {
            Log::error('No Zoho settings available.');
            return '';
        }

        $client = new Client();
        try {
            $response = $client->post('https://accounts.zoho.com/oauth/v2/token', [
                'form_params' => [
                    'refresh_token' => $zoho_setting->refresh_token,
                    'client_id' => '1000.ESZPSZARBVUVMML69E5OF334GFTMKW',  // Moved to .env and accessed via config
                    'client_secret' => config('services.zoho.clientSecret'),  // Moved to .env
                    'grant_type' => 'refresh_token',
                ]
            ]);

            $responseBody = json_decode((string) $response->getBody(), true);
            if (isset($responseBody['access_token'])) {
                $zoho_setting->token = $responseBody['access_token'];
                $zoho_setting->expires = Carbon::now()->addDays(30)->timestamp;
                $zoho_setting->save();
                return $zoho_setting->token;
            } else {
                Log::error('Failed to refresh Zoho token: ' . json_encode($responseBody));
                return '';
            }
        } catch (GuzzleException $e) {
            Log::error('HTTP request failed: ' . $e->getMessage());
            return '';
        }
    }


    public function getZohoOrganizationId()
    {
        $accessToken = $this->getAccessToken();
        $response = $this->client->get('https://desk.zoho.com/api/v1/organizations', [
            'headers' => [
                'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
                'Content-Type' => 'application/json',
            ]
        ]);

        $responseBody = json_decode($response->getBody(), true);
        if (isset($responseBody['data'][0]['id'])) {
            return $responseBody['data'][0]['id'];
        } else {
            Log::error('Failed to retrieve Zoho Organization ID: ' . json_encode($responseBody));
            return null;
        }
    }
    public function getZohoUserId()
{
    $accessToken = $this->getAccessToken();
    $url = 'https://meeting.zoho.com/api/v2/user.json';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Zoho-oauthtoken ' . $accessToken,
        'Content-Type: application/json;charset=UTF-8',
    ]);
    curl_setopt($ch, CURLOPT_HTTPGET, true);

    $response = curl_exec($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlInfo = curl_getinfo($ch);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return ['error' => 'Curl error: ' . $error_msg];
    }

    curl_close($ch);

    if ($httpStatus >= 400) {
        return [
            'error' => 'HTTP error: ' . $httpStatus,
            'response' => $response,
            'curl_info' => $curlInfo
        ];
    }

    $decodedResponse = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'error' => 'JSON decode error: ' . json_last_error_msg(),
            'response' => $response,
            'curl_info' => $curlInfo
        ];
    }

    return $decodedResponse;
}

    public function createZohoMeeting($meetingDetails)
    {
      $zohoUserId = $this->getZohoUserId()['userDetails']['zuid'];
        $zsoid = $this->getZohoOrganizationId();
        if (!$zsoid) {
            return ['error' => 'Failed to retrieve Zoho Organization ID'];
        }
    
        $accessToken = $this->getAccessToken();
        $url = "https://meeting.zoho.com/api/v2/{$zsoid}/sessions.json";
        $payload = [
            'session' => [
                'topic' => 'Test Meeting',
                'agenda' => 'Points to get noted during meeting.',
                'presenter' => $zohoUserId, // Replace with actual valid ZUID
                'startTime' => 'Aug 05, 2025 10:00 PM',
                'duration' => 3600000, // Duration in milliseconds (1 hour)
                'timezone' => 'Asia/Kolkata',
                'participants' => [
                    [
                        'email' => 'arsalanqayum@gmail.com'
                    ]
                ]
            ]
        ];
    
    
        $jsonPayload = json_encode($payload);
    
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Zoho-oauthtoken ' . $accessToken,
            'Content-Type: application/json;charset=UTF-8',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
    
        $response = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlInfo = curl_getinfo($ch);
    
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return ['error' => 'Curl error: ' . $error_msg];
        }
    
        curl_close($ch);
    
        if ($httpStatus >= 400) {
            return [
                'error' => 'HTTP error: ' . $httpStatus,
                'response' => $response,
                'curl_info' => $curlInfo
            ];
        }
    
        $decodedResponse = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'error' => 'JSON decode error: ' . json_last_error_msg(),
                'response' => $response,
                'curl_info' => $curlInfo
            ];
        }
    
        return $decodedResponse['session']['joinLink'];
    }
    
    

    
    // public function createZohoMeeting($meetingDetails)
    // {
    //     $zsoid = $this->getZohoOrganizationId();
    //     // dd($zsoid);
    //     if (!$zsoid) {
    //         return ['error' => 'Failed to retrieve Zoho Organization ID'];
    //     }


    //     $accessToken = $this->getAccessToken();
    //     $url = "https://meeting.zoho.com/api/v2/{$zsoid}/sessions.json";
    //     $payload = [
    //         'session' => [
    //             'topic' => 'New Meeting',
    //             'agenda' => 'Points to get noted during meeting.',
    //             'presenter' => 5879676000001556002, // Replace with actual presenter ID
    //             'startTime' => 'Aug 05, 2025 10:00 PM',
    //             'duration' => 3600000, // Duration in milliseconds (1 hour)
    //             'timezone' => 'Asia/Kolkata',
    //             'participants' => [
    //                 [
    //                     'email' => 'arsalanqayum@gmail.com'
    //                 ]
    //             ]
    //         ]
    //     ];

    //     $jsonPayload = json_encode($payload);

    //     $ch = curl_init($url);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, [
    //         'Authorization: Zoho-oauthtoken ' . $accessToken,
    //         'Content-Type: application/json;charset=UTF-8',
    //     ]);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);

    //     $response = curl_exec($ch);

    //     if (curl_errno($ch)) {
    //         // Log or handle error
    //         $error_msg = curl_error($ch);
    //         echo 'Error: ' . $error_msg;
    //     }

    //     curl_close($ch);

    //     // Decode and return response
    //     return json_decode($response, true);

    //     //     $url = "https://meeting.zoho.com/api/v2/{$zsoid}/sessions.json";
    //     //     $payload = [
    //     //         'session' => [
    //     //             'topic' => 'New Meeting',
    //     //             'agenda' => 'Points to get noted during meeting.',
    //     //             'presenter' => 5879676000001556002, // Replace with actual presenter ID
    //     //             'startTime' => 'Aug 05, 2025 10:00 PM',
    //     //             'duration' => 1800000, // Duration in milliseconds (30 minutes)
    //     //             'timezone' => 'Asia/Kolkata',
    //     //             'participants' => [
    //     //                 [
    //     //                     'email' => 'arsalanqayum@gmail.com'
    //     //                 ]
    //     //             ]
    //     //         ]
    //     //     ];

    //     //     $jsonPayload = json_encode($payload);
    //     // // dd('Zoho-oauthtoken '.$accessToken);
    //     //     $response = $this->client->request('POST', $url, [
    //     //         'headers' => [
    //     //             'Authorization' => 'Zoho-oauthtoken '.$accessToken,
    //     //             'Content-Type' => 'application/json;charset=UTF-8',
    //     //         ],
    //     //         'body' => $jsonPayload,
    //     //     ]);

    //     //     return json_decode($response->getBody()->getContents(), true);
    // }

    //     public function createZohoMeeting($meetingDetails)
    // {
    //     $zsoid = $this->getZohoOrganizationId();

    //     if (!$zsoid) {
    //         return ['error' => 'Failed to retrieve Zoho Organization ID'];
    //     }

    //     $accessToken = $this->getAccessToken();
    //     $url = "https://meeting.zoho.com/api/v2/{$zsoid}/sessions.json";
    //     $payload = [
    //         'session' => [
    //             'topic' => 'New Meeting',
    //             'start_time' => '2025-08-05T22:00:00+05:30',
    //             'end_time' => '2025-08-05T22:30:00+05:30',
    //             'timezone' => 'Asia/Kolkata',
    //             'se_module' => 'Contacts',
    //             'participants' => [
    //                 [
    //                     'email' => 'arsalanqayum@gmail.com',
    //                     'name' => 'Arsalan Qayum',
    //                     'invited' => true,
    //                     'type' => 'contact',
    //                     'participant_id' => '5879676000001556002'
    //                 ]
    //             ],
    //             'send_notification' => true
    //         ]
    //     ];

    //     $jsonPayload = json_encode($payload);

    //     $response = $this->client->request('POST', $url, [
    //         'headers' => [
    //             'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
    //             'Content-Type' => 'application/json;charset=UTF-8',
    //         ],
    //         'body' => $jsonPayload,
    //     ]);

    //     return json_decode($response->getBody()->getContents(), true);
    // }

    // public function createZohoMeeting($meetingDetails)
    // {
    //     $zsoid = $this->getZohoOrganizationId();

    //     if (!$zsoid) {
    //         return ['error' => 'Failed to retrieve Zoho Organization ID'];
    //     }

    //     $accessToken = $this->getAccessToken();
    //     $url = "https://meeting.zoho.com/api/v2/{$zsoid}/sessions.json";
    //     $payload = [
    //         'session' => [
    //             'topic' => 'New Meeting',
    //             'scheduled_on' => '2025-08-05T22:00:00+05:30',
    //             'duration' => 30,
    //             'timezone' => 'Asia/Kolkata',
    //             'who_id' => '5879676000001656001',
    //             'se_module' => 'Contacts',
    //             'participants' => [
    //                 [
    //                     'email' => 'arsalanqayum@gmail.com',
    //                     'name' => 'Arsalan Qayum',
    //                     'invited' => true,
    //                     'type' => 'contact',
    //                     'participant_id' => '5879676000001556002'
    //                 ]
    //             ],
    //             'send_notification' => true
    //         ]
    //     ];

    //     $jsonPayload = json_encode($payload);

    //     $response = $this->client->request('POST', $url, [
    //         'headers' => [
    //             'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
    //             'Content-Type' => 'application/json;charset=UTF-8',
    //         ],
    //         'body' => $jsonPayload,
    //     ]);

    //     return json_decode($response->getBody()->getContents(), true);
    // }


    //     public function createZohoMeeting($meetingDetails)
    // {
    //     $zsoid = $this->getZohoOrganizationId();

    //     if (!$zsoid) {
    //         return ['error' => 'Failed to retrieve Zoho Organization ID'];
    //     }

    //     $accessToken = $this->getAccessToken();
    //     $url = "https://meeting.zoho.com/api/v2/{$zsoid}/sessions.json";
    //     $payload = [
    //         'session' => [
    //             'topic' => 'New Meeting',
    //             'start_time' => '2025-08-05T22:00:00+05:30',
    //             'end_time' => '2025-08-05T22:30:00+05:30',
    //             'who_id' => '5879676000001656001',
    //             'se_module' => 'Contacts',
    //             'participants' => [
    //                 [
    //                     'email' => 'arsalanqayum@gmail.com',
    //                     'name' => 'Arsalan Qayum',
    //                     'invited' => true,
    //                     'type' => 'contact',
    //                     'participant_id' => '5879676000001556002'
    //                 ]
    //             ],
    //             'send_notification' => true
    //         ]
    //     ];

    //     $jsonPayload = json_encode($payload);

    //     $response = $this->client->request('POST', $url, [
    //         'headers' => [
    //             'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
    //             'Content-Type' => 'application/json;charset=UTF-8',
    //         ],
    //         'body' => $jsonPayload,
    //     ]);

    //     return json_decode($response->getBody()->getContents(), true);
    // }

    //     public function createZohoMeeting($meetingDetails)
    // {
    //     $zsoid = $this->getZohoOrganizationId();

    //     if (!$zsoid) {
    //         return ['error' => 'Failed to retrieve Zoho Organization ID'];
    //     }

    //     $accessToken = $this->getAccessToken();
    //     $url = "https://meeting.zoho.com/api/v2/{$zsoid}/sessions.json";
    //     $payload = [
    //         'session' => [
    //             'topic' => 'New Meeting',
    //             'start_time' => '2025-08-05T22:00:00+05:30',
    //             'end_time' => '2025-08-05T22:30:00+05:30',
    //             'who_id' => '5879676000001656001',
    //             'se_module' => 'Contacts',
    //             'participants' => [
    //                 [
    //                     'email' => 'arsalanqayum@gmail.com',
    //                     'name' => 'Arsalan Qayum',
    //                     'invited' => true,
    //                     'type' => 'contact',
    //                     'participant_id' => '5879676000001556002'
    //                 ]
    //             ],
    //             'send_notification' => true
    //         ]
    //     ];

    //     $jsonPayload = json_encode($payload);
    //     echo $jsonPayload; // Debugging line to print the payload

    //     $response = $this->client->request('POST', $url, [
    //         'headers' => [
    //             'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
    //             'Content-Type' => 'application/json;charset=UTF-8',
    //         ],
    //         'body' => $jsonPayload,
    //     ]);

    //     return json_decode($response->getBody()->getContents(), true);
    // }

    //     public function createZohoMeeting($meetingDetails)
    // {
    //     $zsoid = $this->getZohoOrganizationId();

    //     if (!$zsoid) {
    //         return ['error' => 'Failed to retrieve Zoho Organization ID'];
    //     }

    //     $accessToken = $this->getAccessToken();
    //     $url = "https://meeting.zoho.com/api/v2/{$zsoid}/sessions.json";
    //     $payload = [
    //         'Event_Title' => 'New Meeting',
    //         'send_notification' => true,
    //         'Start_DateTime' => '2025-08-05T22:00:00+05:30',
    //         'End_DateTime' => '2025-08-05T22:30:00+05:30',
    //         'Who_Id' => '5879676000001656001',
    //         'se_module' => 'Contacts',
    //         'Participants' => [
    //             [
    //                 'Email' => 'arsalanqayum@gmail.com',
    //                 'name' => 'Arsalan Qayum',
    //                 'invited' => true,
    //                 'type' => 'contact',
    //                 'participant' => '5879676000001556002'
    //             ]
    //         ]
    //     ];

    //     $response = $this->client->request('POST', $url, [
    //         'headers' => [
    //             'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
    //             'Content-Type' => 'application/json;charset=UTF-8',
    //         ],
    //         'body' => json_encode($payload),
    //     ]);

    //     return json_decode($response->getBody()->getContents(), true);
    // }

    // public function createZohoMeeting($meetingDetails)
    // {
    //     $zsoid = $this->getZohoOrganizationId();

    //     if (!$zsoid) {
    //         return ['error' => 'Failed to retrieve Zoho Organization ID'];
    //     }

    //     $accessToken = $this->getAccessToken();
    //     $url = "https://meeting.zoho.com/api/v2/{$zsoid}/sessions.json";
    //     $payload = [
    //         'Event_Title' => 'New Meeting',
    //         'send_notification' => true,
    //         'Start_DateTime' => '2025-08-05T22:00:00+05:30',
    //         'End_DateTime' => '2025-08-05T22:30:00+05:30',
    //         'meeting_details' => [
    //             'tool_name' => 'ZoomMeeting'
    //         ],
    //         'Who_Id' => '5879676000001656001',
    //         'se_module' => 'Contacts',
    //         'Participants' => [
    //             [
    //                 'Email' => 'arsalanqayum@gmail.com',
    //                 'name' => 'Arsalan Qayum',
    //                 'invited' => true,
    //                 'type' => 'contact',
    //                 'participant' => '5879676000001556002'
    //             ]
    //         ]
    //     ];

    //     $response = $this->client->request('POST', $url, [
    //         'headers' => [
    //             'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
    //             'Content-Type' => 'application/json;charset=UTF-8',
    //         ],
    //         'body' => json_encode($payload),
    //     ]);

    //     return json_decode($response->getBody()->getContents(), true);
    // }

    // public function createZohoMeeting($meetingDetails)
    // {
    //     $zsoid = $this->getZohoOrganizationId();

    //     if (!$zsoid) {
    //         return ['error' => 'Failed to retrieve Zoho Organization ID'];
    //     }

    //     $accessToken = $this->getAccessToken();
    //     $url = "https://meeting.zoho.com/api/v2/{$zsoid}/sessions.json";
    //     $payload = [
    //         'data' => [
    //             [
    //                 'Event_Title' => 'New Meeting',
    //                 'send_notification' => true,
    //                 'Start_DateTime' => '2025-08-05T22:00:00+05:30',
    //                 'End_DateTime' => '2025-08-05T22:30:00+05:30',
    //                 'meeting_details' => [
    //                     'tool_name' => 'ZoomMeeting'
    //                 ],
    //                 'Who_Id' => '5879676000001656001',
    //                 'se_module' => 'Contacts',
    //                 'Participants' => [
    //                     [
    //                         'Email' => 'arsalanqayum@gmail.com',
    //                         'name' => 'Arsalan Qayum',
    //                         'invited' => true,
    //                         'type' => 'contact',
    //                         'participant' => '5879676000001556002'
    //                     ]
    //                 ]
    //             ]
    //         ]
    //     ];

    //     $response = $this->client->request('POST', $url, [
    //         'headers' => [
    //             'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
    //             'Content-Type' => 'application/json;charset=UTF-8',
    //         ],
    //         'body' => json_encode($payload),
    //     ]);

    //     return json_decode($response->getBody()->getContents(), true);
    // }


    // public function createZohoMeeting($meetingDetails)
    // {

    //     $zsoid = $this->getZohoOrganizationId();

    //     if (!$zsoid) {
    //         return ['error' => 'Failed to retrieve Zoho Organization ID'];
    //     }

    //     $accessToken = $this->getAccessToken();
    //     $url = "https://meeting.zoho.com/api/v2/{$zsoid}/sessions.json";
    //     $payload = json_encode([
    //         'data' => [
    //             [
    //                 'Event_Title' => 'New Meeting',
    //                 'send_notification' => true,
    //                 'Start_DateTime' => '2025-08-05T22:00:00+05:30',
    //                 'End_DateTime' => '2025-08-05T22:30:00+05:30',
    //                 'meeting_details' => [
    //                     'tool_name' => 'ZoomMeeting'
    //                 ],
    //                 'Who_Id' => '5879676000001656001',
    //                 'se_module' => 'Contacts',
    //                 'Participants' => [
    //                     [
    //                         'Email' => 'arsalanqayum@gmail.com',
    //                         'name' => 'Arsalan Qayum',
    //                         'invited' => true,
    //                         'type' => 'contact',
    //                         'participant' => '5879676000001556002'
    //                     ]
    //                 ]
    //             ]
    //         ]
    //     ]);
    //     $payload = json_encode($payload);

    //     $response = $this->client->request('POST', $url, [
    //         'headers' => [
    //             'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
    //             'Content-Type' => 'application/json;charset=UTF-8',
    //         ],
    //         'body' => $payload,
    //     ]);

    //     return json_decode($response->getBody()->getContents(), true);
    // }


    public function createMeeting($params)
    {
        $accessToken = $this->getAccessToken();

        $payload = [
            'data' => [
                [
                    'Event_Title' => 'New Meeting',
                    'send_notification' => true,
                    'Start_DateTime' => '2025-08-05T22:00:00+05:30',
                    'End_DateTime' => '2025-08-05T22:30:00+05:30',
                    'meeting_details' => [
                        'tool_name' => 'ZoomMeeting',
                        'meeting_url' => 'https://zoom.us/j/1234567890'
                    ],
                    'Who_Id' => '5879676000001656001',
                    'se_module' => 'Contacts',
                    'Participants' => [
                        [
                            'Email' => 'arsalanqayum@gmail.com',
                            'name' => 'Arsalan Qayum',
                            'invited' => true,
                            'type' => 'contact',
                            'participant' => '5879676000001556002'
                        ]
                    ]
                ]
            ]
        ];

        try {
            $response = $this->client->post('https://www.zohoapis.com/crm/v2/Events', [
                'headers' => [
                    'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
                    'Content-Type' => 'application/json'
                ],
                'json' => $payload
            ]);

            $responseBody = json_decode($response->getBody(), true);
            if (isset($responseBody['data'][0]['details']['id'])) {
                $this->getMeetingDetails($responseBody['data'][0]['details']['id']);
            } else {
                Log::error('Failed to create meeting: ' . json_encode($responseBody));
            }
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            Log::error('HTTP request failed: ' . $e->getMessage());
        }
    }


    public function getMeetingDetails($meetingId)
    {
        $accessToken = $this->getAccessToken();
        $response = $this->client->get('https://www.zohoapis.com/crm/v2/Events/' . $meetingId, [
            'headers' => [
                'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
                'Content-Type' => 'application/json',
            ]
        ]);

        $responseBody = json_decode($response->getBody(), true);
        if (isset($responseBody['data'])) {
            echo json_encode($responseBody['data']);
        } else {
            Log::error('Meeting link not found in meeting details: ' . json_encode($responseBody));
            throw new \Exception('Meeting link not found');
        }
    }



    //   public function authorization()
    //   {
    //       $access_token = "";
    //       $zoho_setting = ZohoSetting::find(1);
    //       $refresh_token = $zoho_setting->refresh_token;
    //       $expireTimestamp = strtotime($zoho_setting->updated_at); // Replace this with your given timestamp
    //       $currentTimestamp = Carbon::now()->timestamp;
    //       $timeDifference = $currentTimestamp - $expireTimestamp;
    //       if ($timeDifference > 3600) {

    //           $endpointUrl =  "https://accounts.zoho.com/oauth/v2/token?";
    //           $endpointUrl = $endpointUrl. "refresh_token=" . $refresh_token ."&";
    //           $endpointUrl = $endpointUrl. "client_id=" . config('services.zoho.clientId') ."&";
    //           $endpointUrl = $endpointUrl. "client_secret=" . config('services.zoho.clientSecret') ."&";
    //           $endpointUrl = $endpointUrl. "redirect_uri=" . config('services.zoho.redirectUri') ."&";
    //           $endpointUrl = $endpointUrl. "grant_type=" . "refresh_token";
    //           $curl = curl_init();

    //           curl_setopt_array($curl, array(
    //               CURLOPT_URL => $endpointUrl,
    //               CURLOPT_RETURNTRANSFER => true,
    //               CURLOPT_ENCODING => '',
    //               CURLOPT_MAXREDIRS => 10,
    //               CURLOPT_TIMEOUT => 0,
    //               CURLOPT_FOLLOWLOCATION => true,
    //               CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //               CURLOPT_CUSTOMREQUEST => 'POST',

    //           ));

    //           $response = curl_exec($curl);

    //           curl_close($curl);
    //           $response = json_decode($response, true);
    //           if(isset($response['access_token'])){
    //               $access_token = $response['access_token'];
    //           }
    //           $zoho_setting->token = $access_token;
    //           $zoho_setting->save();
    //       } else {
    //           $access_token = $zoho_setting->token;
    //       }
    //       return $access_token ?? '';
    //   }
    public function createInvoice($requestData)
    {
        $customer_id = Patient::find($requestData->patient_id)->zoho_customer_id;
        $access_token = $this->getAccessToken();
        $apiRequestData['date'] = date('Y-m-d');

        $invoiceData = [
            'Subject' => $requestData['invoice_subject'],
            'Account_Id' => $customer_id,
            'date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime($requestData->due_date)),
            'is_discount_before_tax' => true,
            'discount_type' => 'item_level',
            'Product_Details' => [],
            'Billing_Address' => '1234 Billing St, City, State, Zip',
            'Shipping_Address' => '1234 Shipping St, City, State, Zip',
            'Terms' => 'Net 30',

        ];
        $productDetails = [];
        foreach ($requestData['items'] as $item) {
            $productDetail = [
                'product' => $item['service_name']['zoho_item_id'],
                'description' => $item['description'],
                'quantity' => $item['qty'],
                'rate' => $item['price'],
                'discount' => $item['discount']
            ];

            // You can also include other details like discounts, taxes, etc., if needed:
            $productDetail['discount'] = $item['discountAmount'];
            $productDetail['tax_id'] = $item['taxAmount'];

            $productDetails[] = $productDetail;
        }
        // dd($productDetail);
        $invoiceData['Product_Details'][] = $productDetail;
        // dd($invoiceData);
        // return response()->json(['data'=>$productDetail]);




        // You can now pass this $invoiceData to your invoice creation logic

        $url = 'https://www.zohoapis.com/crm/v2/Invoices';

        try {
            $response = $this->client->request('POST', $url, [
                'headers' => [
                    'Authorization' => 'Zoho-oauthtoken ' . $access_token,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'data' => [$invoiceData]
                ]
            ]);
            // dd($invoiceData,json_decode($response->getBody()->getContents(), true));
            $this->markInvoiceSent(json_decode($response->getBody()->getContents(), true)['data'][0]['details']['id']);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            // Handle exceptions or log errors
            return ['error' => $e->getMessage()];
        }
    }
    //   public function createInvoice($requestData){

    //       $access_token = $this->getAccessToken();
    //       $customer_id = Patient::find($requestData->patient_id)->zoho_customer_id;
    //     //  dd($access_token,$customer_id);
    //       $curl = curl_init();

    //       $apiRequestData = [];
    //       $apiRequestData['customer_id'] = $customer_id;
    //       $apiRequestData['date'] = date('Y-m-d');
    //       $apiRequestData['due_date'] = date('Y-m-d', strtotime($requestData->due_date));
    //       $apiRequestData["is_discount_before_tax"] = true;
    //       $apiRequestData["discount_type"] = "item_level";
    //       $itemData = [];
    // //      dd($requestData->items);
    //       foreach ($requestData->items as $key => $item){
    // //          dd($item['total']);
    //           $itemData[$key]["item_id"] = Service::find($item['item_id'])->zoho_item_id ?? '4678568000000083182';
    //           $itemData[$key]["name"] = $item['item_name'];
    //           $itemData[$key]["rate"] = $item['rate'];
    //           $itemData[$key]["quantity"] = $item['qty'];
    //           $itemData[$key]["item_total"] = $item['total'];
    // //          $itemData[$key]["discount"] = $item['discountAmount'];
    // //          $itemData[$key]["tax_id"] = $item['discountAmount'];
    // //          $itemData[$key]["discount"] = $item['discountAmount'];
    // //          $itemData[$key]["tax_id"] = $item['tax_name']['tax_id'] ?? '4678568000000083182';
    //       }
    //       $apiRequestData['line_items'] = $itemData;
    // //      dd(json_encode($apiRequestData));

    //       curl_setopt_array($curl, array(
    //           CURLOPT_URL => 'https://www.zohoapis.com/invoice/v3/invoices',
    //           CURLOPT_RETURNTRANSFER => true,
    //           CURLOPT_ENCODING => '',
    //           CURLOPT_MAXREDIRS => 10,
    //           CURLOPT_TIMEOUT => 0,
    //           CURLOPT_FOLLOWLOCATION => true,
    //           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //           CURLOPT_CUSTOMREQUEST => 'POST',
    //           CURLOPT_POSTFIELDS =>json_encode($apiRequestData),
    //           CURLOPT_HTTPHEADER => array(
    //               'Authorization: Bearer '.$access_token,
    //               'content-type: application/json',
    // //              'Cookie: 0d082fb755=ff591c2abd8a7fed30ff6853fff1ab4c; JSESSIONID=89189B2F8F4CAEF11075C6929742F655; _zcsr_tmp=29462252-242a-49a6-acd1-643fad56f20e; zbcscook=29462252-242a-49a6-acd1-643fad56f20e'
    //           ),
    //       ));

    //       $response = curl_exec($curl);

    //       curl_close($curl);
    //       $response = json_decode($response, true);
    //       dd($response);
    //       $markInvoiceSent = $this->markInvoiceSent($response['invoice']['invoice_id']);
    // //      dd($response);
    //       return $response;
    //   }

    public function createContact(array $contactData)
    {
        $accessToken = $this->getAccessToken(); // Ensure this function retrieves a valid access token
        $client = new Client();
        // dd($contactData);
        try {
            $response = $client->request('POST', 'https://www.zohoapis.com/crm/v2/Contacts', [
                'headers' => [
                    'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'data' => [
                        [
                            'First_Name' => $contactData['first_name'], // Assuming you're passing this
                            'Last_Name' => $contactData['last_name'],   // Make sure this is included
                            // Include other fields as needed
                        ]
                    ],
                    'trigger' => [
                        'approval',
                        'workflow',
                        'blueprint'
                    ]
                ]
            ]);

            return json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            return ['error' => 'Failed to create contact', 'details' => $e->getMessage()];
        }
    }
    //   public function createContact($requestData){

    //       $access_token = $this->getAccessToken();
    //     //  dd($access_token);
    //       $curl = curl_init();

    //       $apiRequestData = [];
    //       $apiRequestData['contact_name'] = $requestData['name'];
    // //      $apiRequestData['contact_name'] = 'test customer 7';
    // //      dd(json_encode($apiRequestData));

    //       curl_setopt_array($curl, array(
    //           CURLOPT_URL => 'https://www.zohoapis.com/invoice/v3/contacts',
    //           CURLOPT_RETURNTRANSFER => true,
    //           CURLOPT_ENCODING => '',
    //           CURLOPT_MAXREDIRS => 10,
    //           CURLOPT_TIMEOUT => 0,
    //           CURLOPT_FOLLOWLOCATION => true,
    //           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //           CURLOPT_CUSTOMREQUEST => 'POST',
    //           CURLOPT_POSTFIELDS =>json_encode($apiRequestData),
    //           CURLOPT_HTTPHEADER => array(
    //               'Authorization: Zoho-oauthtoken '.$access_token,
    //               'content-type: application/json',
    // //              'Cookie: 0d082fb755=ff591c2abd8a7fed30ff6853fff1ab4c; JSESSIONID=89189B2F8F4CAEF11075C6929742F655; _zcsr_tmp=29462252-242a-49a6-acd1-643fad56f20e; zbcscook=29462252-242a-49a6-acd1-643fad56f20e'
    //           ),
    //       ));

    //       $response = curl_exec($curl);

    //       curl_close($curl);
    // //      echo $response;
    //       $response = json_decode($response, true);
    //       dd($response);
    //       // handle error response return from zoho api for contact
    // //      if(isset($response['code'])){
    // //          return '';
    // //      }
    //       if(isset($response['contact']['contact_id'])){
    //         $contactId = $response['contact']['contact_id'];
    //       }else{
    //         $contactId = $this->getContactId($requestData);
    //       }
    // //      dd($response);
    //       return $contactId;
    //   }
    public function listContact($requestData)
    {

        $access_token = $this->authorization();
        //      dd($access_token);
        $curl = curl_init();

        $apiRequestData = [];

        //      $apiRequestData['contact_name'] = 'test customer 7';
        //      dd(json_encode($apiRequestData));
        $url = 'https://www.zohoapis.com/invoice/v3/contacts?';
        if (isset($requestData['email']) && $requestData['email'] != '') {
            $url .= 'email=' . $requestData['email'];
        }
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            //            CURLOPT_POSTFIELDS =>json_encode($apiRequestData),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $access_token,
                'content-type: application/json',
                //              'Cookie: 0d082fb755=ff591c2abd8a7fed30ff6853fff1ab4c; JSESSIONID=89189B2F8F4CAEF11075C6929742F655; _zcsr_tmp=29462252-242a-49a6-acd1-643fad56f20e; zbcscook=29462252-242a-49a6-acd1-643fad56f20e'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        //      echo $response;
        $response = json_decode($response, true);
        // handle error response return from zoho api for contact
        //        if(isset($response['code'])){
        //            return '';
        //        }
        //      dd($response);
        return $response;
    }

    public function createItem($requestData)
    {
        //      dd($requestData);
        // integration zoho invoice item create api
        $access_token = $this->authorization();
        //      $apiRequestData = [];
        $apiRequestData['name'] = $requestData['name'];
        $apiRequestData['rate'] = $requestData['rate'] ?? 0;
        $apiRequestData['description'] = $requestData['description'] ?? '';
        if (isset($requestData['tax_id']) && $requestData['tax_id'] != '') {
            $apiRequestData['tax_id'] = $requestData['tax_id'];
        }
        if (isset($requestData['sku']) && $requestData['sku'] != '') {
            $apiRequestData['sku'] = $requestData['sku'];
        }
        $apiRequestData['product_type'] = $requestData['service_type'] ?? 'service';


        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://www.zohoapis.com/invoice/v3/items',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($apiRequestData),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $access_token,
                'content-type: application/json',
            ),
        ));
        $response = curl_exec($curl);

        curl_close($curl);
        //      echo $response;
        $response = json_decode($response, true);
        return $response;
    }
    public function markInvoiceSent($invoiceId)
    {
        $access_token = $this->getAccessToken();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://www.zohoapis.com/invoice/v3/invoices/' . $invoiceId . '/status/sent',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $access_token,
                'content-type: application/json',
            ),
        ));
        $response = curl_exec($curl);

        curl_close($curl);
        //      echo $response;
        $response = json_decode($response, true);
        return $response;
    }
    public function createCustomerPayment($requestData)
    {
        $access_token = $this->authorization();
        $curl = curl_init();
        //        $apiRequestData = [];
        $apiRequestData['customer_id'] = $requestData['customer_id'];
        $apiRequestData['payment_mode'] = $requestData['payment_mode'];
        $apiRequestData['amount'] = $requestData['amount'];
        $apiRequestData['bank_charges'] = $requestData['bank_charges'];
        $apiRequestData['date'] = date('Y-m-d');
        $apiRequestData['reference_number'] = $requestData['reference_number'];
        $apiRequestData['description'] = $requestData['description'];
        $apiRequestData['invoices'] = $requestData['invoices'];
        //        dd(json_encode($apiRequestData));
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://www.zohoapis.com/invoice/v3/customerpayments',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($apiRequestData),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $access_token,
                'content-type: application/json',
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        //      echo $response;
        //        dd($response, $apiRequestData);
        $response = json_decode($response, true);
        return $response;
    }

    public function apiRequest($method, $url, $data = [])
    {
        $accessToken = $this->getAccessToken();

        try {
            $response = Http::withToken($accessToken)->withHeaders([
                'Content-Type' => 'application/json',
            ])->send($method, $url, [
                'json' => $data,
            ]);

            // Check the status code of the response
            if ($response->successful()) {
                return $response->json();
            } else {
                // Log the full response for debugging
                Log::error('Zoho API request failed:', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return [
                    'error' => true,
                    'message' => 'Zoho API request failed',
                    'status' => $response->status(),
                    'response' => $response->body()
                ];
            }
        } catch (\Exception $e) {
            // Log the error message for debugging
            Log::error('Zoho API request exception:', ['message' => $e->getMessage()]);

            return [
                'error' => true,
                'message' => 'Failed to perform Zoho API request',
                'details' => $e->getMessage()
            ];
        }
    }

    // public function createMeeting($meetingDetails)
    // {
    //     $url = 'https://meeting.zoho.com/api/v1/meeting';
    //     return $this->apiRequest('POST', $url, $meetingDetails);
    // }

    public function getMeetings()
    {
        $url = 'https://meeting.zoho.com/api/v1/meetings';
        return $this->apiRequest('GET', $url);
    }
}

// $obj = new Record();
// $obj->initialize();
// $obj->getRecord();
