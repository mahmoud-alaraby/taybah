<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrintReceipt;
use App\Models\PrintPayment;
use App\Models\PrintMonthlyTarget;
use App\Models\Employee;
use Illuminate\Http\Request;

class PrintCostsController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = $request->get('year', date('Y'));
        $currentMonth = $request->get('month', date('n'));
        $search = $request->get('search');
        $employeeId = $request->get('employee_id');

        $receiptsQuery = PrintReceipt::with('employee')
            ->forPeriod($currentYear, $currentMonth)
            ->orderBy('date', 'desc');
        $paymentsQuery = PrintPayment::with('employee')
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

        $totalReceipts = PrintReceipt::forPeriod($currentYear, $currentMonth)
            ->when($employeeId, fn($q) => $q->where('employee_id', $employeeId))
            ->when($search, fn($q) => $q->search($search))
            ->sum('amount');

        $totalPayments = PrintPayment::forPeriod($currentYear, $currentMonth)
            ->when($employeeId, fn($q) => $q->where('employee_id', $employeeId))
            ->when($search, fn($q) => $q->search($search))
            ->sum('amount');

        $receipts = $receiptsQuery->paginate(10, ['*'], 'receipts_page');
        $payments = $paymentsQuery->paginate(10, ['*'], 'payments_page');

        $netAmount = $totalReceipts - $totalPayments;

        $targetAmount = 0;
        $achievementPercentage = 0;

        if ($employeeId) {
            $monthlyTarget = PrintMonthlyTarget::where('employee_id', $employeeId)
                ->where('year', $currentYear)
                ->where('month', $currentMonth)
                ->first();
            $targetAmount = $monthlyTarget ? $monthlyTarget->target_amount : 0;
            $achievementPercentage = $targetAmount > 0 ? round(($totalReceipts / $targetAmount) * 100, 2) : 0;
        } else {
            $targetAmount = PrintMonthlyTarget::where('year', $currentYear)
                ->where('month', $currentMonth)
                ->sum('target_amount');
            $achievementPercentage = $targetAmount > 0 ? round(($totalReceipts / $targetAmount) * 100, 2) : 0;
        }

        // الموظفين الذين لديهم صلاحية تكاليف الطباعة
        $employees = Employee::whereHas('roles.permissions', function ($q) {
            $q->where('name', 'print_costs');
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

        return view('admin.print-costs.index', compact(
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

        PrintReceipt::create([
            'employee_id' => $request->employee_id,
            'description' => $request->description,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);

        return redirect()->route('admin.print-costs.index')
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

        PrintPayment::create([
            'employee_id' => $request->employee_id,
            'description' => $request->description,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);

        return redirect()->route('admin.print-costs.index')
            ->with('success', 'تم إضافة المدفوع بنجاح');
    }

    public function updateTarget(Request $request)
    {
        $request->validate([
          
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

        PrintMonthlyTarget::updateOrCreateTarget(
            $request->year,
            $request->month,
            $request->target_amount,
            $request->employee_id
        );

        return redirect()->route('admin.print-costs.index', [
            'year' => $request->year,
            'month' => $request->month,
            'employee_id' => $request->employee_id
        ])->with('success', 'تم تحديث التارجت بنجاح');
    }

    public function deleteReceipt(PrintReceipt $receipt)
    {
        $receipt->delete();
        return redirect()->route('admin.print-costs.index')
            ->with('success', 'تم حذف المقبوض بنجاح');
    }

    public function deletePayment(PrintPayment $payment)
    {
        $payment->delete();
        return redirect()->route('admin.print-costs.index')
            ->with('success', 'تم حذف المدفوع بنجاح');
    }

    public function printReport(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));
        $employeeId = $request->get('employee_id');

        $receiptsQuery = PrintReceipt::with('employee')->forPeriod($year, $month)->orderBy('date', 'asc');
        $paymentsQuery = PrintPayment::with('employee')->forPeriod($year, $month)->orderBy('date', 'asc');

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
            $monthlyTarget = PrintMonthlyTarget::where('employee_id', $employeeId)
                ->where('year', $year)
                ->where('month', $month)
                ->first();
            $targetAmount = $monthlyTarget ? $monthlyTarget->target_amount : 0;
        } else {
            $targetAmount = PrintMonthlyTarget::where('year', $year)
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

        $employees = Employee::whereHas('roles.permissions', function ($q) {
            $q->where('name', 'print_costs');
        })
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.print-costs.print', compact(
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

    public function editReceipt(PrintReceipt $receipt)
    {
        $currentYear = request()->get('year', date('Y'));
        $currentMonth = request()->get('month', date('n'));
        $employeeId = request()->get('employee_id');

        if ($employeeId && $receipt->employee_id != $employeeId) {
            return redirect()->route('admin.print-costs.index', [
                'year' => $currentYear,
                'month' => $currentMonth,
                'employee_id' => $employeeId
            ])->with('error', 'لا يمكن تعديل هذا المقبوض');
        }

        $employees = Employee::whereHas('roles.permissions', function ($q) {
            $q->where('name', 'print_costs');
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

    public function updateReceipt(Request $request, PrintReceipt $receipt)
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

        return redirect()->route('admin.print-costs.index', request()->only(['year', 'month', 'employee_id', 'search']))
            ->with('success', 'تم تحديث المقبوض بنجاح');
    }

    public function editPayment(PrintPayment $payment)
    {
        $currentYear = request()->get('year', date('Y'));
        $currentMonth = request()->get('month', date('n'));
        $employeeId = request()->get('employee_id');

        if ($employeeId && $payment->employee_id != $employeeId) {
            return redirect()->route('admin.print-costs.index', [
                'year' => $currentYear,
                'month' => $currentMonth,
                'employee_id' => $employeeId
            ])->with('error', 'لا يمكن تعديل هذا المدفوع');
        }

        $employees = Employee::whereHas('roles.permissions', function ($q) {
            $q->where('name', 'print_costs');
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

    public function updatePayment(Request $request, PrintPayment $payment)
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

        return redirect()->route('admin.print-costs.index', request()->only(['year', 'month', 'employee_id', 'search']))
            ->with('success', 'تم تحديث المدفوع بنجاح');
    }
}