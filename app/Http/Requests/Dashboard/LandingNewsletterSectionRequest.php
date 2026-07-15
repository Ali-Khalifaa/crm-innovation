<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class LandingNewsletterSectionRequest extends FormRequest
{
    private const MAX_SHORT = 100;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $imageRule = 'nullable|file|mimes:jpeg,jpg,png,webp,svg|max:5120';

        return [
            'title'       => 'required|json',
            'placeholder' => 'required|json',
            'button_text' => 'required|json',
            'bg_image'    => $imageRule,
            'is_active'   => 'boolean',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateLocalizedField($validator, 'title', 'العنوان', self::MAX_SHORT, true);
            $this->validateLocalizedField($validator, 'placeholder', 'Placeholder', self::MAX_SHORT, true);
            $this->validateLocalizedField($validator, 'button_text', 'نص الزر', self::MAX_SHORT, true);
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

            if ($required && $value === '') {
                $validator->errors()->add("{$field}.{$lang}", "{$label} ({$langLabel}) مطلوب.");
            }

            if (mb_strlen($value) > $max) {
                $validator->errors()->add("{$field}.{$lang}", "{$label} ({$langLabel}) يجب ألا يتجاوز {$max} حرف.");
            }
        }
    }
}
