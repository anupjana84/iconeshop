<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $page_title = 'Company List';
        $search = $request->input('search');

        // Start query
        $query = Company::query();

        // Apply search if available
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('gst_number', 'like', "%{$search}%");
            });
        }

        // Get results (latest first)
        $companies = $query->latest()->paginate(10);

        return view('admin.company.index', compact('page_title', 'companies', 'search'));
    }

    public function create()
    {
        $page_title = 'Add Company';
        return view('admin.company.form', compact('page_title'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'gst_number' => 'nullable|string|max:30',
        ]);

        Company::create($validated);

        return redirect()->route('companies.list')->with('success', 'Company created successfully.');
    }

    public function edit($id)
    {
        $company = Company::findOrFail($id);
        $page_title = 'Edit Company';
        return view('admin.company.form', compact('company', 'page_title'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'gst_number' => 'nullable|string|max:30',
        ]);

        $company = Company::findOrFail($id);
        $company->update($validated);

        return redirect()->route('companies.list')->with('success', 'Company updated successfully.');
    }
    public function destroy($id)
{
    try {
        $company = Company::findOrFail($id);
        $company->delete();

        return redirect()
            ->route('companies.list')
            ->with('success', 'Company deleted successfully.');
    } catch (\Exception $e) {
        return redirect()
            ->route('companies.list')
            ->with('fail', 'Failed to delete company. Please try again.');
    }
}

}
