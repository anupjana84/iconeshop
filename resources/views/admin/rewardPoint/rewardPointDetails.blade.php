@extends('layouts.main')

@section('content_page')

<div class="bg-white p-4 rounded shadow mb-6">

    <h2 class="text-2xl font-bold mb-4">
        Reward Point Statement
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

        <div>

            <p class="font-bold">
                Customer Name
            </p>

            <p>
                {{ $reward->customer_name }}
            </p>

        </div>

        <div>

            <p class="font-bold">
                Mobile
            </p>

            <p>
                {{ $reward->mobile }}
            </p>

        </div>

        <div>

            <p class="font-bold">
                Balance
            </p>

            <p class="text-green-700 font-bold">

                {{ round($reward->balance,2) }}

            </p>

        </div>

        <div>

            <p class="font-bold">
                Total Earned
            </p>

            <p>

                {{ round($reward->total_earned,2) }}

            </p>

        </div>

    </div>

</div>

<div class="bg-white p-4 rounded shadow overflow-x-auto">

    <table class="w-full border-collapse">

        <thead>

            <tr class="bg-gray-800 text-white">

                <th class="py-2 px-4 border">
                    Date
                </th>

                <th class="py-2 px-4 border">
                    Invoice No
                </th>

                <th class="py-2 px-4 border">
                    Type
                </th>

                <th class="py-2 px-4 border">
                    Points
                </th>

                <th class="py-2 px-4 border">
                    Note
                </th>

            </tr>

        </thead>

        <tbody>

            @foreach($transactions as $item)

            <tr class="border">

                <td class="py-2 px-4 border">

                    {{ $item->created_at }}

                </td>

                <td class="py-2 px-4 border">

                    @php

                        $invoice = '';

                        if($item->sale_id){

                            $sale =
                                \App\Models\Sale::find(
                                    $item->sale_id
                                );

                            $invoice =
                                $sale
                                ? $sale->invoice_number
                                : '';
                        }

                    @endphp

                    {{ $invoice }}

                </td>

                <td class="py-2 px-4 border">

                    {{ ucfirst($item->transaction_type) }}

                </td>

                <td class="py-2 px-4 border font-bold">

                    {{ round($item->points,2) }}

                </td>

                <td class="py-2 px-4 border">

                    {{ $item->note }}

                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

</div>

<div class="mt-4">

    {{ $transactions->links() }}

</div>

@endsection