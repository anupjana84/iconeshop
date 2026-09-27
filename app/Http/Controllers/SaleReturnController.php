<?php

namespace App\Http\Controllers;

use App\Models\LedgerEntries;
use App\Models\Product;
use App\Models\SaleReturnDetails;
use App\Models\SaleReturnMaster;
use App\Models\SalesItems;
use App\Models\Sale;
use App\Models\RewardPoint;
use App\Models\RewardPointTransaction;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class SaleReturnController extends Controller
{
    public function saleReturn(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:sales_items,id',
            'return_quantity' => 'required|integer|min:1',
        ]);
        try {
            DB::beginTransaction();
            // Fetch the sale item
            $saleItem = SalesItems::find($request->id);
            $saleItem->return_quantity += $request->return_quantity;
            $saleItem->save();

            $return = new SaleReturnMaster();
            $return->sale_id = $saleItem->sale_id;
            $return->return_date = now();
            $return->save();

            // FETCH SALE
            $sale =Sale::find(
            $saleItem->sale_id
            );

            // FETCH REWARD ACCOUNT
            $reward =RewardPoint::where(
           'mobile',
            $sale->reward_mobile
            )->first();

            $returnItem = new SaleReturnDetails();
            $returnItem->return_master_id = $return->id;
            $returnItem->saleItem_id = $request->id;
            $returnItem->product_id = $saleItem->product_id;
            $returnItem->quantity = $request->return_quantity;
            $returnItem->price = $saleItem->price;
            $returnItem->total = $request->return_quantity * $saleItem->price;
            $returnItem->save();

            $return->total += $returnItem->total;
            $return->save();

// REWARD REFUND
if ($reward) {

    /*
    |--------------------------------------------------------------------------
    | CHECK ALREADY REVERSED
    |--------------------------------------------------------------------------
    */

    $alreadyReverse = RewardPointTransaction::where(
        'sale_return_id',
        $return->id
    )
    ->where('transaction_type', 'reverse')
    ->exists();

    $alreadyRefund = RewardPointTransaction::where(
        'sale_return_id',
        $return->id
    )
    ->where('transaction_type', 'refund')
    ->exists();

    /*
    |--------------------------------------------------------------------------
    | REFUND USED POINT
    |--------------------------------------------------------------------------
    */

    if (
        !$alreadyRefund &&
        $sale->used_reward_points > 0
    ) {

        $reward->balance +=
            $sale->used_reward_points;

        $reward->total_refunded +=
            $sale->used_reward_points;

        RewardPointTransaction::create([

            'reward_point_id' =>
                $reward->id,

            'sale_return_id' =>
                $return->id,

            'sale_id' =>
                $sale->id,

            'mobile' =>
                $sale->reward_mobile,

            'transaction_type' =>
                'refund',

            'points' =>
                $sale->used_reward_points,

            'note' =>
                'Reward point refunded from sale return',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | REVERSE EARNED POINT
    |--------------------------------------------------------------------------
    */

    if (
        !$alreadyReverse &&
        $sale->earned_reward_points > 0
    ) {

        $reward->balance -=
            $sale->earned_reward_points;

        // PREVENT NEGATIVE
        if ($reward->balance < 0) {

            $reward->balance = 0;
        }

        RewardPointTransaction::create([

            'reward_point_id' =>
                $reward->id,

            'sale_return_id' =>
                $return->id,

            'sale_id' =>
                $sale->id,

            'mobile' =>
                $sale->reward_mobile,

            'transaction_type' =>
                'reverse',

            'points' =>
                $sale->earned_reward_points,

            'note' =>
                'Earned reward point reversed from sale return',
        ]);
    }

    $reward->save();
}

            $product=Product::find($saleItem->product_id);
            $product->stock += $request->return_quantity;
            $product->save();

            $lastBalance = LedgerEntries::where('customer_id', $saleItem->sale->customer_id)
                ->latest('id')
                ->value('balance_after') ?? 0;
            $newBalance = $lastBalance - $returnItem->total;
            LedgerEntries::create([
                'customer_id' => $saleItem->sale->customer_id,
                'entity_type' => 'customer',
                'invoice_id' => $saleItem->sale->sale_invoice_no,
                'transaction_type' => 'sale_return',
                'type' => 'credit',
                'amount' => $returnItem->total,
                'description' => 'sale return for sale ID: ' . $saleItem->master_id,
                'date' => now(),
                'balance_after' => $newBalance,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'sale return processed successfully.');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('fail', 'An error occurred while processing the return.');
        }
    }

    
public function returnList(Request $request)
{
    $page_title = 'Sale Return List';
    $flag = false;
    $search = $request->input('search', '');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    // Base query with relationships
    $query = SaleReturnMaster::with(['details.product', 'sale.customer']);

    // 🔍 Search by sale invoice number, customer name or phone
    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->orWhereHas('sale', function ($saleQuery) use ($search) {
                $saleQuery->where('invoice_number', 'LIKE', "%{$search}%")
                          ->orWhereHas('customer', function ($customerQuery) use ($search) {
                              $customerQuery->where('name', 'LIKE', "%{$search}%")
                                            ->orWhere('phone', 'LIKE', "%{$search}%");
                          });
            });
        });
        $flag = true;
    }

    // 📅 Filter by date range (if provided)
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
    $totalReturnAmount=null;
    if ($flag) {
        $totalReturnAmount = (clone $query)->sum('total');
    }
    // 🧾 Fetch paginated results
    $returns = $query->orderBy('id', 'desc')->paginate(20);

    // 💰 Calculate total return amount for filtered results

    // Pass all variables to view
    return view('admin.sale.saleReturn.returnList', compact(
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
        $page_title = 'Sale Return Details';
        $return = SaleReturnMaster::with('details.product', 'sale')->findOrFail($id);
        $returnItems = SaleReturnDetails::where('return_master_id', $id)->with('product')->get();
        $data= compact('page_title', 'return','returnItems');
        return view('admin.sale.saleReturn.returnShow')->with($data);
    }   
}
