<?php

// app/Http/Controllers/VideoCallController.php

namespace App\Http\Controllers;

use App\Services\ZohoCliqService;
use App\Services\ZohoService;
use Illuminate\Http\Request;

class VideoCallController extends Controller
{
    protected $zohoCliqService;

    public function __construct(ZohoService $zohoCliqService)
    {
        $this->zohoCliqService = $zohoCliqService;
    }

    public function createMeeting(Request $request)
    {
        $params = [
            'title' => 'Your Meeting Title',
            'description' => 'Your Meeting Description',
            'start_time' => now()->addMinutes(5)->toIso8601String(),
            'end_time' => now()->addHour()->toIso8601String(),
            'participants' => [
                [
                    'email' => 'participant@example.com'
                ]
            ]
        ];

        $meeting = $this->zohoCliqService->createZohoMeeting($params);

        return response()->json($meeting);
    }
}

