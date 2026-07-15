<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class LandingContactSectionRequest extends FormRequest
{
    private const MAX_SHORT = 100;

    private const MAX_INTRO = 500;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cta_subtitle'   => 'required|json',
            'cta_headline'   => 'required|json',
            'cta_intro'      => 'required|json',
            'cta_btn1_text'  => 'required|json',
            'cta_btn1_link'  => 'nullable|string|max:255',
            'cta_btn2_text'  => 'required|json',
            'cta_btn2_link'  => 'nullable|string|max:255',
            'form_subtitle'  => 'required|json',
            'form_title'     => 'required|json',
            'form_intro'     => 'required|json',
            'is_active'      => 'boolean',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateLocalizedField($validator, 'cta_subtitle', 'عنوان CTA الفرعي', self::MAX_SHORT, true);
            $this->validateLocalizedField($validator, 'cta_intro', 'وصف CTA', self::MAX_INTRO, true);
            $this->validateLocalizedField($validator, 'cta_btn1_text', 'نص الزر الأول', self::MAX_SHORT, true);
            $this->validateLocalizedField($validator, 'cta_btn2_text', 'نص الزر الثاني', self::MAX_SHORT, true);
            $this->validateLocalizedField($validator, 'form_subtitle', 'عنوان النموذج الفرعي', self::MAX_SHORT, true);
            $this->validateLocalizedField($validator, 'form_title', 'عنوان النموذج', self::MAX_SHORT, true);
            $this->validateLocalizedField($validator, 'form_intro', 'وصف النموذج', self::MAX_INTRO, true);
            $this->validateHeadline($validator, 'cta_headline', 'CTA');
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

    private function validateHeadline($validator, string $field, string $label): void
    {
        $headline = $this->decodeJsonField($field);

        if (! is_array($headline) || count($headline) === 0) {
            $validator->errors()->add($field, "يجب إضافة جزء واحد على الأقل في {$label}.");

            return;
        }

        foreach ($headline as $index => $segment) {
            $partNumber = $index + 1;

            if (! is_array($segment['title'] ?? null)) {
                $validator->errors()->add("{$field}.{$index}", "جزء {$label} رقم {$partNumber} غير صالح.");

                continue;
            }

            foreach (['ar', 'en'] as $lang) {
                $langLabel = $lang === 'ar' ? 'عربي' : 'إنجليزي';
                $value     = trim((string) ($segment['title'][$lang] ?? ''));

                if ($value === '') {
                    $validator->errors()->add(
                        "{$field}.{$index}.{$lang}",
                        "{$label} — الجزء {$partNumber} ({$langLabel}) مطلوب."
                    );
                }

                if (mb_strlen($value) > self::MAX_SHORT) {
                    $validator->errors()->add(
                        "{$field}.{$index}.{$lang}",
                        "{$label} — الجزء {$partNumber} ({$langLabel}) يجب ألا يتجاوز " . self::MAX_SHORT . ' حرف.'
                    );
                }
            }
        }
    }
}
