@extends('layouts.app')
@section('title', 'Single Category Report')
@section('heading', 'Single Category Report')

@section('content')
<div class="bg-white p-4 rounded shadow space-y-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label>Category</label>
            <select name="category_id" class="border p-2 rounded">
                @foreach($categories as $c)
                <option value="{{ $c->id }}" {{ $categoryId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Start Date</label>
            <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="border p-2 rounded">
        </div>
        <div>
            <label>End Date</label>
            <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="border p-2 rounded">
        </div>
        <div class="flex items-center gap-2">
            <button class="px-4 py-2 bg-blue-600 text-white rounded">Filter</button>
            <a href="{{ route('product.single.category.report') }}" class="px-4 py-2 bg-gray-500 text-white rounded">Clear</a>
        </div>
    </form>

    <form method="POST" action="{{ route('product.single.category.report.pdf') }}" target="_blank" id="downloadForm">
        @csrf
        <input type="hidden" name="details" id="detailsInput">
        <input type="hidden" name="totals" id="totalsInput">
        <input type="hidden" name="category_name" value="{{ optional($categories->firstWhere('id', $categoryId))->name ?? '' }}">

        <div class="flex justify-end">
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Download PDF</button>
        </div>

        <div class="overflow-x-auto mt-4">
            <table class="min-w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 border">Date</th>
                        <th class="p-2 border">Type</th>
                        <th class="p-2 border text-center">Qty</th>
                        <th class="p-2 border text-right">Amount (৳)</th>
                        <th class="p-2 border">Description</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($details as $d)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-2 border">{{ \Carbon\Carbon::parse($d['date'])->format('Y-m-d') }}</td>
                        <td class="p-2 border">{{ $d['type'] }}</td>
                        <td class="p-2 border text-center">{{ $d['qty'] }}</td>
                        <td class="p-2 border text-right">{{ number_format($d['amount'],2) }}</td>
                        <td class="p-2 border">{{ $d['desc'] ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-gray-500">No records found for this range.</td>
                    </tr>
                    @endforelse
                </tbody>

                <tfoot class="font-semibold bg-gray-50">
                    <tr>
                        <td class="p-2 border text-right" colspan="2">Total</td>
                        <td class="p-2 border text-center">{{ $totals['qty'] ?? 0 }}</td>
                        <td class="p-2 border text-right">{{ number_format($totals['amount'] ?? 0, 2) }}</td>
                        <td class="p-2 border"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Pagination: works when $details is a paginator (LengthAwarePaginator) --}}
        <div class="mt-4">
            @if(method_exists($details, 'links'))
                {{ $details->appends(request()->query())->links() }}
            @endif
        </div>
    </form>
</div>

<script>
document.getElementById('downloadForm').addEventListener('submit', function (e) {
    // Prepare details and totals JSON for PDF endpoint.
    // If $details is paginated we want to collect the current page items; otherwise collect the array.
    const detailsData = @json($details instanceof \Illuminate\Pagination\AbstractPaginator ? $details->items() : $details);
    const totalsData = @json($totals);

    // Put JSON into hidden inputs
    document.getElementById('detailsInput').value = JSON.stringify(detailsData);
    document.getElementById('totalsInput').value = JSON.stringify(totalsData);

    // allow form to submit (PDF download)
});
</script>
@endsection










