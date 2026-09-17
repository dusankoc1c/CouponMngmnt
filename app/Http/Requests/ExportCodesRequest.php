<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ExportCodesRequest extends FormRequest
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
            'bundle_ids' => 'required|array|min:1',
            'bundle_ids.*' => 'exists:bundles,id',
            'created_from' => 'nullable|date',
            'created_to' => 'nullable|date',
            'status' => 'nullable|string',
            'amount_min' => 'nullable|numeric',
            'amount_max' => 'nullable|numeric',
        ];
    }
}
