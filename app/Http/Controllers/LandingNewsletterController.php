<?php

namespace App\Http\Controllers;

use App\Http\Requests\Website\LandingNewsletterSubscribeRequest;
use App\Models\LandingNewsletterSubscriber;

class LandingNewsletterController extends Controller
{
    public function subscribe(LandingNewsletterSubscribeRequest $request)
    {
        $exists = LandingNewsletterSubscriber::where('email', $request->email)->exists();

        if ($exists) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('crm.newsletter_already'),
                ], 422);
            }

            return back()->with('newsletter_error', __('crm.newsletter_already'))->withFragment('footer');
        }

        LandingNewsletterSubscriber::create([
            'email'      => $request->email,
            'ip_address' => $request->ip(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('crm.newsletter_success'),
            ]);
        }

        return back()->with('newsletter_success', true)->withFragment('footer');
    }
}
