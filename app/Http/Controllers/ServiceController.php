<?php

namespace App\Http\Controllers;

use App\Models\ServiceCall;
use App\Models\ServiceStatus;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SalesItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function index1(Request $request)
    {
        $baseQuery = ServiceCall::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('case_id_1', 'like', "%{$search}%")
                        ->orWhere('case_id_2', 'like', "%{$search}%");
                });
            });

        // ⭐ কার্ডগুলোর জন্য সঠিক (পেজিনেশন-নিরপেক্ষ) কাউন্ট — search ফিল্টার প্রয়োগ করেই, কিন্তু status filter ছাড়া
        $totalCount = (clone $baseQuery)->count();
        $caseIdPendingCount = (clone $baseQuery)->whereNull('case_id_1')->count();
        $engineerVisitedCount = (clone $baseQuery)->where('status', 'Engineer Visited')->count();
        $completedCount = (clone $baseQuery)->where('status', 'Completed')->count();

        // ⭐ কার্ডে ক্লিক করলে যে ফিল্টার আসে, সেটা টেবিলে প্রয়োগ করা হচ্ছে
        $bookings = $baseQuery
            ->when($request->filter, function ($query, $filter) {
                switch ($filter) {
                    case 'case_id_pending':
                        $query->whereNull('case_id_1');
                        break;
                    case 'engineer_visited':
                        $query->where('status', 'Engineer Visited');
                        break;
                    case 'completed':
                        $query->where('status', 'Completed');
                        break;
                }
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.service.list2', [
            'bookings' => $bookings,
            'totalCount' => $totalCount,
            'caseIdPendingCount' => $caseIdPendingCount,
            'engineerVisitedCount' => $engineerVisitedCount,
            'completedCount' => $completedCount,
        ]);
    }

    public function serviceCallLogs(Request $request)
    {
        $page_title = 'Service Call Logs';

        // Get search keyword from query string (if any)
        $search = $request->input('search');

        // Base query
        $query = ServiceCall::query();

        // Apply search filter if keyword present
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('note', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Get results with pagination (latest first)
        $services = $query->orderBy('id', 'desc')
            ->paginate(25)
            ->withQueryString(); // 🔥 ADDED

        return view('admin.service.list', compact('page_title', 'services', 'search'));
    }


    public function pendingServiceCalls()
    {
        $page_title = 'Pending Service Calls';
        $services = ServiceCall::where('status', 'Pending')
            ->orderBy('id', 'desc')
            ->paginate(25);

        return view('admin.service.list', compact('page_title', 'services'));
    }

    public function completedServiceCalls()
    {
        $page_title = 'Completed Service Calls';
        $services = ServiceCall::where('status', 'Completed')
            ->orderBy('id', 'desc')
            ->paginate(25);

        return view('admin.service.list', compact('page_title', 'services'));
    }

    public function canceledServiceCalls()
    {
        $page_title = 'Canceled Service Calls';
        $services = ServiceCall::where('status', 'Canceled')
            ->orderBy('id', 'desc')
            ->paginate(25);

        return view('admin.service.list', compact('page_title', 'services'));
    }

    public function create($serviceCallId)
    {
        $page_title = 'Add Service Status';
        $url = route('service-status.store');
        $id = $serviceCallId;
        $serviceCall = ServiceCall::findOrFail($serviceCallId);
        return view('admin.service.call', compact('serviceCall', 'page_title', 'url', 'id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_call_id' => 'required|exists:service_calls,id',
            'company_remark' => 'nullable|string',
            'admin_remark' => 'nullable|string',
            'case_id' => 'nullable|string|max:255',
            'status' => 'required|in:Pending,Completed,Canceled',
            'call_date' => 'required|date',
            'image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);
        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('icon_computer/service_responce', 'public');
        }

        // Insert into service_status table
        $status = ServiceStatus::create([
            'service_call_id' => $request->service_call_id,
            'company_remark' => $request->company_remark,
            'image' => $request->image,
            'admin_remark' => $request->admin_remark,
            'case_id' => $request->case_id,
        ]);
        if ($request->hasFile('image')) {
            $status->image = $path;
            $status->save();
        }

        // Update status of ServiceCall
        ServiceCall::where('id', $request->service_call_id)
            ->update(['status' => $request->status, 'call_id' => $status->id, 'call_date' => $request->call_date]);

        return redirect()->route('service.call.logs')->with('success', 'Service status updated successfully!');
    }

    public function edit($id)
    {
        $page_title = 'Edit Service Status';
        $url = route('service-status.update', $id);
        $status = ServiceStatus::with('serviceCall')->findOrFail($id);
        $serviceCall = $status->serviceCall;

        return view('admin.service.call', compact('status', 'serviceCall', 'page_title', 'url', 'id'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'company_remark' => 'nullable|string',
            'admin_remark' => 'nullable|string',
            'case_id' => 'nullable|string',
            'status' => 'required|in:Pending,Completed,Canceled',
            'call_date' => 'required|date',
            'image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $status = ServiceStatus::findOrFail($id);

        $status->update([
            'company_remark' => $request->company_remark,
            'admin_remark' => $request->admin_remark,
            'case_id' => $request->case_id,
        ]);

        if ($request->hasFile('image')) {

            // Delete old image if exists
            if ($status->image && \Storage::disk('public')->exists($status->image)) {
                \Storage::disk('public')->delete($status->image);
            }

            // Upload new image
            $path = $request->file('image')->store('icon_computer/service_responce', 'public');

            // Save new path
            $status->image = $path;
            $status->save();
        }

        // Update Service Call Main Status
        $status->serviceCall->update([
            'status' => $request->status,
            'call_date' => $request->call_date,
        ]);

        return redirect()->route('service.call.logs')->with('success', 'Status updated successfully.');
    }

    // public function store(Request $request)
//     {
//         // ১. সার্ভিস কলের তথ্য ডাটাবেসে সেভ করা
//         $serviceCall = new \App\Models\ServiceCall();
//         $serviceCall->customer_name = $request->customer_name;
//         $serviceCall->phone         = $request->phone;
//         $serviceCall->product       = $request->product;
//         $serviceCall->case_id       = $request->case_id;
//         $serviceCall->remarks       = $request->remarks;

    //         // ডাটাবেসে স্থায়ীভাবে সেভ
//         $serviceCall->save(); 

    //         // ২. রিডাইরেক্ট করা
//         return redirect()->back()->with('success', 'রেকর্ড সফলভাবে সেভ হয়েছে!');
//     }
    public function byPhone(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'digits:10'],
        ]);

        $customer = Customer::where('phone', $request->phone)->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found'
            ]);
        }

        $sale = Sale::where('customer_id', $customer->id)->get();
        $salesItems = [];
        if ($sale) {
            //$salesItems = SalesItems::with(['product.brand'])->where('sale_id', $sale->id)->get();
            $salesItems = SalesItems::with(['product.brand', 'product.category'])
                ->whereIn('sale_id', $sale->pluck('id'))
                ->get();
        }

        return response()->json([
            'success' => true,
            'customer' => [
                'phone' => $customer->phone,
                'name' => $customer->name,
                'address' => $customer->address,
                'pincode' => $customer->pin,
                'sale' => $sale,
                'saleitems' => $salesItems
            ]
        ]);
    }

    public function userservice()
    {
        $user = auth()->user();
        $customer = Customer::where('phone', $user->phone)->first();
        
        $saleitems = collect();
        if ($customer) {
            $sales = Sale::where('customer_id', $customer->id)->pluck('id');
            if ($sales->isNotEmpty()) {
                $saleitems = SalesItems::with(['product.brand', 'product.category'])
                    ->whereIn('sale_id', $sales)
                    ->get();
            }
        }

        return view('user.userservice', compact('user', 'customer', 'saleitems'));
    }

    public function bookings()
    {
        try {
            $bookings = ServiceCall::query()
                ->latest('id')
                ->get();

            return response()->json([
                'success' => true,
                'bookings' => $bookings,
            ]);
        } catch (\Exception $e) {
            \Log::error('Service bookings load error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'bookings' => [],
            ], 500);
        }
    }

    public function savedata(Request $request)
    {
        try {
            $validated = $request->validate([
                'phone' => ['required', 'regex:/^[6-9][0-9]{9}$/'],
                'name' => ['required', 'string', 'max:255'],
                'address' => ['required', 'string', 'max:500'],
                'pin' => ['required', 'regex:/^[0-9]{6}$/'],
                'bill_date' => ['nullable', 'date'],
                'call_date' => ['required', 'date'],

                'product_id' => ['nullable', 'integer'],
                'sl_no' => ['nullable', 'string', 'max:255'],
                'product_name' => ['required', 'string', 'max:255'],
                'serial_no' => ['required', 'string', 'max:255'],

                'product_id_2' => ['nullable', 'integer'],
                'sl_no_2' => ['nullable', 'string', 'max:255'],
                'product_name_2' => ['nullable', 'string', 'max:255'],
                'serial_no_2' => ['nullable', 'string', 'max:255'],

                'case_id_1' => ['nullable', 'string', 'max:255'],
                'case_id_2' => ['nullable', 'string', 'max:255'],

                'note' => ['nullable', 'string', 'max:1000'],

                'invoice_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            ]);

            $invoiceImages = [];

            if ($request->hasFile('invoice_image')) {
                $path = $request->file('invoice_image')
                    ->store('service-bookings/invoices', 'public');
                $invoiceImages[] = $path;
            }

            // Prevent saving duplicate data within 3 days (72 hours)
            $threeDaysAgo = now()->subDays(3);
            $existingBooking = ServiceCall::where('phone', $validated['phone'])
                ->where('created_at', '>=', $threeDaysAgo)
                ->where(function ($query) use ($validated) {
                    $hasCondition = false;
                    if (!empty($validated['serial_no'])) {
                        $query->where('serial_no', $validated['serial_no']);
                        $hasCondition = true;
                    }
                    if (!empty($validated['product_name'])) {
                        if ($hasCondition) {
                            $query->orWhere('product_name', $validated['product_name']);
                        } else {
                            $query->where('product_name', $validated['product_name']);
                            $hasCondition = true;
                        }
                    }
                    if (!empty($validated['case_id_1'])) {
                        if ($hasCondition) {
                            $query->orWhere('case_id_1', $validated['case_id_1']);
                        } else {
                            $query->where('case_id_1', $validated['case_id_1']);
                            $hasCondition = true;
                        }
                    }
                    if (!$hasCondition) {
                        $query->where('phone', $validated['phone']);
                    }
                })
                ->first();

            if ($existingBooking) {
                return response()->json([
                    'success' => false,
                    'message' => '⚠️ একই কাস্টমার ও প্রোডাক্টের ডাটা গত ৩ দিনের মধ্যে সেভ করা হয়েছে। ৩ দিনের মধ্যে পুনরায় সেভ করা যাবে না।',
                ], 422);
            }

            $booking = ServiceCall::create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'pin' => $validated['pin'],
                'note' => $validated['note'] ?? null,
                'call_date' => $validated['call_date'] ?? now()->toDateString(),
                'invoice_image' => json_encode($invoiceImages),
                'status' => 'Pending',

                'bill_date' => $validated['bill_date'] ?? null,

                'product_id' => $validated['product_id'] ?? null,
                'sl_no' => $validated['sl_no'] ?? null,
                'product_name' => $validated['product_name'] ?? null,
                'serial_no' => $validated['serial_no'] ?? null,

                'product_id_2' => $validated['product_id_2'] ?? null,
                'sl_no_2' => $validated['sl_no_2'] ?? null,
                'product_name_2' => $validated['product_name_2'] ?? null,
                'serial_no_2' => $validated['serial_no_2'] ?? null,

                'case_id_1' => $validated['case_id_1'] ?? null,
                'case_id_2' => $validated['case_id_2'] ?? null,
                'case_id_date' => (!empty($validated['case_id_1']) || !empty($validated['case_id_2'])) ? ($validated['call_date'] ?? now()->toDateString()) : null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'কল বুকিং সফলভাবে সেভ হয়েছে।',
                'booking' => $booking,
            ]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => implode(', ', \Illuminate\Support\Arr::flatten($ve->errors())),
                'errors' => $ve->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Savedata Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'ডাটা সেভ করতে সমস্যা হয়েছে: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function updateCaseId(Request $request, $id)
    {
        // ইনপুট ভ্যালিডেশন
        $validator = Validator::make($request->all(), [
            'case_id_1' => 'required|string|max:100',
        ], [
            'case_id_1.required' => 'কেস আইডি অবশ্যই দিতে হবে।',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // বুকিং খুঁজে বের করা
        $booking = ServiceCall::find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'বুকিং খুঁজে পাওয়া যায়নি।',
            ], 404);
        }

        if ($booking->status === 'Completed') {
            return response()->json([
                'success' => false,
                'message' => 'কমপ্লিট হওয়া বুকিং আর পরিবর্তন করা যাবে না।',
            ], 422);
        }

        $caseDate = $booking->call_date ?? now()->toDateString();

        // ইতিমধ্যে কেস আইডি থাকলে দ্বিতীয়বার দিলে case_id_2 তে সেভ হবে
        if (empty($booking->case_id_1)) {
            $booking->case_id_1 = $request->case_id_1;
            $booking->case_id_date = $caseDate;
        } elseif (empty($booking->case_id_2)) {
            $booking->case_id_2 = $request->case_id_1;
            $booking->case_id_date = $caseDate;
        } else {
            return response()->json([
                'success' => false,
                'message' => 'এই বুকিংয়ে ইতিমধ্যে দুটি কেস আইডি যুক্ত আছে।',
            ], 422);
        }

        if ($request->filled('remarks')) {
            $booking->remarks = $booking->remarks ? ($booking->remarks . ' | ' . $request->remarks) : $request->remarks;
        }

        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'কেস আইডি সফলভাবে সেভ হয়েছে।',
            'case_id_1' => $booking->case_id_1,
            'case_id_2' => $booking->case_id_2,
            'case_id_date' => $booking->case_id_date,
        ]);
    }
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Pending,Engineer Visited,Completed,Canceled',
        ], [
            'status.required' => 'স্ট্যাটাস অবশ্যই দিতে হবে।',
            'status.in' => 'অবৈধ স্ট্যাটাস।',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $booking = ServiceCall::find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'বুকিং খুঁজে পাওয়া যায়নি।',
            ], 404);
        }

        if ($booking->status === 'Completed') {
            return response()->json([
                'success' => false,
                'message' => 'কমপ্লিট হওয়া বুকিং আর পরিবর্তন করা যাবে না।',
            ], 422);
        }

        if ($request->status === 'Pending' && $booking->status !== 'Pending') {
            return response()->json([
                'success' => false,
                'message' => 'আগের স্ট্যাটাসে Pending ফিরিয়ে নেওয়া যাবে না।',
            ], 422);
        }

        $booking->status = $request->status;
        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'স্ট্যাটাস সফলভাবে আপডেট হয়েছে।',
            'status' => $booking->status,
        ]);
    }

    public function destroy($id)
    {
        try {
            $booking = ServiceCall::find($id);
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'বুকিং খুঁজে পাওয়া যায়নি।',
                ], 404);
            }

            $booking->delete();

            return response()->json([
                'success' => true,
                'message' => 'কল বুকিং রেকর্ড সফলভাবে ডিলিট করা হয়েছে।',
            ]);
        } catch (\Exception $e) {
            \Log::error('Delete ServiceCall Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'ডিলিট করতে সমস্যা হয়েছে: ' . $e->getMessage(),
            ], 500);
        }
    }

}
