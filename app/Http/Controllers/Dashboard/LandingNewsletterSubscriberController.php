<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\LandingNewsletterSubscriberResource;
use App\Models\LandingNewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingNewsletterSubscriberController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:landing newsletter read', only: ['index']),
            new Middleware('can:landing newsletter delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = LandingNewsletterSubscriber::orderByDesc('created_at');

        if ($search = trim((string) $request->search)) {
            $query->where('email', 'like', "%{$search}%");
        }

        $perPage = min(max((int) ($request->per_page ?: 15), 5), 100);
        $items   = $query->paginate($perPage);

        $pagination = getPaginates($items);
        $pagination['total_all'] = LandingNewsletterSubscriber::count();

        return responseJson(
            LandingNewsletterSubscriberResource::collection($items),
            'تم بنجاح',
            200,
            $pagination
        );
    }

    public function destroy(LandingNewsletterSubscriber $landingNewsletterSubscriber)
    {
        $landingNewsletterSubscriber->delete();

        return responseJson(null, 'تم الحذف بنجاح');
    }
}
