<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\LandingContactMessageResource;
use App\Models\LandingContactMessage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingContactMessageController extends Controller implements HasMiddleware
{
    public static function middleware(): array {
        return [
            new Middleware('can:landing contact read',   only: ['index', 'show', 'markRead']),
            new Middleware('can:landing contact delete', only: ['destroy']),
        ];
    }

    public function index(Request $request) {
        $query = LandingContactMessage::orderByDesc('created_at');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($search = trim((string) $request->search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $perPage = min(max((int) ($request->per_page ?: 15), 5), 100);
        $items   = $query->paginate($perPage);

        $pagination = getPaginates($items);
        $pagination['total_all'] = LandingContactMessage::count();
        $pagination['total_new'] = LandingContactMessage::where('status', 'new')->count();

        return responseJson(LandingContactMessageResource::collection($items->items()), 'تم بنجاح', 200, $pagination);
    }

    public function show(LandingContactMessage $landingContactMessage) {
        if ($landingContactMessage->status === 'new')
            $landingContactMessage->update(['status' => 'read']);
        return responseJson(new LandingContactMessageResource($landingContactMessage), 'تم بنجاح');
    }

    public function markRead(LandingContactMessage $landingContactMessage) {
        $landingContactMessage->update(['status' => 'replied']);
        return responseJson(new LandingContactMessageResource($landingContactMessage), 'تم التحديث');
    }

    public function destroy(LandingContactMessage $landingContactMessage) {
        $landingContactMessage->delete();
        return responseJson(null, 'تم الحذف بنجاح');
    }
}
