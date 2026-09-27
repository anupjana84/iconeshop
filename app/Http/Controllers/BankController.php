<?php

namespace App\Http\Controllers;
use App\Models\Bank;
use Illuminate\Http\Request;
use App\Models\PaymentMaster;

class BankController extends Controller
{
    public function bankCreate()
    {
        $page_title = "Make New Bank";
        $url = route('bank.store');
        $amountText="Amount";
        $data = compact('page_title', 'url','amountText');
        return view('admin.bank.bankCreate')->with($data);
    }
    public function bankEdit($id)
    {
        $page_title = "Adjustment Bank";
        $amountText="Adjustment Amount";
        $bank=Bank::find($id);
        $url = route('bank.update',$id);
        $data = compact('page_title', 'url','bank','amountText');
        return view('admin.bank.bankCreate')->with($data);
    }

    public function bankList()
    {
        $page_title = "Bank List";
        $expenses = Bank::orderBy('name')->paginate(20);
        $data = compact('page_title', 'expenses');
        return view('admin.bank.bankList')->with($data);
    }

    public function bankStore(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:banks,name',
            'amount' => 'nullable|numeric',
        ]);
        
        $bank= new Bank();
        $bank->name=$request->name;
        $bank->amount=$request->amount;
        $bank->save();

        return redirect()->route('bank.list')->with('success','New Bank created success');
    }

    public function bankUpdate(Request $request,$id)
    {
        $request->validate([
            'name' => 'required|unique:banks,name,'.$id,
            'amount' => 'nullable|numeric',
        ]);
        
        $bank= Bank::find($id);
        $bank->name=$request->name;
        $bank->amount=$request->amount;
        $bank->save();

        return redirect()->route('bank.list')->with('success','New Bank created success');
    }

    public function bankStatement($id)
    {
        $page_title="Bank Statement";
        $statements = PaymentMaster::where('bank_id',$id)
        ->orderBy('id', 'desc')
        ->get();
        $data=compact('page_title','statements');
        return view('admin.bank.bankStatement')->with($data);
    }

    public function addBalancePage($id)
    {
        $page_title="Add Balance";
        $remark=true;
        $amountText="Amount";
        $bank=Bank::find($id);
        $url = route('bank.addBalance',$id);
        $data = compact('page_title', 'url','bank','remark','amountText');
        return view('admin.bank.bankCreate')->with($data);
    }

    public function addBalance(Request $request,$id)
    {
        $request->validate([
            'amount'=>'required|numeric',
            'remark'=>'required|string',
        ]);

        $bank=Bank::find($id);
        $bankBalance=$bank->amount;
        $bank->amount=$bankBalance+$request->amount;
        $bank->save();

        $payment = PaymentMaster::create([
            'flow_type' => 'inflow',
            'amount' => $request->amount,
            'method' => 'online',
            'bank_id'=>$id,
            'remark'=>$request->remark,
            'last_bank_balance'=>$bankBalance+$request->amount,
        ]);
        return redirect()->route('bank.list')->with('success','Bank amount add successfully');
    }


}
