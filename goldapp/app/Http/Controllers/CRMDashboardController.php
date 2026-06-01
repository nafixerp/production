<?php
namespace App\Http\Controllers;

use App\Models\CrmLead;
use App\Models\CrmActivity;
use App\Models\CrmOpportunity;
use App\Models\CustomerComplaint;
use App\Models\CrmCustomer;
use Illuminate\Support\Facades\DB;

class CRMDashboardController extends Controller
{
    public function index()
    {
        // Lead pipeline stats
        $leadsByStatus = CrmLead::selectRaw('status, COUNT(*) as count, SUM(estimated_value) as value')
            ->groupBy('status')->get()->keyBy('status');

        $totalLeads = CrmLead::count();
        $openLeads = CrmLead::whereNotIn('status',['won','lost'])->count();
        $pipelineValue = CrmLead::whereNotIn('status',['won','lost'])->sum('estimated_value');
        $wonThisMonth = CrmLead::where('status','won')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();
        $wonValue = CrmLead::where('status','won')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum('estimated_value');

        // Activities today & upcoming
        $activitiesToday = CrmActivity::whereDate('activity_date', today())->count();
        $activitiesDue = CrmActivity::where('next_action_date', '<=', today())->whereNotNull('next_action')->count();

        // Complaints
        $openComplaints = CustomerComplaint::whereNotIn('status',['resolved','closed'])->count();
        $criticalComplaints = CustomerComplaint::where('priority','critical')
            ->whereNotIn('status',['resolved','closed'])->count();
        $escalatedComplaints = CustomerComplaint::where('escalated',1)
            ->whereNotIn('status',['resolved','closed'])->count();

        // Opportunities pipeline
        $opsByStage = CrmOpportunity::selectRaw('stage, COUNT(*) as count, SUM(value) as value')
            ->groupBy('stage')->get()->keyBy('stage');
        $weightedPipeline = CrmOpportunity::whereNotIn('stage',['closed_lost'])
            ->selectRaw('SUM(value * probability / 100) as weighted')->value('weighted');

        // Customer stats
        $totalCustomers = CrmCustomer::where('status',1)->count();
        $newThisMonth = CrmCustomer::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();

        // Recent activities
        $recentActivities = CrmActivity::with(['lead','customer'])->orderBy('activity_date','desc')->limit(10)->get();

        // Upcoming follow-ups
        $followUps = CrmActivity::whereNotNull('next_action_date')
            ->where('next_action_date', '>=', today())
            ->where('next_action_date', '<=', today()->addDays(7))
            ->orderBy('next_action_date','asc')->limit(10)->get();

        return view('crm.dashboard.index', compact(
            'leadsByStatus','totalLeads','openLeads','pipelineValue','wonThisMonth','wonValue',
            'activitiesToday','activitiesDue','openComplaints','criticalComplaints','escalatedComplaints',
            'opsByStage','weightedPipeline','totalCustomers','newThisMonth','recentActivities','followUps'
        ));
    }
}
