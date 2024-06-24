<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\ErrorHandler\Debug;

class GovApiMembersDataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $ipAddress = trim($this->ip());
        $govToken = trim($this->header('GOV-TOKEN'));
        return $govToken == config('generalSettings.gov_token') && $ipAddress == config('generalSettings.gov_ip_address');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer'],
            'items_per_page' => ['sometimes', 'integer'],
            'no_pagination' => ['sometimes', 'boolean']
        ];
    }
}
