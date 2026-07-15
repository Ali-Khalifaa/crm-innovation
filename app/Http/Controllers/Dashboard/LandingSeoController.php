<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LandingSeoRequest;
use App\Http\Resources\Dashboard\LandingSeoResource;
use App\Models\LandingSeo;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingSeoController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:landing seo read', only: ['show']),
            new Middleware('can:landing seo edit', only: ['update']),
        ];
    }

    public function show()
    {
        $seo = LandingSeo::firstOrCreate([], ['is_active' => true]);

        return responseJson(new LandingSeoResource($seo), 'Data retrieved successfully', 200);
    }

    public function update(LandingSeoRequest $request)
    {
        $seo  = LandingSeo::firstOrCreate([], ['is_active' => true]);
        $data = $this->parseJsonFields($request->validated());

        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        foreach (['og_image', 'twitter_image'] as $imageField) {
            if ($request->hasFile($imageField)) {
                if ($seo->{$imageField}) {
                    unlink_image_by_path($seo->{$imageField});
                }
                $data[$imageField] = store_single_image($request->file($imageField));
            }
        }

        $seo->update($data);

        return responseJson(new LandingSeoResource($seo), 'Updated Successfully', 200);
    }

    private function parseJsonFields(array $data): array
    {
        foreach ([
            'meta_title', 'meta_description', 'meta_keywords',
            'og_title', 'og_description',
            'twitter_title', 'twitter_description',
        ] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = json_decode($data[$field], true);
            }
        }

        return $data;
    }
}
