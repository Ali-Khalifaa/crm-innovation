<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class LandingSolutionSectionRequest extends FormRequest
{
    private const MAX_SHORT = 100;

    private const MAX_DESC = 500;

    private const MAX_BUTTON_TEXT = 100;

    private const MAX_URL = 255;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subtitle'    => 'required|json',
            'headline'    => 'required|json',
            'description' => 'required|json',
            'button'      => 'required|json',
            'is_active'   => 'boolean',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateLocalizedField($validator, 'subtitle', 'العنوان الفرعي', self::MAX_SHORT, true);
            $this->validateHeadline($validator);
            $this->validateLocalizedField($validator, 'description', 'الوصف', self::MAX_DESC, true);
            $this->validateButton($validator);
        });
    }

    public function messages(): array
    {
        return [
            'subtitle.required'    => 'بيانات العنوان الفرعي مطلوبة.',
            'headline.required'    => 'بيانات العنوان الرئيسي مطلوبة.',
            'description.required' => 'بيانات الوصف مطلوبة.',
            'button.required'      => 'بيانات الزر مطلوبة.',
        ];
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

    private function validateHeadline($validator): void
    {
        $headline = $this->decodeJsonField('headline');

        if (! is_array($headline) || count($headline) === 0) {
            $validator->errors()->add('headline', 'يجب إضافة جزء واحد على الأقل في العنوان الرئيسي.');

            return;
        }

        foreach ($headline as $index => $segment) {
            $partNumber = $index + 1;

            if (! is_array($segment['title'] ?? null)) {
                $validator->errors()->add("headline.{$index}", "جزء العنوان رقم {$partNumber} غير صالح.");

                continue;
            }

            foreach (['ar', 'en'] as $lang) {
                $langLabel = $lang === 'ar' ? 'عربي' : 'إنجليزي';
                $value     = trim((string) ($segment['title'][$lang] ?? ''));

                if ($value === '') {
                    $validator->errors()->add(
                        "headline.{$index}.{$lang}",
                        "العنوان الرئيسي — الجزء {$partNumber} ({$langLabel}) مطلوب."
                    );
                }

                if (mb_strlen($value) > self::MAX_SHORT) {
                    $validator->errors()->add(
                        "headline.{$index}.{$lang}",
                        "العنوان الرئيسي — الجزء {$partNumber} ({$langLabel}) يجب ألا يتجاوز " . self::MAX_SHORT . ' حرف.'
                    );
                }
            }
        }
    }

    private function validateButton($validator): void
    {
        $button = $this->decodeJsonField('button');

        if (! is_array($button)) {
            $validator->errors()->add('button', 'بيانات الزر غير صالحة.');

            return;
        }

        $text = $button['text'] ?? null;

        if (! is_array($text)) {
            $validator->errors()->add('button.text', 'نص الزر غير صالح.');

            return;
        }

        foreach (['ar', 'en'] as $lang) {
            $langLabel = $lang === 'ar' ? 'عربي' : 'إنجليزي';
            $value     = trim((string) ($text[$lang] ?? ''));

            if ($value === '') {
                $validator->errors()->add("button.text.{$lang}", "نص الزر ({$langLabel}) مطلوب.");
            }

            if (mb_strlen($value) > self::MAX_BUTTON_TEXT) {
                $validator->errors()->add(
                    "button.text.{$lang}",
                    "نص الزر ({$langLabel}) يجب ألا يتجاوز " . self::MAX_BUTTON_TEXT . ' حرف.'
                );
            }
        }

        $url = trim((string) ($button['url'] ?? ''));

        if ($url === '') {
            $validator->errors()->add('button.url', 'رابط الزر مطلوب.');
        }

        if (mb_strlen($url) > self::MAX_URL) {
            $validator->errors()->add('button.url', 'رابط الزر يجب ألا يتجاوز ' . self::MAX_URL . ' حرف.');
        }
    }
}
