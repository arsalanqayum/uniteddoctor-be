<?php

namespace App\Http\Controllers;

use App\Models\DoctorAvailability;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorAvailabilityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // dd('yes');
       $schedule = Schedule::where('user_id',Auth::user()->id)->with('available')->get();
       return response()
            ->json([
                'error' => false,
                'message' => 'Availibility List',
                'data' => $schedule
            ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd('yes');
        // dd($request->all());
        // foreach($value as $request->all()){
        //         dd($value);
        // }
        $schedule = Schedule::create([
            'type' => $request->type,
            'location' => $request->location,
            'consultation_fee' => $request->consultation_fee,
            'user_id' => Auth::user()->id,
            'offer_label' => $request->offer_label
        ]);
        // dd('sss');
        foreach ($request->schedules as $key => $value) {
            // Code to be executed for each iteration
        //    try{}catch()
            DoctorAvailability::create([
                'type' => $request->type,
                'schedule_id' => $schedule['id'],
                'location' => $request->location,
                'doctor_id' => Auth::user()->id,
                'date' => $value['date'],
                'start_time' => $value['from'],
                'end_time' => $value['to'],
                'is_repeated' => $request->is_repeated,
                'duration' => $value['duration']
            ]);
        }

        $availibility = DoctorAvailability::where('doctor_id', Auth::user()->id)->get();
        return response()
            ->json([
                'error' => false,
                'message' => 'Availibility added.',
                'data' => $availibility
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $schedule= Schedule::where('id',$id)->with(['doctor.education','doctor.expereince','doctor.doctorSpecialities.specialization','available'])->first();
        return response()
            ->json([
                'error' => false,
                'message' => 'Availibility added.',
                'data' => $schedule
            ]);
    }
    public function available($id)
    {
        $schedule= Schedule::where('id',$id)->with(['doctor.education','doctor.expereince','doctor.doctorSpecialities.specialization','available'])->first();
        return response()
            ->json([
                'error' => false,
                'message' => 'Availibility added.',
                'data' => $schedule
            ]);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DoctorAvailability $doctorAvailability)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        $doctor = DoctorAvailability::where('id',$id)->update($request->all());

        return response()->json(['data'=>$doctor,'doctor'=>$id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DoctorAvailability $doctorAvailability)
    {
        //
    }
}
