<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\PhotographyCost;
use App\Models\Employee;
use App\Models\MonthlyTarget;
use Illuminate\Http\Request;

class PhotographyCostController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = $request->get('year', date('Y'));
        $currentMonth = $request->get('month', date('n'));
        $search = $request->get('search');
        $employeeId = auth('employee')->id();

        // استعلامات للمقبوضات والمدفوعات
        $receiptsQuery = PhotographyCost::where('type', 'receipt')
            ->forPeriod($currentYear, $currentMonth)
            ->where(function($q) use ($employeeId) {
                $q->where(function($qq) use ($employeeId) {
                    $qq->where('created_by_type', 'employee')
                      ->where('created_by', $employeeId);
                })->orWhere('created_by_type', 'admin');
            })
            ->orderBy('date', 'desc');
            
        $paymentsQuery = PhotographyCost::where('type', 'payment')
            ->forPeriod($currentYear, $currentMonth)
            ->where(function($q) use ($employeeId) {
                $q->where(function($qq) use ($employeeId) {
                    $qq->where('created_by_type', 'employee')
                      ->where('created_by', $employeeId);
                })->orWhere('created_by_type', 'admin');
            })
            ->orderBy('date', 'desc');

        if ($search) {
            $receiptsQuery->where('note', 'like', '%' . $search . '%');
            $paymentsQuery->where('note', 'like', '%' . $search . '%');
        }

        $totalReceipts = PhotographyCost::where('type', 'receipt')
            ->forPeriod($currentYear, $currentMonth)
            ->where(function($q) use ($employeeId) {
                $q->where(function($qq) use ($employeeId) {
                    $qq->where('created_by_type', 'employee')
                      ->where('created_by', $employeeId);
                })->orWhere('created_by_type', 'admin');
            })
            ->when($search, fn($q) => $q->where('note', 'like', '%' . $search . '%'))
            ->sum('amount');

        $totalPayments = PhotographyCost::where('type', 'payment')
            ->forPeriod($currentYear, $currentMonth)
            ->where(function($q) use ($employeeId) {
                $q->where(function($qq) use ($employeeId) {
                    $qq->where('created_by_type', 'employee')
                      ->where('created_by', $employeeId);
                })->orWhere('created_by_type', 'admin');
            })
            ->when($search, fn($q) => $q->where('note', 'like', '%' . $search . '%'))
            ->sum('amount');

        $receipts = $receiptsQuery->paginate(10, ['*'], 'receipts_page');
        $payments = $paymentsQuery->paginate(10, ['*'], 'payments_page');

        $netAmount = $totalReceipts - $totalPayments;

        // التارجت الشخصي للموظف
        $monthlyTarget = MonthlyTarget::where('employee_id', $employeeId)
            ->where('year', $currentYear)
            ->where('month', $currentMonth)
            ->first();
        $targetAmount = $monthlyTarget ? $monthlyTarget->target_amount : 0;
        $achievementPercentage = $targetAmount > 0 ? round(($totalReceipts / $targetAmount) * 100, 2) : 0;

        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];

        return view('employee.photography-costs.index', compact(
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
            'note' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
        ], [
            'note.required' => 'البيان مطلوب',
            'amount.required' => 'المبلغ مطلوب',
            'amount.min' => 'المبلغ يجب أن يكون أكبر من صفر',
            'date.required' => 'التاريخ مطلوب',
        ]);

        PhotographyCost::create([
            'date' => $request->date,
            'amount' => $request->amount,
            'type' => 'receipt',
            'note' => $request->note,
            'created_by' => auth('employee')->id(),
            'created_by_type' => 'employee',
        ]);

        return redirect()->route('employee.photography-costs.index')
            ->with('success', 'تم إضافة المقبوض بنجاح');
    }

    public function storePayment(Request $request)
    {
        $request->validate([
            'note' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
        ], [
            'note.required' => 'البيان مطلوب',
            'amount.required' => 'المبلغ مطلوب',
            'amount.min' => 'المبلغ يجب أن يكون أكبر من صفر',
            'date.required' => 'التاريخ مطلوب',
        ]);

        PhotographyCost::create([
            'date' => $request->date,
            'amount' => $request->amount,
            'type' => 'payment',
            'note' => $request->note,
            'created_by' => auth('employee')->id(),
            'created_by_type' => 'employee',
        ]);

        return redirect()->route('employee.photography-costs.index')
            ->with('success', 'تم إضافة المدفوع بنجاح');
    }

    public function deleteReceipt($id)
    {
        $receipt = PhotographyCost::where('type', 'receipt')
            ->where('created_by_type', 'employee')
            ->where('created_by', auth('employee')->id())
            ->findOrFail($id);
        $receipt->delete();
        return redirect()->route('employee.photography-costs.index')
            ->with('success', 'تم حذف المقبوض بنجاح');
    }

    public function deletePayment($id)
    {
        $payment = PhotographyCost::where('type', 'payment')
            ->where('created_by_type', 'employee')
            ->where('created_by', auth('employee')->id())
            ->findOrFail($id);
        $payment->delete();
        return redirect()->route('employee.photography-costs.index')
            ->with('success', 'تم حذف المدفوع بنجاح');
    }

    public function printReport(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));
        $employeeId = auth('employee')->id();

        $receiptsQuery = PhotographyCost::where('type', 'receipt')
            ->forPeriod($year, $month)
            ->where(function($q) use ($employeeId) {
                $q->where(function($qq) use ($employeeId) {
                    $qq->where('created_by_type', 'employee')
                      ->where('created_by', $employeeId);
                })->orWhere('created_by_type', 'admin');
            })
            ->orderBy('date', 'asc');
            
        $paymentsQuery = PhotographyCost::where('type', 'payment')
            ->forPeriod($year, $month)
            ->where(function($q) use ($employeeId) {
                $q->where(function($qq) use ($employeeId) {
                    $qq->where('created_by_type', 'employee')
                      ->where('created_by', $employeeId);
                })->orWhere('created_by_type', 'admin');
            })
            ->orderBy('date', 'asc');

        $receipts = $receiptsQuery->get();
        $payments = $paymentsQuery->get();
        $totalReceipts = $receipts->sum('amount');
        $totalPayments = $payments->sum('amount');
        $netAmount = $totalReceipts - $totalPayments;

        $employee = auth('employee')->user();
        $monthlyTarget = MonthlyTarget::where('employee_id', $employeeId)
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

        return view('employee.photography-costs.print', compact(
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

    // الدوال القديمة للتوافق
    public function create()
    {
        return view('employee.photography-costs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:receipt,payment',
        ]);

        PhotographyCost::create([
            'date' => $request->date,
            'amount' => $request->amount,
            'type' => $request->type,
            'note' => $request->note,
            'created_by' => auth('employee')->id(),
            'created_by_type' => 'employee',
        ]);

        return redirect()->route('employee.photography-costs.index')->with('success', 'تمت الإضافة بنجاح');
    }

    public function show(PhotographyCost $photographyCost)
    {
        $employeeId = auth('employee')->id();
        
        if (
            $photographyCost->created_by_type == 'admin'
            || (
                $photographyCost->created_by_type == 'employee'
                && $photographyCost->created_by == $employeeId
            )
        ) {
            return view('employee.photography-costs.show', compact('photographyCost'));
        }

        abort(403);
    }

    public function edit(PhotographyCost $photographyCost)
    {
        if (
            $photographyCost->created_by_type == 'employee'
            && $photographyCost->created_by == auth('employee')->id()
        ) {
            return view('employee.photography-costs.edit', compact('photographyCost'));
        }

        abort(403);
    }

    public function update(Request $request, PhotographyCost $photographyCost)
    {
        if (
            $photographyCost->created_by_type == 'employee'
            && $photographyCost->created_by == auth('employee')->id()
        ) {
            $request->validate([
                'date' => 'required|date',
                'amount' => 'required|numeric|min:0',
                'type' => 'required|in:receipt,payment',
            ]);

            $photographyCost->update($request->only('date', 'amount', 'type', 'note'));

            return redirect()->route('employee.photography-costs.index')->with('success', 'تم التعديل');
        }

        abort(403);
    }

    public function destroy(PhotographyCost $photographyCost)
    {
        if (
            $photographyCost->created_by_type == 'employee'
            && $photographyCost->created_by == auth('employee')->id()
        ) {
            $photographyCost->delete();

            return redirect()->route('employee.photography-costs.index')->with('success', 'تم الحذف');
        }

        abort(403);
    }
}