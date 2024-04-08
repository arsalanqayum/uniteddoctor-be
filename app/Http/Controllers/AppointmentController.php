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
use Illuminate\Support\Str;
class AppointmentController extends Controller
{
    protected $calendarService;
    public function __construct(GoogleCalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }
    public function index()
    {
        $type = Auth::user()->user_type;
        $id = Auth::user()->id;
        $apt = Appointment::when($type == 'doctor', function ($query) use ($id) {
            return $query->where('doctor_id', $id)->with('patient');
        })->when($type == 'patient', function ($query) use ($id) {
            return $query->where('user_id', $id)->with('doctor');
        })->orderby('created_at','desc')->get();
        return response()->json(['error' => false, 'message' => 'Appointment created', 'data' => $apt]);
    }
    public function store(Request $request)
    {
        
    
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
       $schedule = Schedule::where('id',$request->schedule_id)->first();
       if($schedule->type=='remote'){
        try {
            $event = $this->calendarService->createGoogleMeetEvent($meetingDetails);
            // dd($meetingDetails,$event);
            // Optionally save the Google Meet link to the appointment record
            $apt->google_meet_link = $event->hangoutLink ?? 'Link not available';
            // dd($apt->google_meet_link );
            $apt->save();
        } catch (\Exception $e) {
            // Handle the exception (e.g., log the error or send a notification)

        }
        }
        $user->notify(new AppointmentNotification(Auth::user()->first_name . ' ' . Auth::user()->last_name . '. Book an Appointment at ' . Carbon::parse($apt->date)->format('d M') . ' ' . 'at' . ' ' . $apt->start_time,$user,$apt->google_meet_link));
        // $title,$type,$description,$content,$to
        $description = Auth::user()->first_name . ' ' . Auth::user()->last_name . ' ' . 'Booked an appointment ' . Carbon::parse($apt->date)->format('d m') . ' at ' . $apt->start_time;
        $data['user'] = Auth::user();
        $data['data'] = $apt;
        event(new Calling('Appointment Booking', 'apt_booking', $description, $data, $request->doctor_id));
      
            Notification::create([
                'title' => 'Appointment Booking',
                'description' =>$description,
                'type' => 'apt_booking', 'content' => json_encode($data),
                 'to' => $request->doctor_id,
                'read_at'=>null
                
            ]);
        
        return response()->json(['error' => false, 'message' => 'Appointment created', 'data' => $apt]);
    }

    public function appointmentStatus($id, Request $request) {
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
