<?php

namespace App\Http\Controllers;

use App\Models\ExternalService;
use Illuminate\Http\Request;

class ExternalServiceController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'phone'         => 'required|string|max:20',
            'name'          => 'required|string|max:255',
            'address'       => 'required|string',
            'pincode'       => 'required|string|max:10',
            'product'       => 'required|string',
            'serial'        => 'nullable|string|max:100',
            'budget'        => 'nullable|numeric|min:0',
            'final_cost'    => 'nullable|numeric|min:0',
            'receive_date'  => 'required|date|before_or_equal:today',
            'vendor'        => 'nullable|string|max:255',
            'sent_date'     => 'nullable|date',
            'back_date'     => 'nullable|date',
            'delivery_date' => 'nullable|date',
            'warranty'      => 'required|in:In Warranty,Out of Warranty',
        ]);

        // Prevent saving duplicate data within 3 days (72 hours)
        $threeDaysAgo = now()->subDays(3);
        $existingService = ExternalService::where('phone', $validated['phone'])
            ->where('created_at', '>=', $threeDaysAgo)
            ->where(function ($query) use ($validated) {
                $query->where('product', $validated['product']);
                if (!empty($validated['serial'])) {
                    $query->orWhere('serial', $validated['serial']);
                }
            })
            ->first();

        if ($existingService) {
            return response()->json([
                'success' => false,
                'message' => '⚠️ একই কাস্টমার ও প্রোডাক্টের ডাটা গত ৩ দিনের মধ্যে সেভ করা হয়েছে। ৩ দিনের মধ্যে পুনরায় সেভ করা যাবে না।',
            ], 422);
        }

        $validated['status'] = 'pending';

        $service = ExternalService::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'External Service record successfully saved.',
            'data'    => $service,
        ]);
    }

    public function index(Request $request)
    {
        $query = ExternalService::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('product', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $services = $query->latest('id')->get();

        return response()->json([
            'success' => true,
            'data'    => $services,
        ]);
    }

    public function counts()
    {
        return response()->json([
            'success'   => true,
            'total'     => ExternalService::count(),
            'pending'   => ExternalService::where('status', 'pending')->count(),
            'sent'      => ExternalService::where('status', 'sent')->count(),
            'back'      => ExternalService::where('status', 'back')->count(),
            'ready'     => ExternalService::where('status', 'ready')->count(),
            'delivered' => ExternalService::where('status', 'delivered')->count(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $service = ExternalService::findOrFail($id);

        $validated = $request->validate([
            'phone'         => 'sometimes|required|string|max:20',
            'name'          => 'sometimes|required|string|max:255',
            'address'       => 'nullable|string',
            'pincode'       => 'nullable|string|max:10',
            'product'       => 'sometimes|required|string',
            'serial'        => 'nullable|string|max:100',
            'budget'        => 'nullable|numeric|min:0',
            'final_cost'    => 'nullable|numeric|min:0',
            'receive_date'  => 'nullable|date',
            'vendor'        => 'nullable|string|max:255',
            'sent_date'     => 'nullable|date',
            'back_date'     => 'nullable|date',
            'delivery_date' => 'nullable|date',
            'warranty'      => 'nullable|string',
            'status'        => 'nullable|string',
            'remarks'       => 'nullable|string',
        ]);

        if (isset($validated['status'])) {
            $validated['status'] = strtolower($validated['status']);
        }

        $service->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'External Service successfully updated.',
            'data'    => $service,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $status = strtolower($request->status);
        $allowedStatuses = ['pending', 'sent', 'back', 'ready', 'delivered'];
        if (!in_array($status, $allowedStatuses)) {
            return response()->json(['success' => false, 'message' => 'Invalid status value'], 422);
        }

        $service = ExternalService::findOrFail($id);
        $service->status = $status;
        $service->save();

        return response()->json(['success' => true, 'status' => $status]);
    }

    public function updateField(Request $request, $id)
    {
        $allowedFields = ['vendor', 'final_cost', 'budget', 'serial', 'product', 'phone', 'name', 'address', 'pincode', 'receive_date', 'sent_date', 'back_date', 'delivery_date', 'warranty', 'remarks'];

        $request->validate([
            'field' => 'required|string|in:' . implode(',', $allowedFields),
            'value' => 'nullable|string|max:255',
        ]);

        $service = ExternalService::findOrFail($id);
        $service->{$request->field} = $request->value;

        // Auto update status based on date fields if applicable
        if ($request->field === 'sent_date' && !empty($request->value) && $service->status === 'pending') {
            $service->status = 'sent';
        } elseif ($request->field === 'back_date' && !empty($request->value) && in_array($service->status, ['pending', 'sent'])) {
            $service->status = 'back';
        } elseif ($request->field === 'delivery_date' && !empty($request->value) && in_array($service->status, ['pending', 'sent', 'back', 'ready'])) {
            $service->status = 'delivered';
        }

        $service->save();

        return response()->json(['success' => true, 'data' => $service]);
    }

    public function destroy($id)
    {
        ExternalService::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }
}
