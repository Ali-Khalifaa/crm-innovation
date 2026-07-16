<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class LandingLegalPageRequest extends FormRequest
{
    private const MAX_TITLE = 200;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'     => 'required|json',
            'content'   => 'required|json',
            'is_active' => 'boolean',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateLocalizedField($validator, 'title', 'العنوان', self::MAX_TITLE, true);
            $this->validateLocalizedField($validator, 'content', 'المحتوى', 50000, true);
        });
    }

    private function decodeJsonField(string $field): ?array
    {
        $value = $this->input($field);

        if (is_array($value)) {
            return $value;
        }

        if (! is_string($value) || $value === '') {
            return null;
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : null;
    }

    private function validateLocalizedField($validator, string $field, string $label, int $max, bool $required): void
    {
        $data = $this->decodeJsonField($field);

        if (! is_array($data)) {
            $validator->errors()->add($field, "{$label} غير صالح.");

            return;
        }

        foreach (['ar', 'en'] as $lang) {
            $langLabel = $lang === 'ar' ? 'عربي' : 'إنجليزي';
            $value     = trim((string) ($data[$lang] ?? ''));
            $plain     = trim(strip_tags(str_replace(['&nbsp;', '&#160;'], ' ', $value)));

            if ($required && $plain === '') {
                $validator->errors()->add("{$field}.{$lang}", "{$label} ({$langLabel}) مطلوب.");
            }

            if (mb_strlen($value) > $max) {
                $validator->errors()->add("{$field}.{$lang}", "{$label} ({$langLabel}) يجب ألا يتجاوز {$max} حرف.");
            }
        }
    }
}
