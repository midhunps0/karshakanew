<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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
            'date' => 'sometimes|date_format:Y-m-d',
            'type' => 'sometimes|in:payment,receipt,journal',
            'remarks' => 'sometimes|string',
            'clients' => 'required|array',
            'ref_no' => 'sometimes|string'
        ];
    }

    public function messages()
    {
        return [
            'district_id.required' => 'district_id is required',
            'district_id.integer' => 'district_id should be an integer',
            'date.required' => 'date is required',
            'date.date_format' => 'date shall be in the formay Y-m-d (eg: 2021-08-31)',
            'type.required' => 'type is required',
            'type.in' => 'type shall be one among payment, receipt or journal',
            'clients.required' => 'clients is required',
            'clients.array' => 'clients shall be an array'
        ];
    }
}
