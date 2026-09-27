<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Customer;
use App\Models\LedgerEntries;
use App\Models\User;
use Illuminate\Http\Request;
use DB;

class LedgerController extends Controller
{
    public function addRemark( Request $request,$id)
    {
        $ledger=LedgerEntries::find($id);
        $ledger->remark=$request->remark;
        $ledger->save();

        return redirect()->route('report.customer.due')->with('success','Remark add success');
    }

    public function addRemarkPage($id)
    {
        $page_title= "Add remark page";
        $url=route('add.remark',$id);
        $data=compact('url','page_title');
        return view('admin.ledger.addRemark')->with($data);
    }


    public function ledger(Request $request)
    {
        $page_title = "Ledger Entries";
        $search = $request->input('search', '');

        // Base query with relationships
        $query = LedgerEntries::with(['customer', 'company', 'user']);

        // 🔍 Search by name or phone across customer, company, and user
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->orWhereHas('customer', function ($customerQuery) use ($search) {
                    $customerQuery->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('phone', 'LIKE', "%{$search}%");
                })
                    ->orWhereHas('company', function ($companyQuery) use ($search) {
                        $companyQuery->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('phone', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('phone', 'LIKE', "%{$search}%");
                    });
            });
        }

        // 🧾 Fetch paginated results
        $ladgers = $query->orderBy('id', 'asc')->paginate(20)->withQueryString();

        // Return view with data
        return view('admin.ledger.ledgerList', compact(
            'page_title',
            'ladgers',
            'search'
        ));
    }

    public function customerLedger()
    {
        $page_title = "Customer Ledger Entries";
        $ladgers = LedgerEntries::whereNotNull('customer_id')->orderBy('id', 'asc')->paginate(20)->withQueryString();
        $data = compact('page_title', 'ladgers');

        return view('admin.ledger.ledgerList')->with($data);
    }

    public function companyLedger()
    {
        $page_title = "Company Ledger Entries";
        $ladgers = LedgerEntries::whereNotNull('company_id')->orderBy('id', 'asc')->paginate(20)->withQueryString();
        $data = compact('page_title', 'ladgers');

        return view('admin.ledger.ledgerList')->with($data);
    }

    public function subDealerLedger()
    {
        $page_title = "User Ledger Entries";
        $ladgers = LedgerEntries::whereHas('user', function ($query) {
            $query->where('role', 'subdealer');
        })->orderBy('id', 'desc')->paginate(20)->withQueryString();
        $data = compact('page_title', 'ladgers');

        return view('admin.ledger.ledgerList')->with($data);
    }
    public function salesmanLedger()
    {
        $page_title = "User Ledger Entries";
        $ladgers = LedgerEntries::whereHas('user', function ($query) {
            $query->where('role', 'salesman');
        })->orderBy('id', 'asc')->paginate(20)->withQueryString();
        $data = compact('page_title', 'ladgers');

        return view('admin.ledger.ledgerList')->with($data);
    }

    public function ledgerShow($id)
    {
        $page_title = "Ledger Details";
        $record = LedgerEntries::find($id);

        if ($record->customer_id) {
            $id = $record->customer_id;
            $ladgers = LedgerEntries::with('customer')->where('customer_id', $id)->orderBy('id', 'asc')->paginate(30)->withQueryString();
        }
        if ($record->company_id) {
            $id = $record->company_id;
            $ladgers = LedgerEntries::with('company')->where('company_id', $id)->orderBy('id', 'asc')->paginate(30)->withQueryString();
        }
        if ($record->user_id) {
            $id = $record->user_id;
            $ladgers = LedgerEntries::with('user')->where('user_id', $id)->orderBy('id', 'asc')->paginate(30)->withQueryString();
        }
        $filter = true;

        $data = compact('page_title', 'ladgers', 'filter');

        return view('admin.ledger.ledgerList')->with($data);
    }

    public function dueCustomerReport(Request $request)
{
    $page_title = "Customer Due Report";
    $reset = route('report.customer.due');

    $search = $request->search;

    $ladgers = LedgerEntries::select('ledger_entries.*')
        ->join(
            DB::raw('(SELECT MAX(id) AS max_id FROM ledger_entries GROUP BY customer_id) AS latest'),
            'ledger_entries.id',
            '=',
            'latest.max_id'
        )
        ->join('customers', 'customers.id', '=', 'ledger_entries.customer_id')
        ->where('ledger_entries.balance_after', '>', 0)
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customers.name', 'LIKE', "%{$search}%")
                  ->orWhere('customers.phone', 'LIKE', "%{$search}%");
            });
        })
        ->orderBy('ledger_entries.id', 'DESC')
        // 🔥 ONLY ADDITION
        ->paginate(20)
        ->withQueryString();

    return view('admin.ledger.dueReport', compact('page_title', 'ladgers', 'reset', 'search'));
}

    public function dueCompanyReport(Request $request)
    {
        $page_title = "Company Due Report";
        $reset = route('report.company.due');

        $search = $request->search;

        // Latest ledger entry per company
        $query = LedgerEntries::select('ledger_entries.*')
            ->join(
                DB::raw('(SELECT MAX(id) AS max_id FROM ledger_entries GROUP BY company_id) AS latest'),
                'ledger_entries.id',
                '=',
                'latest.max_id'
            )
            ->whereNotNull('ledger_entries.company_id')
            ->where('ledger_entries.balance_after', '>', 0)
            ->orderBy('ledger_entries.id', 'DESC');

        // Search by company name or phone
        if (!empty($search)) {
            $query->whereHas('company', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $ladgers = $query->paginate(20)->withQueryString();

        return view('admin.ledger.dueReport', compact('page_title', 'ladgers', 'search', 'reset'));
    }

    public function previousEntry()
    {
        $page_title = "Previous Ledger Entries";
        // Customers that DO NOT have any ledger
        $customers = Customer::orderBy('name', 'asc')->get();

        // Companies that DO NOT have any ledger
        $companies = Company::orderBy('name', 'asc')->get();
        // Users
        $users = User::orderBy('name', 'asc')->get();

        // Merge all types as "members"
        $members = collect();

        foreach ($customers as $customer) {
            $members->push([
                'type' => 'customer',
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->phone,
            ]);
        }

        foreach ($companies as $company) {
            $members->push([
                'type' => 'company',
                'id' => $company->id,
                'name' => $company->name,
                'phone' => $company->phone,
            ]);
        }

        foreach ($users as $user) {
            $members->push([
                'type' => 'user',
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
            ]);
        }


        return view('admin.ledger.priviousLedger', compact('page_title', 'members'));
    }

    public function previousEntrySave(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required',
            'amount' => 'required|numeric|min:1',
        ]);

        // member_id = "company_1" OR "customer_5"
        [$type, $id] = explode('_', $validated['member_id']);

        // Assign individual variables
        $type = trim($type);     // "company"
        $id = (int) $id;         // 1
        $amount = $validated['amount']; // 100

        // Example: you can now use them easily
        // Delete existing records for this member before creating the new previous entry
        if ($type === 'customer') {
            // LedgerEntries::where('customer_id', $id)->delete();
            LedgerEntries::create([
                'customer_id' => $id,
                'entity_type' => 'customer',
                'transaction_type' => 'privious',
                'balance_after' => $amount,
            ]);
        } elseif ($type === 'company') {
            // LedgerEntries::where('company_id', $id)->delete();
            LedgerEntries::create([
                'company_id' => $id,
                'entity_type' => 'company',
                'transaction_type' => 'privious',
                'balance_after' => $amount,
            ]);
        } elseif ($type === 'user') {
            // LedgerEntries::where('user_id', $id)->delete();
            LedgerEntries::create([
                'user_id' => $id,
                'entity_type' => 'user',
                'transaction_type' => 'privious',
                'balance_after' => $amount,
            ]);
        }

        return back()->with('success', "Ledger entry added for {$type} ID {$id} with amount {$amount}");
    }
}
