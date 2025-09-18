@extends('layouts.app')

@section('title', 'Report Product | Product Management')
@section('heading', 'Report Product | Product Management')


@section('content')
<div class="p-6 bg-white shadow rounded space-y-6">

    {{-- Filter --}}
    <form method="GET" class="flex flex-wrap items-center gap-4 mb-6">

        {{-- Filter type --}}
        <select name="filter_type" id="filter_type" onchange="toggleInputs()" class="border p-2 rounded">
            <option value="day"   {{ $filterType=='day'?'selected':'' }}>Day</option>
            <option value="month" {{ $filterType=='month'?'selected':'' }}>Month</option>
            <option value="year"  {{ $filterType=='year'?'selected':'' }}>Year</option>
            <option value="range" {{ $filterType=='range'?'selected':'' }}>Custom Range</option>
        </select>

        {{-- Day --}}
        <input type="date" name="date" id="dateInput" value="{{ $date ?? '' }}"
            class="border p-2 rounded {{ $filterType!='day'?'hidden':'' }}">

        {{-- Month --}}
        <select name="month" id="monthInput" class="border p-2 rounded {{ $filterType!='month'?'hidden':'' }}">
            @for($m=1;$m<=12;$m++)
                <option value="{{ $m }}" {{ ($month==$m)?'selected':'' }}>
                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                </option>
            @endfor
        </select>

        {{-- Year (for month/year filter) --}}
        <select name="year" id="yearInput" class="border p-2 rounded {{ !in_array($filterType,['month','year'])?'hidden':'' }}">
            @for($y=now()->year; $y>=2020; $y--)
                <option value="{{ $y }}" {{ ($year==$y)?'selected':'' }}>{{ $y }}</option>
            @endfor
        </select>

        {{-- Custom range --}}
        <input type="date" name="start_date" id="startDateInput" value="{{ $startDate ?? '' }}"
            class="border p-2 rounded {{ $filterType!='range'?'hidden':'' }}">
        <input type="date" name="end_date" id="endDateInput" value="{{ $endDate ?? '' }}"
            class="border p-2 rounded {{ $filterType!='range'?'hidden':'' }}">

        <button class="px-4 py-2 bg-blue-600 text-white rounded">Filter</button>
    </form>

    {{-- Main Report Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">Category</th>
                    <th class="p-2 border">Current Stock</th>
                    <th class="p-2 border">Total Stock</th>
                    <th class="p-2 border">Sold</th>
                    <th class="p-2 border">Loss Qty</th>
                    <th class="p-2 border">Revenue</th>
                    <th class="p-2 border">Loss</th>
                    <th class="p-2 border">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $cat)
                <tr>
                    <td class="p-2 border">{{ $cat['name'] }}</td>
                    <td class="p-2 border text-center">{{ $cat['current_stock'] }}</td>
                    <td class="p-2 border text-center">{{ $cat['total_stock'] }}</td>
                    <td class="p-2 border text-center">{{ $cat['sell_qty'] }}</td>
                    <td class="p-2 border text-center">{{ $cat['loss_qty'] }}</td>
                    <td class="p-2 border text-right">{{ number_format($cat['revenue'],2) }}</td>
                    <td class="p-2 border text-right">{{ number_format($cat['loss'],2) }}</td>
                    <td class="p-2 border">
                        <a href="{{ route('product.report', ['filter'=>$filter,'category_id'=>$cat['id']]) }}"
                           class="px-3 py-1 bg-blue-500 text-white rounded">View Details</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-100 font-bold">
                <tr>
                    <td class="p-2 border">Total</td>
                    <td class="p-2 border text-center">{{ $totals['current_stock'] }}</td>
                    <td class="p-2 border text-center">{{ $totals['total_stock'] }}</td>
                    <td class="p-2 border text-center">{{ $totals['sell_qty'] }}</td>
                    <td class="p-2 border text-center">{{ $totals['loss_qty'] }}</td>
                    <td class="p-2 border text-right">{{ number_format($totals['revenue'],2) }}</td>
                    <td class="p-2 border text-right">{{ number_format($totals['loss'],2) }}</td>
                    <td class="p-2 border"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Details Section --}}
    @if($details)
    <div class="mt-8">
        <h3 class="text-xl font-bold mb-4">Details for {{ $categoryName }}</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 border">Date</th>
                        <th class="p-2 border">Product</th>
                        <th class="p-2 border">Stock Qty</th>
                        <th class="p-2 border">Sell Qty</th>
                        <th class="p-2 border">Amount</th>
                        <th class="p-2 border">Sell Desc</th>
                        <th class="p-2 border">Loss Qty</th>
                        <th class="p-2 border">Loss Amount</th>
                        <th class="p-2 border">Loss Desc</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($details as $row)
                    <tr>
                        <td class="p-2 border">{{ $row['date']->format('Y-m-d') }}</td>
                        <td class="p-2 border">{{ $row['product_name'] ?? '-' }}</td>
                        <td class="p-2 border text-center">{{ $row['stock_qty'] ?? '-' }}</td>
                        <td class="p-2 border text-center">{{ $row['sell_qty'] ?? '-' }}</td>
                        <td class="p-2 border text-right">{{ $row['amount'] ?? '-' }}</td>
                        <td class="p-2 border">{{ $row['sell_desc'] ?? '-' }}</td>
                        <td class="p-2 border text-center">{{ $row['loss_qty'] ?? '-' }}</td>
                        <td class="p-2 border text-right">{{ $row['loss_amount'] ?? '-' }}</td>
                        <td class="p-2 border">{{ $row['loss_desc'] ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

<script>
    function toggleInputs() {
        let type = document.getElementById('filter_type').value;
        document.getElementById('dateInput').classList.add('hidden');
        document.getElementById('monthInput').classList.add('hidden');
        document.getElementById('yearInput').classList.add('hidden');
        document.getElementById('startDateInput').classList.add('hidden');
        document.getElementById('endDateInput').classList.add('hidden');

        if (type === 'day') {
            document.getElementById('dateInput').classList.remove('hidden');
        } else if (type === 'month') {
            document.getElementById('monthInput').classList.remove('hidden');
            document.getElementById('yearInput').classList.remove('hidden');
        } else if (type === 'year') {
            document.getElementById('yearInput').classList.remove('hidden');
        } else if (type === 'range') {
            document.getElementById('startDateInput').classList.remove('hidden');
            document.getElementById('endDateInput').classList.remove('hidden');
        }
    }
    toggleInputs();
</script>
@endsection





