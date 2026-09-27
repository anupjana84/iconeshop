<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;
use App\Models\Sale;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function monthlyReport(Request $request)
    {
        $page_title = 'Monthly Report';
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->startOfYear();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfYear();

        $monthlyData = collect();

        // Generate monthly range
        $period = new \DatePeriod(
            $startDate->copy()->startOfMonth(),
            new \DateInterval('P1M'),
            $endDate->copy()->addMonth()->startOfMonth()
        );

        foreach ($period as $month) {
            // Use the current month in the loop, not the full year
            $start = Carbon::instance($month)->startOfMonth();
            $end = Carbon::instance($month)->endOfMonth();

            // Month label logic
            $monthLabel = ($startDate->year == $endDate->year)
                ? $start->format('F')
                : $start->format('M-y');

            // Queries (monthly-based)
            $purchase = DB::table('purchases')
                ->whereBetween('created_at', [$start, $end])
                ->sum('with_gst_total');

            $sale = DB::table('sales')
                ->whereBetween('created_at', [$start, $end])
                ->sum('total');

            $expenses = DB::table('payment_master')
                ->whereNotNull('expenses_id')
                ->where('flow_type', 'outflow')
                ->whereBetween('created_at', [$start, $end])
                ->sum('amount');

            $points = DB::table('points_histories')
                ->whereBetween('created_at', [$start, $end])
                ->sum('points');

            $payment_in = DB::table('payment_master')
                ->where('flow_type', 'inflow')
                ->whereBetween('created_at', [$start, $end])
                ->sum('amount');

            $payment_out = DB::table('payment_master')
                ->where('flow_type', 'outflow')
                ->whereBetween('created_at', [$start, $end])
                ->sum('amount');

            $online_out = DB::table('payment_master')
                ->where('flow_type', 'outflow')
                ->where('method', 'online')
                ->whereBetween('created_at', [$start, $end])
                ->sum('amount');

            $cash_out = DB::table('payment_master')
                ->where('flow_type', 'outflow')
                ->where('method', 'cash')
                ->whereBetween('created_at', [$start, $end])
                ->sum('amount');

            $profit = $sale - $purchase - $expenses - $points;

            $monthlyData->push([
                'month_label' => $monthLabel,
                'purchase' => $purchase,
                'sale' => $sale,
                'expenses' => $expenses,
                'points' => $points,
                'payment_in' => $payment_in,
                'payment_out' => $payment_out,
                'online_out' => $online_out,
                'cash_out' => $cash_out,
                'profit' => $profit,
            ]);
        }

        return view('admin.reports.monthly', compact('monthlyData', 'startDate', 'endDate', 'page_title'));
    }



    // -------------------- DAILY REPORT --------------------
    public function dailyReport(Request $request)
    {
        $page_title = 'Daily Report';
    
        // Get date range (default: current month)
        $startDate = $request->start_date
            ? Carbon::parse($request->start_date)->startOfDay()
            : now()->startOfMonth();
    
        $endDate = $request->end_date
            ? Carbon::parse($request->end_date)->endOfDay()
            : now()->endOfMonth();
    
        // 👇 Prevent showing future data
        $today = now()->endOfDay();
        if ($endDate->gt($today)) {
            $endDate = $today;
        }
    
        $dailyData = collect();
        $total_net_profit=0;
    
        // Generate day-by-day range
        $period = CarbonPeriod::create($startDate, $endDate);
    
        foreach ($period as $day) {
            $start = $day->copy()->startOfDay();
            $end = $day->copy()->endOfDay();
    
            $dayLabel = $day->format('d M y'); // Example: "10 Nov 2025"
    
            // ===== Query data for the day =====
            // $purchase = DB::table('purchases')
            //     ->whereBetween('created_at', [$start, $end])
            //     ->sum('with_gst_total');
    
            $sale = DB::table('sales')
                ->whereBetween('created_at', [$start, $end])
                ->sum('total');
    
            $expenses = DB::table('payment_master')
                ->whereNotNull('expenses_id')
                ->where('flow_type', 'outflow')
                ->whereBetween('created_at', [$start, $end])
                ->sum('amount');
            $points = DB::table('payment_master')
                ->whereNotNull('user_id')
                ->where('flow_type', 'outflow')
                ->whereBetween('created_at', [$start, $end])
                ->sum('amount');
    
            // $points = DB::table('points_histories')
            //     ->whereBetween('created_at', [$start, $end])
            //     ->sum('points');
    
            // $payment_in = DB::table('payment_master')
            //     ->where('flow_type', 'inflow')
            //     ->whereBetween('created_at', [$start, $end])
            //     ->sum('amount');
    
            // $payment_out = DB::table('payment_master')
            //     ->where('flow_type', 'outflow')
            //     ->whereBetween('created_at', [$start, $end])
            //     ->sum('amount');

            $sale_item_purchase_amount = DB::table('sales_items')
                ->whereBetween('created_at', [$start, $end])
                ->select(DB::raw('SUM(purchase_price * quantity) as total'))
                ->value('total');
    
            $online_in = DB::table('payment_master')
                ->where('flow_type', 'inflow')
                ->where('method', 'online')
                ->whereBetween('created_at', [$start, $end])
                ->sum('amount');
    
            $cash_in = DB::table('payment_master')
                ->where('flow_type', 'inflow')
                ->where('method', 'cash')
                ->whereBetween('created_at', [$start, $end])
                ->sum('amount');
    
            $net_profit = $sale - $sale_item_purchase_amount - $expenses - $points;
            $total_net_profit= $total_net_profit + $net_profit;

            $dailyData->push([
                'day_label' => $dayLabel,
                'sale_purchase' => $sale_item_purchase_amount,
                'sale' => $sale,
                'sale_profit'=> $sale - $sale_item_purchase_amount,
                'expenses' => $expenses,
                'points' => $points,
                'cash_in' => $cash_in,
                'online_in' => $online_in,
                'net_profit' => $net_profit,
            ]);
        }
    
        // 👇 Sort by latest date first
        $dailyData = $dailyData->sortByDesc(function ($item) {
            return Carbon::parse($item['day_label']);
        })->values();
    
        return view('admin.reports.daily', compact('dailyData', 'startDate', 'endDate', 'page_title','total_net_profit'));
    }

    public function getByDate($date)
    {
        $page_title="Details Sale Report";
        $not_show=true;
        // Convert "06 Dec 25" into Y-m-d
        $parsed = Carbon::createFromFormat('d M y', $date)->format('Y-m-d');
        $sales = Sale::whereDate('created_at', $parsed)->paginate();
        $data = compact('sales', 'page_title','parsed','not_show');
        return view('admin.sale.salesList')->with($data);
    }
    
}
