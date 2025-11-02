@extends('layouts.app')

@section('title', 'All Category Report')
@section('heading', 'All Category Report')

@section('content')
<div class="bg-white p-4 rounded shadow space-y-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label>Start Date</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="border p-2 rounded">
        </div>
        <div>
            <label>End Date</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="border p-2 rounded">
        </div>
        <button class="px-4 py-2 bg-blue-600 text-white rounded">Filter</button>
        @if(request()->filled('start_date') || request()->filled('end_date'))
        <a href="{{ route('product.category.report') }}" class="px-4 py-2 bg-gray-500 text-white rounded">Clear</a>
        @endif
    </form>

    <form method="GET" action="{{ route('product.category.report.pdf') }}" target="_blank" id="downloadForm">
        @csrf
        <input type="hidden" name="start_date" value="{{ request('start_date') }}">
        <input type="hidden" name="end_date" value="{{ request('end_date') }}">
        <div class="flex justify-between items-center">
            <div>
                <input type="checkbox" id="selectAll" class="mr-2"> <label for="selectAll" class="font-semibold">Select All</label>
            </div>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Download PDF</button>
        </div>

        <div class="overflow-x-auto mt-4">
            <table class="min-w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 border text-center">✔</th>
                        <th class="p-2 border">Category</th>
                        <th class="p-2 border text-center">Total Stock</th>
                        <th class="p-2 border text-center">Current Stock</th>
                        <th class="p-2 border text-center">Sold Qty</th>
                        <th class="p-2 border text-center">Loss Qty</th>
                        <th class="p-2 border text-center">Return Qty</th>
                        <th class="p-2 border text-right">Revenue</th>
                        <th class="p-2 border text-right">Loss</th>
                        <th class="p-2 border text-right">Return</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $cat)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="text-center">
                            <input type="checkbox" name="categories[]" value="{{ $cat['id'] }}" class="row-checkbox">
                        </td>
                        <td class="p-2 border">{{ $cat['name'] }}</td>
                        <td class="p-2 border text-center">{{ $cat['total_stock'] }}</td>
                        <td class="p-2 border text-center">{{ $cat['current_stock'] }}</td>
                        <td class="p-2 border text-center">{{ $cat['sell_qty'] }}</td>
                        <td class="p-2 border text-center">{{ $cat['loss_qty'] }}</td>
                        <td class="p-2 border text-center">{{ $cat['return_qty'] }}</td>
                        <td class="p-2 border text-right">৳{{ number_format($cat['revenue'],2) }}</td>
                        <td class="p-2 border text-right">৳{{ number_format($cat['loss'],2) }}</td>
                        <td class="p-2 border text-right">৳{{ number_format($cat['return'],2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="font-semibold bg-gray-50">
                    <tr>
                        <td colspan="2" class="p-2 border text-right">Total</td>
                        <td class="p-2 border text-center">{{ $totals['total_stock'] }}</td>
                        <td class="p-2 border text-center">{{ $totals['current_stock'] }}</td>
                        <td class="p-2 border text-center">{{ $totals['sell_qty'] }}</td>
                        <td class="p-2 border text-center">{{ $totals['loss_qty'] }}</td>
                        <td class="p-2 border text-center">{{ $totals['return_qty'] }}</td>
                        <td class="p-2 border text-right">৳{{ number_format($totals['revenue'],2) }}</td>
                        <td class="p-2 border text-right">৳{{ number_format($totals['loss'],2) }}</td>
                        <td class="p-2 border text-right">৳{{ number_format($totals['return'],2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </form>

    {{-- ✅ Pagination --}}
    <div class="mt-4">
        {{ $categories->links() }}
    </div>
</div>

<script>
    document.getElementById('selectAll').addEventListener('change', function () {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
    });
</script>
@endsection


