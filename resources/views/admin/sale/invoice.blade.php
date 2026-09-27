@extends('layouts.main')
@push('page_title')
    <title>Customer Invoice</title>
@endpush
@section('content_page')
    <div class="m-auto w-full">

        <div class="flex justify-between p-4 w-full">
            <button class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center"
                onclick="printDiv('printableArea')">Print Invoice</button>
            @if (!isset($invoice->cash_order))
                <button onclick="sendWhatsApp({{ $invoice->id }})" class="bg-green-500 text-white py-2 px-4 rounded">
                    Send to <i class="fa-brands fa-whatsapp"></i>
                </button>
            @endif
        </div>

    </div>

    <div id="printableArea">
        <div class="max-w-4xl text-xs mx-auto bg-white p-2 shadow-lg rounded-lg pr-60">
            <div class="flex justify-between items-center pb-2">
                <div>
                    <p>Invoice No : {{ $invoice->invoice_number }}</p>
                    
                    @if($invoice->earned_reward_points > 0)
                    <p class="text-xs text-green-700 font-bold">Reward Point Earned :
                    <span class="text-sm">{{ round($invoice->earned_reward_points,2) }}
                    Point</span></p>
                    @endif
                    
                </div>
                <div class="text-xl">
                    Tax Invoice
                </div>
                <div class="text-right">
                    <p class="text-xs">Sale Date : {{ $invoice->created_at->format('d-M-Y') }}</p>
                </div>
            </div>
            <div>
                <p class="text-2xl font-bold text-center">ICON COMPUTER</p>
            </div>
            <div class="flex flex-col justify-center items-center text-xs">
                <p>Fultata, Bthuadahari, Nadia, pin-741126, WB</p>
                <p>Phone: 9332226500/8670569446</p>
                <p>GSTIN/UIN: 19ASHPR6782M1ZP</p>
                <p>Email: iconcomputer741126@gmail.com</p>
            </div>
            <div class="flex justify-between">
            <div class="mt-4 grid ml-5"> @isset($invoice->cash_order)
                    <p class="text-xl font-bold">Cash</p>
                    @endisset @isset($invoice->customer_id)
                    <div>
                        <h3 class="font-semibold">Bill To</h3>
                        <li class="text-xs">{{ $invoice->customer->name }}</li>
                        <li class="text-xs">Address : {{ $invoice->customer->address }} 
                        {{ $invoice->customer->state }}, Pin- {{ $invoice->customer->pin }} </li>
                        
                    </div>
                    @isset($invoice->customer->gst_number)
                            <li class="text-xs">GST : {{ $invoice->customer->gst_number}}</li>
                        @endisset
                    <li class="text-xs">Mobile No : {{ $invoice->customer->phone }} / WA No : {{ $invoice->customer->wpnumber }}</li>
                    
                    @if($invoice->reward_mobile)
                    <li class="text-xs">Reward Mobile :<span class="font-semibold">
                    {{ $invoice->reward_mobile }}
                    </span>
                    </li>
                    @endif
                    
                    @if($invoice->referral_mobile)
                    <li class="text-xs">Referral Mobile :<span class="font-semibold">
                    {{ $invoice->referral_mobile }}
                    </span>
                    </li>
                    @endif
                    
                    <p class="text-xs">
                        @isset($invoice->shipping_address)
                        Shipping Address : {{ $invoice->shipping_address }}, Pin-
                        @endisset
                        @isset($invoice->shipping_pin)
                            {{ $invoice->shipping_pin }}
                        @endisset
                    </p>
                @endisset
            </div> 
            <div class="pr-4">
                <img src="{{url('images/iconqr.jpeg')}}" alt="qr_code" height="100px" width="100px">
            </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full mt-1 border border-gray-300 text-xs">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="px-2 border">#</th>
                            <th class="px-2 border">Product</th>
                            <th class="px-2 border">HSN/SAC</th>
                            <th class="px-2 border">Price/Unit</th>
                            <th class="px-2 border">Qty</th>
                            <th class="px-2 border">Total</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs"> @php
                        $total_taxable = 0.0;
                        $i = 1;
                    @endphp @foreach ($products as $item)
                            <tr>
                                <td class="px-2 border">{{ $i }}</td>
                                <td class="px-2 border"> {{ $item->product->brand->name }}
                                    {{ $item->product->category->name }} <div class="text-xs text-gray-500">Model:
                                        {{ $item->product->model }}</div>
                                        <div class="text-xs text-gray-500">code:
                                            {{ $item->product->code }}</div>
                                    <div class="text-xs text-gray-500">Sl no: {{ $item->sl_no }}</div>
                                </td>
                                <td class="px-2 border">{{ $item->product->category->hsn_code }} <p> Warranty: <br /><span>
                                            {{ $item->warranty }}</span></p>
                                </td>
                                <td class="px-2 border">
                                    ₹
                                    @php
                                        $gstPercent = $item->gst;          // 18
                                        $priceWithGst = $item->price;      // 560
                                
                                        $taxable_price = $priceWithGst / (1 + ($gstPercent / 100));
                                        $gstAmount = $priceWithGst - $taxable_price;
                                        $cgst = $gstAmount / 2;
                                        $sgst = $gstAmount / 2;
                                    @endphp
                                
                                    {{ number_format($taxable_price, 2) }}
                                
                                    <p class="text-xs">
                                        GST: {{ $gstPercent }}<span class="text-gray-500">( % )</span>
                                    </p>
                                    <p class="text-xs">CGST: ₹{{ number_format($cgst, 2) }}</p>
                                    <p class="text-xs">SGST: ₹{{ number_format($sgst, 2) }}</p>
                                </td>
                                <td class="px-2 border">{{ $item->quantity }}</td>
                                <td class="px-2 border">₹{{ $item->total }}</td>
                            </tr> @php
                                $total_taxable = $total_taxable + $item->total;
                                $i++;
                            @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex justify-between flex-col">
                <div class="text-right">
                    
                    @php
                    $subTotal = $total_taxable;
                    $deliveryCharge = $invoice->total_delivery_charges ?? 0;
                    $discount = $invoice->discount ?? 0;
                    $rewardUsed = $invoice->used_reward_points ?? 0;
                    $netAmount = ($subTotal + $deliveryCharge) - $discount - $rewardUsed;
                    @endphp
                    
                    <p class="text-sm">Sub Total : <span class="text-sm font-bold">₹{{ round($subTotal,2) }}</span></p>
                    
                    @if($invoice->used_reward_points > 0)
                    <p class="text-sm text-red-600 font-bold">Reward Point Used :
                    -{{ round($rewardUsed,2) }}</p>
                    @endif
                    
                    <p class="text-xs">Total delivery Charges : ₹{{ round($deliveryCharge,2) }}</p>
                    <p class="text-xs">Extra Discount: ₹{{ round($discount,2) }}</p>
                    <p class="text-sm font-bold">Net Amount : ₹<span
                                id="final_amount">{{ round($netAmount,2) }}</span></p>
                </div>
                <div class="mt-4">
                    <p class="text-[9px] font-semibold">Total Amount in Words: <br><span class="italic text-xs"
                            id="word_amount"></span></p>
                </div>
            </div>
            <div class="mt-1 flex justify-end ">
                <div class="p-6 w-1/3 text-center flex justify-center items-end">
                    <div class="h-10 border-t border-gray-400 mt-4"></div>
                    <hr>
                    <p class="text-xs font-semibold">Admin Stamp & Signature</p>
                </div>
            </div>
        </div>
    </div>
    <!-- <div class="flex justify-center m-4"> <button
            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center"
            onclick="printDiv('printableArea')">Print Invoice</button> </div> -->

