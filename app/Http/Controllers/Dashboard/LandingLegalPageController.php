<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LandingLegalPageRequest;
use App\Http\Resources\Dashboard\LandingLegalPageResource;
use App\Models\LandingLegalPage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingLegalPageController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:landing legal read', only: ['index', 'show']),
            new Middleware('can:landing legal edit', only: ['update']),
        ];
    }

    public function index()
    {
        $pages = LandingLegalPage::orderBy('type')->get();

        return responseJson(LandingLegalPageResource::collection($pages), 'Data retrieved successfully', 200);
    }

    public function show(LandingLegalPage $landingLegalPage)
    {
        return responseJson(new LandingLegalPageResource($landingLegalPage), 'Data retrieved successfully', 200);
    }

    public function update(LandingLegalPageRequest $request, LandingLegalPage $landingLegalPage)
    {
        $data = $request->validated();

        foreach (['title', 'content'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = json_decode($data[$field], true);
            }
        }

        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        $landingLegalPage->update($data);

        return responseJson(new LandingLegalPageResource($landingLegalPage), 'Updated Successfully', 200);
    }
}
