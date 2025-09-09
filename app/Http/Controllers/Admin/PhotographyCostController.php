<?php

namespace App\Http\Controllers\Admin;

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
        $employeeId = $request->get('employee_id');

        // استعلامات منفصلة بدون eager loading للعلاقات المشكوك فيها
        $receiptsQuery = PhotographyCost::where('type', 'receipt')
            ->forPeriod($currentYear, $currentMonth)
            ->orderBy('date', 'desc');
            
        $paymentsQuery = PhotographyCost::where('type', 'payment')
            ->forPeriod($currentYear, $currentMonth)
            ->orderBy('date', 'desc');

        if ($employeeId) {
            $receiptsQuery->where(function($q) use ($employeeId) {
                $q->where(function($qq) use ($employeeId) {
                    $qq->where('created_by_type', 'employee')
                      ->where('created_by', $employeeId);
                })->orWhere(function($qq) use ($employeeId) {
                    $qq->where('created_by_type', 'admin')
                      ->where('created_by', $employeeId);
                });
            });
            
            $paymentsQuery->where(function($q) use ($employeeId) {
                $q->where(function($qq) use ($employeeId) {
                    $qq->where('created_by_type', 'employee')
                      ->where('created_by', $employeeId);
                })->orWhere(function($qq) use ($employeeId) {
                    $qq->where('created_by_type', 'admin')
                      ->where('created_by', $employeeId);
                });
            });
        }

        if ($search) {
            $receiptsQuery->where('note', 'like', '%' . $search . '%');
            $paymentsQuery->where('note', 'like', '%' . $search . '%');
        }

        $totalReceipts = PhotographyCost::where('type', 'receipt')
            ->forPeriod($currentYear, $currentMonth)
            ->when($employeeId, function($q) use ($employeeId) {
                $q->where(function($qq) use ($employeeId) {
                    $qq->where(function($qqq) use ($employeeId) {
                        $qqq->where('created_by_type', 'employee')
                           ->where('created_by', $employeeId);
                    })->orWhere(function($qqq) use ($employeeId) {
                        $qqq->where('created_by_type', 'admin')
                           ->where('created_by', $employeeId);
                    });
                });
            })
            ->when($search, fn($q) => $q->where('note', 'like', '%' . $search . '%'))
            ->sum('amount');

        $totalPayments = PhotographyCost::where('type', 'payment')
            ->forPeriod($currentYear, $currentMonth)
            ->when($employeeId, function($q) use ($employeeId) {
                $q->where(function($qq) use ($employeeId) {
                    $qq->where(function($qqq) use ($employeeId) {
                        $qqq->where('created_by_type', 'employee')
                           ->where('created_by', $employeeId);
                    })->orWhere(function($qqq) use ($employeeId) {
                        $qqq->where('created_by_type', 'admin')
                           ->where('created_by', $employeeId);
                    });
                });
            })
            ->when($search, fn($q) => $q->where('note', 'like', '%' . $search . '%'))
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

        // جلب الموظفين الذين لهم صلاحية photography_costs
        $employees = Employee::whereHas('roles.permissions', function($q){
                $q->where('name', 'photography_costs');
            })
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];

        return view('admin.photography-costs.index', compact(
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
            'created_by' => auth('admin')->id(),
            'created_by_type' => 'admin',
        ]);

        return redirect()->route('admin.photography-costs.index')
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
            'created_by' => auth('admin')->id(),
            'created_by_type' => 'admin',
        ]);

        return redirect()->route('admin.photography-costs.index')
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

        MonthlyTarget::updateOrCreate(
            [
                'employee_id' => $request->employee_id,
                'year' => $request->year,
                'month' => $request->month,
            ],
            [
                'target_amount' => $request->target_amount,
            ]
        );

        return redirect()->route('admin.photography-costs.index', [
            'year' => $request->year,
            'month' => $request->month,
            'employee_id' => $request->employee_id
        ])->with('success', 'تم تحديث التارجت بنجاح');
    }

    public function deleteReceipt($id)
    {
        $receipt = PhotographyCost::where('type', 'receipt')->findOrFail($id);
        $receipt->delete();
        return redirect()->route('admin.photography-costs.index')
            ->with('success', 'تم حذف المقبوض بنجاح');
    }

    public function deletePayment($id)
    {
        $payment = PhotographyCost::where('type', 'payment')->findOrFail($id);
        $payment->delete();
        return redirect()->route('admin.photography-costs.index')
            ->with('success', 'تم حذف المدفوع بنجاح');
    }

    public function printReport(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));
        $employeeId = $request->get('employee_id');

        $receiptsQuery = PhotographyCost::where('type', 'receipt')
            ->forPeriod($year, $month)
            ->orderBy('date', 'asc');
            
        $paymentsQuery = PhotographyCost::where('type', 'payment')
            ->forPeriod($year, $month)
            ->orderBy('date', 'asc');

        if ($employeeId) {
            $receiptsQuery->where(function($q) use ($employeeId) {
                $q->where(function($qq) use ($employeeId) {
                    $qq->where('created_by_type', 'employee')
                      ->where('created_by', $employeeId);
                })->orWhere(function($qq) use ($employeeId) {
                    $qq->where('created_by_type', 'admin')
                      ->where('created_by', $employeeId);
                });
            });
            
            $paymentsQuery->where(function($q) use ($employeeId) {
                $q->where(function($qq) use ($employeeId) {
                    $qq->where('created_by_type', 'employee')
                      ->where('created_by', $employeeId);
                })->orWhere(function($qq) use ($employeeId) {
                    $qq->where('created_by_type', 'admin')
                      ->where('created_by', $employeeId);
                });
            });
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

        $employees = Employee::whereHas('roles.permissions', function($q){
                $q->where('name', 'photography_costs');
            })
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.photography-costs.print', compact(
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

    // باقي الدوال القديمة للتوافق مع النظام الحالي
    public function create()
    {
        return view('admin.photography-costs.create');
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
            'created_by' => auth('admin')->id(),
            'created_by_type' => 'admin',
        ]);

        return redirect()->route('admin.photography-costs.index')->with('success', 'تمت الإضافة بنجاح');
    }

    public function show(PhotographyCost $photographyCost)
    {
        return view('admin.photography-costs.show', compact('photographyCost'));
    }

    public function edit(PhotographyCost $photographyCost)
    {
        return view('admin.photography-costs.edit', compact('photographyCost'));
    }

    public function update(Request $request, PhotographyCost $photographyCost)
    {
        $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:receipt,payment',
        ]);

        $photographyCost->update($request->only('date', 'amount', 'type', 'note'));

        return redirect()->route('admin.photography-costs.index')->with('success', 'تم التعديل');
    }

    public function destroy(PhotographyCost $photographyCost)
    {
        $photographyCost->delete();
        return redirect()->route('admin.photography-costs.index')->with('success', 'تم الحذف');
    }
}