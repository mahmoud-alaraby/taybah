<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhotographyCost;
use App\Models\Employee;
use Illuminate\Http\Request;

class PhotographyCostController extends Controller
{
    public function index(Request $request)
    {
        $query = PhotographyCost::query();

        // فلترة حسب النوع
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // فلترة من المنشيء
        if ($request->filled('creator_source')) {
            if ($request->creator_source === 'admin') {
                $query->where('created_by_type', 'admin');
            } elseif ($request->creator_source === 'me') {
                $query->where('created_by_type', 'admin')
                      ->where('created_by', auth('admin')->id());
            } elseif ($request->creator_source === 'others') {
                $query->where(function($q) {
                    $q->where('created_by_type', 'employee');
                })->orWhere(function($q) {
                    $q->where('created_by_type', 'admin')
                      ->where('created_by', '!=', auth('admin')->id());
                });
            }
        }

        // بحث باسم المنشئ النصي الإضافي إن موجود
        if ($request->filled('creator_name')) {
            $creatorName = $request->creator_name;
            $query->where(function ($q) use ($creatorName) {
                $q->whereHas('employee', function ($qq) use ($creatorName) {
                    $qq->where('name', 'like', '%' . $creatorName . '%');
                })->orWhereHas('admin', function ($qq) use ($creatorName) {
                    $qq->where('name', 'like', '%' . $creatorName . '%');
                });
            });
        }

        // فلترة بالتواريخ
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        // فلترة حسب الموظف منشئ البيانات إذا مطلوب
        if ($request->has('employee_id') && $request->employee_id) {
            $allowedEmployees = Employee::whereHas('roles.permissions', function ($q) {
                $q->where('name', 'photography_costs');
            })->pluck('id')->toArray();

            $query->where('created_by_type', 'employee')
                ->whereIn('created_by', $allowedEmployees)
                ->where('created_by', $request->employee_id);
        }

        $costs = $query->latest('date')->paginate(15)->withQueryString();

        $employeesWithPermission = Employee::whereHas('roles.permissions', function ($q) {
            $q->where('name', 'photography_costs');
        })->get();

        // احصائيات الشهر الحالي
        $totalReceipts = PhotographyCost::where('type', 'receipt')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        $totalPayments = PhotographyCost::where('type', 'payment')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        return view('admin.photography_costs.index', compact('costs', 'totalReceipts', 'totalPayments', 'employeesWithPermission'));
    }

    public function create()
    {
        return view('admin.photography_costs.create');
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
        return view('admin.photography_costs.show', compact('photographyCost'));
    }

    public function edit(PhotographyCost $photographyCost)
    {
        return view('admin.photography_costs.edit', compact('photographyCost'));
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
