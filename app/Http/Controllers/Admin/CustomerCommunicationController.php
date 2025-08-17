<?php
// app/Http/Controllers/Admin/CustomerCommunicationController.php
namespace App\Http\Controllers\Admin;

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

        $query = DB::table('customer_communications as cc')
            ->join('potential_customers as pc', 'cc.potential_customer_id', '=', 'pc.id')
            ->leftJoin('employees as e', 'cc.employee_id', '=', 'e.id')
            ->select('pc.id as potential_customer_id', 'pc.customer_name', 'pc.phone',
                DB::raw('COUNT(cc.id) as total_messages'),
                DB::raw('SUM(cc.is_read_by_admin = 0) as unread_messages'),
                DB::raw('MAX(cc.created_at) as last_message_time'),
                DB::raw('MAX(cc.is_read_by_admin) as all_read'),
                'e.name as employee_name')
            ->whereIn('pc.id', $subQuery)
            ->groupBy('pc.id', 'pc.customer_name', 'pc.phone', 'e.name')
            ->orderBy('last_message_time', 'desc');

        if ($filter === 'calls_pending') {
            $query->whereRaw('JSON_CONTAINS(pc.customer_classifications, \'["requested_call"]\')');
        } elseif ($filter === 'visits_pending') {
            $query->whereRaw('JSON_CONTAINS(pc.customer_classifications, \'["requested_visit"]\')');
        } elseif ($filter === 'read') {
            $query->havingRaw('SUM(cc.is_read_by_admin = 0) = 0');
        } elseif ($filter === 'unread') {
            $query->havingRaw('SUM(cc.is_read_by_admin = 0) > 0');
        }

        $paginated = $query->paginate(10);

        $stats = [
            'total' => DB::table('potential_customers')
                ->whereRaw('JSON_CONTAINS(customer_classifications, \'["requested_call"]\') OR JSON_CONTAINS(customer_classifications, \'["requested_visit"]\')')
                ->count(),
            'calls_pending' => DB::table('potential_customers')
                ->whereRaw('JSON_CONTAINS(customer_classifications, \'["requested_call"]\')')
                ->count(),
            'visits_pending' => DB::table('potential_customers')
                ->whereRaw('JSON_CONTAINS(customer_classifications, \'["requested_visit"]\')')
                ->count(),
        ];

        return view('admin.customer_communication.index', [
            'communications' => $paginated,
            'filter' => $filter,
            'stats' => $stats
        ]);
    }

    public function show($potential_customer_id)
    {
        $customer = DB::table('potential_customers')->find($potential_customer_id);

        $communications = DB::table('customer_communications')
            ->where('potential_customer_id', $potential_customer_id)
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read by admin when show is loaded
        DB::table('customer_communications')
            ->where('potential_customer_id', $potential_customer_id)
            ->update(['is_read_by_admin' => 1]);

        return view('admin.customer_communication.show', compact('customer', 'communications'));
    }

    public function reply(Request $request, $potential_customer_id)
    {
        DB::table('customer_communications')->insert([
            'potential_customer_id' => $potential_customer_id,
            'employee_id' => null,
            'notes' => $request->input('admin_reply'),
            'employee_contacted_status' => 'contacted',
            'is_read_by_admin' => 1,
            'is_read_by_employee' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'تم إرسال الرد بنجاح');
    }

    public function markRead(Request $request, $potential_customer_id)
    {
        DB::table('customer_communications')
            ->where('potential_customer_id', $potential_customer_id)
            ->update(['is_read_by_admin' => 1]);

        return back()->with('success', 'تم تعليم العميل كمقروء');
    }
}