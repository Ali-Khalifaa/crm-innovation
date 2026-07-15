<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LandingHowSectionRequest;
use App\Http\Resources\Dashboard\LandingHowSectionResource;
use App\Models\LandingHowSection;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingHowSectionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:landing how it works read', only: ['show']),
            new Middleware('can:landing how it works edit', only: ['update']),
        ];
    }

    public function show()
    {
        $section = LandingHowSection::firstOrCreate([], ['is_active' => true]);

        return responseJson(new LandingHowSectionResource($section), 'Data retrieved successfully', 200);
    }

    public function update(LandingHowSectionRequest $request)
    {
        $section = LandingHowSection::firstOrCreate([], ['is_active' => true]);
        $data    = $this->parseJsonFields($request->validated());

        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        $section->update($data);

        return responseJson(new LandingHowSectionResource($section), 'Updated Successfully', 200);
    }

    private function parseJsonFields(array $data): array
    {
        foreach (['subtitle', 'headline'] as $field) {
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
