<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice PDF</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        .container {
            width: 100%;
            border: 1px solid #000;
            padding: 15px;
        }

        .w-full {
            width: 100%;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .mb-10 {
            margin-bottom: 10px;
        }

        .mt-10 {
            margin-top: 10px;
        }

        .border {
            border: 1px solid #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 6px;
            font-size: 12px;
        }

        th {
            background: #f0f0f0;
        }

        .sign-box {
            width: 240px;
            border: 1px solid #444;
            padding: 20px;
            margin-top: 20px;
            text-align: center;
        }

        .small {
            font-size: 11px;
        }
    </style>
</head>

<body>

    <div class="container">


        <p>Invoice No: {{ $invoice->invoice_number }}</p>
        
        @if($invoice->earned_reward_points > 0)
<p style="color:green;font-weight:bold;">
    Reward Point Earned :
    {{ round($invoice->earned_reward_points,2) }} Point
</p>
        @endif
        
        <p class="small">Sale Date: {{ $invoice->created_at->format('d-M-Y') }}</p>

        <h2 class="text-center bold" style="margin: 10px 0;">ICON COMPUTER</h2>

        <div class="text-center small">
            Fultata, Bthuadahari, Nadia, pin-741126, WB <br>
            Phone: 9332226500 / 8670569446 <br>
            GSTIN/UIN: 19ASHPR6782M1ZP <br>
            Email: iconcomputer741126@gmail.com
        </div>

        <br>

        {{-- Customer Details --}}
        <table class="w-full">
            <tr>
                <td width="50%">
                    @isset($invoice->cash_order)
                        <p class="bold">Cash</p>
                    @endisset

                    @isset($invoice->customer_id)
                        <p class="bold">Bill To</p>
                        <p>{{ $invoice->customer->name }}</p>
                        <p class="small">Address: {{ $invoice->customer->address }}</p>
                        <p class="small">{{ $invoice->customer->state }}, Pin: {{ $invoice->customer->pin }}</p>

                        @if ($invoice->customer->gst_no)
                            <p class="small">GST: {{ $invoice->customer->gst_no }}</p>
                        @endif
                    @endisset
                </td>

                <td width="50%">
                    @isset($invoice->customer_id)
                        <p class="small">Mobile: {{ $invoice->customer->phone }} / {{ $invoice->customer->wpnumber }}</p>

                        @isset($invoice->shipping_address)
                            <p class="small">{{ $invoice->shipping_address }}</p>
                        @endisset

                        @isset($invoice->shipping_pin)
                            <p class="small">{{ $invoice->shipping_pin }}</p>
                        @endisset
                    @endisset
                </td>
                <td>
                <div>
                <img src="{{asset('images/iconqr.jpeg')}}" alt="qr_code" height="100px" width="100px">
            </div>
                </td>
            </tr>
        </table>

        {{-- Product Table --}}
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>HSN/SAC</th>
                    <th>Price/Unit</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $i = 1;
                    $total_taxable = 0;
                @endphp

                @foreach ($products as $item)
                    <tr>
                        <td>{{ $i }}</td>
                        <td>
                            {{ $item->product->brand->name }} {{ $item->product->category->name }} <br>
                            <span class="small">Model: {{ $item->product->model }}</span><br>
                            <span class="small">Sl No: {{ $item->sl_no }}</span>
                        </td>
                        <td>
                            {{ $item->product->category->hsn_code }} <br>
                            <span class="small">Warranty: {{ $item->warranty }}</span>
                        </td>
                        <td>
                            @php
                                $taxable_price = $item->price - $item->price * ($item->gst / 100);
                            @endphp
                            ₹{{ round($taxable_price, 2) }} <br>
                            <span class="small">GST: {{ $item->gst }}%</span><br>
                            <span class="small">CGST: ₹{{ ($item->price * ($item->gst / 100)) / 2 }}</span><br>
                            <span class="small">SGST: ₹{{ ($item->price * ($item->gst / 100)) / 2 }}</span>
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td>₹{{ $item->total }}</td>
                    </tr>

                    @php
                        $total_taxable += $item->total;
                        $i++;
                    @endphp
                @endforeach
            </tbody>
        </table>

        {{-- Total Section --}}
        <table class="w-full mt-10">
            <tr>
                <td class="text-right">
                    @php
                    $subTotal = $total_taxable;
                    $deliveryCharge = $invoice->total_delivery_charges ?? 0;
                    $discount = $invoice->discount ?? 0;
                    $rewardUsed = $invoice->used_reward_points ?? 0;
                    $netAmount = ($subTotal + $deliveryCharge) - $discount - $rewardUsed;
                    @endphp
                    
                    <p class="bold">Sub Total: ₹{{ round($subTotal,2) }}</p>
                       @if($rewardUsed > 0)
                    <p style="color:red;font-weight:bold;">Reward Point Used :
                       -{{ round($rewardUsed,2) }}</p> 
                       @endif
                    <p class="small">Delivery Charges: ₹{{ round($deliveryCharge,2) }}</p>
                    <p class="small">Extra Discount: ₹{{ round($discount,2) }}</p>
                    <p class="bold">Net Amount: ₹{{ round($netAmount,2) }}</p>
                </td>
            </tr>
        </table>

        <p class="mt-10 bold">Amount in Words:</p>
        <p class="small italic">{{ $amount_in_words }}</p>

        {{-- Signature --}}
        <div class="sign-box">
            <p class="small bold">Admin Stamp & Signature</p>
        </div>

    </div>

</body>

</html>
