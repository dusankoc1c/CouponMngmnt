<?php

namespace App\Helpers;

use App\Http\Requests\ExportAllCodesRequest;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmailTemplateHelper
{
    public static function getDefaultInitialTemplate(): string
    {
        return "Hello {{name}},\n\nHere is your gift card worth \${{amount}} from {{store_name}}!\n\nClick the link below to get started.";
    }

    public static function getDefaultReminderTemplate(): string
    {
        return "Hello {{name}},\n\nYou still haven't used your gift card worth \${{amount}} from {{store_name}}. It expires in {{days_left}} days!\n\nClick the link below to get started.";
    }

    public static function render(string $template, array $data = []): string
    {
        $render = $template;
        foreach ($data as $key => $value) {
            $render = str_replace('{{' . $key . '}}', $value, $render);
        }

        return $render;
    }

    public static function initialTemplateContainsRequiredPlaceholders(string $template): bool
    {
        if (!str_contains($template, '{{name}}')) {
            return false;
        }

        if (!str_contains($template, '{{amount}}')) {
            return false;
        }

        if (!str_contains($template, '{{store_name}}')) {
            return false;
        }

        return true;
    }

    public static function reminderTemplateContainsRequiredPlaceholders(string $template): bool
    {
        if (!str_contains($template, '{{name}}')) {
            return false;
        }

        if (!str_contains($template, '{{amount}}')) {
            return false;
        }

        if (!str_contains($template, '{{store_name}}')) {
            return false;
        }

        if (!str_contains($template, '{{days_left}}')) {
            return false;
        }

        return true;
    }
}
