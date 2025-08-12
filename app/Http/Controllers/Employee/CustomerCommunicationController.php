<?php
// app/Http/Controllers/Employee/CustomerCommunicationController.php
namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class CustomerCommunicationController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'all');

        $subQuery = DB::table('potential_customers')
            ->whereRaw('JSON_CONTAINS(customer_classifications, \'["requested_call"]\') OR JSON_CONTAINS(customer_classifications, \'["requested_visit"]\')')
            ->pluck('id');

        $query = DB::table('potential_customers as pc')
            ->leftJoin('customer_communications as cc', 'pc.id', '=', 'cc.potential_customer_id')
            ->whereIn('pc.id', $subQuery);

        if ($filter === 'calls_pending') {
            $query->whereRaw('JSON_CONTAINS(pc.customer_classifications, \'["requested_call"]\')');
        } elseif ($filter === 'visits_pending') {
            $query->whereRaw('JSON_CONTAINS(pc.customer_classifications, \'["requested_visit"]\')');
        } elseif ($filter === 'contacted') {
            $query->where('cc.employee_contacted_status', 'contacted');
        } elseif ($filter === 'not_contacted') {
            $query->where(function ($q) {
                $q->whereNull('cc.employee_contacted_status')->orWhere('cc.employee_contacted_status', 'not_contacted');
            });
        }

        $paginated = $query->select('pc.id', 'pc.customer_name', 'pc.phone', 'pc.work_description',
            DB::raw('MAX(cc.created_at) as last_message_time'), 'cc.employee_contacted_status')
            ->groupBy('pc.id', 'pc.customer_name', 'pc.phone', 'pc.work_description', 'cc.employee_contacted_status')
            ->orderBy('last_message_time', 'desc')
            ->paginate(10);

        // معلومات احصائية مشابهة لادمن
        $total = DB::table('potential_customers')
            ->whereRaw('JSON_CONTAINS(customer_classifications, \'["requested_call"]\') OR JSON_CONTAINS(customer_classifications, \'["requested_visit"]\')')
            ->count();

        $calls_pending = DB::table('potential_customers')
            ->whereRaw('JSON_CONTAINS(customer_classifications, \'["requested_call"]\')')
            ->count();

        $visits_pending = DB::table('potential_customers')
            ->whereRaw('JSON_CONTAINS(customer_classifications, \'["requested_visit"]\')')
            ->count();

        $stats = [
            'total' => $total,
            'calls_pending' => $calls_pending,
            'visits_pending' => $visits_pending,
        ];

        return view('employee.customer_communication.index', compact('paginated', 'filter', 'stats'));
    }

    public function show($potential_customer_id)
    {
        $customer = DB::table('potential_customers')->find($potential_customer_id);

        $communications = DB::table('customer_communications')
            ->where('potential_customer_id', $potential_customer_id)
            ->orderBy('created_at', 'asc')
            ->get();

        DB::table('customer_communications')
            ->where('potential_customer_id', $potential_customer_id)
            ->update(['is_read_by_employee' => 1]);

        return view('employee.customer_communication.show', compact('customer', 'communications'));
    }

    public function store(Request $request, $potential_customer_id)
    {
        DB::table('customer_communications')->insert([
            'potential_customer_id' => $potential_customer_id,
            'employee_id' => auth()->id(),
            'notes' => $request->input('notes'),
            'employee_contacted_status' => 'contacted',
            'is_read_by_admin' => 0,
            'is_read_by_employee' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('employee.customer-communication.show', $potential_customer_id)->with('success', 'تم إضافة الملاحظة بنجاح');
    }

    public function markContacted(Request $request, $potential_customer_id)
    {
        DB::table('customer_communications')
            ->where('potential_customer_id', $potential_customer_id)
            ->update(['employee_contacted_status' => 'contacted']);

        return back()->with('success', 'تم تعليم العميل كمتم التواصل معه');
    }
}
