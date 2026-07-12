<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LandingFaqRequest;
use App\Http\Resources\Dashboard\LandingFaqResource;
use App\Models\LandingFaq;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingFaqController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:landing faq read',   only: ['index', 'show']),
            new Middleware('can:landing faq create', only: ['store']),
            new Middleware('can:landing faq edit',   only: ['update']),
            new Middleware('can:landing faq delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $faqs = LandingFaq::orderBy('sort_order')->orderBy('id')->paginate(15);
        return responseJson(LandingFaqResource::collection($faqs->items()), '', 200, getPaginates($faqs));
    }

    public function store(LandingFaqRequest $request)
    {
        LandingFaq::create($request->validated());
        return responseJson([], 'Created Successfully', 200);
    }

    public function show(LandingFaq $landingFaq)
    {
        return responseJson(new LandingFaqResource($landingFaq), 'Data retrieved successfully', 200);
    }

    public function update(LandingFaqRequest $request, LandingFaq $landingFaq)
    {
        $landingFaq->update($request->validated());
        return responseJson(new LandingFaqResource($landingFaq), 'Updated Successfully', 200);
    }

    public function destroy(LandingFaq $landingFaq)
    {
        $landingFaq->delete();
        return responseJson([], 'Deleted Successfully', 200);
    }
}
