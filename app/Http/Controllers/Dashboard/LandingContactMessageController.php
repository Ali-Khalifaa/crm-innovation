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
        if ($request->status) $query->where('status', $request->status);
        $items = $query->paginate(15);
        return responseJson(LandingContactMessageResource::collection($items), 'تم بنجاح', 200, getPaginates($items));
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
