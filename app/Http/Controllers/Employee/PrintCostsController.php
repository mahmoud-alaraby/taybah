<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\PrintReceipt;
use App\Models\PrintPayment;
use App\Models\PrintMonthlyTarget;
use Illuminate\Http\Request;

class PrintCostsController extends Controller
{
    public function index(Request $request)
    {
        $employee = auth('employee')->user();
        $currentYear = $request->get('year', date('Y'));
        $currentMonth = $request->get('month', date('n'));
        $search = $request->get('search');

        // استعلام المقبوضات للموظف الحالي
        $receiptsQuery = PrintReceipt::where('employee_id', $employee->id)
            ->forPeriod($currentYear, $currentMonth)
            ->orderBy('date', 'desc');

        // استعلام المدفوعات للموظف الحالي
        $paymentsQuery = PrintPayment::where('employee_id', $employee->id)
            ->forPeriod($currentYear, $currentMonth)
            ->orderBy('date', 'desc');

        if ($search) {
            $receiptsQuery->search($search);
            $paymentsQuery->search($search);
        }

        // حساب المجاميع
        $totalReceipts = PrintReceipt::where('employee_id', $employee->id)
            ->forPeriod($currentYear, $currentMonth)
            ->when($search, fn($q) => $q->search($search))
            ->sum('amount');

        $totalPayments = PrintPayment::where('employee_id', $employee->id)
            ->forPeriod($currentYear, $currentMonth)
            ->when($search, fn($q) => $q->search($search))
            ->sum('amount');

        $netAmount = $totalReceipts - $totalPayments;

        // التارجت الشهري للموظف
        $monthlyTarget = PrintMonthlyTarget::where('employee_id', $employee->id)
            ->where('year', $currentYear)
            ->where('month', $currentMonth)
            ->first();

        $targetAmount = $monthlyTarget ? $monthlyTarget->target_amount : 0;
        $achievementPercentage = $targetAmount > 0 ? round(($totalReceipts / $targetAmount) * 100, 2) : 0;

        $receipts = $receiptsQuery->paginate(10, ['*'], 'receipts_page');
        $payments = $paymentsQuery->paginate(10, ['*'], 'payments_page');

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

        return view('employee.print-costs.index', compact(
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
            'employee'
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

        PrintReceipt::create([
            'employee_id' => auth('employee')->id(),
            'description' => $request->description,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);

        return redirect()->route('employee.print-costs.index')
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

        PrintPayment::create([
            'employee_id' => auth('employee')->id(),
            'description' => $request->description,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);

        return redirect()->route('employee.print-costs.index')
            ->with('success', 'تم إضافة المدفوع بنجاح');
    }

    public function deleteReceipt(PrintReceipt $receipt)
    {
        // التأكد أن المقبوض يخص الموظف الحالي
        if ($receipt->employee_id !== auth('employee')->id()) {
            return redirect()->route('employee.print-costs.index')
                ->with('error', 'غير مصرح لك بحذف هذا المقبوض');
        }

        $receipt->delete();
        return redirect()->route('employee.print-costs.index')
            ->with('success', 'تم حذف المقبوض بنجاح');
    }

    public function deletePayment(PrintPayment $payment)
    {
        // التأكد أن المدفوع يخص الموظف الحالي
        if ($payment->employee_id !== auth('employee')->id()) {
            return redirect()->route('employee.print-costs.index')
                ->with('error', 'غير مصرح لك بحذف هذا المدفوع');
        }

        $payment->delete();
        return redirect()->route('employee.print-costs.index')
            ->with('success', 'تم حذف المدفوع بنجاح');
    }

    public function printReport(Request $request)
    {
        $employee = auth('employee')->user();
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));

        $receipts = PrintReceipt::where('employee_id', $employee->id)
            ->forPeriod($year, $month)
            ->orderBy('date', 'asc')
            ->get();

        $payments = PrintPayment::where('employee_id', $employee->id)
            ->forPeriod($year, $month)
            ->orderBy('date', 'asc')
            ->get();

        $totalReceipts = $receipts->sum('amount');
        $totalPayments = $payments->sum('amount');
        $netAmount = $totalReceipts - $totalPayments;

        $monthlyTarget = PrintMonthlyTarget::where('employee_id', $employee->id)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        $targetAmount = $monthlyTarget ? $monthlyTarget->target_amount : 0;
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

        return view('employee.print-costs.print', compact(
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
            'employee'
        ));
    }

    public function editReceipt(PrintReceipt $receipt)
    {
        if ($receipt->employee_id !== auth('employee')->id()) {
            return response()->json(['error' => 'غير مصرح لك بتعديل هذا المقبوض'], 403);
        }

        return response()->json([
            'success' => true,
            'receipt' => $receipt
        ]);
    }

    public function updateReceipt(Request $request, PrintReceipt $receipt)
    {
        if ($receipt->employee_id !== auth('employee')->id()) {
            return redirect()->route('employee.print-costs.index')
                ->with('error', 'غير مصرح لك بتعديل هذا المقبوض');
        }

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

        $receipt->update([
            'description' => $request->description,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);

        return redirect()->route('employee.print-costs.index', request()->only(['year', 'month', 'search']))
            ->with('success', 'تم تحديث المقبوض بنجاح');
    }

    public function editPayment(PrintPayment $payment)
    {
        if ($payment->employee_id !== auth('employee')->id()) {
            return response()->json(['error' => 'غير مصرح لك بتعديل هذا المدفوع'], 403);
        }

        return response()->json([
            'success' => true,
            'payment' => $payment
        ]);
    }

    public function updatePayment(Request $request, PrintPayment $payment)
    {
        if ($payment->employee_id !== auth('employee')->id()) {
            return redirect()->route('employee.print-costs.index')
                ->with('error', 'غير مصرح لك بتعديل هذا المدفوع');
        }

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

        $payment->update([
            'description' => $request->description,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);

        return redirect()->route('employee.print-costs.index', request()->only(['year', 'month', 'search']))
            ->with('success', 'تم تحديث المدفوع بنجاح');
    }
}