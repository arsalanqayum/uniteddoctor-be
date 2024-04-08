<?php

namespace App\Http\Controllers;
use DB;
use Auth;
use Illuminate\Http\Request;
use App\Models\Notification;
use Carbon\Carbon;
class NotificationController extends Controller
{
    public function index(){
        $notifications=DB::table('notifications')->where('to',Auth::user()->id)->whereNull('read_at') ->orderBy('created_at', 'desc')->get();
        return response()->json(['error'=>false,'message'=>'Notification List','data'=>$notifications]);

    }
    public function update($id){
        $notifications=Notification::where('id',$id)->first();
        $notifications->read_at=Carbon::now();
        $notifications->save();
        return response()->json(['error'=>false,'message'=>'Notification updated','data'=>$notifications]);

    }
}
