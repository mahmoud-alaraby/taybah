<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use App\Models\Payment;
use App\Models\MonthlyTarget;
use App\Models\Employee;
use Illuminate\Http\Request;

class ReceiptsPaymentsController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = $request->get('year', date('Y'));
        $currentMonth = $request->get('month', date('n'));
        $search = $request->get('search');
        $employeeId = $request->get('employee_id');

        $receiptsQuery = Receipt::with('employee')
            ->forPeriod($currentYear, $currentMonth)
            ->orderBy('date', 'desc');
        $paymentsQuery = Payment::with('employee')
            ->forPeriod($currentYear, $currentMonth)
            ->orderBy('date', 'desc');

        if ($employeeId) {
            $receiptsQuery->where('employee_id', $employeeId);
            $paymentsQuery->where('employee_id', $employeeId);
        }
        if ($search) {
            $receiptsQuery->search($search);
            $paymentsQuery->search($search);
        }

        $totalReceipts = Receipt::forPeriod($currentYear, $currentMonth)
            ->when($employeeId, fn($q) => $q->where('employee_id', $employeeId))
            ->when($search, fn($q) => $q->search($search))
            ->sum('amount');

        $totalPayments = Payment::forPeriod($currentYear, $currentMonth)
            ->when($employeeId, fn($q) => $q->where('employee_id', $employeeId))
            ->when($search, fn($q) => $q->search($search))
            ->sum('amount');

        $receipts = $receiptsQuery->paginate(10, ['*'], 'receipts_page');
        $payments = $paymentsQuery->paginate(10, ['*'], 'payments_page');

        $netAmount = $totalReceipts - $totalPayments;

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
            $targetAmount = MonthlyTarget::where('year', $currentYear)
                ->where('month', $currentMonth)
                ->sum('target_amount');
            $achievementPercentage = $targetAmount > 0 ? round(($totalReceipts / $targetAmount) * 100, 2) : 0;
        }

        // التعديل هنا: جلب الموظفين النشطين الديناميكي عبر البيرمشن
        $employees = Employee::whereHas('roles.permissions', function ($q) {
            $q->where('name', 'receipts_payments');
        })
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $months = [
            1 => 'يناير',
            2 => 'فبراير',
            3 => 'مارس',
            4 => 'أبريل',
            5 => 'مايو',
            6 => 'يونيو',
            7 => 'يوليو',
            8 => 'أغسطس',
            9 => 'سبتمبر',
            10 => 'أكتوبر',
            11 => 'نوفمبر',
            12 => 'ديسمبر'
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
            1 => 'يناير',
            2 => 'فبراير',
            3 => 'مارس',
            4 => 'أبريل',
            5 => 'مايو',
            6 => 'يونيو',
            7 => 'يوليو',
            8 => 'أغسطس',
            9 => 'سبتمبر',
            10 => 'أكتوبر',
            11 => 'نوفمبر',
            12 => 'ديسمبر'
        ];

        // التعديل هنا: الموظفين الديناميكي البيرمشن
        $employees = Employee::whereHas('roles.permissions', function ($q) {
            $q->where('name', 'receipts_payments');
        })
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

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
            'selectedEmployee',
            'employees'
        ));
    }

    public function editReceipt(Receipt $receipt)
    {
        $currentYear = request()->get('year', date('Y'));
        $currentMonth = request()->get('month', date('n'));
        $employeeId = request()->get('employee_id');

        // التحقق من أن المقبوض يخص نفس الموظف المحدد (إن وجد)
        if ($employeeId && $receipt->employee_id != $employeeId) {
            return redirect()->route('admin.receipts-payments.index', [
                'year' => $currentYear,
                'month' => $currentMonth,
                'employee_id' => $employeeId
            ])->with('error', 'لا يمكن تعديل هذا المقبوض');
        }

        $employees = Employee::whereHas('roles.permissions', function ($q) {
            $q->where('name', 'receipts_payments');
        })
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'receipt' => $receipt,
            'employees' => $employees
        ]);
    }

    public function updateReceipt(Request $request, Receipt $receipt)
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

        $receipt->update([
            'employee_id' => $request->employee_id,
            'description' => $request->description,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);

        return redirect()->route('admin.receipts-payments.index', request()->only(['year', 'month', 'search']))
            ->with('success', 'تم تحديث المقبوض بنجاح');
    }

    public function editPayment(Payment $payment)
    {
        $currentYear = request()->get('year', date('Y'));
        $currentMonth = request()->get('month', date('n'));
        $employeeId = request()->get('employee_id');

        // التحقق من أن المدفوع يخص نفس الموظف المحدد (إن وجد)
        if ($employeeId && $payment->employee_id != $employeeId) {
            return redirect()->route('admin.receipts-payments.index', [
                'year' => $currentYear,
                'month' => $currentMonth,
                'employee_id' => $employeeId
            ])->with('error', 'لا يمكن تعديل هذا المدفوع');
        }

        $employees = Employee::whereHas('roles.permissions', function ($q) {
            $q->where('name', 'receipts_payments');
        })
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'payment' => $payment,
            'employees' => $employees
        ]);
    }

    public function updatePayment(Request $request, Payment $payment)
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

        $payment->update([
            'employee_id' => $request->employee_id,
            'description' => $request->description,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);

        return redirect()->route('admin.receipts-payments.index', request()->only(['year', 'month', 'search']))
            ->with('success', 'تم تحديث المدفوع بنجاح');
    }
}
