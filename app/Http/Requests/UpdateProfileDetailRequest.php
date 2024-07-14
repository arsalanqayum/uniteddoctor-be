<?php

namespace App\Http\Requests;

use App\Traits\RequestTraits\ValidationErrorResponse;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileDetailRequest extends FormRequest
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
        return [
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'username' => 'string|unique:users,username,'.auth('api')->user()->id,
            'password' => 'string|min:6|max:255',
            'role_id' => 'integer|max:255|exists:roles,id',
            'avatar' => 'file',
            'mobile' => 'string|max:255',
            "mobile_no"=>"string|max:255",
            'dob' => 'string',
            'gender' => 'string|max:10',
            'education' => 'string|max:255',
            'designation' => 'string|max:255',
            'department_id' => 'sometimes|exists:departments,id',
            'address' => 'string|max:255',
            'city' => 'sometimes|string|max:255',
            'state' => 'sometimes|string|max:255',
            'country' => 'sometimes|string|max:255',
            'postal_code' => 'string|max:255',
            'biography' => 'string|max:255',
            'status' => 'sometimes|boolean',
            'practice_state' => 'string|max:255',
            'license_image' => 'file'     
        ];
    }
}
