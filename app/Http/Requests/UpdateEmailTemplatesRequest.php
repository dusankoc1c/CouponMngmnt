<?php
namespace App\Http\Requests;

use App\Helpers\EmailTemplateHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Validator;

class UpdateEmailTemplatesRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('workWith', $this->route('store'));
    }

    public function rules()
    {
        return [
            'initial_email_template' => 'required|string',
            'reminder_email_template' => 'required|string',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $initialTemplate = $this->input('initial_email_template');
            $reminderTemplate = $this->input('reminder_email_template');
            if ($initialTemplate != null) {
                if (!EmailTemplateHelper::initialTemplateContainsRequiredPlaceholders($initialTemplate)) {
                    $validator->errors()->add('initial_email_template', 'Inicijalni template mora da sadrzi {{name}}, {{amount}} i {{store_name}}.');
                }
            }

            if ($reminderTemplate != null) {
                if (!EmailTemplateHelper::reminderTemplateContainsRequiredPlaceholders($reminderTemplate)) {
                    $validator->errors()->add('reminder_email_template', 'Reminder template mora da sadrzi {{name}}, {{amount}}, {{store_name}} i {{days_left}}.');
                }
            }
        });
    }
}
