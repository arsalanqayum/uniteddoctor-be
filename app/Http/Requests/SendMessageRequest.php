<?php

namespace App\Http\Requests;

use App\Traits\RequestTraits\ValidationErrorResponse;
use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
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
            'sender_id' => 'required|integer|max:255|min:1',
            'receiver_id' => 'required|integer|max:255|min:1',
            'conversation_id' => 'required|integer|max:255|min:1',
            'reply_to' => 'integer|max:255|min:1|exists:messages,id',
            'body' => 'required|string|max:255|min:1',
            'attachment' => 'file',
        ];
    }
}
