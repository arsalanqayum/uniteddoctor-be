<?php

namespace App\Http\Controllers;

use App\Events\AppointmentEvent;
use App\Events\Calling;
use App\Models\Appointment;
use App\Models\Notification;
use App\Models\ReviewToken;
use App\Models\Schedule;
use App\Models\User;
use App\Notifications\AppointmentNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\GoogleCalendarService;
use App\Services\ZohoService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AppointmentController extends Controller
{
    protected $zohoService;
    public function __construct(ZohoService $zohoService)
    {
        $this->zohoService = $zohoService;
    }
    public function appointmentStat()
    {
        $type = Auth::user()->user_type;
        $id = Auth::user()->id;
        $type;
        DB::enableQueryLog();
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $totalCounts = Appointment::select('status', DB::raw('count(*) as total'))
            ->when($type === 'doctor', function ($q) use ($id) {
                return $q->where('doctor_id', $id);
            })
            ->when($type === 'patient', function ($q) use ($id) {
                return $q->where('user_id', $id);
            })
            ->groupBy('status')
            ->get()
            ->keyBy('status')
            ->map(function ($item) {
                return ['total' => $item->total];
            });

        // Fetch daily counts
        $dailyCounts = Appointment::whereDate('created_at', $today)
            ->when($type === 'doctor', function ($q) use ($id) {
                return $q->where('doctor_id', $id);
            })
            ->when($type === 'patient', function ($q) use ($id) {
                return $q->where('user_id', $id);
            })
            ->count();

        // Fetch weekly counts
        $weeklyCounts = Appointment::whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->when($type === 'doctor', function ($q) use ($id) {
                return $q->where('doctor_id', $id);
            })
            ->when($type === 'patient', function ($q) use ($id) {
                return $q->where('user_id', $id);
            })
            ->count();
        $totolDoctor = User::where('user_type', 'doctor')->count();
        $totalPatient = User::where('user_type', 'patient')->count();
        $queries = DB::getQueryLog();
        $allTimeTotal = Appointment::when($type === 'doctor', function ($q) use ($id) {
            return $q->where('doctor_id', $id);
        })
            ->when($type === 'patient', function ($q) use ($id) {
                return $q->where('user_id', $id);
            })
            ->count();
        // You can return both the individual counts and the total sum:

        $this->getLastSevenDays();
        return response()->json([
            'counts' => $totalCounts,
            'today' =>  $dailyCounts,
            'totalAllAppointments' => $allTimeTotal,
            'thisWeek' =>  $weeklyCounts,
            'patient' => $totolDoctor,
            'doctor' => $totalPatient,
            'last_seven_days' =>$this->getLastSevenDays()
        ]);
    }
    public function getLastSevenDays()
    {
        $sevenDaysAgo = Carbon::now()->subDays(6)->startOfDay(); // Includes today and goes back six more days
        $today = Carbon::now()->endOfDay();
        $appointments = DB::table('appointments')
        ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
        ->whereBetween('created_at', [$sevenDaysAgo, $today])
        ->groupBy(DB::raw('DATE(created_at)')) // Group by the date part of created_at
        ->orderBy('date', 'asc') // Order by the date for consistency
        ->get();
        $dates = [];
        for ($i = 0; $i < 7; $i++) {
            $dates[Carbon::now()->subDays(6 - $i)->format('Y-m-d')] = 0; // Initialize with zero
        }

        // Populate the counts from the query
        foreach ($appointments as $appointment) {
            $dates[$appointment->date] = (int) $appointment->count; // Cast to integer if necessary
        }

        // Response format for chart
        return $response = [
            'name' => 'Last Seven Days Appointment',
            'data' => array_values($dates) // Gets only the counts
        ];
    }
    public function index()
    {
        $type = Auth::user()->user_type;
        $id = Auth::user()->id;
        $apt = Appointment::when($type == 'doctor', function ($query) use ($id) {
            return $query->where('doctor_id', $id)->with('patient');
        })->when($type == 'patient', function ($query) use ($id) {
            return $query->where('user_id', $id)->with('doctor');
        })->orderby('created_at', 'desc')->get();
        return response()->json(['error' => false, 'message' => 'Appointment created', 'data' => $apt]);
    }
    public function store(Request $request)
    {

// dd($this->createRoom());
        $apt = Appointment::create([
            'doctor_availability_id' => $request->doctor_availability_id,
            'user_id' => Auth::user()->id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'schedule_id' => $request->schedule_id,
            'status' => 'pending',
            'doctor_id' => $request->doctor_id
        ]);


        $user = User::find($request->doctor_id);

        $meetingDetails = [
            'summary' => 'Appointment with Dr. ' . $user->first_name, // Adjust based on your user model
            'description' => 'Appointment booked through the app.',
            'startDateTime' => Carbon::parse($request->date . ' ' . $request->start_time)->toAtomString(),
            'endDateTime' => Carbon::parse($request->date . ' ' . $request->end_time)->toAtomString(),
        ];
        $schedule = Schedule::where('id', $request->schedule_id)->first();
        if ($schedule->type == 'remote') {
            try {
                // dd('yes');
                // $event = $this->calendarService->createGoogleMeetEvent($meetingDetails);
                // dd($meetingDetails,$event);
                $addAptLink = Appointment::where('id',$apt->id)->first();
                // Optionally save the Google Meet link to the appointment record
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
                $addAptLink->google_meet_link = $this->zohoService->createZohoMeeting($params) ?? 'Link not available';
                // dd($apt->google_meet_link );
                $addAptLink->save();
            } catch (\Exception $e) {
                // Handle the exception (e.g., log the error or send a notification)

            }
        }
        $user->notify(new AppointmentNotification(Auth::user()->first_name . ' ' . Auth::user()->last_name . '. Book an Appointment at ' . Carbon::parse($apt->date)->format('d M') . ' ' . 'at' . ' ' . $apt->start_time, $user, $apt->google_meet_link));
        // dd($apt);
        // $title,$type,$description,$content,$to
        $description = Auth::user()->first_name . ' ' . Auth::user()->last_name . ' ' . 'Booked an appointment ' . Carbon::parse($apt->date)->format('d m') . ' at ' . $apt->start_time;
        $data['user'] = Auth::user();
        $data['data'] = $apt;
        event(new Calling('Appointment Booking', 'apt_booking', $description, $data, $request->doctor_id));

        Notification::create([
            'title' => 'Appointment Booking',
            'description' => $description,
            'type' => 'apt_booking', 'content' => json_encode($data),
            'to' => $request->doctor_id,
            'read_at' => null

        ]);

        return response()->json(['error' => false, 'message' => 'Appointment created', 'data' => $apt]);
    }


    public function createRoom()
    {
        $client = new Client();
        $apiKey = '63c4224730aa76c8c4598821d7db289e81cc623006e6fde8e52dccbdfa3a4ed7';
        $roomName = 'room-' . substr(md5(mt_rand()), 0, 9);

        try {
            $response = $client->post('https://api.daily.co/v1/rooms', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'name' => $roomName,
                    'properties' => [
                        'exp' => now()->addDay()->timestamp, // Room expires in 24 hours
                        'enable_chat' => true,
                        'enable_screenshare' => true,
                        // 'enable_recording' => 'local', // Remove this line or set it to a valid value
                    ],
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            return  $data['url'];
        } catch (RequestException $e) {
            $responseBody = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : 'No response';
            Log::error('Error creating Daily.co room: ' . $e->getMessage());
            Log::error('Response body: ' . $responseBody);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function appointmentStatus($id, Request $request)
    {
        // Update the appointment status
        $apt = Appointment::where('id', $id)->update(['status' => $request->status]);

        // Generate the notification description
        $description = 'Dr.' . Auth::user()->first_name . ' ' . Auth::user()->last_name . ' ' . $request->status . ' appointment';
        $data['user'] = Auth::user();
        $data['data'] = Appointment::where('id', $id)->first();

        // Retrieve the user to be notified
        $to = Appointment::where('id', $id)->first()['user_id'];
        $user = User::find($to);

        // Generate a unique token for the review link
        $reviewToken = Str::random(60);
        // Save or associate this token with the appointment or user in some way in your database, if necessary

        // Construct the review link to point to the frontend
        $frontendBaseUrl = config('app.frontend_url'); // Fallback URL in case the .env entry is missing
        $reviewPath = "/submit-review"; // Adjust based on your frontend routing
        $reviewLink = $frontendBaseUrl . $reviewPath . "?token=" . $reviewToken;

        // Continue with your existing logic to create events and notifications
        event(new Calling('Appointment ' . $request->status, 'apt_booking', $description, $data, $to));
        Notification::create([
            'title' => 'Appointment Status',
            'description' => $description,
            'type' => 'apt-status',
            'content' => json_encode($data),
            'to' => $to,
            'read_at' => null
        ]);

        // Notify the user about the appointment status update and provide the review link if the status is 'completed'
        if ($request->status == 'completed') {
            ReviewToken::create([
                'appointment_id' => $id,
                'token' => $reviewToken,
                // Add other necessary fields
            ]);
            // You might want to adjust the notification to include the reviewLink
            $notificationMessage = 'Dr.' . Auth::user()->first_name . ' ' . Auth::user()->last_name . ' has marked your appointment as completed. Please leave a review: ' . $reviewLink;
            $user->notify(new AppointmentNotification($notificationMessage, $user, null));
        } else {
            $user->notify(new AppointmentNotification($description, $user, null));
        }

        return response()->json(['error' => false, 'message' => 'Status Updated', 'data' => '']);
    }
}
