<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\PhotoGraphyBooking;
use App\Models\BookingNotification;
use App\Models\Admin;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhotoGraphyBookingController extends Controller
{

    public function index(Request $request)
    {
        $employee = Auth::guard('employee')->user();

        $query = PhotoGraphyBooking::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('client_name')) {
            $query->where('client_name', 'like', '%' . $request->client_name . '%');
        }
        if ($request->filled('date_from')) {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }

        // الفلتر المعتمد كليًا على الموظف الحالي
        if ($request->filled('booking_filter')) {
            switch ($request->booking_filter) {
                case 'assigned_by_admin':
                    $query->where('assigned_person_id', $employee->id)
                          ->whereNotNull('created_by')
                          ->whereNull('created_by_employee');
                    break;

                case 'created_by_me':
                    $query->where('created_by_employee', $employee->id);
                    break;

                case 'not_assigned_to_me':
                    $query->where(function ($q) use ($employee) {
                        $q->where(function ($subQ) use ($employee) {
                            $subQ->where('assigned_person_id', '!=', $employee->id)
                                ->orWhereNull('assigned_person_id');
                        })->where(function ($subQ) use ($employee) {
                            $subQ->where('created_by_employee', '!=', $employee->id)
                                ->orWhereNull('created_by_employee');
                        });
                    });
                    break;

                case 'all':
                default:
                    $query->where(function ($q) use ($employee) {
                        $q->where('assigned_person_id', $employee->id)
                          ->orWhere('created_by_employee', $employee->id)
                          ->orWhereNull('assigned_person_id');
                    });
                    break;
            }
        } else {
            $query->where(function ($q) use ($employee) {
                $q->where('assigned_person_id', $employee->id)
                  ->orWhere('created_by_employee', $employee->id)
                  ->orWhereNull('assigned_person_id');
            });
        }

        $bookings = $query->with(['assignedPerson', 'creator', 'employeeCreator'])
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc')
            ->paginate(15);

        // إحصائيات الفئات (تحسب بناءً على الموظف الحالي)
        $stats = [
            'assigned_by_admin' => PhotoGraphyBooking::where('assigned_person_id', $employee->id)
                ->whereNotNull('created_by')->whereNull('created_by_employee')->count(),
            'created_by_me' => PhotoGraphyBooking::where('created_by_employee', $employee->id)->count(),
            'not_assigned_to_me' => PhotoGraphyBooking::where(function ($q) use ($employee) {
                $q->where(function ($subQ) use ($employee) {
                    $subQ->where('assigned_person_id', '!=', $employee->id)
                        ->orWhereNull('assigned_person_id');
                })->where(function ($subQ) use ($employee) {
                    $subQ->where('created_by_employee', '!=', $employee->id)
                        ->orWhereNull('created_by_employee');
                });
            })->count(),
        ];

        return view('employee.photography-booking.index', compact('bookings', 'stats'));
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
            'assigned_person_id' => $employee->id,
            'created_by' => null, // هذا هو التعديل المهم
            'created_by_employee' => $employee->id
        ]);

        $this->createNotificationForAdmins($booking, 'created', $employee);

        return redirect()->route('employee.photography-booking.index')
            ->with('success', 'تم إنشاء الحجز بنجاح');
    }

    public function show(PhotoGraphyBooking $photographyBooking)
    {
        $employee = Auth::guard('employee')->user();
        
        // التحقق من الصلاحية للعرض
        if (!$this->canAccessBooking($photographyBooking, $employee)) {
            return redirect()->route('employee.photography-booking.index')
                ->with('error', 'ليس لديك صلاحية لعرض هذا الحجز');
        }
        
        return view('employee.photography-booking.show', compact('photographyBooking'));
    }

    public function edit(PhotoGraphyBooking $photographyBooking)
    {
        $employee = Auth::guard('employee')->user();

        if (!$this->canEditBooking($photographyBooking, $employee)) {
            return redirect()->route('employee.photography-booking.index')
                ->with('error', 'ليس لديك صلاحية لتعديل هذا الحجز');
        }

        return view('employee.photography-booking.edit', compact('photographyBooking'));
    }

    public function update(Request $request, PhotoGraphyBooking $photographyBooking)
    {
        $employee = Auth::guard('employee')->user();

        if (!$this->canEditBooking($photographyBooking, $employee)) {
            return redirect()->route('employee.photography-booking.index')
                ->with('error', 'ليس لديك صلاحية لتعديل هذا الحجز');
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

        $photographyBooking->update([
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
        ]);

        $this->createNotificationForAdmins($photographyBooking, 'updated', $employee);

        return redirect()->route('employee.photography-booking.index')
            ->with('success', 'تم تحديث الحجز بنجاح');
    }

    public function destroy(PhotoGraphyBooking $photographyBooking)
    {
        $employee = Auth::guard('employee')->user();

        if (!$this->canDeleteBooking($photographyBooking, $employee)) {
            return redirect()->route('employee.photography-booking.index')
                ->with('error', 'ليس لديك صلاحية لحذف هذا الحجز');
        }

        $this->createNotificationForAdmins($photographyBooking, 'deleted', $employee);
        $photographyBooking->delete();

        return redirect()->route('employee.photography-booking.index')
            ->with('success', 'تم حذف الحجز بنجاح');
    }

    // Helper methods للتحقق من الصلاحيات
    private function canAccessBooking($booking, $employee)
    {
        return $booking->assigned_person_id == $employee->id || 
               $booking->created_by_employee == $employee->id ||
               $booking->assigned_person_id === null;
    }

    private function canEditBooking($booking, $employee)
    {
        return $booking->created_by_employee == $employee->id || 
               $booking->assigned_person_id == $employee->id;
    }

    private function canDeleteBooking($booking, $employee)
    {
        return $booking->created_by_employee == $employee->id;
    }

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
