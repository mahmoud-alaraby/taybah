<?php
// app/Http/Controllers/Admin/PhotoGraphyBookingController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhotoGraphyBooking;
use App\Models\Employee;
use App\Models\BookingNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhotoGraphyBookingController extends Controller
{
    public function index(Request $request)
    {
        $query = PhotoGraphyBooking::with(['assignedPerson', 'creator']);

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

        $bookings = $query->with(['assignedPerson', 'creator', 'employeeCreator'])
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc')
            ->paginate(15);

        // تعديل: جلب الموظفين النشطين الذين لديهم صلاحية photography_booking فقط
        $employees = Employee::whereHas('roles.permissions', function($q){
                $q->where('name', 'photography_booking');
            })
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.photography-booking.index', compact('bookings', 'employees'));
    }

    public function create()
    {
        // تعديل: جلب الموظفين مع صلاحية photography_booking فقط
        $employees = Employee::whereHas('roles.permissions', function($q){
                $q->where('name', 'photography_booking');
            })
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.photography-booking.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'work_description' => 'required|string',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:20',
            'agreement' => 'nullable|string',
            'first_session' => 'nullable|date',
            'last_session' => 'nullable|date|after_or_equal:first_session',
            'sessions_count' => 'nullable|integer|min:1',
            'montage_start' => 'nullable|date',
            'initial_delivery' => 'nullable|date',
            'final_delivery' => 'nullable|date|after_or_equal:initial_delivery',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'nullable|date_format:H:i',
            'duration_hours' => 'nullable|integer|min:1|max:24',
            'location' => 'nullable|string|max:500',
            'status' => 'required|in:in_progress,completed,bad_debt',
            'assigned_person_id' => 'nullable|exists:employees,id',
            'work_notes' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        PhotoGraphyBooking::create([
            'work_description' => $request->work_description,
            'work_notes' => $request->work_notes,
            'client_name' => $request->client_name,
            'client_phone' => $request->client_phone,
            'agreement' => $request->agreement,
            'first_session' => $request->first_session,
            'last_session' => $request->last_session,
            'sessions_count' => $request->sessions_count ?? 1,
            'montage_start' => $request->montage_start,
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

        return redirect()->route('admin.photography-booking.index')
            ->with('success', 'تم إنشاء الحجز بنجاح');
    }

    public function show(PhotoGraphyBooking $photographyBooking)
    {
        $photographyBooking->load(['assignedPerson', 'creator', 'employeeCreator']);
        return view('admin.photography-booking.show', compact('photographyBooking'));
    }

    public function edit(PhotoGraphyBooking $photographyBooking)
    {
        // تعديل: جلب الموظفين بالاعتماد على صلاحية photography_booking
        $employees = Employee::whereHas('roles.permissions', function($q){
                $q->where('name', 'photography_booking');
            })
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.photography-booking.edit', compact('photographyBooking', 'employees'));
    }

    public function update(Request $request, PhotoGraphyBooking $photographyBooking)
    {
        $request->validate([
            'work_description' => 'required|string',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:20',
            'agreement' => 'nullable|string',
            'first_session' => 'nullable|date',
            'last_session' => 'nullable|date',
            'sessions_count' => 'nullable|integer|min:1',
            'montage_start' => 'nullable|date',
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
            'first_session' => $request->first_session,
            'last_session' => $request->last_session,
            'sessions_count' => $request->sessions_count ?? 1,
            'montage_start' => $request->montage_start,
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

        $photographyBooking->update($updateData);

        return redirect()->route('admin.photography-booking.index')
            ->with('success', 'تم تحديث الحجز بنجاح');
    }

    public function destroy(PhotoGraphyBooking $photographyBooking)
    {
        $photographyBooking->delete();

        return redirect()->route('admin.photography-booking.index')
            ->with('success', 'تم حذف الحجز بنجاح');
    }

    // عرض الإشعارات
    public function notifications()
    {
        $notifications = BookingNotification::with(['booking', 'employee'])
            ->forAdmin(Auth::guard('admin')->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.photography-booking.notifications', compact('notifications'));
    }

    // تحديد الإشعار كمقروء
    public function markNotificationRead($notificationId)
    {
        $notification = BookingNotification::where('admin_id', Auth::guard('admin')->id())
            ->findOrFail($notificationId);

        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    // إحصائيات سريعة للداشبورد
    public function getDashboardStats()
    {
        $stats = [
            'total_bookings' => PhotoGraphyBooking::count(),
            'in_progress_bookings' => PhotoGraphyBooking::where('status', 'in_progress')->count(),
            'completed_bookings' => PhotoGraphyBooking::where('status', 'completed')->count(),
            'bad_debt_bookings' => PhotoGraphyBooking::where('status', 'bad_debt')->count(),
        ];

        return response()->json($stats);
    }
}
