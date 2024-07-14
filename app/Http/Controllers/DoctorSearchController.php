<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DoctorSearchController extends Controller
{
    public function index(Request $request)
    {
        // dd('yes');
        $city = $request->input('city');
        $specializationName = $request->input('specialization');
        $consultation = $request->input('consultation_fee');
        $search = $request->input('query');
        $gender = $request->input('gender');
        // dd($city,$specializationName);
        $doctors = User::where('user_type', 'doctor')->when($gender,function($q) use($gender){
            $q->where('gender',$gender);
        })->where(function ($query) use ($search) {
            $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $search . '%']);
        })->with(['doctorSpecialities.specialization', 'schedules.available'])
            ->when($city,function($q) use($city){
                $q->where('city', $city);
            })
            ->whereHas('schedules', function ($q) use ($consultation) {
                $q->whereBetween('consultation_fee', [$consultation[0], $consultation[1]]);
            })
            ->when($specializationName,function($q) use($specializationName){
                $q->whereHas('doctorSpecialities', function ($query) use ($specializationName) {
                    $query->whereHas('specialization', function ($query) use ($specializationName) {
                        $query->where('id', $specializationName);
                    });
                })    ;
            })
            
            ->get();

        return response()->json(['error' => false, 'message' => 'Doctor list', 'data' => $doctors]);
    }
}
