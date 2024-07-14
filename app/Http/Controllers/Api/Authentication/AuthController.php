<?php

namespace App\Http\Controllers\Api\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\RegisterationRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Hash;

class AuthController extends Controller
{
    public function verify(Request $request)
    {
        $verify = User::where('email', $request->email)->update(['email_verified_at' => Carbon::now()]);
        return response()->json(['message' => 'Email verifed Now you can Start using System']);
    }
    public function profile(ProfileRequest $request)
    {

        if ($request->file('avatar')) {
            $this->upload($request->file('avatar'), $request->id);
        }
        $user = User::find($request->id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->city = $request->city;
        $user->country = $request->country;
        $user->gender = $request->gender;
        $user->email = $request->email;
        $user->save();
        return response()->json(['error' => false, 'message' => 'User List', 'data' => $user]);
    }
    public function upload($file, $id)
    {


        // Retrieve the user from the database
        $user = User::findOrFail($id);

        // Check if the user already has a profile picture
        if ($user->avatar) {
            // Delete the existing profile picture file (optional)
            Storage::delete(public_path($user->profile_picture));
        }

        // Store the uploaded profile picture in the public directory
        $profilePicturePath = $file->store('profile_pictures', 'public');

        // Update the user's profile picture path in the database
        $user->avatar = $profilePicturePath;
        $user->save();
    }
    public function pusherAuth(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response('Unauthorized', 401);
        }

        $socket_id = $request->input('socket_id');
        $channel_name = $request->input('channel_name');

        $pusher = new \Pusher\Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options')
        );

        $presence_data = [
            'name' => $user->name,
            // Add any other user information you want to send to Pusher
        ];

        $auth = $pusher->presence_auth($channel_name, $socket_id, $user->id, $presence_data);

        return response($auth);
    }
    /** User Login return outh2 token with user detail */
    public function login(Request $request)
    {
        if(User::where('email',$request->email)->where('user_type','doctor')->where('status',0)->first()){
            return response()->json(['error'=>true,'message'=>'Account is not approved Yet by administration'],400);
        }
        // return response()->json(['data'=>"ues"]);
        $credentials = $request->only('password');

        // Check if the provided value is an email or a username
        $field = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        // dd($request->only('password'));
        $credentials[$field] = $request->email;
        // $credentials['status'] = 1;
        if (Auth::attempt($credentials)) {

            $user = Auth::user();


            // dd($checkRole, 'in');

            $token = $user->createToken('authToken')->accessToken;
            // $userResource = new UserResource($user);
            return response()->json(['result' => true, 'message' => 'Login successfully.', 'data' => $user, 'token' => $token], 200);
        } else {
            return response()->json(['result' => false, 'message' => 'Unauthorized', 'data' => ''], 401);
        }
    }

    public function adminLogin(Request $request)
    {
        // return response()->json(['data'=>"ues"]);
        $credentials = $request->only('password');

        // Check if the provided value is an email or a username
        $field = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        // dd($request->only('password'));
        $credentials[$field] = $request->email;

        if (Auth::attempt($credentials)) {

            $user = Auth::user();


            // dd($checkRole, 'in');

            $token = $user->createToken('authToken')->accessToken;
            // $userResource = new UserResource($user);
            return response()->json(['result' => true, 'message' => 'Login successfully.', 'data' => $user, 'token' => $token], 200);
        } else {
            return response()->json(['result' => false, 'message' => 'Unauthorized', 'data' => ''], 401);
        }
    }
    /** Register new user return token with user detail */
    public function register(RegisterationRequest $request)
    {
        // dd('in', $request->validated());

        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => $request->password,
            'user_type' => $request->user_type,
            'city' => $request->city,
            'avatar' => $request->avatar,
            'gender' => $request->gender,
            'lat' => $request->lat,
            'long' => $request->long,
        ];
        if ($request->user_type == 'doctor') {
            $data['status'] = false;
        }
    //  dd($data);
        $user = User::create($data);
        $user->sendEmailVerificationNotification();
        DB::commit();
        $token = $user->createToken('authToken')->accessToken;

        return response()->json(['result' => true, 'message' => 'User Register Successfully', 'data' => $user, 'token' => $token], 201);
    }
}
