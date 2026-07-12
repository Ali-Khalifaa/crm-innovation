<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\CrmAuth\Models\Tenant;
use Modules\Contacts\Models\Contact;
use Modules\Deals\Models\Deal;
use Modules\Invoices\Models\Invoice;
use Modules\Plans\Models\Plan;

class CrmStatisticsController extends Controller
{
    public function index(): JsonResponse
    {
        $totalTenants  = Tenant::count();
        $activeTenants = Tenant::where('status', 'active')->count();
        $trialTenants  = Tenant::where('status', 'trial')->count();

        $totalContacts = DB::table('contacts')->count();
        $totalDeals    = DB::table('deals')->count();
        $totalInvoices = DB::table('invoices')->count();

        $totalRevenue = DB::table('invoices')
            ->where('status', 'paid')
            ->sum('total');

        $pendingUpgrades = DB::table('plan_change_requests')
            ->where('status', 'pending')
            ->count();

        // Tenants per plan
        $byPlan = Plan::withCount('tenants')
            ->orderBy('sort_order')
            ->get()
            ->map(fn($p) => [
                'plan'          => $p->name,
                'tenants_count' => $p->tenants_count,
                'monthly_price' => $p->monthly_price,
            ]);

        // Monthly new tenants (last 6 months)
        $monthlyTenants = Tenant::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')
            ->get();

        return responseJson([
            'total_tenants'    => $totalTenants,
            'active_tenants'   => $activeTenants,
            'trial_tenants'    => $trialTenants,
            'total_contacts'   => $totalContacts,
            'total_deals'      => $totalDeals,
            'total_invoices'   => $totalInvoices,
            'total_revenue'    => (float) $totalRevenue,
            'pending_upgrades' => $pendingUpgrades,
            'tenants_by_plan'  => $byPlan,
            'monthly_tenants'  => $monthlyTenants,
        ], 'CRM Statistics', 200);
    }

    public function upgrades(): JsonResponse
    {
        $requests = DB::table('plan_change_requests')
            ->join('tenants', 'tenants.id', '=', 'plan_change_requests.tenant_id')
            ->join('plans', 'plans.id', '=', 'plan_change_requests.requested_plan_id')
            ->select(
                'plan_change_requests.*',
                'tenants.name as tenant_name',
                'tenants.email as tenant_email',
                'plans.name as requested_plan'
            )
            ->where('plan_change_requests.status', 'pending')
            ->orderByDesc('plan_change_requests.created_at')
            ->get();

        return responseJson($requests, '', 200);
    }

    public function approveUpgrade(int $id): JsonResponse
    {
        $req = DB::table('plan_change_requests')->find($id);

        if (! $req || $req->status !== 'pending') {
            return responseJson(null, 'Request not found or already processed', 404);
        }

        DB::table('tenants')->where('id', $req->tenant_id)->update([
            'plan_id'        => $req->requested_plan_id,
            'status'         => 'active',
            'plan_starts_at' => now(),
            'updated_at'     => now(),
        ]);

        DB::table('plan_change_requests')->where('id', $id)->update([
            'status'     => 'approved',
            'updated_at' => now(),
        ]);

        return responseJson(null, 'Upgrade approved successfully', 200);
    }
}
