<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreInvoiceRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'invoice.client_id' => 'required',
            'invoice.my_company_id' => 'required',
            'invoice.status' => 'required',
            'invoice.currency' => 'required',
            'invoice.invoice_date' => 'required',
            'invoice.invoice_due' => 'required',
            'invoice.subtotal' => 'required',
            'invoice.total' => 'required',

            'invoice_items' => 'required|array',
            'invoice_items.*.description' => 'nullable|string',
            'invoice_items.*.qty' => 'required',
            'invoice_items.*.rate' => 'required',
            'invoice_items.*.tax' => 'nullable|string',

            'invoice_payments.*.amount' => 'required',
            'invoice_payments.*.date' => 'required',
            'invoice_payments.*.type' => 'required',
            'invoice_payments.*.note' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'invoice.client_id.required' => 'Client is required',
            'invoice.my_company_id.required' => 'Company is required',
            'invoice.status.required' => 'Status is required',
            'invoice.currency.required' => 'Currency is required',
            'invoice.invoice_date.required' => 'Invoice date is required',
            'invoice.invoice_due.required' => 'Invoice due is required',
            'invoice.sub_total.required' => 'Sub total is required',
            'invoice.total.required' => 'Total is required',

            'invoice_items.required' => 'Invoice items are required',
            'invoice_items.*.description.string' => 'Invoice item description must be a string',
            'invoice_items.*.qty.required' => 'Invoice item quantity is required',
            'invoice_items.*.rate.required' => 'Invoice item rate is required',
            'invoice_items.*.tax.string' => 'Invoice item tax must be a string',

            'invoice_payments.*.amount.required' => 'Invoice payment amount is required',
            'invoice_payments.*.date.required' => 'Invoice payment date is required',
            'invoice_payments.*.type.required' => 'Invoice payment type is required',
            'invoice_payments.*.note.string' => 'Invoice payment note must be a string',
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
