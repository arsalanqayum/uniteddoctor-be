<?php

use App\Http\Controllers\Api\Authentication\AuthController;

use App\Http\Controllers\Api\RolePermission\RoleController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DoctorAvailabilityController;
use App\Http\Controllers\DoctorSearchController;
use App\Http\Controllers\DoctorSpecialityController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\ExperianceController;
use App\Http\Controllers\SpecializationController;
use App\Http\Controllers\NotificationController;
use App\Models\DoctorAvailability;
use App\Http\Controllers\GoogleOAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\GooglePlacesController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\VideoCallController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('video-call', [VideoCallController::class, 'createMeeting']);
Route::get('zoho-callback', [AuthController::class, 'handleZohoCallback']);
Route::prefix('v1')->group(function (){
    Route::get('search-places', [GooglePlacesController::class, 'searchPlaces']);
    Route::prefix('admin')->group(function (){
        Route::middleware('auth:api')->group(function () {
        Route::get('appointmentStat',[AppointmentController::class,'appointmentStat']);
        });
        Route::post('login', [AuthController::class, 'adminLogin']); 
        Route::get('/doctors', [DoctorController::class, 'index']);
        Route::post('/doctors/status/{user}', [DoctorController::class, 'updateStatus']);   
    });
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('password/reset', [ResetPasswordController::class, 'reset']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/verify', [AuthController::class, 'verify']);
    Route::post('login', [AuthController::class, 'login']);
    Route::get('zoho-oauth', [AuthController::class, 'redirectToZoho']);
   
    
    Route::get('doctor-search',[DoctorSearchController::class,'index']);
    Route::resource('specialization', SpecializationController::class); 
    Route::get('/google/oauth', [GoogleOAuthController::class, 'redirect']);
    Route::get('available/{id}', [DoctorAvailabilityController::class,'available']); 
Route::get('/google/callback', [GoogleOAuthController::class, 'callback']);
    // Route::get('education', EducationController::class,'index'); 
    // Route::post('/authenticatreview', [ReviewController::class, 'submitReview']);  
  Route::post('/submit-review/{token}', [ReviewController::class, 'submitReview']);

    Route::middleware('auth:api')->group(function () {
        Route::prefix('notifications')->group(function (){
            Route::get("/",[NotificationController::class,"index"]);
            Route::put("/{id}",[NotificationController::class,"update"]);
        });
    Route::resource('education', EducationController::class); 
    Route::resource('expereince', ExperianceController::class); 
    
    Route::prefix('appointment')->group(function (){
        Route::post('/',[AppointmentController::class,'store']);
        Route::get('/',[AppointmentController::class,'index']);
        Route::get('appointmentStat',[AppointmentController::class,'appointmentStat']);
        Route::post('/{id}',[AppointmentController::class,'appointmentStatus']);
        
    });
    });
    Route::post('profile',[AuthController::class,'profile']);
    Route::prefix('doctor')->group(function (){
        Route::middleware('auth:api')->group(function () {
            Route::resource('speciliality', DoctorSpecialityController::class); 
            Route::resource('availability', DoctorAvailabilityController::class); 
        });
    });

});
Route::get('/allRoles', [RoleController::class, 'allRoles']);
Route::get('social-login/{provider}', [AuthController::class, 'socialLoginRedirection']);
Route::get('social-login/callback/{role}/{provider}', [AuthController::class, 'handleSocialCallback']);
Route::post('social-login/login/{provider}', [AuthController::class, 'handleSocialLogin']);


Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Route::fallback(function(){
//     return response()->json([
//         'message' => 'Page Not Found. If error persists, contact info@website.com'], 404);
//  });
