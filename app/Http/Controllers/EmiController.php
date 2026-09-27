<?php

namespace App\Http\Controllers;

use App\Models\Emi;
use App\Models\Finance;
use App\Models\Bank;
use App\Models\PaymentMaster;
use App\Models\LedgerEntries;
use DB;
use Illuminate\Http\Request;

class EmiController extends Controller
{
    public function index(Request $request)
    {
        $page_title = "EMI List";
        $search = $request->input('search');
        $input_finance = $request->input('input_finance');
        $status = $request->input('status') ?? null;
    
        $finances = DB::table('finance')->orderBy('name')->get();
    
        // Base query with customer relationship
        $query = Emi::with('customer')->latest();
    
        // Apply search filter if provided
        if (!empty($input_finance)) {
            $query->where('emi_id', '=', $input_finance);
        }
    
        if (!empty($status)) {
            $query->where('status', '=', $status);
        }
    
        if (!empty($search)) {
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
    
        // 🔥 ONLY ADDITION
        $emis = $query->paginate(20)->withQueryString();
    
        $data = compact(
            'finances',
            'emis',
            'page_title',
            'search',
            'input_finance',
            'status'
        );
    
        return view('admin.emi.emiList')->with($data);
    }
    

    public function emiClear(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'emi_id' => 'required|exists:emi,id',
            'clear_date' => 'required|date',
            'bank_id' => 'required',
        ]);
        try {
            DB::beginTransaction();
        
        $emi = Emi::findOrFail($request->emi_id);
        $emi->status = 'completed';
        $emi->emi_clear_date = $request->clear_date;
        $emi->save();

        $bank_data=Bank::find($request->bank_id);
        $bankBalance=$bank_data->amount;
        $bank_data->amount=$bankBalance + $emi->amount;

        $payment= new PaymentMaster();
        $payment->flow_type='inflow';
        $payment->invoice_no=$emi->sale->invoice_number;
        $payment->invoice_type='sale';
        $payment->customer_id=$emi->customer_id;
        $payment->emi_company_id=$emi->emi_id;
        $payment->amount=$emi->amount;
        $payment->bank_id=$emi->amount;
        $payment->last_bank_balance = $bankBalance + $emi->amount;
        $payment->method='online';
        $payment->payment_date=$request->clear_date;
        $payment->save();
        $bank_data->save();

        $lastBalance = LedgerEntries::where('customer_id', $emi->customer_id)
        ->latest('id')
        ->value('balance_after') ?? 0;

        $newBalance = $lastBalance - $emi->amount;
        $method = 'online';

        LedgerEntries::create([
            'entity_type' => 'customer',
            'customer_id' => $emi->customer_id,
            'invoice_id' => $emi->sale->invoice_number,
            'transaction_type' => 'payment',
            'method' => $method,
            'type' => 'credit',
            'amount' => $emi->amount,
            'balance_after' => $newBalance,
        ]);

            DB::commit();
            return redirect()->route('emi.list')->with('success', 'EMI cleared successfully.');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('fail', 'An error occurred while clearing the EMI: ' . $th->getMessage());
        }
    }

    public function financeList()
    {
        $page_title = "Finance List";
        $finances =Finance::orderBy('name')->paginate(20);
        $data = compact('finances', 'page_title');
        return view('admin.emi.financeList')->with($data);
    }

    public function create()
    {
        $page_title = "Add Finance";
        $url=route('finance.store');
        return view('admin.emi.financeCreate', compact('page_title','url'));
    }

    public function financeStore(Request $request)
    {
        $request->validate([
            'finance' => 'required|unique:finance,name',
        ]);

        $finance = new Finance();
        $finance->name = $request->finance;
        $finance->save();

        return redirect()->route('finance.list')->with('success', 'Finance added successfully.');
    }

    public function financeEdit($id)
    {
        $finance = Finance::findOrFail($id);
        $page_title = "Edit Finance";
        $url=route('finance.update',$id);
        return view('admin.emi.financeCreate', compact('finance', 'page_title','url'));
    }

    public function financeUpdate(Request $request, $id)
    {
        $request->validate([
            'finance' => 'required|unique:finance,name,' . $id,
        ]);

        $finance = Finance::findOrFail($id);
        $finance->name = $request->finance;
        $finance->save();

        return redirect()->route('finance.list')->with('success', 'Finance updated successfully.');
    }

    public function pay(Request $request, $id)
    {
        $finance = Emi::findOrFail($id);
        $banks = Bank::all();
        $page_title = "Emi Pay";
        return view('admin.emi.pay', compact('finance', 'banks', 'page_title'));
    }
}
