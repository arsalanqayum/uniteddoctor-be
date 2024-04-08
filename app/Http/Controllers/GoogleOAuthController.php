<?php
namespace App\Http\Controllers;
use App\Services\GoogleCalendarService; // Import your GoogleService
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class GoogleOAuthController extends Controller
{
    public function redirect(GoogleCalendarService $googleService)
    {
        $client = $googleService->getClient();
    $authUrl = $client->createAuthUrl();
    return response()->json(['auth_url' => $authUrl]);
    }

    public function callback(Request $request, GoogleCalendarService $googleService)
    {
        try {
            $client = $googleService->getClient();
           
            $token = $client->fetchAccessTokenWithAuthCode($request->code);

            if (!isset($token['error'])) {
                // Store the token and refresh token in the database
                $googleService->saveTokens($token);
                return response()->json(['message' => 'Google account connected successfully']);
            } else {
                // Handle the error returned by Google
                return response()->json(['error' => 'Failed to authenticate with Google', 'details' => $token['error']], Response::HTTP_UNAUTHORIZED);
            }
        } catch (\Exception $e) {
            Log::error("Error handling Google callback: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred during authentication'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
