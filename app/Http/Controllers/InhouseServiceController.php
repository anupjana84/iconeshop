<?php

namespace App\Http\Controllers;

use App\Models\InhouseService;
use Illuminate\Http\Request;

class InhouseServiceController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'alt_phone' => 'nullable|string|max:20',
            'address' => 'required|string',
            'pincode' => 'required|string|max:10',
            'product' => 'required|string',
            'estimate' => 'nullable|numeric|min:0',
            'final_amount' => 'nullable|numeric|min:0',
            'receive_date' => 'required|date|before_or_equal:today',
            'delivery_date' => 'nullable|date',
            'warranty' => 'required|in:In Warranty,Out of Warranty',
        ]);

        // Prevent saving duplicate data within 3 days (72 hours)
        $threeDaysAgo = now()->subDays(3);
        $existingService = InhouseService::where('phone', $validated['phone'])
            ->where('created_at', '>=', $threeDaysAgo)
            ->where('product', $validated['product'])
            ->first();

        if ($existingService) {
            return response()->json([
                'success' => false,
                'message' => '⚠️ একই কাস্টমার ও প্রোডাক্টের ডাটা গত ৩ দিনের মধ্যে সেভ করা হয়েছে। ৩ দিনের মধ্যে পুনরায় সেভ করা যাবে না।',
            ], 422);
        }

        $validated['status'] = 'pending';

        $service = InhouseService::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'In-House Service record successfully saved.',
            'data' => $service
        ]);
    }
    
public function index(Request $request)
{
    $query = InhouseService::query();

    // Search
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('phone', 'like', "%{$search}%")
              ->orWhere('name', 'like', "%{$search}%")
              ->orWhere('product', 'like', "%{$search}%");
        });
    }

    // Status filter
    if ($request->filled('status') && $request->status !== 'all') {
        $query->where('status', $request->status);
    }

    $services = $query
        ->latest('id')
        ->get();

    return response()->json([
        'success' => true,
        'data' => $services
    ]);
}


public function counts()
{
    return response()->json([
        'success' => true,

        'total' => InhouseService::count(),

        'pending' => InhouseService::where(
            'status',
            'pending'
        )->count(),

        'ready' => InhouseService::where(
            'status',
            'ready'
        )->count(),

        'delivered' => InhouseService::where(
            'status',
            'delivered'
        )->count(),
    ]);
}

    public function update(Request $request, $id)
    {
        $service = InhouseService::findOrFail($id);

        $validated = $request->validate([
            'phone'        => 'sometimes|required|string|max:20',
            'name'         => 'sometimes|required|string|max:255',
            'alt_phone'    => 'nullable|string|max:20',
            'address'      => 'nullable|string',
            'pincode'      => 'nullable|string|max:10',
            'product'      => 'sometimes|required|string',
            'estimate'     => 'nullable|numeric|min:0',
            'final_amount' => 'nullable|numeric|min:0',
            'receive_date' => 'nullable|date',
            'delivery_date'=> 'nullable|date',
            'warranty'     => 'nullable|string',
            'status'       => 'nullable|string',
            'remarks'      => 'nullable|string',
        ]);

        $service->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'In-House Service successfully updated.',
            'data'    => $service,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $service = InhouseService::findOrFail($id);
        $service->status = $request->status;
        $service->save();

        return response()->json(['success' => true, 'status' => $service->status]);
    }

    public function updateField(Request $request, $id)
    {
        $allowedFields = ['estimate', 'final_amount', 'product', 'phone', 'name', 'address', 'pincode', 'receive_date', 'delivery_date', 'warranty', 'remarks'];

        $request->validate([
            'field' => 'required|string|in:' . implode(',', $allowedFields),
            'value' => 'nullable|string|max:255',
        ]);

        $service = InhouseService::findOrFail($id);
        $service->{$request->field} = $request->value;
        $service->save();

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        try {
            $service = InhouseService::find($id);
            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'ইন-হাউস সার্ভিস রেকর্ড খুঁজে পাওয়া যায়নি।',
                ], 404);
            }

            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'ইন-হাউস সার্ভিস রেকর্ড সফলভাবে ডিলিট করা হয়েছে।',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ডিলিট করতে সমস্যা হয়েছে: ' . $e->getMessage(),
            ], 500);
        }
    }
}