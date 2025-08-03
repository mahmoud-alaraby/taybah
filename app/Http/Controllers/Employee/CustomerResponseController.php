<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\CustomerResponse;
use App\Models\CustomerResponseCategory;
use Illuminate\Http\Request;

class CustomerResponseController extends Controller
{
 
    public function index(Request $request)
    {
        $categories = CustomerResponseCategory::all();
        $category_id = $request->category_id;
        $creator_source = $request->creator_source;
        $employeeId = auth('employee')->id();

        $query = CustomerResponse::query();

        if ($category_id) {
            $query->where('category_id', $category_id);
        }

        if ($creator_source === 'admin') {
            $query->where('created_by_type', 'admin');
        } elseif ($creator_source === 'me') {
            $query->where('created_by_type', 'employee')
                  ->where('created_by', $employeeId);
        } elseif ($creator_source === 'others') {
            $query->where('created_by_type', 'employee')
                  ->where('created_by', '!=', $employeeId);
        }

        $responses = $query->orderBy('id', 'desc')->get();

        return view('employee.customer-response.index', compact('responses', 'categories', 'category_id', 'creator_source'));
    }

    public function create()
    {
        $categories = CustomerResponseCategory::all();
        return view('employee.customer-response.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:customer_response_categories,id',
            'title'       => 'required|string|max:80',
            'body'        => 'required|string|max:1000',
        ]);
        CustomerResponse::create([
            'category_id'      => $request->category_id,
            'title'            => $request->title,
            'body'             => $request->body,
            'created_by'       => auth('employee')->id(),
            'created_by_type'  => 'employee',
        ]);
        return redirect()->route('employee.customer-response.index')->with('success','تمت الإضافة');
    }

    public function show(CustomerResponse $customerResponse)
    {
        return view('employee.customer-response.show', compact('customerResponse'));
    }

    public function edit(CustomerResponse $customerResponse)
    {
        $categories = CustomerResponseCategory::all();
        return view('employee.customer-response.edit', compact('customerResponse', 'categories'));
    }

    public function update(Request $request, CustomerResponse $customerResponse)
    {
        $request->validate([
            'category_id' => 'required|exists:customer_response_categories,id',
            'title'       => 'required|string|max:80',
            'body'        => 'required|string|max:1000',
        ]);
        $customerResponse->update($request->only('category_id','title','body'));
        return redirect()->route('employee.customer-response.index')->with('success','تم التعديل');
    }

    public function destroy(CustomerResponse $customerResponse)
    {
        $customerResponse->delete();
        return back()->with('success','تم الحذف');
    }
}
