<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $transactionId = $this->route('transaction')->id ?? null;

        return [
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'account_id' => 'required|exists:accounts,id,user_id,' . auth()->id(),
            'category_id' => [
                'nullable',
                Rule::exists('categories', 'id')->where(function ($query) {
                    $query->where('user_id', auth()->id());
                }),
            ],
            'type' => ['required', Rule::in(['expense', 'income', 'transfer'])],
            'transaction_date' => 'required|date',
            'transfer_to_account_id' => [
                'required_if:type,transfer',
                'nullable',
                Rule::exists('accounts', 'id')->where(function ($query) {
                    $query->where('user_id', auth()->id());
                }),
                'different:account_id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'description.required' => 'The transaction description is required.',
            'description.string' => 'The description must be text.',
            'description.max' => 'The description cannot exceed 255 characters.',
            'amount.required' => 'The transaction amount is required.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.min' => 'The amount must be at least 0.01.',
            'account_id.required' => 'The account is required.',
            'account_id.exists' => 'The selected account is invalid or does not belong to you.',
            'category_id.exists' => 'The selected category is invalid or does not belong to you.',
            'type.required' => 'The transaction type is required.',
            'type.in' => 'The selected transaction type is invalid.',
            'transaction_date.required' => 'The transaction date is required.',
            'transaction_date.date' => 'The transaction date must be a valid date.',
            'transfer_to_account_id.required_if' => 'The destination account is required for transfers.',
            'transfer_to_account_id.exists' => 'The selected destination account is invalid or does not belong to you.',
            'transfer_to_account_id.different' => 'The destination account cannot be the same as the source account.',
        ];
    }
}