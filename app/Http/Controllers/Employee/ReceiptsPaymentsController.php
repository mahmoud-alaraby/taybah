<?php
// app/Http/Controllers/Employee/ReceiptsPaymentsController.php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use App\Models\Payment;
use App\Models\MonthlyTarget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceiptsPaymentsController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = $request->get('year', date('Y'));
        $currentMonth = $request->get('month', date('n'));
        $search = $request->get('search');

        // الحصول على المقبوضات والمدفوعات للشهر المحدد
        $receiptsQuery = Receipt::forCurrentEmployee()
                              ->forPeriod($currentYear, $currentMonth)
                              ->orderBy('date', 'desc');

        $paymentsQuery = Payment::forCurrentEmployee()
                              ->forPeriod($currentYear, $currentMonth)
                              ->orderBy('date', 'desc');

        // البحث
        if ($search) {
            $receiptsQuery->search($search);
            $paymentsQuery->search($search);
        }

        $receipts = $receiptsQuery->paginate(10, ['*'], 'receipts_page');
        $payments = $paymentsQuery->paginate(10, ['*'], 'payments_page');

        // حساب الإجماليات
        $totalReceipts = $receipts->sum('amount');
        $totalPayments = $payments->sum('amount');
        $netAmount = $totalReceipts - $totalPayments;

        // الحصول على التارجت
        $monthlyTarget = MonthlyTarget::where('employee_id', auth('employee')->id())
                                    ->where('year', $currentYear)
                                    ->where('month', $currentMonth)
                                    ->first();

        $targetAmount = $monthlyTarget ? $monthlyTarget->target_amount : 0;
        $achievementPercentage = $targetAmount > 0 ? round(($totalReceipts / $targetAmount) * 100, 2) : 0;

        // قائمة الشهور للفلترة
        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];

        return view('employee.systems.receipts-payments', compact(
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
            'search'
        ));
    }

    public function storeReceipt(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
        ], [
            'description.required' => 'البيان مطلوب',
            'amount.required' => 'المبلغ مطلوب',
            'amount.min' => 'المبلغ يجب أن يكون أكبر من صفر',
            'date.required' => 'التاريخ مطلوب',
        ]);

        Receipt::create([
            'employee_id' => auth('employee')->id(),
            'description' => $request->description,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);

        return redirect()->route('employee.receipts-payments')
                       ->with('success', 'تم إضافة المقبوض بنجاح');
    }

    public function storePayment(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
        ], [
            'description.required' => 'البيان مطلوب',
            'amount.required' => 'المبلغ مطلوب',
            'amount.min' => 'المبلغ يجب أن يكون أكبر من صفر',
            'date.required' => 'التاريخ مطلوب',
        ]);

        Payment::create([
            'employee_id' => auth('employee')->id(),
            'description' => $request->description,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);

        return redirect()->route('employee.receipts-payments')
                       ->with('success', 'تم إضافة المدفوع بنجاح');
    }

    public function updateTarget(Request $request)
    {
        $request->validate([
            'target_amount' => 'required|numeric|min:0',
            'year' => 'required|integer|min:2020|max:2050',
            'month' => 'required|integer|min:1|max:12',
        ], [
            'target_amount.required' => 'مبلغ التارجت مطلوب',
            'target_amount.min' => 'التارجت لا يمكن أن يكون سالباً',
            'year.required' => 'السنة مطلوبة',
            'month.required' => 'الشهر مطلوب',
        ]);

        MonthlyTarget::updateOrCreateTarget(
            $request->year,
            $request->month,
            $request->target_amount,
            auth('employee')->id()
        );

        return redirect()->route('employee.receipts-payments', [
            'year' => $request->year,
            'month' => $request->month
        ])->with('success', 'تم تحديث التارجت بنجاح');
    }

    public function deleteReceipt(Receipt $receipt)
    {
        // التأكد من أن المقبوض يخص الموظف الحالي
        if ($receipt->employee_id !== auth('employee')->id()) {
            abort(403);
        }

        $receipt->delete();

        return redirect()->route('employee.receipts-payments')
                       ->with('success', 'تم حذف المقبوض بنجاح');
    }

    public function deletePayment(Payment $payment)
    {
        // التأكد من أن المدفوع يخص الموظف الحالي
        if ($payment->employee_id !== auth('employee')->id()) {
            abort(403);
        }

        $payment->delete();

        return redirect()->route('employee.receipts-payments')
                       ->with('success', 'تم حذف المدفوع بنجاح');
    }

    public function printReport(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));

        $receipts = Receipt::forCurrentEmployee()
                         ->forPeriod($year, $month)
                         ->orderBy('date', 'asc')
                         ->get();

        $payments = Payment::forCurrentEmployee()
                         ->forPeriod($year, $month)
                         ->orderBy('date', 'asc')
                         ->get();

        $totalReceipts = $receipts->sum('amount');
        $totalPayments = $payments->sum('amount');
        $netAmount = $totalReceipts - $totalPayments;

        $monthlyTarget = MonthlyTarget::where('employee_id', auth('employee')->id())
                                    ->where('year', $year)
                                    ->where('month', $month)
                                    ->first();

        $targetAmount = $monthlyTarget ? $monthlyTarget->target_amount : 0;
        $achievementPercentage = $targetAmount > 0 ? round(($totalReceipts / $targetAmount) * 100, 2) : 0;

        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];

        return view('employee.systems.receipts-payments-print', compact(
            'receipts',
            'payments',
            'totalReceipts',
            'totalPayments',
            'netAmount',
            'targetAmount',
            'achievementPercentage',
            'year',
            'month',
            'months'
        ));
    }
}