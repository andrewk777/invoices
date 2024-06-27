<?php

namespace App\Http\Requests\Client;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
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
    public function rules()
    {
        return [
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:255',
            'company_phone' => 'required|string|max:255|regex:/^([0-9\s\-\+\(\)]*)$/|min:8',
            'company_email' => 'required|string|email|max:255',
            'main_contact_first_name' => 'required|string|max:255',
            'main_contact_last_name' => 'required|string|max:255',
            'main_contact_phone' => 'required|string|max:255|regex:/^([0-9\s\-\+\(\)]*)$/|min:8',
            'main_contact_email' => 'required|string|email|max:255',
            'ap_first_name' => 'nullable|string|max:255',
            'ap_last_name' => 'nullable|string|max:255',
            'ap_phone' => 'nullable|string|max:255|regex:/^([0-9\s\-\+\(\)]*)$/|min:8',
            'ap_email' => 'nullable|string|email|max:255',
            'notes' => 'nullable|string|max:255',
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
