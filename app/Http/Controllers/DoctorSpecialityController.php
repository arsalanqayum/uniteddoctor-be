<?php

namespace App\Http\Controllers;

use App\Models\DoctorSpeciality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorSpecialityController extends Controller
{
   
    public function index(){
        $speciality = DoctorSpeciality::where('user_id',Auth::user()->id)->with('specialization')->get();
        return response()->json(['error' => false, 'message' => 'Doctor Speciality created', 'data' => $speciality]);

    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       
        foreach($request->specialization_id as $data){
           
            $doctorSpeciality = DoctorSpeciality::create(['specialization_id' => $data, 'user_id' => Auth::user()->id]);
        }
        // $doctorSpeciality = DoctorSpeciality::create(['specialization_id' => $request->specialization_id, 'user_id' => Auth::user()->id]);
        return response()->json(['error' => false, 'message' => 'Doctor Speciality created', 'data' => $doctorSpeciality]);
    }
}
