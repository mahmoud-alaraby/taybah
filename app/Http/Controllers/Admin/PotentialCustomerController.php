<?php

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
    
        $query = PotentialCustomer::with('employee')->orderBy('created_at', 'desc');
    
        if ($employeeId) $query->where('employee_id', $employeeId);
        if ($classification) $query->withClassification($classification);
        if ($search) $query->search($search);
    
        $customers = $query->paginate(10);
    
        $employees = Employee::whereHas('roles.permissions', function($q){
            $q->where('name', 'potential_customers');
        })->where('status', 'active')->orderBy('name')->get();
    
        $classifications = PotentialCustomerClassification::getWithColors();
        $classificationStats = PotentialCustomer::getClassificationStats($employeeId);
    
        return view('admin.potential-customers.index', compact(
            'customers', 'employees', 'classifications', 'classificationStats', 'search', 'employeeId', 'classification'
        ));
    }
    
    public function create()
    {
        $employees = Employee::whereHas('roles.permissions', function($q){
            $q->where('name', 'potential_customers');
        })->where('status', 'active')->orderBy('name')->get();
    
        $classifications = PotentialCustomerClassification::getWithColors();
    
        return view('admin.potential-customers.addedit', compact('employees', 'classifications'));
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
            'notes' => 'nullable|string',
        ]);
    
        $customer = PotentialCustomer::create([
            'employee_id' => $request->employee_id,
            'customer_name' => $request->customer_name,
            'phone' => $request->phone,
            'work_description' => $request->work_description,
            'notes' => $request->notes,
        ]);
    
        if ($request->customer_classifications) {
            $customer->updateClassifications($request->customer_classifications);
        }
    
        return redirect()->route('admin.potential-customers.index')->with('success', 'تم إضافة العميل المحتمل بنجاح');
    }
    
    public function edit(PotentialCustomer $potentialCustomer)
    {
        $employees = Employee::whereHas('roles.permissions', function($q){
            $q->where('name', 'potential_customers');
        })->where('status', 'active')->orderBy('name')->get();
    
        $classifications = PotentialCustomerClassification::getWithColors();
    
        return view('admin.potential-customers.addedit', compact('potentialCustomer', 'employees', 'classifications'));
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
            'notes' => 'nullable|string',
        ]);
    
        $potentialCustomer->update([
            'employee_id' => $request->employee_id,
            'customer_name' => $request->customer_name,
            'phone' => $request->phone,
            'work_description' => $request->work_description,
            'notes' => $request->notes,
        ]);
    
        if ($request->customer_classifications) {
            $potentialCustomer->updateClassifications($request->customer_classifications);
        }
    
        return redirect()->route('admin.potential-customers.index')->with('success', 'تم تحديث العميل المحتمل بنجاح');
    }
    
    public function printReport(Request $request)
{
    $search = $request->get('search');
    $employeeId = $request->get('employee_id');
    $classificationName = $request->get('classification'); // لتصفية حسب تصنيف

    $query = \App\Models\PotentialCustomer::with('employee')
        ->orderBy('created_at', 'desc');

    if ($employeeId) {
        $query->where('employee_id', $employeeId);
    }

    if ($classificationName) {
        $query->withClassification($classificationName);
    }

    if ($search) {
        $query->search($search);
    }

    $customers = $query->get();

    // جلب الموظف المحدد (إن وجد)
    $selectedEmployee = $employeeId ? \App\Models\Employee::find($employeeId) : null;

    // جلب التصنيف المحدد (إن وجد)
    $selectedClassification = $classificationName ? \App\Models\PotentialCustomerClassification::where('name', $classificationName)->first() : null;

    // إحصائيات التصنيفات: مصفوفة من ['display_name'=>..., 'count'=>...]
    $classificationStats = \App\Models\PotentialCustomer::getClassificationStats($employeeId);

    // مجموع العملاء الكلي
    $totalCustomers = $customers->count();

    // تمرير كل المتغيرات إلى الفيو
    return view('admin.potential-customers.print-report', compact(
        'customers',
        'selectedEmployee',
        'selectedClassification',
        'search',
        'classificationStats',
        'totalCustomers'
    ));
}

    public function destroy(PotentialCustomer $potentialCustomer)
    {
        $potentialCustomer->delete();
        return redirect()->route('admin.potential-customers.index')->with('success', 'تم حذف العميل المحتمل بنجاح');
    }
}
