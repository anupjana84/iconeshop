@extends('layouts.main')

@push('page_title')
<title>Reward Point History</title>
@endpush

@section('content_page')

<!-- SEARCH -->

<form method="GET" class="mb-6">

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

        <div>

            <input type="text"
                name="search"
                placeholder="Search Mobile / Name"

                value="{{ request('search') }}"

                class="p-2 border rounded w-full">

        </div>

        <div>

            <input type="date"
                name="start_date"

                value="{{ request('start_date') }}"

                class="p-2 border rounded w-full">

        </div>

        <div>

            <input type="date"
                name="end_date"

                value="{{ request('end_date') }}"

                class="p-2 border rounded w-full">

        </div>

        <div>

            <button type="submit"

                class="bg-green-600 text-white px-4 py-2 rounded w-full">

                Search

            </button>

        </div>

        <div>

            <a href="{{ route('reward.point.history') }}"

                class="bg-red-600 text-white px-4 py-2 rounded block text-center">

                Reset

            </a>

        </div>

    </div>

</form>

<!-- TOTALS -->

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

    <div class="bg-green-100 p-4 rounded">

        <h2 class="font-bold">
            Total Point Earned
        </h2>

        <p class="text-xl font-bold">

            {{ round($totalEarned,2) }}

        </p>

    </div>

    <div class="bg-red-100 p-4 rounded">

        <h2 class="font-bold">
            Total Point Used
        </h2>

        <p class="text-xl font-bold">

            {{ round($totalUsed,2) }}

        </p>

    </div>

    <div class="bg-blue-100 p-4 rounded">

        <h2 class="font-bold">
            Total Point Balance
        </h2>

        <p class="text-xl font-bold">

            {{ round($totalBalance,2) }}

        </p>

    </div>

</div>

<!-- TABLE -->

<div class="bg-white p-4 rounded shadow overflow-x-auto">

    <table class="w-full border-collapse">

        <thead>

            <tr class="bg-gray-800 text-white">

                <th class="py-2 px-4 border">
                    Sl
                </th>

                <th class="py-2 px-4 border">
                    Customer Name
                </th>

                <th class="py-2 px-4 border">
                    Mobile
                </th>

                <th class="py-2 px-4 border">
                    Point Earned
                </th>

                <th class="py-2 px-4 border">
                    Point Used
                </th>

                <th class="py-2 px-4 border">
                    Point Refund
                </th>

                <th class="py-2 px-4 border">
                    Point Balance
                </th>

                <th class="py-2 px-4 border">
                    Last Activity
                </th>

                <th class="py-2 px-4 border">
                    Details | Send-wp
                </th>

            </tr>

        </thead>

        <tbody>

            @foreach($history as $key => $item)

            <tr class="border">

                <td class="py-2 px-4 border">
                    {{ $history->firstItem() + $key }}
                </td>

                <td class="py-2 px-4 border">
                    {{ $item->customer_name }}
                </td>

                <td class="py-2 px-4 border">
                    {{ $item->mobile }}
                </td>

                <td class="py-2 px-4 border text-green-700 font-bold">
                    {{ round($item->total_earned,2) }}
                </td>

                <td class="py-2 px-4 border text-red-700 font-bold">
                    {{ round($item->total_used,2) }}
                </td>

                <td class="py-2 px-4 border text-blue-700 font-bold">
                    {{ round($item->total_refunded,2) }}
                </td>

                <td class="py-2 px-4 border font-bold">
                    {{ round($item->balance,2) }}
                </td>

                <td class="py-2 px-4 border">

                    {{ $item->updated_at }}

                </td>

<td class="py-2 px-4 border text-center">

    <a href="{{ route('reward.point.details',$item->mobile) }}"

        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded inline-flex items-center gap-2">

        <i class="fas fa-eye"></i>

    </a>

<button

onclick="sendRewardWhatsApp(
'{{ $item->mobile }}'
)"

class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded inline-flex items-center gap-2">

    <i class="fa-brands fa-whatsapp"></i>

</button>

</td>

            </tr>

            @endforeach

        </tbody>

    </table>

</div>

<div class="mt-4">

    {{ $history->links() }}

</div>

<script>

function sendRewardWhatsApp(mobile)
{
    fetch('/reward-point/send-wp/' + mobile)

    .then(res => res.json())

    .then(data => {

        alert(data.message);

    })

    // .catch(err => console.log(err));
}

</script>

@endsection