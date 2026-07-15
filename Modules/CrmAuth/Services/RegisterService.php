<?php

namespace Modules\CrmAuth\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\CrmAuth\Mail\WelcomeMail;
use Modules\CrmAuth\Models\Tenant;
use Modules\CrmAuth\Models\User;
use Modules\Plans\Models\Plan;
use Database\Seeders\DealStagesSeeder;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegisterService
{
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $plan = Plan::findOrFail($data['plan_id']);

            // 1. Create Tenant
            $trialDays = (int) env('CRM_TRIAL_DAYS', 14);
            $tenant = Tenant::create([
                'name'          => $data['company_name'],
                'slug'          => $this->generateSlug($data['company_name']),
                'email'         => $data['email'],
                'phone'         => $data['phone'] ?? null,
                'plan_id'       => $plan->id,
                'billing_cycle' => $data['billing_cycle'],
                'status'        => 'trial',
                'trial_ends_at' => now()->addDays($trialDays),
                'plan_starts_at' => now(),
            ]);

            // 2. Create Owner User
            $user = User::create([
                'tenant_id' => $tenant->id,
                'name'      => $data['name'],
                'email'     => $data['email'],
                'password'  => Hash::make($data['password']),
                'role'      => 'owner',
                'status'    => 'active',
            ]);

            // 3. Create default deal stages
            DealStagesSeeder::createForTenant($tenant->id);

            // 4. Generate JWT token for the new user
            $token = JWTAuth::fromUser($user);

            $tenant->load('plan');
            $user->load('tenant.plan');

            // 5. Send welcome email (queued)
            try {
                Mail::to($user->email)->queue(new WelcomeMail($user, $tenant));
            } catch (\Exception $e) {
                // Don't fail registration if email fails
            }

            return compact('token', 'user', 'tenant');
        });
    }

    private function generateSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $count = 1;

        while (Tenant::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $count++;
        }

        return $slug;
    }
}
