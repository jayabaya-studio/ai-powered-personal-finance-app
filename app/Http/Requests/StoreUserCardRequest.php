<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'card_type' => ['required', 'string', 'in:VISA,Mastercard'],
            'card_number' => ['required', 'string', 'digits:4'],
            'expiry_date' => ['required', 'string', 'regex:/^(0[1-9]|1[0-2])\/?([0-9]{2})$/'], // MM/YY format
            'is_default' => ['sometimes', 'boolean'],
        ];
    }
}
