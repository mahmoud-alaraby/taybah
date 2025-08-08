<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RenewalDate;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RenewalDateController extends Controller
{
   public function index(Request $request)
{
    $query = RenewalDate::query();

    // فلترة حسب التاريخ المحدد (نفس اليوم أو بعده حسب اختيارك)
    if ($request->filled('date')) {
        $date = Carbon::parse($request->date)->startOfDay();

        // إذا تريد فقط الأحداث التي في ذلك اليوم بالضبط:
        // $query->whereDate('renewal_date', $date);

        // إذا تريد كل الأحداث بعد ذلك التاريخ (تشمل التاريخ نفسه):
        $query->where('renewal_date', '>=', $date);
    }

    // فلترة حسب الحالة
    if ($request->filled('status')) {
        $allowedStatuses = ['active', 'completed', 'cancelled'];
        if (in_array($request->status, $allowedStatuses)) {
            $query->where('status', $request->status);
        }
    }

    // فلترة حسب التكرار (اختياري)
    if ($request->filled('frequency')) {
        $allowedFrequencies = ['yearly', 'quarterly', 'monthly'];
        if (in_array($request->frequency, $allowedFrequencies)) {
            $query->where('frequency', $request->frequency);
        }
    }

    // فلترة نصية في العنوان أو الوصف (اختياري)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    $renewalDates = $query->orderBy('renewal_date', 'asc')->paginate(10);

    // الإحصائيات وأجزاء إضافية كما هي (يمكنك تعديلها حسب حاجتك)
    $statistics = [
        'total' => RenewalDate::count(),
        'active' => RenewalDate::active()->count(),
        'upcoming' => RenewalDate::upcoming(3)->count(),
        'today' => RenewalDate::today()->count(),
        'overdue' => RenewalDate::overdue()->count()
    ];

    $upcomingRenewals = RenewalDate::upcoming(3)->orderBy('renewal_date', 'asc')->get();
    $todayRenewals = RenewalDate::today()->get();
    $overdueRenewals = RenewalDate::overdue()->orderBy('renewal_date', 'asc')->get();

    return view('admin.renewal-dates.index', compact(
        'renewalDates',
        'upcomingRenewals',
        'todayRenewals',
        'overdueRenewals',
        'statistics'
    ));
}


    public function create()
    {
        return view('admin.renewal-dates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'renewal_date' => 'required|date',
            'frequency' => 'required|in:yearly,quarterly,monthly',
            'amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,completed,cancelled'
        ], [
            'title.required' => 'عنوان الحدث مطلوب',
            'renewal_date.required' => 'تاريخ التجديد مطلوب',
            'renewal_date.date' => 'تاريخ التجديد يجب أن يكون تاريخ صحيح',
            'frequency.required' => 'تكرار الحدث مطلوب',
            'amount.numeric' => 'المبلغ يجب أن يكون رقم',
            'amount.min' => 'المبلغ لا يمكن أن يكون سالب'
        ]);

        try {
            $renewalDate = new RenewalDate();
            $renewalDate->title = $request->title;
            $renewalDate->description = $request->description;
            $renewalDate->renewal_date = $request->renewal_date;
            $renewalDate->frequency = $request->frequency;
            $renewalDate->amount = $request->amount;
            $renewalDate->status = $request->status;
            $renewalDate->created_by_admin = auth('admin')->id();

            // حساب التاريخ التالي للتجديد
            $renewalDate->next_renewal_date = $renewalDate->calculateNextRenewalDate();
            $renewalDate->save();

            return redirect()->route('admin.renewal-dates.index')
                ->with('success', 'تم إضافة موعد التجديد بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة الحدث: ' . $e->getMessage());
        }
    }

    public function show(RenewalDate $renewalDate)
    {
        $renewalDate->load(['createdByAdmin', 'createdByEmployee', 'updatedByAdmin', 'updatedByEmployee']);
        return view('admin.renewal-dates.show', compact('renewalDate'));
    }

    public function edit(RenewalDate $renewalDate)
    {
        return view('admin.renewal-dates.edit', compact('renewalDate'));
    }

    public function update(Request $request, RenewalDate $renewalDate)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'renewal_date' => 'required|date',
            'frequency' => 'required|in:yearly,quarterly,monthly',
            'amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,completed,cancelled'
        ], [
            'title.required' => 'عنوان الحدث مطلوب',
            'renewal_date.required' => 'تاريخ التجديد مطلوب',
            'renewal_date.date' => 'تاريخ التجديد يجب أن يكون تاريخ صحيح',
            'frequency.required' => 'تكرار الحدث مطلوب',
            'amount.numeric' => 'المبلغ يجب أن يكون رقم',
            'amount.min' => 'المبلغ لا يمكن أن يكون سالب'
        ]);

        try {
            $renewalDate->fill($request->all());
            $renewalDate->updated_by_admin = auth('admin')->id();

            // إعادة حساب التاريخ التالي للتجديد إذا تغير التاريخ الأساسي أو التكرار
            if ($renewalDate->isDirty(['renewal_date', 'frequency'])) {
                $renewalDate->next_renewal_date = $renewalDate->calculateNextRenewalDate();
            }

            $renewalDate->save();

            return redirect()->route('admin.renewal-dates.index')
                ->with('success', 'تم تحديث موعد التجديد بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الحدث: ' . $e->getMessage());
        }
    }

    // دالة الحذف - تم تعديلها لترجع redirect بدلاً من JSON
    public function destroy(RenewalDate $renewalDate)
    {
        try {
            $title = $renewalDate->title; // حفظ العنوان قبل الحذف
            $renewalDate->delete();

            return redirect()->route('admin.renewal-dates.index')
                ->with('success', "تم حذف الحدث '{$title}' بنجاح");
        } catch (\Exception $e) {
            return redirect()->route('admin.renewal-dates.index')
                ->with('error', 'حدث خطأ أثناء حذف الحدث: ' . $e->getMessage());
        }
    }

    public function calendar(Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);

        $renewalDates = RenewalDate::active()
            ->whereMonth('renewal_date', $month)
            ->whereYear('renewal_date', $year)
            ->orderBy('renewal_date', 'asc')
            ->get();

        return view('admin.renewal-dates.calendar', compact('renewalDates', 'month', 'year'));
    }

    public function markCompleted(RenewalDate $renewalDate)
    {
        try {
            $renewalDate->update([
                'status' => 'completed',
                'updated_by_admin' => auth('admin')->id()
            ]);

            return response()->json(['success' => true, 'message' => 'تم تحديد الحدث كمكتمل']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء التحديث']);
        }
    }

    public function renew(RenewalDate $renewalDate)
    {
        try {
            // إنشاء حدث تجديد جديد
            $newRenewal = $renewalDate->replicate();
            $newRenewal->renewal_date = $renewalDate->next_renewal_date ?? $renewalDate->calculateNextRenewalDate();
            $newRenewal->next_renewal_date = $newRenewal->calculateNextRenewalDate();
            $newRenewal->notification_sent = false;
            $newRenewal->last_notification_date = null;
            $newRenewal->status = 'active';
            $newRenewal->created_by_admin = auth('admin')->id();
            $newRenewal->updated_by_admin = null;
            $newRenewal->save();

            // تحديث الحدث الحالي كمكتمل
            $renewalDate->update([
                'status' => 'completed',
                'updated_by_admin' => auth('admin')->id()
            ]);

            return response()->json(['success' => true, 'message' => 'تم تجديد الحدث بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء التجديد']);
        }
    }
}
