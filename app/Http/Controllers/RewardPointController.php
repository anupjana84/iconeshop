<?php

namespace App\Http\Controllers;

use App\Models\RewardPoint;
use App\Models\RewardPointTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RewardPointController extends Controller
{

    // POINT HISTORY PAGE
    public function pointHistory(Request $request)
    {

        $page_title = 'Reward Point History';

        $flag = false;

        $search = $request->input('search', '');

        $startDate = $request->input('start_date');

        $endDate = $request->input('end_date');

        // QUERY
        $query = RewardPoint::query();

        // SEARCH BY MOBILE / NAME
        if (!empty($search)) {

            $query->where(function ($q) use ($search) {

                $q->where('customer_name', 'LIKE', "%{$search}%")

                    ->orWhere('mobile', 'LIKE', "%{$search}%");
            });

            $flag = true;
        }

        // DATE FILTER
        if (!empty($startDate) && !empty($endDate)) {

            $request->validate([

                'start_date' =>
                    'required|date',

                'end_date' =>
                    'required|date|after_or_equal:start_date',
            ]);

            $start =
                Carbon::parse($startDate)->startOfDay();

            $end =
                Carbon::parse($endDate)->endOfDay();

            $query->whereBetween(
                'created_at',
                [$start, $end]
            );

            $flag = true;
        }

        // DATA
        $history =
            $query->latest()->paginate(20);

        // TOTALS
        $totalEarned = 0;

        $totalUsed = 0;

        $totalBalance = 0;

        if ($flag) {

            $totalEarned =
                (clone $query)->sum('total_earned');

            $totalUsed =
                (clone $query)->sum('total_used');

            $totalBalance =
                (clone $query)->sum('balance');
        }

        return view(
            'admin.rewardPoint.rewardPoint',
            compact(
                'history',
                'page_title',
                'search',
                'startDate',
                'endDate',
                'totalEarned',
                'totalUsed',
                'totalBalance'
            )
        );
    }

    // FETCH BALANCE
    public function getBalance($mobile)
    {

        $reward =
            RewardPoint::where(
                'mobile',
                $mobile
            )->first();

        return response()->json([

            'balance' =>
                $reward
                ? $reward->balance
                : 0

        ]);
    }

    // DETAILS PAGE
    public function details($mobile)
{

    $page_title =
        'Reward Point Details';

    $reward =
        RewardPoint::where(
            'mobile',
            $mobile
        )->first();

    if (!$reward) {

        return back()->with(
            'fail',
            'Reward account not found.'
        );
    }

    $transactions =
        RewardPointTransaction::where(
            'mobile',
            $mobile
        )
        ->latest()
        ->paginate(50);

    return view(
        'admin.rewardPoint.rewardPointDetails',
        compact(
            'reward',
            'transactions',
            'page_title'
        )
    );
  }
}