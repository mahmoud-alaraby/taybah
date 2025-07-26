<?php
// app/Http/Controllers/Admin/ReceiptsPaymentsController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use App\Models\Payment;
use App\Models\MonthlyTarget;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceiptsPaymentsController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = $request->get('year', date('Y'));
        $currentMonth = $request->get('month', date('n'));
        $search = $request->get('search');
        $employeeId = $request->get('employee_id');

        // الحصول على المقبوضات والمدفوعات للشهر المحدد
        $receiptsQuery = Receipt::with('employee')
                              ->forPeriod($currentYear, $currentMonth)
                              ->orderBy('date', 'desc');

        $paymentsQuery = Payment::with('employee')
                              ->forPeriod($currentYear, $currentMonth)
                              ->orderBy('date', 'desc');

        // فلترة بالموظف
        if ($employeeId) {
            $receiptsQuery->where('employee_id', $employeeId);
            $paymentsQuery->where('employee_id', $employeeId);
        }

        // البحث
        if ($search) {
            $receiptsQuery->search($search);
            $paymentsQuery->search($search);
        }

        // حساب الإجماليات أولاً قبل pagination
        $totalReceipts = Receipt::forPeriod($currentYear, $currentMonth)
                              ->when($employeeId, function($q) use ($employeeId) {
                                  $q->where('employee_id', $employeeId);
                              })
                              ->when($search, function($q) use ($search) {
                                  $q->search($search);
                              })
                              ->sum('amount');

        $totalPayments = Payment::forPeriod($currentYear, $currentMonth)
                              ->when($employeeId, function($q) use ($employeeId) {
                                  $q->where('employee_id', $employeeId);
                              })
                              ->when($search, function($q) use ($search) {
                                  $q->search($search);
                              })
                              ->sum('amount');

        // Pagination للعرض
        $receipts = $receiptsQuery->paginate(10, ['*'], 'receipts_page');
        $payments = $paymentsQuery->paginate(10, ['*'], 'payments_page');

        $netAmount = $totalReceipts - $totalPayments;

        // الحصول على التارجت
        $targetAmount = 0;
        $achievementPercentage = 0;
        
        if ($employeeId) {
            $monthlyTarget = MonthlyTarget::where('employee_id', $employeeId)
                                        ->where('year', $currentYear)
                                        ->where('month', $currentMonth)
                                        ->first();
            $targetAmount = $monthlyTarget ? $monthlyTarget->target_amount : 0;
            $achievementPercentage = $targetAmount > 0 ? round(($totalReceipts / $targetAmount) * 100, 2) : 0;
        } else {
            // إجمالي كل التارجتات للشهر
            $targetAmount = MonthlyTarget::where('year', $currentYear)
                                       ->where('month', $currentMonth)
                                       ->sum('target_amount');
            $achievementPercentage = $targetAmount > 0 ? round(($totalReceipts / $targetAmount) * 100, 2) : 0;
        }

        // قائمة الموظفين للفلترة
        $employees = Employee::active()->orderBy('name')->get();

        // قائمة الشهور للفلترة
        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];

        return view('admin.receipts-payments.index', compact(
            'receipts',
            'payments',
            'totalReceipts',
            'totalPayments',
            'netAmount',
            'targetAmount',
            'achievementPercentage',
            'currentYear',
            'currentMonth',
            'months',
            'search',
            'employees',
            'employeeId'
        ));
    }

    public function storeReceipt(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
        ], [
            'employee_id.required' => 'الموظف مطلوب',
            'description.required' => 'البيان مطلوب',
            'amount.required' => 'المبلغ مطلوب',
            'amount.min' => 'المبلغ يجب أن يكون أكبر من صفر',
            'date.required' => 'التاريخ مطلوب',
        ]);

        Receipt::create([
            'employee_id' => $request->employee_id,
            'description' => $request->description,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);

        return redirect()->route('admin.receipts-payments.index')
                       ->with('success', 'تم إضافة المقبوض بنجاح');
    }

    public function storePayment(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
        ], [
            'employee_id.required' => 'الموظف مطلوب',
            'description.required' => 'البيان مطلوب',
            'amount.required' => 'المبلغ مطلوب',
            'amount.min' => 'المبلغ يجب أن يكون أكبر من صفر',
            'date.required' => 'التاريخ مطلوب',
        ]);

        Payment::create([
            'employee_id' => $request->employee_id,
            'description' => $request->description,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);

        return redirect()->route('admin.receipts-payments.index')
                       ->with('success', 'تم إضافة المدفوع بنجاح');
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

        MonthlyTarget::updateOrCreateTarget(
            $request->year,
            $request->month,
            $request->target_amount,
            $request->employee_id
        );

        return redirect()->route('admin.receipts-payments.index', [
            'year' => $request->year,
            'month' => $request->month,
            'employee_id' => $request->employee_id
        ])->with('success', 'تم تحديث التارجت بنجاح');
    }

    public function deleteReceipt(Receipt $receipt)
    {
        $receipt->delete();

        return redirect()->route('admin.receipts-payments.index')
                       ->with('success', 'تم حذف المقبوض بنجاح');
    }

    public function deletePayment(Payment $payment)
    {
        $payment->delete();

        return redirect()->route('admin.receipts-payments.index')
                       ->with('success', 'تم حذف المدفوع بنجاح');
    }

    public function printReport(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));
        $employeeId = $request->get('employee_id');

        $receiptsQuery = Receipt::with('employee')->forPeriod($year, $month)->orderBy('date', 'asc');
        $paymentsQuery = Payment::with('employee')->forPeriod($year, $month)->orderBy('date', 'asc');

        if ($employeeId) {
            $receiptsQuery->where('employee_id', $employeeId);
            $paymentsQuery->where('employee_id', $employeeId);
        }

        $receipts = $receiptsQuery->get();
        $payments = $paymentsQuery->get();

        $totalReceipts = $receipts->sum('amount');
        $totalPayments = $payments->sum('amount');
        $netAmount = $totalReceipts - $totalPayments;

        $targetAmount = 0;
        $achievementPercentage = 0;
        $selectedEmployee = null;

        if ($employeeId) {
            $selectedEmployee = Employee::find($employeeId);
            $monthlyTarget = MonthlyTarget::where('employee_id', $employeeId)
                                        ->where('year', $year)
                                        ->where('month', $month)
                                        ->first();
            $targetAmount = $monthlyTarget ? $monthlyTarget->target_amount : 0;
        } else {
            $targetAmount = MonthlyTarget::where('year', $year)
                                       ->where('month', $month)
                                       ->sum('target_amount');
        }

        $achievementPercentage = $targetAmount > 0 ? round(($totalReceipts / $targetAmount) * 100, 2) : 0;

        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];

        return view('admin.receipts-payments.print', compact(
            'receipts',
            'payments',
            'totalReceipts',
            'totalPayments',
            'netAmount',
            'targetAmount',
            'achievementPercentage',
            'year',
            'month',
            'months',
            'selectedEmployee'
        ));
    }
}