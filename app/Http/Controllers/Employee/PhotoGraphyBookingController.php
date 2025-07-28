<?php
// app/Http/Controllers/Employee/PhotoGraphyBookingController.php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\PhotoGraphyBooking;
use App\Models\BookingNotification;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhotoGraphyBookingController extends Controller
{
    public function index(Request $request)
    {
        $employee = Auth::guard('employee')->user();

        // جلب جميع الحجوزات المخصصة للموظف (بدون تقييد النوع)
        $query = PhotoGraphyBooking::where('assigned_person_id', $employee->id);

        // تصفية حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // تصفية حسب اسم العميل
        if ($request->filled('client_name')) {
            $query->where('client_name', 'like', '%' . $request->client_name . '%');
        }

        // تصفية حسب التاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }

        $bookings = $query->orderBy('booking_date', 'desc')
                         ->orderBy('booking_time', 'desc')
                         ->paginate(15);

        return view('employee.photography-booking.index', compact('bookings'));
    }

    public function create()
    {
        return view('employee.photography-booking.create');
    }

    public function store(Request $request)
    {
        $employee = Auth::guard('employee')->user();

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
            'work_notes' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $booking = PhotoGraphyBooking::create([
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
            'assigned_person_id' => $employee->id
        ]);

        // إرسال إشعار للأدمن
        $this->createNotificationForAdmins($booking, 'created', $employee);

        return redirect()->route('employee.photography-booking.index')
            ->with('success', 'تم إنشاء الحجز بنجاح');
    }

    public function show(PhotoGraphyBooking $photographyBooking)
    {
        $employee = Auth::guard('employee')->user();
        
        // التحقق من أن الحجز مخصص لهذا الموظف
        if ($photographyBooking->assigned_person_id !== $employee->id) {
            abort(403);
        }

        return view('employee.photography-booking.show', compact('photographyBooking'));
    }

    public function edit(PhotoGraphyBooking $photographyBooking)
    {
        $employee = Auth::guard('employee')->user();
        
        // التحقق من أن الحجز مخصص لهذا الموظف
        if ($photographyBooking->assigned_person_id !== $employee->id) {
            abort(403);
        }

        return view('employee.photography-booking.edit', compact('photographyBooking'));
    }

public function update(Request $request, PhotoGraphyBooking $photographyBooking)
{
    $employee = Auth::guard('employee')->user();
    
    // التحقق من أن الحجز مخصص لهذا الموظف
    if ($photographyBooking->assigned_person_id !== $employee->id) {
        abort(403);
    }

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
        'notes' => $request->notes
    ];

    $photographyBooking->update($updateData);

    // إرسال إشعار للأدمن
    $this->createNotificationForAdmins($photographyBooking, 'updated', $employee);

    return redirect()->route('employee.photography-booking.index')
        ->with('success', 'تم تحديث الحجز بنجاح');
}


    public function destroy(PhotoGraphyBooking $photographyBooking)
    {
        $employee = Auth::guard('employee')->user();
        
        // التحقق من أن الحجز مخصص لهذا الموظف
        if ($photographyBooking->assigned_person_id !== $employee->id) {
            abort(403);
        }

        // إرسال إشعار للأدمن قبل الحذف
        $this->createNotificationForAdmins($photographyBooking, 'deleted', $employee);
        
        $photographyBooking->delete();

        return redirect()->route('employee.photography-booking.index')
            ->with('success', 'تم حذف الحجز بنجاح');
    }

    // دالة إنشاء الإشعار للأدمن
    private function createNotificationForAdmins($booking, $action, $employee)
    {
        $actionLabels = [
            'created' => 'أنشأ',
            'updated' => 'عدّل',
            'deleted' => 'حذف'
        ];

        $message = sprintf(
            '%s %s حجز: %s - العميل: %s',
            $employee->name,
            $actionLabels[$action],
            $booking->work_description,
            $booking->client_name
        );

        // إرسال إشعار لجميع الأدمن
        $admins = Admin::where('status', 'active')->get();
        
        foreach ($admins as $admin) {
            BookingNotification::create([
                'booking_id' => $booking->id,
                'admin_id' => $admin->id,
                'employee_id' => $employee->id,
                'action_type' => $action,
                'message' => $message
            ]);
        }
    }
}
