<?php

namespace App\Http\Controllers;

use App\Models\LedgerEntries;
use App\Models\PaymentMaster;
use App\Models\Bank;
use DB;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function paymentHistory(Request $request)
{
    $page_title = "Payment History";
    $filter = false;

    // 🔹 Start query with relationships
    $query = PaymentMaster::with(['customer', 'company', 'user', 'expenses']);

    // 🗓️ 1. Date Range Filter
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $filter = true;
        $query->whereBetween('payment_date', [$request->start_date, $request->end_date]);
    } elseif ($request->filled('start_date')) {
        $filter = true;
        $query->whereDate('payment_date', '>=', $request->start_date);
    } elseif ($request->filled('end_date')) {
        $filter = true;
        $query->whereDate('payment_date', '<=', $request->end_date);
    }

    // 💳 2. Method Filter (cash / online)
    if ($request->filled('payMethod')) {
        $filter = true;
        $query->where('method', $request->payMethod);
    }

    if ($request->filled('pay_type')) {
        $filter = true;
        $query->where('flow_type', $request->pay_type);
    }

    // 🔍 3. Search Filter (invoice / customer / company / user / expenses)
    if ($request->filled('search')) {
        $filter = true;
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('invoice_no', 'like', "%{$search}%")
              ->orWhereHas('customer', function ($q2) use ($search) {
                  $q2->where('name', 'like', "%{$search}%")
                     ->orWhere('phone', 'like', "%{$search}%")
                     ->orWhere('wpnumber', 'like', "%{$search}%");
              })
              ->orWhereHas('company', function ($q3) use ($search) {
                  $q3->where('name', 'like', "%{$search}%")
                     ->orWhere('phone', 'like', "%{$search}%")
                     ->orWhere('gst_number', 'like', "%{$search}%");
              })
              ->orWhereHas('user', function ($q4) use ($search) {
                  $q4->where('name', 'like', "%{$search}%")
                     ->orWhere('phone', 'like', "%{$search}%")
                     ->orWhere('wpnumber', 'like', "%{$search}%")
                     ->orWhere('role', 'like', "%{$search}%");
              })
              ->orWhereHas('expenses', function ($q5) use ($search) {
                  $q5->where('name', 'like', "%{$search}%");
              });
        });
    }

    $totalSaleAmount = null;
    if ($filter) {
        $totalSaleAmount = (clone $query)->sum('amount');
    }

    // 📄 5. Paginate results with query string preserved
    $payments = $query->latest()->paginate(20)->withQueryString(); // 🔥 ADDED

    // 📦 6. Return view
    return view('admin.payment.paymentList', [
        'page_title' => $page_title,
        'payments' => $payments,
        'totalSaleAmount' => $totalSaleAmount,
        'search' => $request->search,
    ]);
}



    public function paymentCreate()
    {
        $page_title = "Make Payment";
        $banks=Bank::orderBy('name')->get();
        $data = compact('page_title','banks');
        return view('admin.payment.makePayment')->with($data);
    }

    public function paymentStore(Request $request)
    {
        $request->validate([
            'entry_member' => 'required',
            'member_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'pay_method' => 'required|in:cash,online',
            'pay_type' => 'required|in:inflow,outflow',
            'date' => 'required|date',
        ]);
        if($request->pay_method=='online')
        {
            $request->validate([
                'bank_id' => 'required|integer|exists:banks,id',
            ]);
        }

        // dd($request->all());

        try {
            DB::beginTransaction();
            $payment = new PaymentMaster();
            $payment->flow_type = $request->pay_type;
            if ($request->entry_member == 'company') {
                $payment->company_id = $request->member_id;
            } elseif ($request->entry_member == 'customer') {
                $payment->customer_id = $request->member_id;
            } elseif ($request->entry_member == 'expenses') {
                $payment->expenses_id = $request->member_id;
            } else {
                $payment->user_id = $request->member_id;
            }
            $payment->amount = $request->amount;
            $payment->method = $request->pay_method;
            $payment->bank_id = $request->bank_id;
            $payment->payment_date = $request->date;
            $payment->remark = $request->description;

            if (isset($request->bank_id)) {
                $bank=Bank::find($request->bank_id);
                $bank_last_balance=$bank->amount;
                $payment->bank_id = $request->bank_id;

                if ($request->pay_type == 'inflow') {
                    $payment->last_bank_balance = $bank_last_balance + $request->amount;
                    $bank->amount=$bank_last_balance + $request->amount;
                }
                elseif($request->pay_type == 'outflow'){
                    $payment->last_bank_balance = $bank_last_balance - $request->amount;
                    $bank->amount=$bank_last_balance - $request->amount;
                }else{
                    return "somthing Error";
                }
                $bank->save();
            }
            $payment->save(); 

            if ($request->entry_member == 'expenses') {
                DB::commit();
                return redirect()->route('payment.history')->with('success', 'Payment expenses recorded successfully.');
            }
            $ledger = new LedgerEntries();
            // $ledger->entity_type = $request->entry_member;
            if ($request->entry_member == 'company') {
                $ledger->entity_type = 'company';
                $ledger->company_id = $request->member_id;
                $lastBalance = LedgerEntries::where('company_id', $request->member_id)
                    ->latest('id')
                    ->value('balance_after') ?? 0;
                if ($request->pay_type == 'inflow') {
                    $newBalance = $lastBalance + $request->amount;
                }
                if ($request->pay_type == 'outflow') {
                    $newBalance = $lastBalance - $request->amount;
                }
            } elseif ($request->entry_member == 'customer') {
                $ledger->entity_type = 'customer';
                $ledger->customer_id = $request->member_id;
                $lastBalance = LedgerEntries::where('customer_id', $request->member_id)
                    ->latest('id')
                    ->value('balance_after') ?? 0;
                if ($request->pay_type == 'inflow') {
                    $newBalance = $lastBalance - $request->amount;
                }
                if ($request->pay_type == 'outflow') {
                    $newBalance = $lastBalance + $request->amount;
                }
            } else {
                $ledger->user_id = $request->member_id;
                $ledger->entity_type = 'user';
                $lastBalance = LedgerEntries::where('user_id', $request->member_id)
                    ->latest('id')
                    ->value('balance_after') ?? 0;
                if ($request->pay_type == 'inflow') {
                    $newBalance = $lastBalance + $request->amount;
                }
                if ($request->pay_type == 'outflow') {
                    $newBalance = $lastBalance - $request->amount;
                }
            }
            $ledger->transaction_type = 'payment';
            if ($request->pay_type == 'inflow') {
                $ledger->type = 'credit';
            } else {
                $ledger->type = 'debit';
            }
            $ledger->amount = $request->amount;
            $ledger->description = $request->description;
            $ledger->method = $request->pay_method;
            $ledger->date = $request->date;
            $ledger->balance_after = $newBalance;
            $ledger->save();
            DB::commit();
            return redirect()->route('payment.history')->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('fail', 'An error occurred while recording the payment: ' . $e->getMessage())->withInput();
        }
    }


}
