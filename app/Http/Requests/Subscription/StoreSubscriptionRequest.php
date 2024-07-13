<?php

namespace App\Http\Requests\Subscription;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreSubscriptionRequest extends FormRequest
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
        return [
            'subscription.name' => 'required|string|max:255|unique:subscriptions,name',
            'subscription.my_company_id' => 'required',
            'subscription.client_id' => 'required',
            'subscription.currency' => 'required',
            'subscription.next_charge_date' => 'required',
            'subscription.due_in_days' => 'required',
            'subscription.frequency_month' => 'required',
            //'subscription.starting_date' => 'required',
            'subscription.expiration_date' => 'required',
            'subscription.subtotal' => 'required',
            'subscription.taxes' => 'required',
            'subscription.total' => 'required',

        ];
    }

    public function messages(): array
    {
        return [
            'subscription.name.required' => 'Name is required',
            'subscription.my_company_id.required' => 'Company is required',
            'subscription.client_id.required' => 'Client is required',
            'subscription.currency.required' => 'Currency is required',
            'subscription.next_charge_date.required' => 'Next charge date is required',
            'subscription.due_in_days.required' => 'Due in days is required',
            'subscription.frequency_month.required' => 'Frequency month is required',
            'subscription.starting_date.required' => 'Starting date is required',
            'subscription.expiration_date.required' => 'Expiration date is required',
            'subscription.subtotal.required' => 'Sub total is required',
            'subscription.taxes.required' => 'Taxes is required',
            'subscription.total.required' => 'Total is required',
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
