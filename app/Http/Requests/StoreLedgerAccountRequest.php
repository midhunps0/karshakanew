<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLedgerAccountRequest extends FormRequest
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
            'group_id' => 'required|integer',
            'name' => 'required|unique:ledger_accounts,name',
            'description' => 'sometimes|string',
            'opening_balance' => 'sometimes|numeric',
            'opening_bal_tyoe' => 'sometimes|string'
        ];
    }
}
