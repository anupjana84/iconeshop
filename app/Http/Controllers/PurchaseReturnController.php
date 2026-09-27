<?php

namespace App\Http\Controllers;

use App\Models\LedgerEntries;
use App\Models\Product;
use App\Models\PurchaseItems;
use App\Models\PurchaseReturnDetails;
use App\Models\PurchaseReturnMaster;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class PurchaseReturnController extends Controller
{
    public function purchaseReturn(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:purchase_items,id',
            'return_quantity' => 'required|integer|min:1',
        ]);
        try {
            DB::beginTransaction();
            // Fetch the purchase item
            $purchaseItem = PurchaseItems::find($request->id);
            $purchaseItem->return_quantity += $request->return_quantity;
            $purchaseItem->save();
            $return = new PurchaseReturnMaster();
            $return->purchase_id = $purchaseItem->master_id;
            $return->return_date = now();
            $return->save();

            $returnItem = new PurchaseReturnDetails();
            $returnItem->return_master_id = $return->id;
            $returnItem->purchaseItem_id = $request->id;
            $returnItem->product_id = $purchaseItem->product_id;
            $returnItem->quantity = $request->return_quantity;
            $returnItem->price = $purchaseItem->price;
            $returnItem->total = $request->return_quantity * $purchaseItem->price;
            $returnItem->save();

            $product = Product::find($purchaseItem->product_id);
            $product->stock -= $request->return_quantity;
            $product->save();

            $return->total += $returnItem->total;
            $return->save();

            $lastBalance = LedgerEntries::where('company_id', $purchaseItem->purchase->company_id)
                ->latest('id')
                ->value('balance_after') ?? 0;
            $newBalance = $lastBalance + $returnItem->total;
            LedgerEntries::create([
                'company_id' => $purchaseItem->purchase->company_id,
                'entity_type' => 'company',
                'invoice_id' => $purchaseItem->purchase->purchase_invoice_no,
                'transaction_type' => 'purchase_return',
                'type' => 'debit',
                'amount' => $returnItem->total,
                'description' => 'Purchase return for Purchase ID: ' . $purchaseItem->master_id,
                'date' => now(),
                'balance_after' => $newBalance,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Purchase return processed successfully.');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('fail', 'An error occurred while processing the return.');
        }
    }

    public function returnList(Request $request)
    {
        $page_title = "Purchase Return List";
        $flag = false;
        $search = $request->input('search', '');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Base query with necessary relationships
        $query = PurchaseReturnMaster::with(['details.product', 'purchase.company']);

        // 🔍 Search by return invoice or company name/phone
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('purchase.company', function ($companyQuery) use ($search) {
                    $companyQuery->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('phone', 'LIKE', "%{$search}%");
                });
            });
            $flag = true;
        }

        // 📅 Date range filter (if provided)
        if (!empty($startDate) && !empty($endDate)) {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();

            $query->whereBetween('return_date', [$start, $end]);
            $flag = true;
        }
        $totalReturnAmount = null;
        if ($flag) {
            $totalReturnAmount = (clone $query)->sum('total');
        }

        // 🧾 Paginate the filtered results
        $returns = $query->orderBy('return_date', 'desc')->paginate(20);

        // 💰 Calculate total return amount for current filtered results
        // (Use clone to avoid affecting pagination query)

        // Pass all data to the view
        return view('admin.purchase.purchaseReturn.returnList', compact(
            'page_title',
            'returns',
            'search',
            'startDate',
            'endDate',
            'totalReturnAmount'
        ));
    }

    public function returnShow($id)
    {
        $page_title = "Purchase Return Details";
        $return = PurchaseReturnMaster::with('details.product', 'purchase.company')->findOrFail($id);
        $returnItems = PurchaseReturnDetails::with('product')->where('return_master_id', $id)->get();
        $data = compact('page_title', 'return', 'returnItems');
        return view('admin.purchase.purchaseReturn.returnShow')->with($data);
    }
}