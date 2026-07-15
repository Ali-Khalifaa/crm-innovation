<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LandingSolutionPointRequest;
use App\Http\Resources\Dashboard\LandingSolutionPointResource;
use App\Models\LandingSolutionPoint;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingSolutionPointController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:landing solutions read', only: ['index', 'show']),
            new Middleware('can:landing solutions edit', only: ['store', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $points = LandingSolutionPoint::orderBy('sort_order')->paginate(20);

        return responseJson(
            LandingSolutionPointResource::collection($points->items()),
            '',
            200,
            getPaginates($points)
        );
    }

    public function store(LandingSolutionPointRequest $request)
    {
        $data              = $this->mapLocalizedFields($request->validated());
        $data['is_active'] = $request->boolean('is_active');

        LandingSolutionPoint::create($data);

        return responseJson([], 'Created Successfully', 200);
    }

    public function show(LandingSolutionPoint $landingSolutionPoint)
    {
        return responseJson(new LandingSolutionPointResource($landingSolutionPoint), 'Data retrieved successfully', 200);
    }

    public function update(LandingSolutionPointRequest $request, LandingSolutionPoint $landingSolutionPoint)
    {
        $data              = $this->mapLocalizedFields($request->validated());
        $data['is_active'] = $request->boolean('is_active');

        $landingSolutionPoint->update($data);

        return responseJson(new LandingSolutionPointResource($landingSolutionPoint), 'Updated Successfully', 200);
    }

    public function destroy(LandingSolutionPoint $landingSolutionPoint)
    {
        $landingSolutionPoint->delete();

        return responseJson([], 'Deleted Successfully', 200);
    }

    private function mapLocalizedFields(array $data): array
    {
        $data['text'] = [
            'ar' => $data['text_ar'],
            'en' => $data['text_en'],
        ];

        unset($data['text_ar'], $data['text_en']);

        return $data;
    }
}
