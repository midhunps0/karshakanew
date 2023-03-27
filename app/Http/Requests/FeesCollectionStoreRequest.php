<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\FormRequest;

class FeesCollectionStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        info($this);
        $memberId = $this->route()->parameter('id');
        $member = Member::find($memberId);
        /**
         * @var User
         */
        $user = User::find(auth()->user()->id);
        return $user->hasPermissionTo('Fee Collection: Create In Any District')
            || ($member->district_id == $user->district_id
                && $user->hasPermissionTo('Fee Collection: Create In Own District'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'date' => ['required', 'string'],
            'fee_item.*.fee_type_id' => ['required', 'integer'],
            'fee_item.*.tenure' => ['sometimes', 'integer'],
            'fee_item.*.period_from' => ['sometimes', 'string'],
            'fee_item.*.period_to' => ['sometimes', 'string'],
            'fee_item.*.amount' => ['required', 'numeric'],
            'notes' => ['sometimes', 'string']
        ];
    }
}