@endsection
@push('extra_js')
    <style>
        @media print {
            @page {
                padding-bottom: 400px;
                /* top-right-bottom-left padding for every print page */
            }
        }
    </style>
    <script>
        function printDiv(divId) {

            // Inject print CSS for bottom padding on every page
            const style = document.createElement("style");
            style.innerHTML = `
            @media print {
                @page {
                    margin-left: 10px !important;
                    margin-bottom: 340px !important; 
                }
            }
        `;
            document.head.appendChild(style);

            var divContent = document.getElementById(divId).innerHTML;
            var originalContent = document.body.innerHTML;

            document.body.innerHTML = divContent;
            window.print();
            document.body.innerHTML = originalContent;

            location.reload(); // To restore event listeners after printing
        }
    </script>


    {{-- number to text  --}}
    <script>
        window.onload = function(e) {
            let number = document.getElementById('final_amount').innerHTML;
            let amount = number2text(number);
            document.getElementById('word_amount').innerHTML = amount;
            // console.log(number);
            // console.log(amount);
        }

        function number2text(value) {
            var fraction = Math.round(frac(value) * 100);
            var f_text = "";

            if (fraction > 0) {
                f_text = "AND " + convert_number(fraction) + " PAISE";
            }

            return convert_number(value) + " RUPEE " + f_text + " ONLY";
        }

        function frac(f) {
            return f % 1;
        }

        function convert_number(number) {
            if ((number < 0) || (number > 999999999)) {
                return "NUMBER OUT OF RANGE!";
            }
            var Gn = Math.floor(number / 10000000); /* Crore */
            number -= Gn * 10000000;
            var kn = Math.floor(number / 100000); /* lakhs */
            number -= kn * 100000;
            var Hn = Math.floor(number / 1000); /* thousand */
            number -= Hn * 1000;
            var Dn = Math.floor(number / 100); /* Tens (deca) */
            number = number % 100; /* Ones */
            var tn = Math.floor(number / 10);
            var one = Math.floor(number % 10);
            var res = "";

            if (Gn > 0) {
                res += (convert_number(Gn) + " CRORE");
            }
            if (kn > 0) {
                res += (((res == "") ? "" : " ") +
                    convert_number(kn) + " LAKH");
            }
            if (Hn > 0) {
                res += (((res == "") ? "" : " ") +
                    convert_number(Hn) + " THOUSAND");
            }

            if (Dn) {
                res += (((res == "") ? "" : " ") +
                    convert_number(Dn) + " HUNDRED");
            }


            var ones = Array("", "ONE", "TWO", "THREE", "FOUR", "FIVE", "SIX", "SEVEN", "EIGHT", "NINE", "TEN", "ELEVEN",
                "TWELVE", "THIRTEEN", "FOURTEEN", "FIFTEEN", "SIXTEEN", "SEVENTEEN", "EIGHTEEN", "NINETEEN");
            var tens = Array("", "", "TWENTY", "THIRTY", "FOURTY", "FIFTY", "SIXTY", "SEVENTY", "EIGHTY", "NINETY");

            if (tn > 0 || one > 0) {
                if (!(res == "")) {
                    res += " AND ";
                }
                if (tn < 2) {
                    res += ones[tn * 10 + one];
                } else {

                    res += tens[tn];
                    if (one > 0) {
                        res += ("-" + ones[one]);
                    }
                }
            }

            if (res == "") {
                res = "zero";
            }
            return res;
        }
    </script>
    <script>
        function sendWhatsApp(id) {
            fetch(`/invoice/${id}/send-wp`)
                .then(res => res.json())
                .then(data => alert(data.message))
                .catch(err => console.error(err));
        }
    </script>
@endpush
