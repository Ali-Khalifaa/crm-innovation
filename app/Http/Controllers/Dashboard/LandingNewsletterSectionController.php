<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LandingNewsletterSectionRequest;
use App\Http\Resources\Dashboard\LandingNewsletterSectionResource;
use App\Models\LandingNewsletterSection;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingNewsletterSectionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:landing newsletter read', only: ['show']),
            new Middleware('can:landing newsletter edit', only: ['update']),
        ];
    }

    public function show()
    {
        $section = LandingNewsletterSection::firstOrCreate([], ['is_active' => true]);

        return responseJson(new LandingNewsletterSectionResource($section), 'Data retrieved successfully', 200);
    }

    public function update(LandingNewsletterSectionRequest $request)
    {
        $section = LandingNewsletterSection::firstOrCreate([], ['is_active' => true]);
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

        return responseJson(new LandingNewsletterSectionResource($section), 'Updated Successfully', 200);
    }

    private function parseJsonFields(array $data): array
    {
        foreach (['title', 'placeholder', 'button_text'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = json_decode($data[$field], true);
            }
        }

        return $data;
    }
}
