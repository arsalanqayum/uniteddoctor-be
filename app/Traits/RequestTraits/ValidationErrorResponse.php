<?php
namespace App\Traits\RequestTraits;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;

trait ValidationErrorResponse {
    
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            new JsonResponse(['result' => false, 'message' => 'Validation Error!', 'data' => '', 'errors' => $validator->errors()], 422)
        );
    }
}