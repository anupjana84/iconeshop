<?php

namespace App\Http\Controllers;

use App\Models\Expenses;
use App\Models\PaymentMaster;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpensesController extends Controller
{
    public function expensesPaymentList(Request $request)
{
    $page_title = "Expenses Payment History";
    $search = $request->input('search', '');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    // Base query with relation
    $query = PaymentMaster::with('expenses')->whereNotNull('expenses_id');

    // 🔍 Search by expense name
    if (!empty($search)) {
        $query->whereHas('expenses', function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%");
        });
    }

    // 📅 Date range filter
    if (!empty($startDate) && !empty($endDate)) {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $query->whereBetween('payment_date', [$start, $end]);
    }

    // 🧾 Fetch paginated results
    $payments = $query->latest()->paginate(20);

    // Send data to the view
    return view('admin.expenses.expensesPaymentList', compact(
        'page_title',
        'payments',
        'search',
        'startDate',
        'endDate'
    ));
}

    public function expensesList()
    {
        $page_title = "Expenses List";
        $expenses = Expenses::orderBy('name')->paginate(20);
        $data = compact('page_title', 'expenses');
        return view('admin.expenses.expensesList')->with($data);
    }

    public function expensesCreate()
    {
        $page_title = "Make Expenses Type";
        
        $url = route('expenses.store');
        $data = compact('page_title', 'url');
        return view('admin.expenses.expenseCreate')->with($data);
    }

    public function expensesStore(Request $request)
    {
        $request->validate([
            'expenses' => 'required|unique:expenses,name',
        ]);

        Expenses::create([
            'name' => $request->expenses,
        ]);

        return redirect()->route('expenses.list')->with('success', 'Expenses payment recorded successfully.');
    }

    public function expensesEdit($id)
    {
        $expenses = Expenses::findOrFail($id);
        $page_title = "Edit Expenses";
        $url = route('expenses.update', $id);
        $data = compact('page_title', 'url', 'expenses');
        return view('admin.expenses.expenseCreate')->with($data);
    }

    public function expensesUpdate(Request $request, $id)
    {
        $request->validate([
            'expenses' => 'required|unique:expenses,name,' . $id,
        ]);

        $expenses = Expenses::findOrFail($id);
        $expenses->name = $request->expenses;
        $expenses->save();

        return redirect()->route('expenses.list')->with('success', 'Expenses updated successfully.');
    }
}
