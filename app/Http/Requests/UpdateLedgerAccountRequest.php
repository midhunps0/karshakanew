<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLedgerAccountRequest extends FormRequest
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
            'group_id' => 'sometimes|integer',
            'name' => 'sometimes|unique:ledger_accounts,name,'.$this->id.',id',
            'description' => 'sometimes|string',
            'opening_balance' => 'sometimes|numeric',
            'opening_bal_tyoe' => 'sometimes|string'
        ];
    }
}
