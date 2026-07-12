<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LandingStatRequest;
use App\Http\Resources\Dashboard\LandingStatResource;
use App\Models\LandingStat;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingStatController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:landing stat read',   only: ['index', 'show']),
            new Middleware('can:landing stat create', only: ['store']),
            new Middleware('can:landing stat edit',   only: ['update']),
            new Middleware('can:landing stat delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $stats = LandingStat::orderBy('sort_order')->orderBy('id')->paginate(15);
        return responseJson(LandingStatResource::collection($stats->items()), '', 200, getPaginates($stats));
    }

    public function store(LandingStatRequest $request)
    {
        LandingStat::create($request->validated());
        return responseJson([], 'Created Successfully', 200);
    }

    public function show(LandingStat $landingStat)
    {
        return responseJson(new LandingStatResource($landingStat), 'Data retrieved successfully', 200);
    }

    public function update(LandingStatRequest $request, LandingStat $landingStat)
    {
        $landingStat->update($request->validated());
        return responseJson(new LandingStatResource($landingStat), 'Updated Successfully', 200);
    }

    public function destroy(LandingStat $landingStat)
    {
        $landingStat->delete();
        return responseJson([], 'Deleted Successfully', 200);
    }
}
