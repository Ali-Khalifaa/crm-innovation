<?php
namespace App\Http\Controllers;

use App\Http\Requests\Website\LandingContactRequest;
use App\Models\LandingContactMessage;

class LandingContactController extends Controller
{
    public function send(LandingContactRequest $request)
    {
        LandingContactMessage::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'subject'    => __('crm.contact_default_subject'),
            'message'    => $request->message,
            'ip_address' => $request->ip(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('crm.contact_success'),
            ]);
        }

        return back()->with('contact_success', true)->withFragment('contact');
    }
}
