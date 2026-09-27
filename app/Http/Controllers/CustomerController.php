<?php

namespace App\Http\Controllers;

use App\Exports\CustomersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function customersList(Request $request)
{
    $page_title = 'Customer List';
    $search = $request->search;

    $customers = Customer::query()
        ->when($search, function ($q) use ($search) {
            $q->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('wpnumber', 'LIKE', "%{$search}%")
                  ->orWhere('address', 'LIKE', "%{$search}%")
                  ->orWhere('pin', 'LIKE', "%{$search}%");
            });
        })
        ->with('salesman', 'salesman.user')
        ->latest()
        ->paginate(15)
        ->withQueryString();

    return view('admin.customer.customersList', compact(
        'page_title',
        'customers',
        'search'
    ));
}

// ✅ EXPORT EXCEL (FIXED)
    public function export()
    {
        return Excel::download(new CustomersExport, 'customers.xlsx');
    }
   
    public function customersRemove($id)
    {
        $customer = Customer::find($id);
        if ($customer) {
            try {
                $customer->delete();
            return redirect()->back()->with('success', 'Customer deleted successfully.');
            } catch (\Throwable $th) {
                return redirect()->back()->with('fail', 'Customer can\'t deleted.');
            }
        } else {
            return redirect()->back()->with('fail', 'Customer not found.');
        }
    }

    public function customersCreate()
    {
        $page_title = 'Add Customer';
        return view('admin.customer.customerForm', compact('page_title'));
    }

    public function customersStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric|digits:10|unique:customers,phone',
            'wpnumber' => 'nullable|numeric|digits:10|unique:customers,wpnumber',
            'address' => 'nullable|string|max:500',
            'pin' => 'nullable|string|max:10',
            'gst_number' => 'nullable|string|max:20',
        ]);

        $customer = new Customer();
        $customer->name = $request->input('name');
        $customer->phone = $request->input('phone');
        $customer->wpnumber = $request->input('wpnumber');
        $customer->address = $request->input('address');
        $customer->pin = $request->input('pin');
        $customer->gst_number = $request->input('gst_number');
        $customer->save();

        return redirect()->route('customers.list')->with('success', 'Customer added successfully.');
    }

    public function customersEdit($id)
    {
        $page_title = 'Edit Customer';
        $customer = Customer::find($id);
        $data=compact('page_title', 'customer');
        if ($customer) {
            return view('admin.customer.customerForm')->with($data);
        } else {
            return redirect()->back()->with('fail', 'Customer not found.');
        }
    }

    public function customersUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric|digits:10',
            'wpnumber' => 'nullable|numeric|digits:10',
            'address' => 'nullable|string|max:500',
            'pin' => 'nullable|string|max:10',
            'gst_number' => 'nullable|string|max:20',
        ]);

        $customer = Customer::find($id);
        if ($customer) {
            $customer->name = $request->input('name');
            $customer->phone = $request->input('phone');
            $customer->wpnumber = $request->input('wpnumber');
            $customer->address = $request->input('address');
            $customer->pin = $request->input('pin');
            $customer->gst_number = $request->input('gst_number');
            $customer->save();

            return redirect()->route('customers.list')->with('success', 'Customer updated successfully.');
        } else {
            return redirect()->back()->with('fail', 'Customer not found.');
        }
    }

    public function getByPhone($phone)
    {
        $customer = Customer::where('phone', $phone)->orWhere('wpnumber', $phone)->first();
        if ($customer) {
            return response()->json(['success' => true, 'data' => $customer]);
        }
        return response()->json(['success' => false]);
    }
}
