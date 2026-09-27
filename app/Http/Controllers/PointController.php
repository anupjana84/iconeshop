<?php

namespace App\Http\Controllers;

use App\Models\PointsHistories;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PointController extends Controller
{
    public function pointHistory(Request $request)
{
    $page_title = 'Point History';
    $flag=false;
    $search = $request->input('search', '');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    // Base query with user relation
    $query = PointsHistories::with('user');

    // 🔍 Search by user name, phone, or WhatsApp number
    if (!empty($search)) {
        $query->whereHas('user', function ($userQuery) use ($search) {
            $userQuery->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('phone', 'LIKE', "%{$search}%")
                      ->orWhere('wpnumber', 'LIKE', "%{$search}%");
        });
        $flag=true;
    }

    // 📅 Filter by date range
    if (!empty($startDate) && !empty($endDate)) {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $query->whereBetween('created_at', [$start, $end]);
        $flag=true;
    }

    // 🧾 Fetch paginated results
    $history = $query->latest()->paginate(20);

    $totalPoints=null;
    // 💰 Calculate total points (for filtered results)
    if ($flag) {
        $totalPoints = (clone $query)->sum('points');
    }

    // Pass all data to the view
    return view('admin.pointHistory.pointList', compact(
        'history',
        'page_title',
        'search',
        'startDate',
        'endDate',
        'totalPoints'
    ));
}
}
