<?php
// app/Http/Controllers/Employee/PotentialCustomerController.php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\PotentialCustomer;
use App\Models\PotentialCustomerClassification;
use Illuminate\Http\Request;

class PotentialCustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $classification = $request->get('classification');

        // الحصول على العملاء المحتملين للموظف الحالي
        $query = PotentialCustomer::forCurrentEmployee()
                                ->orderBy('created_at', 'desc');

        // فلترة بالتصنيف
        if ($classification) {
            $query->withClassification($classification);
        }

        // البحث
        if ($search) {
            $query->search($search);
        }

        $customers = $query->paginate(15);

        // التصنيفات المتاحة
        $classifications = PotentialCustomerClassification::getWithColors();

        // إحصائيات التصنيفات للموظف الحالي
        $classificationStats = PotentialCustomer::getClassificationStats(auth('employee')->id());

        return view('employee.systems.potential-customers', compact(
            'customers',
            'classifications',
            'classificationStats',
            'search',
            'classification'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'work_description' => 'required|string|max:1000',
            'customer_classifications' => 'nullable|array',
            'customer_classifications.*' => 'exists:potential_customer_classifications,name',
            'notes' => 'nullable|string|max:1000',
        ], [
            'customer_name.required' => 'اسم العميل مطلوب',
            'phone.required' => 'رقم الجوال مطلوب',
            'work_description.required' => 'وصف العمل مطلوب',
            'customer_classifications.*.exists' => 'التصنيف المختار غير صحيح',
        ]);

        PotentialCustomer::create([
            'employee_id' => auth('employee')->id(),
            'customer_name' => $request->customer_name,
            'phone' => $request->phone,
            'work_description' => $request->work_description,
            'customer_classifications' => $request->customer_classifications ?? [],
            'notes' => $request->notes,
        ]);

        return redirect()->route('employee.potential-customers')
                       ->with('success', 'تم إضافة العميل المحتمل بنجاح');
    }

    public function edit(PotentialCustomer $potentialCustomer)
    {
        // التأكد من أن العميل يخص الموظف الحالي
        if ($potentialCustomer->employee_id !== auth('employee')->id()) {
            abort(403);
        }

        $classifications = PotentialCustomerClassification::getWithColors();
        
        return view('employee.systems.potential-customers-edit', compact(
            'potentialCustomer',
            'classifications'
        ));
    }

    public function update(Request $request, PotentialCustomer $potentialCustomer)
    {
        // التأكد من أن العميل يخص الموظف الحالي
        if ($potentialCustomer->employee_id !== auth('employee')->id()) {
            abort(403);
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'work_description' => 'required|string|max:1000',
            'customer_classifications' => 'nullable|array',
            'customer_classifications.*' => 'exists:potential_customer_classifications,name',
            'notes' => 'nullable|string|max:1000',
        ], [
            'customer_name.required' => 'اسم العميل مطلوب',
            'phone.required' => 'رقم الجوال مطلوب',
            'work_description.required' => 'وصف العمل مطلوب',
            'customer_classifications.*.exists' => 'التصنيف المختار غير صحيح',
        ]);

        $potentialCustomer->update([
            'customer_name' => $request->customer_name,
            'phone' => $request->phone,
            'work_description' => $request->work_description,
            'notes' => $request->notes,
        ]);

        // تحديث التصنيفات مع تسجيل التاريخ
        $potentialCustomer->updateClassifications($request->customer_classifications ?? []);

        return redirect()->route('employee.potential-customers')
                       ->with('success', 'تم تحديث العميل المحتمل بنجاح');
    }

    public function destroy(PotentialCustomer $potentialCustomer)
    {
        // التأكد من أن العميل يخص الموظف الحالي
        if ($potentialCustomer->employee_id !== auth('employee')->id()) {
            abort(403);
        }

        $potentialCustomer->delete();

        return redirect()->route('employee.potential-customers')
                       ->with('success', 'تم حذف العميل المحتمل بنجاح');
    }

    public function updateClassifications(Request $request, PotentialCustomer $potentialCustomer)
    {
        // التأكد من أن العميل يخص الموظف الحالي
        if ($potentialCustomer->employee_id !== auth('employee')->id()) {
            abort(403);
        }

        $request->validate([
            'classifications' => 'required|array',
            'classifications.*' => 'exists:potential_customer_classifications,name',
        ], [
            'classifications.required' => 'يجب اختيار تصنيف واحد على الأقل',
            'classifications.*.exists' => 'التصنيف المختار غير صحيح',
        ]);

        $potentialCustomer->updateClassifications($request->classifications);

        return redirect()->back()->with('success', 'تم تحديث تصنيفات العميل بنجاح');
    }

    public function printReport(Request $request)
    {
        $search = $request->get('search');
        $classification = $request->get('classification');

        $query = PotentialCustomer::forCurrentEmployee()
                                ->orderBy('created_at', 'desc');

        if ($classification) {
            $query->withClassification($classification);
        }

        if ($search) {
            $query->search($search);
        }

        $customers = $query->get();
        $selectedClassification = $classification ? PotentialCustomerClassification::where('name', $classification)->first() : null;
        $classificationStats = PotentialCustomer::getClassificationStats(auth('employee')->id());

        return view('employee.systems.potential-customers-print', compact(
            'customers',
            'selectedClassification',
            'classificationStats',
            'search'
        ));
    }

    public function getClassificationStats()
    {
        $stats = PotentialCustomer::getClassificationStats(auth('employee')->id());
        
        return response()->json($stats);
    }

    public function quickClassification(Request $request, PotentialCustomer $potentialCustomer)
    {
        // التأكد من أن العميل يخص الموظف الحالي
        if ($potentialCustomer->employee_id !== auth('employee')->id()) {
            abort(403);
        }

        $request->validate([
            'classification' => 'required|exists:potential_customer_classifications,name',
            'action' => 'required|in:add,remove',
        ]);

        if ($request->action === 'add') {
            $potentialCustomer->addClassification($request->classification);
            $message = 'تم إضافة التصنيف بنجاح';
        } else {
            $potentialCustomer->removeClassification($request->classification);
            $message = 'تم إزالة التصنيف بنجاح';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'classifications' => $potentialCustomer->fresh()->classifications_badges
        ]);
    }
}