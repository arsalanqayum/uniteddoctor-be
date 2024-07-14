<?php

namespace App\Http\Requests;

use App\Traits\RequestTraits\ValidationErrorResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatientRequest extends FormRequest
{
    use ValidationErrorResponse;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user');
        return [
           
            "first_name"=>"required",
            "last_name"=>"required",
            "username"=>["required",Rule::unique('users', 'username')->ignore($userId)],
            "email"=>["required", Rule::unique('users', 'email')->ignore($userId)],
            "mobile_no"=>"required",
            "dob"=>"required",
            "gender"=>"required",
            "address"=>"required",
            "city"=>"required",
            "state"=>"required",
            "country"=>"required",
            "postal_code"=>"required"
        ];
    }
}
