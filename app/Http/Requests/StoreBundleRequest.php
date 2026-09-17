<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreBundleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('workWith', $this->route('store'));

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date',
            'coupons' => 'nullable|array',
            'coupons.*.receiver_name' => 'nullable|string|max:255',
            'coupons.*.receiver_email' => 'nullable|email|max:255',
            'coupons.*.discount_amount' => 'required_with:coupons|numeric|min:0',
            'coupons.*.send_date' => 'nullable|date',
            'tiers' => 'nullable|array',
            'tiers.*.quantity' => 'required_with:tiers|integer|min:1',
            'tiers.*.amount' => 'required_with:tiers|numeric|min:0',
        ];
    }
}
