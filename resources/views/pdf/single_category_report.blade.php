<div class="mt-8 overflow-x-auto">
        <h3 class="text-xl font-bold mb-4">Details for {{ $categoryName }}</h3>
        <table class="min-w-full border text-xs sm:text-sm md:text-base">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">Date</th>
                    <th class="p-2 border text-center">Stock Qty</th>
                    <th class="p-2 border text-right">Stock Amount</th>
                    <th class="p-2 border text-center">Sell Qty</th>
                    <th class="p-2 border text-right">Sell Amount</th>
                    <th class="p-2 border">Sell Comment</th>
                    <th class="p-2 border text-center">Loss Qty</th>
                    <th class="p-2 border text-right">Loss Amount</th>
                    <th class="p-2 border">Loss Comment</th>
                    <th class="p-2 border text-center">Return Qty</th>
                    <th class="p-2 border text-right">Return Amount</th>
                    <th class="p-2 border">Return Comment</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details as $row)
                <tr>
                    <td class="p-2 border">{{ $row['date']->format('Y-m-d') }}</td>
                    <td class="p-2 border text-center">{{ $row['stock_qty'] ?? '-' }}</td>
                    <td class="p-2 border text-center">{{ $row['s_type'] == 'taka' ? '৳' : '$' }}{{ $row['stock_amount'] ?? 0 }}</td>
                    <td class="p-2 border text-center">{{ $row['sell_qty'] ?? '-' }}</td>
                    <td class="p-2 border text-right">{{ $row['sa_type'] == 'taka' ? '৳' : '$' }}{{ $row['amount'] ?? 0 }}</td>
                    <td class="p-2 border">{{ $row['sell_desc'] ?? '-' }}</td>
                    <td class="p-2 border text-center">{{ $row['loss_qty'] ?? '-' }}</td>
                    <td class="p-2 border text-right">{{ $row['la_type'] == 'taka' ? '৳' : '$' }}{{ $row['loss_amount'] ?? 0 }}</td>
                    <td class="p-2 border">{{ $row['loss_desc'] ?? '-' }}</td>
                    <td class="p-2 border text-center">{{ $row['return_qty'] ?? '-' }}</td>
                    <td class="p-2 border text-right">{{ $row['ra_type'] == 'taka' ? '৳' : '$' }}{{ $row['return_amount'] ?? 0 }}</td>
                    <td class="p-2 border">{{ $row['return_desc'] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>