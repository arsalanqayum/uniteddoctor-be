<?php

namespace App\Http\Controllers;

use App\Models\Education;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EducationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $edu = Education::where('user_id', Auth::user()->id)->get();
        return response()->json(['error' => false, 'message' => 'Education List', 'data' => $edu]);
    }
    public function store(Request $request)
    {
     
        $edu = Education::create([
            "degree" => $request->degree, "graduationYear" => $request->graduationYear, "institution" => $request->institution,'user_id'=>Auth::user()->id
        ]);

        return response()->json(['error' => false, 'message' => 'Education added', 'data' => $edu]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
