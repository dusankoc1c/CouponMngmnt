<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateCouponRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('workWith', $this->route('coupon')->bundle->store);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'receiver_name' => 'required|string|max:255',
            'receiver_email' => 'required|email|max:255',
            'discount_amount' => 'required|numeric|min:0',
            'send_date' => 'nullable|date',
            'expires_at' => 'nullable|date',
        ];
    }
}
