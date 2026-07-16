<?php

namespace Modules\Reports\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Companies\Models\Company;
use Modules\Contacts\Models\Contact;
use Modules\Core\Helpers\ApiResponse;
use Modules\Deals\Models\Deal;
use Modules\Invoices\Models\Invoice;
use Modules\Tasks\Models\Task;

class ReportController extends Controller
{
    public function dashboard(): JsonResponse
    {
        $tenantId = Auth::guard('tenant_api')->user()->tenant_id;

        $totalContacts = Contact::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)->count();

        $totalDeals = Deal::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)->count();

        $wonDeals = Deal::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('status', 'won')->count();

        $totalRevenue = Invoice::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('status', 'paid')
            ->sum('total');

        $openDealsValue = Deal::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('status', 'open')
            ->sum('value');

        $pendingTasks = Task::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('status', '!=', 'completed')->count();

        $overdueInvoices = Invoice::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('status', 'overdue')->count();

        $newContactsThisMonth = Contact::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $lostDeals = Deal::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('status', 'lost')->count();

        $totalCompanies = Company::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)->count();

        $contactsBySource = Contact::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->whereNotNull('lead_source')
            ->select('lead_source', DB::raw('COUNT(*) as count'))
            ->groupBy('lead_source')
            ->orderByDesc('count')
            ->get();

        return ApiResponse::success([
            'total_contacts'          => $totalContacts,
            'total_deals'             => $totalDeals,
            'won_deals'               => $wonDeals,
            'lost_deals'              => $lostDeals,
            'win_rate'                => $totalDeals > 0 ? round($wonDeals / $totalDeals * 100, 1) : 0,
            'total_revenue'           => $totalRevenue,
            'open_deals_value'        => $openDealsValue,
            'pending_tasks'           => $pendingTasks,
            'overdue_invoices'        => $overdueInvoices,
            'new_contacts_this_month' => $newContactsThisMonth,
            'total_companies'         => $totalCompanies,
            'contacts_by_source'      => $contactsBySource,
        ]);
    }

    public function deals(): JsonResponse
    {
        $tenantId = Auth::guard('tenant_api')->user()->tenant_id;

        // Deals by stage
        $byStage = Deal::withoutGlobalScope('tenant')
            ->where('deals.tenant_id', $tenantId)
            ->join('deal_stages', 'deals.stage_id', '=', 'deal_stages.id')
            ->select('deal_stages.name as stage', DB::raw('COUNT(*) as count'), DB::raw('SUM(deals.value) as total_value'))
            ->groupBy('deal_stages.id', 'deal_stages.name')
            ->orderBy('deal_stages.order')
            ->get();

        // Deals by status
        $byStatus = Deal::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(value) as total_value'))
            ->groupBy('status')
            ->get();

        // Monthly closed deals (last 6 months)
        $monthlyDeals = Deal::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('status', 'won')
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(value) as total_value')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')
            ->get();

        return ApiResponse::success([
            'by_stage'     => $byStage,
            'by_status'    => $byStatus,
            'monthly_won'  => $monthlyDeals,
        ]);
    }

    public function revenue(): JsonResponse
    {
        $tenantId = Auth::guard('tenant_api')->user()->tenant_id;

        // Monthly revenue (last 12 months)
        $monthly = Invoice::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('status', 'paid')
            ->where('issue_date', '>=', now()->subMonths(12))
            ->select(
                DB::raw('YEAR(issue_date) as year'),
                DB::raw('MONTH(issue_date) as month'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')
            ->get();

        // Revenue by status
        $byStatus = Invoice::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total_amount'))
            ->groupBy('status')
            ->get();

        $totalPaid    = Invoice::withoutGlobalScope('tenant')->where('tenant_id', $tenantId)->where('status', 'paid')->sum('total');
        $totalPending = Invoice::withoutGlobalScope('tenant')->where('tenant_id', $tenantId)->whereIn('status', ['sent', 'overdue'])->sum('total');

        return ApiResponse::success([
            'monthly_revenue' => $monthly,
            'by_status'       => $byStatus,
            'total_paid'      => $totalPaid,
            'total_pending'   => $totalPending,
        ]);
    }
}
