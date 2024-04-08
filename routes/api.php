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
use App\Http\Controllers\ReviewController;
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
Route::prefix('v1')->group(function (){

Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('password/reset', [ResetPasswordController::class, 'reset']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/verify', [AuthController::class, 'verify']);
    Route::post('login', [AuthController::class, 'login']);
    Route::get('doctor-search',[DoctorSearchController::class,'index']);
    Route::resource('specialization', SpecializationController::class); 
    Route::get('/google/oauth', [GoogleOAuthController::class, 'redirect']);
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
        Route::post('/{id}',[AppointmentController::class,'appointmentStatus']);
        
    });
    });
    Route::post('profile',[AuthController::class,'profile']);
    Route::prefix('doctor')->group(function (){
        Route::middleware('auth:api')->group(function () {
            Route::resource('speciliality', DoctorSpecialityController::class); 
            Route::resource('availibility', DoctorAvailabilityController::class); 
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
