<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerMovement;
use App\Models\CustomerMovementTarget;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerMovementController extends Controller
{
  public function index(Request $request)
{
    $currentYear = $request->get('year', date('Y'));
    $currentMonth = $request->get('month', date('n'));
    $search = $request->get('search');
    $employeeId = $request->get('employee_id');
    $customerType = $request->get('customer_type');
    $workStatus = $request->get('work_status');

    $query = CustomerMovement::with('employee')
        ->forPeriod($currentYear, $currentMonth)
        ->orderBy('agreement_start_date', 'desc');

    if ($employeeId) {
        $query->where('employee_id', $employeeId);
    }
    if ($customerType) {
        $query->where('customer_type', $customerType);
    }
    if ($workStatus) {
        $query->where('work_status', $workStatus);
    }
    if ($search) {
        $query->search($search);
    }

    $totalAgreed = CustomerMovement::getTotalAgreedForMonth($currentYear, $currentMonth, $employeeId);
    $totalPaid   = CustomerMovement::getTotalPaidForMonth($currentYear, $currentMonth, $employeeId);
    $totalDebts  = CustomerMovement::getTotalDebtsForMonth($currentYear, $currentMonth, $employeeId);

    $targetAmount = 0;
    if ($employeeId) {
        $target = CustomerMovementTarget::where('employee_id', $employeeId)
            ->where('year', $currentYear)
            ->where('month', $currentMonth)
            ->first();
        $targetAmount = $target ? $target->target_amount : 0;
    } else {
        $targetAmount = CustomerMovementTarget::where('year', $currentYear)
            ->where('month', $currentMonth)
            ->sum('target_amount');
    }

    $achievementPercentage = $targetAmount > 0 ? round(($totalAgreed / $targetAmount) * 100, 2) : 0;

    $movements = $query->paginate(15);

    $employees = Employee::whereHas('roles.permissions', function ($q) {
        $q->where('name', 'customer_movement');
    })
        ->active()
        ->orderBy('name')
        ->get();

    $customerTypes = CustomerMovement::getCustomerTypes();
    $workStatuses  = CustomerMovement::getWorkStatuses();

    $months = [
        1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
        5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
        9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
    ];

    return view('admin.customer-movement.index', compact(
        'movements', 'totalAgreed', 'totalPaid', 'totalDebts',
        'targetAmount', 'achievementPercentage',
        'currentYear', 'currentMonth', 'months',
        'search', 'employees', 'employeeId', 'customerTypes', 'workStatuses',
        'customerType', 'workStatus'
    ));
}


    public function create()
    {
        $employees = Employee::whereHas('roles.permissions', function($q){
                $q->where('name', 'customer_movement');
            })
            ->active()
            ->orderBy('name')
            ->get();

        $customerTypes = CustomerMovement::getCustomerTypes();
        $workStatuses = CustomerMovement::getWorkStatuses();

        return view('admin.customer-movement.addedit', compact(
            'employees',
            'customerTypes',
            'workStatuses'
        ));
    }

    public function store(Request $request)
    {
        Log::debug('CustomerMovementController@store called with data: ', $request->all());

        $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'work_description' => 'required|string|max:1000',
            'agreement_start_date' => 'required|date',
            'initial_delivery_date' => 'required|date|after_or_equal:agreement_start_date',
            'final_delivery_date' => 'required|date|after_or_equal:initial_delivery_date',
            'agreed_amount' => 'required|numeric|min:0',
            'first_payment' => 'nullable|numeric|min:0',
            'second_payment' => 'nullable|numeric|min:0',
            'third_payment' => 'nullable|numeric|min:0',
            'fourth_payment' => 'nullable|numeric|min:0',
            'customer_type' => 'required|in:' . implode(',', array_keys(CustomerMovement::getCustomerTypes())),
            'work_status' => 'required|in:' . implode(',', array_keys(CustomerMovement::getWorkStatuses())),
        ], [
            'employee_id.exists' => 'الموظف المختار غير موجود',
            'customer_name.required' => 'اسم العميل مطلوب',
            'customer_phone.required' => 'رقم جوال العميل مطلوب',
            'work_description.required' => 'وصف العمل مطلوب',
            'agreement_start_date.required' => 'بداية الاتفاق مطلوبة',
            'initial_delivery_date.required' => 'موعد التسليم الأولي مطلوب',
            'final_delivery_date.required' => 'موعد التسليم النهائي مطلوب',
            'agreed_amount.required' => 'المبلغ المتفق عليه مطلوب',
            'customer_type.required' => 'نوعية العميل مطلوبة',
            'work_status.required' => 'حالة العمل مطلوبة',
        ]);

        CustomerMovement::create([
            'employee_id' => $request->employee_id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'work_description' => $request->work_description,
            'agreement_start_date' => $request->agreement_start_date,
            'initial_delivery_date' => $request->initial_delivery_date,
            'final_delivery_date' => $request->final_delivery_date,
            'agreed_amount' => $request->agreed_amount,
            'first_payment' => $request->first_payment ?? 0,
            'second_payment' => $request->second_payment ?? 0,
            'third_payment' => $request->third_payment ?? 0,
            'fourth_payment' => $request->fourth_payment ?? 0,
            'customer_type' => $request->customer_type,
            'work_status' => $request->work_status,
        ]);

        return redirect()->route('admin.customer-movement.index')
               ->with('success', 'تم إضافة حركة العميل بنجاح');
    }

    public function edit(CustomerMovement $customerMovement)
    {
        $employees = Employee::whereHas('roles.permissions', function($q){
                $q->where('name', 'customer_movement');
            })
            ->active()
            ->orderBy('name')
            ->get();

        $customerTypes = CustomerMovement::getCustomerTypes();
        $workStatuses = CustomerMovement::getWorkStatuses();

        return view('admin.customer-movement.addedit', compact(
            'customerMovement',
            'employees',
            'customerTypes',
            'workStatuses'
        ));
    }

    public function update(Request $request, CustomerMovement $customerMovement)
    {
        Log::debug('CustomerMovementController@update called with data: ', $request->all());

        $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'work_description' => 'required|string|max:1000',
            'agreement_start_date' => 'required|date',
            'initial_delivery_date' => 'required|date|after_or_equal:agreement_start_date',
            'final_delivery_date' => 'required|date|after_or_equal:initial_delivery_date',
            'agreed_amount' => 'required|numeric|min:0',
            'first_payment' => 'nullable|numeric|min:0',
            'second_payment' => 'nullable|numeric|min:0',
            'third_payment' => 'nullable|numeric|min:0',
            'fourth_payment' => 'nullable|numeric|min:0',
            'customer_type' => 'required|in:' . implode(',', array_keys(CustomerMovement::getCustomerTypes())),
            'work_status' => 'required|in:' . implode(',', array_keys(CustomerMovement::getWorkStatuses())),
        ], [
            'employee_id.exists' => 'الموظف المختار غير موجود',
            'customer_name.required' => 'اسم العميل مطلوب',
            'customer_phone.required' => 'رقم جوال العميل مطلوب',
            'work_description.required' => 'وصف العمل مطلوب',
            'agreement_start_date.required' => 'بداية الاتفاق مطلوبة',
            'initial_delivery_date.required' => 'موعد التسليم الأولي مطلوب',
            'final_delivery_date.required' => 'موعد التسليم النهائي مطلوب',
            'agreed_amount.required' => 'المبلغ المتفق عليه مطلوب',
            'customer_type.required' => 'نوعية العميل مطلوبة',
            'work_status.required' => 'حالة العمل مطلوبة',
        ]);

        $customerMovement->update([
            'employee_id' => $request->employee_id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'work_description' => $request->work_description,
            'agreement_start_date' => $request->agreement_start_date,
            'initial_delivery_date' => $request->initial_delivery_date,
            'final_delivery_date' => $request->final_delivery_date,
            'agreed_amount' => $request->agreed_amount,
            'first_payment' => $request->first_payment ?? 0,
            'second_payment' => $request->second_payment ?? 0,
            'third_payment' => $request->third_payment ?? 0,
            'fourth_payment' => $request->fourth_payment ?? 0,
            'customer_type' => $request->customer_type,
            'work_status' => $request->work_status,
        ]);

        return redirect()->route('admin.customer-movement.index')
               ->with('success', 'تم تحديث حركة العميل بنجاح');
    }

