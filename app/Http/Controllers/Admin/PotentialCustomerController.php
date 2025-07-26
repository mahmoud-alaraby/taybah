<?php
// app/Http/Controllers/Admin/PotentialCustomerController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PotentialCustomer;
use App\Models\PotentialCustomerClassification;
use App\Models\Employee;
use Illuminate\Http\Request;

class PotentialCustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $employeeId = $request->get('employee_id');
        $classification = $request->get('classification');

        // الحصول على العملاء المحتملين
        $query = PotentialCustomer::with('employee')
                                ->orderBy('created_at', 'desc');

        // فلترة بالموظف
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        // فلترة بالتصنيف
        if ($classification) {
            $query->withClassification($classification);
        }

        // البحث
        if ($search) {
            $query->search($search);
        }

        $customers = $query->paginate(15);

        // قائمة الموظفين والتصنيفات للفلترة
        $employees = Employee::active()->orderBy('name')->get();
        $classifications = PotentialCustomerClassification::getWithColors();

        // إحصائيات التصنيفات
        $classificationStats = PotentialCustomer::getClassificationStats($employeeId);

        return view('admin.potential-customers.index', compact(
            'customers',
            'employees',
            'classifications',
            'classificationStats',
            'search',
            'employeeId',
            'classification'
        ));
    }

    public function create()
    {
        $employees = Employee::active()->orderBy('name')->get();
        $classifications = PotentialCustomerClassification::getWithColors();
        
        return view('admin.potential-customers.addedit', compact(
            'employees', 
            'classifications'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
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
            'employee_id' => $request->employee_id,
            'customer_name' => $request->customer_name,
            'phone' => $request->phone,
            'work_description' => $request->work_description,
            'customer_classifications' => $request->customer_classifications ?? [],
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.potential-customers.index')
                       ->with('success', 'تم إضافة العميل المحتمل بنجاح');
    }

    public function edit(PotentialCustomer $potentialCustomer)
    {
        $employees = Employee::active()->orderBy('name')->get();
        $classifications = PotentialCustomerClassification::getWithColors();
        
        return view('admin.potential-customers.addedit', compact(
            'potentialCustomer',
            'employees', 
            'classifications'
        ));
    }

    public function update(Request $request, PotentialCustomer $potentialCustomer)
    {
        $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
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
            'employee_id' => $request->employee_id,
            'customer_name' => $request->customer_name,
            'phone' => $request->phone,
            'work_description' => $request->work_description,
            'notes' => $request->notes,
        ]);

        // تحديث التصنيفات مع تسجيل التاريخ
        $potentialCustomer->updateClassifications($request->customer_classifications ?? []);

        return redirect()->route('admin.potential-customers.index')
                       ->with('success', 'تم تحديث العميل المحتمل بنجاح');
    }

    public function destroy(PotentialCustomer $potentialCustomer)
    {
        $potentialCustomer->delete();

        return redirect()->route('admin.potential-customers.index')
                       ->with('success', 'تم حذف العميل المحتمل بنجاح');
    }

    public function updateClassifications(Request $request, PotentialCustomer $potentialCustomer)
    {
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
        $employeeId = $request->get('employee_id');
        $classification = $request->get('classification');

        $query = PotentialCustomer::with('employee')
                                ->orderBy('created_at', 'desc');

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        if ($classification) {
            $query->withClassification($classification);
        }

        if ($search) {
            $query->search($search);
        }

        $customers = $query->get();
        $selectedEmployee = $employeeId ? Employee::find($employeeId) : null;
        $selectedClassification = $classification ? PotentialCustomerClassification::where('name', $classification)->first() : null;
        $classificationStats = PotentialCustomer::getClassificationStats($employeeId);

        return view('admin.potential-customers.print', compact(
            'customers',
            'selectedEmployee',
            'selectedClassification',
            'classificationStats',
            'search'
        ));
    }

    public function getClassificationStats(Request $request)
    {
        $employeeId = $request->get('employee_id');
        $stats = PotentialCustomer::getClassificationStats($employeeId);
        
        return response()->json($stats);
    }

    public function bulkUpdateClassifications(Request $request)
    {
        $request->validate([
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:potential_customers,id',
            'action' => 'required|in:add,remove,replace',
            'classifications' => 'required|array',
            'classifications.*' => 'exists:potential_customer_classifications,name',
        ]);

        $customers = PotentialCustomer::whereIn('id', $request->customer_ids)->get();

        foreach ($customers as $customer) {
            switch ($request->action) {
                case 'add':
                    foreach ($request->classifications as $classification) {
                        $customer->addClassification($classification);
                    }
                    break;

                case 'remove':
                    foreach ($request->classifications as $classification) {
                        $customer->removeClassification($classification);
                    }
                    break;

                case 'replace':
                    $customer->updateClassifications($request->classifications);
                    break;
            }
        }

        return redirect()->back()->with('success', 'تم تحديث تصنيفات العملاء بنجاح');
    }
}