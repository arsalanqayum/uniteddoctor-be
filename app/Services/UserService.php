<?php
namespace App\Services;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorService;
use App\Models\Patient;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Stripe\Stripe;
use Stripe\AccountLink as StripeAccountLink;
use Stripe\Account as StripeAccount;

class UserService{
    public function getOnlineUsers(){
        $users = User::where('last_seen', '>=', now()->subMinutes(2))->get();
        return $users;
    }
    public function handleMultiRoleRegistration(array $validatedData, $user, $role_id){
        $output = [];
        // dd($user, $role_id);
        switch ($role_id) {
            case 1:
                // admin
                break;
            case 4:
                // doctor
                $validatedData['user_id'] = $user->id;
                try {
                    $doctorPayload = [
                        'type' => 'custom',
                        'country' => 'US',
                        'email' => $validatedData['email'],
                        'type' => 'express',
                        'capabilities' => [
                            'card_payments' => ['requested' => true],
                            'transfers' => ['requested' => true],
                        ],
                    ];
                    $stripeService = new StripeService();
                    $accountId = $stripeService->createDoctorStripeConnectedAccount($doctorPayload);
                    $validatedData['stripe_account_id'] = $accountId;
                    $doctor = Doctor::create($validatedData);
                    $user->addRole($role_id);
                    $output['message'] = 'Doctor Created Successfully';
                    $output['status'] = true;
                } catch (\Throwable $th) {
//                    dd($th);
                    $output['message'] = 'Doctor create error please check log';
                    $output['status'] = false;
                }
                break;
            case 5:
                // patient
                $validatedData['user_id'] = $user->id;
                /** create patient contact in zoho api */
                $zohoService = new ZohoService();
                $validatedData['zoho_customer_id'] = $zohoService->createContact($validatedData);
                $user->addRole($role_id);
                $user->createAsStripeCustomer();
                $patient = Patient::create($validatedData);
                /** Assigning Basic Subscription using laravel cashier */
                $plan = SubscriptionPlan::where('plan_type', 'basic')->first();
                $price = $plan->stripe_price_id;
                $subsription = $user->newSubscription(
                    $plan->name, $plan->stripe_price_id
                )->createAndSendInvoice();
                DB::table('subscriptions')->where('id',$subsription->id)->update([
                    'transfer_type' => 3, // no payment required
                    'subscription_plan_id' => $plan->id,
                ]);

                /** create patient contact in xero api */
//                $xeroService = new XeroService();
//                $xeroService->setContactAttributes('', $validatedData['email'], $validatedData['first_name']." ".$validatedData['last_name']);
//                $xeroService->contactCreate();
                $output['message'] = 'Patient Created Successfully';
                $output['status'] = true;
                break;
            case 6:
                # staff

                break;
            default:
                $output['message'] = 'Role Does not exitst';
                $output['status'] = false;
                break;

        }
        return $output;
    }
    public function handleMultiRoleLogin($user, $role_id){
        $output = [];
        // dd($user, $role_id);
        switch ($role_id) {
            case 1:
                // admin
                if ($user->role_id == 1) {
                    $output['message'] = 'Admin Logged In Successfully';
                    $output['status'] = true;
                }else{
                    $output['message'] = 'Provided Credential Are Not For Admin';
                    $output['status'] = false;
                }
                break;
            case 4:
                // doctor
                if ($user->role_id == 4) {
                    $output['message'] = 'Doctor Logged In Successfully';
                    $output['status'] = true;
                }else{
                    $output['message'] = 'Provided Credential Are Not For Doctor';
                    $output['status'] = false;
                }
                break;
            case 5:
                // patient
                if ($user->role_id == 5) {
                    $output['message'] = 'Patient Logged In Successfully';
                    $output['status'] = true;
                }else{
                    $output['message'] = 'Provided Credential Are Not For Patient';
                    $output['status'] = false;
                }
                break;
            case 6:
                # staff
                if ($user->role_id == 6) {
                    $output['message'] = 'Staff Logged In Successfully';
                    $output['status'] = true;
                }else{
                    $output['message'] = 'Provided Credential Are Not For Staff';
                    $output['status'] = false;
                }
                break;
            default:
                $output['message'] = 'User Not Exist With Any Role';
                $output['status'] = false;
                break;

        }
        return $output;
    }
    public function handleRoleBasedUpdate(array $validatedData, User $user, $previousRole =0)
    {
//         dd('in', $user->roles, $user->hasRole('doctor'));
        if ($user->hasRole('doctor')) {
            // dd('in', $validatedData);
            $doctor = Doctor::where('user_id', $user->id)->first();
            /** check file in request */
            if (Request::hasFile('license_image')) {
                /** delete old file if exists */
                if ($doctor->license_image) {
                    if (Storage::disk('public')->exists($doctor->license_image) && $doctor->license_image) {
                        Storage::disk('public')->delete($doctor->license_image);
                    }
                }
                /** save new file */
                $filePath = now()->year . '/' . $user->id . '/' . 'doctor';
                $license_image = Request::file('license_image')->store($filePath, 'public');
                $validatedData['license_image'] = $license_image;
            }
            /** check file in request */
            if (Request::hasFile('advisor')) {
                // dd('in');
                /** delete old file if exists */
                if ($doctor->advisor) {
                    if(Storage::disk('public')->exists($doctor->advisor) && $doctor->advisor){
                        Storage::disk('public')->delete($doctor->advisor);
                    }
                }
                /** save new file */
                $filePath = now()->year . '/' . $user->id . '/' . 'doctor' ;
                $advisor = Request::file('advisor')->store($filePath, 'public');
                $validatedData['advisor'] = $advisor;
            }
//            dd(Request::has('doctor_services'));
            if (Request::has('doctor_services')){
                $services = Request::get('doctor_services');
                DoctorService::where('doctor_id', $doctor->id)->delete();
                foreach ($services as $service) {
                    DoctorService::create([
                        'doctor_id' => $doctor->id,
                        'service_id' => $service
                    ]);
                }
            }

            if (!$doctor) {
                return false;
            }else{
                $doctor->update($validatedData);
            }

        }
        if ($user->hasRole('patient')) {
            // dd($previousRole);
            $patient = Patient::where('user_id', $user->id)->first();
            if (!$patient) {
                $output = '';
                switch ($previousRole) {
                    case 4:
                        $output = Doctor::where('user_id', $user->id)->first();
                        break;
                    case 6:
                        $output = Staff::where('user_id', $user->id)->first();
                        break;
                }
                if ($output == '') {
                    return true;
                }
                $validatedData['user_id'] = $user->id;
                $validatedData['first_name'] = $user->first_name;
                $validatedData['last_name'] = $user->last_name;
                $validatedData['mobile_no'] = $output->mobile;
                $validatedData['dob'] = $output->dob;
                $validatedData['gender'] = $output->gender;
                $validatedData['address'] = $output->address;
                $validatedData['city'] = $output->city;
                $validatedData['state'] = $output->state;
                $validatedData['country'] = $output->country;
                $validatedData['postal_code'] = $output->postal_code;
                Patient::create($validatedData);
            }else{
                $patient->update($validatedData);
            }

        }
        if ($user->hasRole('member')) {
            $staff = Staff::where('user_id', $user->id)->first();
            if (!$staff) {
                $output = '';
                switch ($previousRole) {
                    case 5:
                        $output = Patient::where('user_id', $user->id)->first();
                        break;
                    case 4:
                        $output = Doctor::where('user_id', $user->id)->first();
                        break;
                }
                if ($output == '') {
                    return true;
                }
                $validatedData['user_id'] = $user->id;
                $validatedData['first_name'] = $user->first_name;
                $validatedData['last_name'] = $user->last_name;
                $validatedData['mobile'] = isset($output->mobile) ? $output->mobile : $output->mobile_no;
                $validatedData['dob'] = $output->dob;
                $validatedData['gender'] = $output->gender;
                $validatedData['address'] = $output->address;
                $validatedData['city'] = $output->city;
                $validatedData['state'] = $output->state;
                $validatedData['country'] = $output->country;
                $validatedData['postal_code'] = $output->postal_code;
                Staff::create($validatedData);
            }else{
                $staff->update($validatedData);
            }
            // if ($user->hasRole('super-admin')) {
                dd("yes");
            // }
        }
        return true;
    }
}
