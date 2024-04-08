<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review; // Adjust based on your model
use App\Models\ReviewToken;
use App\Models\Appointment;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function submitReview(Request $request,$token)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'rating' => 'required|numeric|min:0|max:5',
            'comment' => 'required|string|max:1000',
            // Add any other fields you need
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $reveiwToken = ReviewToken::where('token',$token)->first();
        if(!$reveiwToken){
            return response()->json(['error'=>true,'message'=>'Appointment not found'],400);
        }
        // return response()->json(['data'=>$reveiwToken]);
        $apt=Appointment::where('id',$reveiwToken->id)->first();
        // return response()->json(['data'=>$apt]);
            // Create a new Review
        $review = new Review();
        $review->rating = $request->rating;
        $review->comment = $request->comment;
        $review->patient_id = $apt->user_id;
        $review->doctor_id=$apt->doctor_id;
        $review->appointment_id=$apt->id;
        // Add any other fields you need
        $review->save();

        // Return a response
        return response()->json(['message' => 'Review submitted successfully!'], 200);
    }
}