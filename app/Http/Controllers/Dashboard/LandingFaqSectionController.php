<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LandingFaqSectionRequest;
use App\Http\Resources\Dashboard\LandingFaqSectionResource;
use App\Models\LandingFaqSection;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingFaqSectionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:landing faq read', only: ['show']),
            new Middleware('can:landing faq edit', only: ['update']),
        ];
    }

    public function show()
    {
        $section = LandingFaqSection::firstOrCreate([], ['is_active' => true]);

        return responseJson(new LandingFaqSectionResource($section), 'Data retrieved successfully', 200);
    }

    public function update(LandingFaqSectionRequest $request)
    {
        $section = LandingFaqSection::firstOrCreate([], ['is_active' => true]);
        $data    = $this->parseJsonFields($request->validated());

        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        if ($request->hasFile('bg_image')) {
            if ($section->bg_image) {
                unlink_image_by_path($section->bg_image);
            }
            $data['bg_image'] = store_single_image($request->bg_image);
        }

        $section->update($data);

        return responseJson(new LandingFaqSectionResource($section), 'Updated Successfully', 200);
    }

    private function parseJsonFields(array $data): array
    {
        foreach (['subtitle', 'headline', 'intro'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = json_decode($data[$field], true);
            }
        }

        if (isset($data['headline']) && is_array($data['headline'])) {
            $data['headline'] = array_map(function ($segment) {
                return [
                    'title' => $segment['title'] ?? ['ar' => '', 'en' => ''],
                    'check' => ! empty($segment['check']) ? 1 : 0,
                ];
            }, $data['headline']);
        }

        return $data;
    }
}
