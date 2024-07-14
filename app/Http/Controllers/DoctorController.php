<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\DoctorStatusChanged;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = User::where('user_type', 'doctor')->with('doctorSpecialities.specialization')->get();
        return response()->json($doctors);
    }

    // Update doctor status
    public function updateStatus(Request $request, User $user)
    {
        

        $status = $request->input('status', false); // default to false if not provided
        $user->status = $status;
        $user->save();

        // Send notification
        $user->notify(new DoctorStatusChanged($status));


        return response()->json(['message' => 'User status updated successfully']);
    }
}
