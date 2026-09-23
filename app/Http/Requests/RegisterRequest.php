<?php

namespace App\Http\Requests;

use App\Models\Invite;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $inviteId = $this->input('invite_id') ?? $this->query('invite_id');
        $invite = Invite::findOrFail($inviteId);

        if($invite->used_at != null){
            abort(403, 'Invite je iskoriscen');
        }

        return true;
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
            'password' => 'required|string|min:6|confirmed'
        ];
    }
}
