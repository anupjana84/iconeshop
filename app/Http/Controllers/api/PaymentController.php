<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMaster;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function userPayment($id)
    {
        $payment=PaymentMaster::where('user_id',$id)
        ->where('flow_type','=','outflow')->select('id','amount','method','payment_date','created_at')->latest()->get();
        $response = [
            'status' => 1,
            'message' => 'Payment Data get successfully',
            'payments' => $payment
        ];
        return response()->json($response, 200);
    }
}
