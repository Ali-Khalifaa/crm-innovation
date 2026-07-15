<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class LandingProblemSectionRequest extends FormRequest
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
            'subtitle'  => 'required|json',
            'headline'  => 'required|json',
            'intro'     => 'required|json',
            'insights'  => 'required|json',
            'cta'       => 'required|json',
            'is_active' => 'boolean',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateLocalizedField($validator, 'subtitle', 'العنوان الفرعي', self::MAX_SHORT, true);
            $this->validateHeadline($validator);
            $this->validateLocalizedField($validator, 'intro', 'المقدمة', self::MAX_INTRO, true);
            $this->validateInsights($validator);
            $this->validateCta($validator);
        });
    }

    public function messages(): array
    {
        return [
            'subtitle.required' => 'بيانات العنوان الفرعي مطلوبة.',
            'headline.required' => 'بيانات العنوان الرئيسي مطلوبة.',
            'intro.required'    => 'بيانات المقدمة مطلوبة.',
            'insights.required' => 'بيانات الإحصائيات مطلوبة.',
            'cta.required'      => 'بيانات زر الحل مطلوبة.',
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

    private function validateInsights($validator): void
    {
        $insights = $this->decodeJsonField('insights');

        if (! is_array($insights) || count($insights) !== 3) {
            $validator->errors()->add('insights', 'يجب إدخال 3 إحصائيات بالضبط.');

            return;
        }

        foreach ($insights as $index => $insight) {
            $num = $index + 1;

            if (! is_array($insight)) {
                $validator->errors()->add("insights.{$index}", "الإحصائية {$num} غير صالحة.");

                continue;
            }

            foreach (['value', 'label'] as $key) {
                if (! is_array($insight[$key] ?? null)) {
                    $validator->errors()->add("insights.{$index}.{$key}", "الإحصائية {$num} — {$key} غير صالح.");

                    continue;
                }

                foreach (['ar', 'en'] as $lang) {
                    $langLabel = $lang === 'ar' ? 'عربي' : 'إنجليزي';
                    $value     = trim((string) ($insight[$key][$lang] ?? ''));
                    $max       = $key === 'value' ? 50 : self::MAX_SHORT;
                    $label     = $key === 'value' ? 'القيمة' : 'الوصف';

                    if ($value === '') {
                        $validator->errors()->add(
                            "insights.{$index}.{$key}.{$lang}",
                            "الإحصائية {$num} — {$label} ({$langLabel}) مطلوب."
                        );
                    }

                    if (mb_strlen($value) > $max) {
                        $validator->errors()->add(
                            "insights.{$index}.{$key}.{$lang}",
                            "الإحصائية {$num} — {$label} ({$langLabel}) يجب ألا يتجاوز {$max} حرف."
                        );
                    }
                }
            }
        }
    }

    private function validateCta($validator): void
    {
        $data = $this->decodeJsonField('cta');

        if (! is_array($data)) {
            $validator->errors()->add('cta', 'زر الحل غير صالح.');

            return;
        }

        foreach (['ar', 'en'] as $lang) {
            $langLabel = $lang === 'ar' ? 'عربي' : 'إنجليزي';
            $bridge    = trim((string) ($data['bridge'][$lang] ?? ''));
            $value     = trim((string) ($data['text'][$lang] ?? ''));

            if ($bridge === '') {
                $validator->errors()->add("cta.bridge.{$lang}", "نص الجسر ({$langLabel}) مطلوب.");
            } elseif (mb_strlen($bridge) > self::MAX_INTRO) {
                $validator->errors()->add("cta.bridge.{$lang}", "نص الجسر ({$langLabel}) يجب ألا يتجاوز " . self::MAX_INTRO . ' حرف.');
            }

            if ($value === '') {
                $validator->errors()->add("cta.text.{$lang}", "نص زر الحل ({$langLabel}) مطلوب.");
            }

            if (mb_strlen($value) > self::MAX_SHORT) {
                $validator->errors()->add("cta.text.{$lang}", "نص زر الحل ({$langLabel}) يجب ألا يتجاوز " . self::MAX_SHORT . ' حرف.');
            }
        }

        $url = trim((string) ($data['url'] ?? ''));

        if ($url === '') {
            $validator->errors()->add('cta.url', 'رابط زر الحل مطلوب.');
        } elseif (mb_strlen($url) > 255) {
            $validator->errors()->add('cta.url', 'رابط زر الحل يجب ألا يتجاوز 255 حرف.');
        }
    }
}
