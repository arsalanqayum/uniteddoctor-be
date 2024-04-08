<?php

namespace App\Http\Controllers;

use App\Models\Eperiance;
use App\Models\vr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExperianceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exp = Eperiance::where('user_id', Auth::user()->id)->get();
        return response()->json(['error' => false, 'message' => 'User listing', 'data' => $exp]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $expereince = Eperiance::create(['user_id'=>Auth::user()->id,'description' => $request->description, 'employer' => $request->employer, 'endDate' => $request->endDate, 'jobTitle' => $request->jobTitle, 'startDate' => $request->startDate]);
        return response()->json(['error' => false, 'message' => "Expereince added", 'data' => $expereince]);
    }

    

}
