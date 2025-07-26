<?php
// app/Http/Controllers/Admin/CustomerMovementController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerMovement;
use App\Models\CustomerMovementTarget;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

        // الحصول على حركة العملاء للشهر المحدد
        $query = CustomerMovement::with('employee')
                              ->forPeriod($currentYear, $currentMonth)
                              ->orderBy('agreement_start_date', 'desc');

        // فلترة بالموظف
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        // فلترة بنوع العميل
        if ($customerType) {
            $query->where('customer_type', $customerType);
        }

        // فلترة بحالة العمل
        if ($workStatus) {
            $query->where('work_status', $workStatus);
        }

        // البحث
        if ($search) {
            $query->search($search);
        }

        // حساب الإجماليات
        $totalAgreed = CustomerMovement::getTotalAgreedForMonth($currentYear, $currentMonth, $employeeId);
        $totalPaid = CustomerMovement::getTotalPaidForMonth($currentYear, $currentMonth, $employeeId);
        $totalDebts = CustomerMovement::getTotalDebtsForMonth($currentYear, $currentMonth, $employeeId);

        // الحصول على التارجت
        $targetAmount = 0;
        $achievementPercentage = 0;
        
        if ($employeeId) {
            $target = CustomerMovementTarget::where('employee_id', $employeeId)
                                          ->where('year', $currentYear)
                                          ->where('month', $currentMonth)
                                          ->first();
            $targetAmount = $target ? $target->target_amount : 0;
        } else {
            // إجمالي كل التارجتات للشهر
            $targetAmount = CustomerMovementTarget::where('year', $currentYear)
                                                 ->where('month', $currentMonth)
                                                 ->sum('target_amount');
        }
        
        $achievementPercentage = $targetAmount > 0 ? round(($totalAgreed / $targetAmount) * 100, 2) : 0;

        $movements = $query->paginate(15);

        // قائمة الموظفين والخيارات للفلترة
        $employees = Employee::active()->orderBy('name')->get();
        $customerTypes = CustomerMovement::getCustomerTypes();
        $workStatuses = CustomerMovement::getWorkStatuses();

        // قائمة الشهور للفلترة
        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];

        return view('admin.customer-movement.index', compact(
            'movements',
            'totalAgreed',
            'totalPaid',
            'totalDebts',
            'targetAmount',
            'achievementPercentage',
            'currentYear',
            'currentMonth',
            'months',
            'search',
            'employees',
            'employeeId',
            'customerTypes',
            'workStatuses',
            'customerType',
            'workStatus'
        ));
    }

    public function create()
    {
        $employees = Employee::active()->orderBy('name')->get();
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
        $employees = Employee::active()->orderBy('name')->get();
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

    public function destroy(CustomerMovement $customerMovement)
    {
        $customerMovement->delete();

        return redirect()->route('admin.customer-movement.index')
                       ->with('success', 'تم حذف حركة العميل بنجاح');
    }

    public function updateTarget(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'target_amount' => 'required|numeric|min:0',
            'year' => 'required|integer|min:2020|max:2050',
            'month' => 'required|integer|min:1|max:12',
        ], [
            'employee_id.required' => 'الموظف مطلوب',
            'target_amount.required' => 'مبلغ التارجت مطلوب',
            'target_amount.min' => 'التارجت لا يمكن أن يكون سالباً',
            'year.required' => 'السنة مطلوبة',
            'month.required' => 'الشهر مطلوب',
        ]);

        CustomerMovementTarget::updateOrCreateTarget(
            $request->year,
            $request->month,
            $request->target_amount,
            $request->employee_id
        );

        return redirect()->route('admin.customer-movement.index', [
            'year' => $request->year,
            'month' => $request->month,
            'employee_id' => $request->employee_id
        ])->with('success', 'تم تحديث التارجت بنجاح');
    }

    public function printReport(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));
        $employeeId = $request->get('employee_id');

        $query = CustomerMovement::with('employee')
                               ->forPeriod($year, $month)
                               ->orderBy('agreement_start_date', 'asc');

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $movements = $query->get();

        $totalAgreed = CustomerMovement::getTotalAgreedForMonth($year, $month, $employeeId);
        $totalPaid = CustomerMovement::getTotalPaidForMonth($year, $month, $employeeId);
        $totalDebts = CustomerMovement::getTotalDebtsForMonth($year, $month, $employeeId);

        $targetAmount = 0;
        $achievementPercentage = 0;
        $selectedEmployee = null;

        if ($employeeId) {
            $selectedEmployee = Employee::find($employeeId);
            $target = CustomerMovementTarget::where('employee_id', $employeeId)
                                          ->where('year', $year)
                                          ->where('month', $month)
                                          ->first();
            $targetAmount = $target ? $target->target_amount : 0;
        } else {
            $targetAmount = CustomerMovementTarget::where('year', $year)
                                                 ->where('month', $month)
                                                 ->sum('target_amount');
        }

        $achievementPercentage = $targetAmount > 0 ? round(($totalAgreed / $targetAmount) * 100, 2) : 0;

        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];

        return view('admin.customer-movement.print', compact(
            'movements',
            'totalAgreed',
            'totalPaid',
            'totalDebts',
            'targetAmount',
            'achievementPercentage',
            'year',
            'month',
            'months',
            'selectedEmployee'
        ));
    }
}