<?php
namespace App\Http\Controllers;

use App\Models\PaymentSchedule;
use App\Models\CrmCustomer;
use App\Models\VendorSupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentScheduleController extends Controller
{
    public function index(Request $request)
    {
        $partyType = $request->party_type ?? 'all';
        $status = $request->status;
        $fromDue = $request->from_due;
        $toDue = $request->to_due;

        $q = PaymentSchedule::query();
        if ($partyType !== 'all') $q->where('party_type', $partyType);
        if ($status) $q->where('status', $status);
        if ($fromDue) $q->where('due_date', '>=', $fromDue);
        if ($toDue) $q->where('due_date', '<=', $toDue);

        // Auto-mark overdue
        PaymentSchedule::where('due_date', '<', today())
            ->whereNotIn('status',['paid'])
            ->update(['status' => 'overdue']);

        $schedules = $q->orderBy('due_date','asc')->paginate(30)->withQueryString();

        // Ageing buckets
        $ageing = DB::table('payment_schedules')
            ->selectRaw("
                SUM(CASE WHEN due_date >= CURDATE() THEN amount - paid_amount ELSE 0 END) as current_due,
                SUM(CASE WHEN due_date < CURDATE() AND due_date >= DATE_SUB(CURDATE(),INTERVAL 30 DAY) THEN amount - paid_amount ELSE 0 END) as overdue_30,
                SUM(CASE WHEN due_date < DATE_SUB(CURDATE(),INTERVAL 30 DAY) AND due_date >= DATE_SUB(CURDATE(),INTERVAL 60 DAY) THEN amount - paid_amount ELSE 0 END) as overdue_60,
                SUM(CASE WHEN due_date < DATE_SUB(CURDATE(),INTERVAL 60 DAY) THEN amount - paid_amount ELSE 0 END) as overdue_90_plus
            ")->first();

        return view('finance.payment-schedule.index', compact('schedules','partyType','status','ageing'));
    }
}
