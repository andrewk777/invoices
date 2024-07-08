<?php

namespace App\Http\Requests\CreditCard;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCreditCardRequest extends FormRequest
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
     * @return array
     */
    public function rules(): array
    {
        return [
            'cc_number' => 'required',
            'cc_exp_month' => 'required|string|max:2',
            'cc_exp_year' => 'required|string|max:4',
        ];
    }

    protected function failedValidation(Validator $validator){
        // return errors in json object/array
        //$message = $validator->errors()->all();
        $message = $validator->errors()->getMessages();
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $message
        ], 422));
    }
}
