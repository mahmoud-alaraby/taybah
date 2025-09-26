<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrintBooking;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrintBookingController extends Controller
{
    public function index(Request $request)
    {
        $query = PrintBooking::with(['assignedPerson', 'creator', 'employeeCreator']);

        // تصفية حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // تصفية حسب التاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }

        // تصفية حسب الموظف المسؤول
        if ($request->filled('assigned_person_id')) {
            $query->where('assigned_person_id', $request->assigned_person_id);
        }

        // تصفية حسب اسم العميل
        if ($request->filled('client_name')) {
            $query->where('client_name', 'like', '%' . $request->client_name . '%');
        }

        $bookings = $query->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc')
            ->paginate(10);

        // جلب الموظفين النشطين للفلتر
        $employees = Employee::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.print-booking.index', compact('bookings', 'employees'));
    }

    public function create()
    {
        // جلب الموظفين النشطين
        $employees = Employee::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.print-booking.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'work_description' => 'required|string',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:20',
            'agreement' => 'nullable|string',
            'first_order' => 'nullable|date',
            'last_order' => 'nullable|date|after_or_equal:first_order',
            'orders_count' => 'nullable|integer|min:1',
            'initial_delivery' => 'nullable|date',
            'final_delivery' => 'nullable|date|after_or_equal:initial_delivery',
            'booking_date' => 'required|date',
            'booking_time' => 'nullable',
            'duration_hours' => 'nullable|integer|min:1|max:24',
            'location' => 'nullable|string|max:500',
            'status' => 'required|in:in_progress,completed,bad_debt',
            'assigned_person_id' => 'nullable|exists:employees,id',
            'work_notes' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        PrintBooking::create([
            'work_description' => $request->work_description,
            'work_notes' => $request->work_notes,
            'client_name' => $request->client_name,
            'client_phone' => $request->client_phone,
            'agreement' => $request->agreement,
            'first_order' => $request->first_order,
            'last_order' => $request->last_order,
            'orders_count' => $request->orders_count ?? 1,
            'initial_delivery' => $request->initial_delivery,
            'final_delivery' => $request->final_delivery,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'duration_hours' => $request->duration_hours ?? 1,
            'location' => $request->location,
            'status' => $request->status,
            'notes' => $request->notes,
            'assigned_person_id' => $request->assigned_person_id,
            'created_by' => Auth::guard('admin')->id()
        ]);

        return redirect()->route('admin.print-booking.index')
            ->with('success', 'تم إنشاء حجز الطباعة بنجاح');
    }

    public function show(PrintBooking $printBooking)
    {
        $printBooking->load(['assignedPerson', 'creator', 'employeeCreator']);
        return view('admin.print-booking.show', compact('printBooking'));
    }

    public function edit(PrintBooking $printBooking)
    {
        // جلب الموظفين النشطين
        $employees = Employee::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.print-booking.edit', compact('printBooking', 'employees'));
    }

    public function update(Request $request, PrintBooking $printBooking)
    {
        $request->validate([
            'work_description' => 'required|string',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:20',
            'agreement' => 'nullable|string',
            'first_order' => 'nullable|date',
            'last_order' => 'nullable|date',
            'orders_count' => 'nullable|integer|min:1',
            'initial_delivery' => 'nullable|date',
            'final_delivery' => 'nullable|date',
            'booking_date' => 'nullable|date',
            'booking_time' => 'nullable',
            'duration_hours' => 'nullable|integer|min:1|max:24',
            'location' => 'nullable|string|max:500',
            'status' => 'required|in:in_progress,completed,bad_debt',
            'assigned_person_id' => 'nullable|exists:employees,id',
            'work_notes' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $updateData = [
            'work_description' => $request->work_description,
            'work_notes' => $request->work_notes,
            'client_name' => $request->client_name,
            'client_phone' => $request->client_phone,
            'agreement' => $request->agreement,
            'first_order' => $request->first_order,
            'last_order' => $request->last_order,
            'orders_count' => $request->orders_count ?? 1,
            'initial_delivery' => $request->initial_delivery,
            'final_delivery' => $request->final_delivery,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'duration_hours' => $request->duration_hours ?? 1,
            'location' => $request->location,
            'status' => $request->status,
            'notes' => $request->notes,
            'assigned_person_id' => $request->assigned_person_id
        ];

        $printBooking->update($updateData);

        return redirect()->route('admin.print-booking.index')
            ->with('success', 'تم تحديث حجز الطباعة بنجاح');
    }

    public function destroy(PrintBooking $printBooking)
    {
        $printBooking->delete();

        return redirect()->route('admin.print-booking.index')
            ->with('success', 'تم حذف حجز الطباعة بنجاح');
    }

    // إحصائيات سريعة للداشبورد
    public function getDashboardStats()
    {
        $stats = [
            'total_bookings' => PrintBooking::count(),
            'in_progress_bookings' => PrintBooking::where('status', 'in_progress')->count(),
            'completed_bookings' => PrintBooking::where('status', 'completed')->count(),
            'bad_debt_bookings' => PrintBooking::where('status', 'bad_debt')->count(),
            'today_bookings' => PrintBooking::whereDate('created_at', today())->count(),
            'this_month_bookings' => PrintBooking::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
        ];

        return response()->json($stats);
    }

    // طباعة التقارير
    public function printReport(Request $request)
    {
        $query = PrintBooking::with(['assignedPerson', 'creator', 'employeeCreator']);

        // تطبيق نفس فلاتر الصفحة الرئيسية
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }

        if ($request->filled('assigned_person_id')) {
            $query->where('assigned_person_id', $request->assigned_person_id);
        }

        if ($request->filled('client_name')) {
            $query->where('client_name', 'like', '%' . $request->client_name . '%');
        }

        $bookings = $query->orderBy('booking_date', 'desc')->get();

        // إحصائيات للتقرير
        $stats = [
            'total' => $bookings->count(),
            'in_progress' => $bookings->where('status', 'in_progress')->count(),
            'completed' => $bookings->where('status', 'completed')->count(),
            'bad_debt' => $bookings->where('status', 'bad_debt')->count(),
        ];

        return view('admin.print-booking.print', compact('bookings', 'stats'));
    }
}