<?php

namespace App\Http\Requests;

use App\Helpers\AppHelper;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
            'district_id' => 'sometimes|integer',
            'date' => 'required|date_format:d-m-Y',
            'type' => 'required|in:voucher,receipt,journal',
            'remarks' => 'sometimes|nullable|string',
            'clients' => 'required|array',
            'ref_no' => 'sometimes|nullable|string',
            'instrument_no' => 'sometimes|nullable|string'
        ];
    }

}
