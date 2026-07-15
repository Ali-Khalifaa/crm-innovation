<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LandingContactSectionRequest;
use App\Http\Resources\Dashboard\LandingContactSectionResource;
use App\Models\LandingContactSection;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingContactSectionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:landing contact read', only: ['show']),
            new Middleware('can:landing contact edit', only: ['update']),
        ];
    }

    public function show()
    {
        $section = LandingContactSection::firstOrCreate([], ['is_active' => true]);

        return responseJson(new LandingContactSectionResource($section), 'Data retrieved successfully', 200);
    }

    public function update(LandingContactSectionRequest $request)
    {
        $section = LandingContactSection::firstOrCreate([], ['is_active' => true]);
        $data    = $this->parseJsonFields($request->validated());

        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        $section->update($data);

        return responseJson(new LandingContactSectionResource($section), 'Updated Successfully', 200);
    }

    private function parseJsonFields(array $data): array
    {
        foreach (['cta_subtitle', 'cta_intro', 'cta_btn1_text', 'cta_btn2_text', 'form_subtitle', 'form_title', 'form_intro'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = json_decode($data[$field], true);
            }
        }

        if (isset($data['cta_headline']) && is_string($data['cta_headline'])) {
            $data['cta_headline'] = json_decode($data['cta_headline'], true);
        }

        if (isset($data['cta_headline']) && is_array($data['cta_headline'])) {
            $data['cta_headline'] = array_map(function ($segment) {
                return [
                    'title' => $segment['title'] ?? ['ar' => '', 'en' => ''],
                    'check' => ! empty($segment['check']) ? 1 : 0,
                ];
            }, $data['cta_headline']);
        }

        return $data;
    }
}
