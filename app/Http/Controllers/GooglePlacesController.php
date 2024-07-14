<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
class GooglePlacesController extends Controller
{
    public function searchPlaces(Request $request)
    {
        $client = new Client(); // Initialize the Guzzle HTTP client
// dd(config('services.google_place.places_api_key'));
        // Construct the API URL and include the API key from the configuration
        $url = 'https://maps.googleapis.com/maps/api/place/textsearch/json';
        $response = $client->request('GET', $url, [
            'query' => [
                'query' => $request->input('query'),
                'key' => config('services.google_place.places_api_key')
            ]
        ]);

        // Decode the JSON response
        $data = json_decode($response->getBody()->getContents(), true);

        // Return the data or handle it as per your application's needs
        return response()->json($data);
    }
}
