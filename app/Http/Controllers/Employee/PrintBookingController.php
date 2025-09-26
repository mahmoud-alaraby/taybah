<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\PrintBooking;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrintBookingController extends Controller
{
    public function index(Request $request)
    {
        $employee = Auth::guard('employee')->user();

        $query = PrintBooking::query();

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
            'assigned_by_admin' => PrintBooking::where('assigned_person_id', $employee->id)
                ->whereNotNull('created_by')->whereNull('created_by_employee')->count(),
            'created_by_me' => PrintBooking::where('created_by_employee', $employee->id)->count(),
            'not_assigned_to_me' => PrintBooking::where(function ($q) use ($employee) {
                $q->where(function ($subQ) use ($employee) {
                    $subQ->where('assigned_person_id', '!=', $employee->id)
                        ->orWhereNull('assigned_person_id');
                })->where(function ($subQ) use ($employee) {
                    $subQ->where('created_by_employee', '!=', $employee->id)
                        ->orWhereNull('created_by_employee');
                });
            })->count(),
        ];

        return view('employee.print-booking.index', compact('bookings', 'stats'));
    }

    public function create()
    {
        return view('employee.print-booking.create');
    }

    public function store(Request $request)
    {
        $employee = Auth::guard('employee')->user();

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
            'assigned_person_id' => $employee->id,
            'created_by' => null,
            'created_by_employee' => $employee->id
        ]);

        return redirect()->route('employee.print-booking.index')
            ->with('success', 'تم إنشاء حجز الطباعة بنجاح');
    }

    public function show(PrintBooking $printBooking)
    {
        $employee = Auth::guard('employee')->user();
        
        // التحقق من الصلاحية للعرض
        if (!$this->canAccessBooking($printBooking, $employee)) {
            return redirect()->route('employee.print-booking.index')
                ->with('error', 'ليس لديك صلاحية لعرض هذا الحجز');
        }
        
        return view('employee.print-booking.show', compact('printBooking'));
    }

    public function edit(PrintBooking $printBooking)
    {
        $employee = Auth::guard('employee')->user();

        if (!$this->canEditBooking($printBooking, $employee)) {
            return redirect()->route('employee.print-booking.index')
                ->with('error', 'ليس لديك صلاحية لتعديل هذا الحجز');
        }

        return view('employee.print-booking.edit', compact('printBooking'));
    }

    public function update(Request $request, PrintBooking $printBooking)
    {
        $employee = Auth::guard('employee')->user();

        if (!$this->canEditBooking($printBooking, $employee)) {
            return redirect()->route('employee.print-booking.index')
                ->with('error', 'ليس لديك صلاحية لتعديل هذا الحجز');
        }

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
            'work_notes' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $printBooking->update([
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
            'notes' => $request->notes
        ]);

        return redirect()->route('employee.print-booking.index')
            ->with('success', 'تم تحديث حجز الطباعة بنجاح');
    }

    public function destroy(PrintBooking $printBooking)
    {
        $employee = Auth::guard('employee')->user();

        if (!$this->canDeleteBooking($printBooking, $employee)) {
            return redirect()->route('employee.print-booking.index')
                ->with('error', 'ليس لديك صلاحية لحذف هذا الحجز');
        }

        $printBooking->delete();

        return redirect()->route('employee.print-booking.index')
            ->with('success', 'تم حذف حجز الطباعة بنجاح');
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
}