public function updateTarget(Request $request)
{
    $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'year' => 'required|integer|min:2000|max:2100',
        'month' => 'required|integer|min:1|max:12',
        'target_amount' => 'required|numeric|min:0',
    ]);

    // استدعاء موديل CustomerMovementTarget لتحديث أو إنشاء التارجت
    \App\Models\CustomerMovementTarget::updateOrCreateTarget(
        $request->year,
        $request->month,
        $request->target_amount,
        $request->employee_id
    );

    return redirect()->back()->with('success', 'تم تحديث التارجت بنجاح');
}

public function printReport(Request $request)
{
    $year = $request->get('year', date('Y'));
    $month = $request->get('month', date('n'));
    $employeeId = $request->get('employee_id');
    $customerType = $request->get('customer_type');
    $workStatus = $request->get('work_status');

    $query = CustomerMovement::with('employee')
        ->forPeriod($year, $month)
        ->orderBy('agreement_start_date', 'desc');

    if ($employeeId) {
        $query->where('employee_id', $employeeId);
    }
    if ($customerType) {
        $query->where('customer_type', $customerType);
    }
    if ($workStatus) {
        $query->where('work_status', $workStatus);
    }

    $movements = $query->get();

    $months = [
        1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
        5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
        9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
    ];

    // حسابات المبالغ حسب الموديل
    $totalAgreed = CustomerMovement::getTotalAgreedForMonth($year, $month, $employeeId);
    $totalPaid = CustomerMovement::getTotalPaidForMonth($year, $month, $employeeId);
    $totalDebts = CustomerMovement::getTotalDebtsForMonth($year, $month, $employeeId);

    // حساب التارجت
    $targetAmount = 0;
    if ($employeeId) {
        $target = CustomerMovementTarget::where('employee_id', $employeeId)
            ->where('year', $year)
            ->where('month', $month)
            ->first();
        $targetAmount = $target ? $target->target_amount : 0;
        $selectedEmployee = Employee::find($employeeId);
    } else {
        $targetAmount = CustomerMovementTarget::where('year', $year)
            ->where('month', $month)
            ->sum('target_amount');
        $selectedEmployee = null;
    }

    $achievementPercentage = $targetAmount > 0 ? round(($totalAgreed / $targetAmount) * 100, 2) : 0;

    return view('admin.customer-movement.print-report', compact(
        'movements', 'year', 'month', 'months', 'selectedEmployee',
        'totalAgreed', 'totalPaid', 'totalDebts', 'targetAmount', 'achievementPercentage',
        'customerType', 'workStatus'
    ));
}


    public function destroy(CustomerMovement $customerMovement)
    {
        $customerMovement->delete();

        return redirect()->route('admin.customer-movement.index')
               ->with('success', 'تم حذف حركة العميل بنجاح');
    }

    // لديك تعريفات أخرى حسب حاجتك ...
}
