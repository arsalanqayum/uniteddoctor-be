<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
{
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = request()->id;
        return [
            'first_name'=>'required',
            'last_name'=>'required',
            "email"=>["required", Rule::unique('users', 'email')->ignore($id)],
            "phone"=>["required", Rule::unique('users', 'phone')->ignore($id)],
            'city'=>'required',
            'country'=>'required'
        ];
    }
}
