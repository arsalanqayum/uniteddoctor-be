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
        $schedule = Schedule::where('user_id', Auth::user()->id)->with('available')->orderBy('created_at', 'desc')->get();
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
        // Basic validation for required fields
        $validatedData = $request->validate([
            'type' => 'required|string',
            'consultation_fee' => 'required|numeric',
            'offer_label' => 'required|string',
            'schedules' => 'required|array',
            'schedules.*.from' => 'required|date_format:H:i',
            'schedules.*.to' => 'required|date_format:H:i|after:schedules.*.from',
            'schedules.*.duration' => 'required|date_format:H:i',
            'schedules.*.date' => 'nullable|date',
            'schedules.*.validUntil' => 'nullable|date',
        ]);

        // Extract the location fields if type is not remote
        $locationName = $request->type !== 'remote' ? $request->location['name'] ?? null : null;
        $locationId = $request->type !== 'remote' ? $request->location['id'] ?? null : null;
        $locationAddress = $request->type !== 'remote' ? $request->location['address'] ?? null : null;
        $latitude = $request->type !== 'remote' ? $request->location['latitude'] ?? null : null;
        $longitude = $request->type !== 'remote' ? $request->location['longitude'] ?? null : null;

        if ($request->type !== 'remote' && (!$locationName || !$locationId || !$locationAddress || !$latitude || !$longitude)) {
            return response()->json([
                'error' => true,
                'message' => 'Invalid location data.',
            ], 400);
        }

        // Create the schedule
        $schedule = Schedule::create([
            'type' => $request->type,
            'location' => $locationName,
            'consultation_fee' => $request->consultation_fee,
            'user_id' => Auth::user()->id,
            'offer_label' => $request->offer_label
        ]);

        // Create the doctor availability entries
        foreach ($request->schedules as $value) {
            // dd($value['type']);
            if ($value['type'] === 'day' && isset($value['day'])) {
                $currentDate = new \DateTime();
                $currentDate->modify('next ' . $value['day']);
                $value['date'] = $currentDate->format('Y-m-d');
            }

            DoctorAvailability::create([
                'type' => $request->type,
                'schedule_id' => $schedule['id'],
                'location' => $locationName,
                'location_id' => $locationId,
                'location_address' => $locationAddress,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'doctor_id' => Auth::user()->id,
                'date' => $value['date'],
                'start_time' => $value['from'],
                'end_time' => $value['to'],
                'is_repeated' => $request->is_repeated,
                'duration' => $value['duration'],
                'day' => $value['day'],
                'validUntil' => $value['validUntil']
            ]);
        }

        // Retrieve and return the availability data
        $availability = DoctorAvailability::where('doctor_id', Auth::user()->id)->get();
        return response()
            ->json([
                'error' => false,
                'message' => 'Availability added.',
                'data' => $availability
            ]);
    }





    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $schedule = Schedule::where('id', $id)->with(['doctor.education', 'doctor.expereince', 'doctor.doctorSpecialities.specialization', 'available'])->first();
        return response()
            ->json([
                'error' => false,
                'message' => 'Availibility added.',
                'data' => $schedule
            ]);
    }
    public function available($id)
    {
        $schedule = Schedule::where('id', $id)->with(['doctor.education', 'doctor.expereince', 'doctor.doctorSpecialities.specialization', 'available'])->first();
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
        $doctor = DoctorAvailability::where('id', $id)->update($request->all());

        return response()->json(['data' => $doctor, 'doctor' => $id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DoctorAvailability $doctorAvailability)
    {
        //
    }
}
