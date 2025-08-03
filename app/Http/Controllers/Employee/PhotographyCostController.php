<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\PhotographyCost;
use Illuminate\Http\Request;

class PhotographyCostController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth('employee')->id();

        $query = PhotographyCost::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // فلتر من المنشيء
        if ($request->filled('creator_source')) {
            if ($request->creator_source === 'admin') {
                $query->where('created_by_type', 'admin');
            } elseif ($request->creator_source === 'me') {
                $query->where('created_by_type', 'employee')
                      ->where('created_by', $userId);
            } elseif ($request->creator_source === 'others') {
                $query->where(function($q) use ($userId) {
                    $q->where('created_by_type', 'employee')
                      ->where('created_by', '!=', $userId);
                });
            }
        }

        // بحث حسب اسم المنشئ النصي
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

        // فلترة بالتاريخ من وإلى
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        // يرى فقط القيود التي أنشأها هو أو الأدمن
        $query->where(function ($q) use ($userId) {
            $q->where('created_by_type', 'admin')
              ->orWhere(function ($qq) use ($userId) {
                  $qq->where('created_by_type', 'employee')
                      ->where('created_by', $userId);
              });
        });

        $costs = $query->latest('date')->paginate(15)->withQueryString();

        $totalReceipts = PhotographyCost::where('type', 'receipt')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        $totalPayments = PhotographyCost::where('type', 'payment')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        return view('employee.photography_costs.index', compact('costs', 'totalReceipts', 'totalPayments'));
    }

    public function create()
    {
        return view('employee.photography_costs.create');
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
        if (
            $photographyCost->created_by_type == 'admin'
            || (
                $photographyCost->created_by_type == 'employee'
                && $photographyCost->created_by == auth('employee')->id()
            )
        ) {
            return view('employee.photography_costs.show', compact('photographyCost'));
        }

        abort(403);
    }

    public function edit(PhotographyCost $photographyCost)
    {
        if (
            $photographyCost->created_by_type == 'employee'
            && $photographyCost->created_by == auth('employee')->id()
        ) {
            return view('employee.photography_costs.edit', compact('photographyCost'));
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
