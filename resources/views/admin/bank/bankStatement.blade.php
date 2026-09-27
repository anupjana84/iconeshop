@extends('layouts.main')
@push('page_title')
    <title>Bank Statement</title>
@endpush
@section('content_page')
    <div>
        <!-- Subheading for Table -->
        <div class="bg-white p-4 rounded shadow overflow-visible">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="py-2 px-4 border">Date</th>
                        <th class="py-2 px-4 border">Description</th>
                        <th class="py-2 px-4 border">Remarks</th>
                        <th class="py-2 px-4 border">Debit</th>
                        <th class="py-2 px-4 border">Credit</th>
                        <th class="py-2 px-4 border">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i=1;
                    @endphp
                    @foreach ($statements as $key => $item)
                        <tr class="border border-gray-300">
                            <td class="py-2 px-4 border border-gray-300">{{ $item->created_at->format('d/M/y g:i a') }}</td>
                            <td class="py-2 px-4 border border-gray-300">
                                @isset($item->company)
                                {{ $item->company->name }}
                            @endisset
                            @isset($item->customer)
                                {{ $item->customer->name }}
                                @isset($item->emi_company_id )
                                    ( <small class="text-amber-700">{{ $item->emi->name }}</small> )
                                @endisset
                            @endisset
                            @isset($item->user)
                                {{ $item->user->name }}
                            @endisset
                            </td>
                            <td class="py-2 px-4 border border-gray-300"> {{ $item->remark }}</td>
                            <td class="py-2 px-4 border border-gray-300">
                                @if ($item->flow_type=='outflow')
                                {{ $item->amount }}
                                @endif
                            </td>
                            <td class="py-2 px-4 border border-gray-300">
                                 @if ($item->flow_type=='inflow')
                                {{ $item->amount }}
                                @endif</td>
                            <td class="py-2 px-4 border border-gray-300">{{ $item->last_bank_balance }}</td>
                        </tr>
                        @php
                            $i++;
                        @endphp
                    @endforeach
                </tbody>
            </table>
            @if ($i == 1)
                <div class="text-red-600 text-center">No record found</div>
            @endif
        </div>
        {{-- <div class="py-4">
            {{ $expenses->links() }}
        </div> --}}
    </div>
@endsection
@push('extra_style')
@endpush
@push('extra_js')
@endpush
