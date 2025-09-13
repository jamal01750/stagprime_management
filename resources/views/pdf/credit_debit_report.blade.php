<!DOCTYPE html>
<html>
<head>
    <title>Transactions Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Transactions Report</h2>

      <table class="min-w-full text-sm">
        <thead>
            <tr>
                <th class="px-2 py-1 text-left">Date</th>
                <th class="px-2 py-1 text-left">Type</th>
                <th class="px-2 py-1 text-left">Credit Amount</th>
                <th class="px-2 py-1 text-left">Debit Amount</th>
                <th class="px-2 py-1 text-left">Comment</th>
            </tr>
        </thead>
        <tbody>
          @foreach($transactions as $transaction)
              <tr>
                <td class="px-2 py-1">{{$transaction -> date}}</td>
                <td class="px-2 py-1">{{$transaction -> type}}</td>
                <td class="px-2 py-1">
                  @if($transaction->type == 'credit')
                    {{ $transaction->amount }}
                    @else
                    {{ number_format(0, 2) }}
                  @endif
                </td>
                <td class="px-2 py-1">
                  @if($transaction->type == 'debit')
                      {{ $transaction->amount }}
                      @else
                      {{ number_format(0, 2) }}
                  @endif
                </td>
                <td class="px-2 py-1">{{$transaction -> description}}</td>
              </tr>
          @endforeach
          <tr>
              <td class="px-2 py-1"></td>
              <td class="px-2 py-1 font-bold">Total</td>
              <td class="px-2 py-1 font-bold">{{ $totalNewCredits }}</td>
              <td class="px-2 py-1 font-bold">{{ $totalNewDebits }}</td>
              <td class="px-2 py-1">Balance : {{ $newBalance }}</td>
          </tr>
        </tbody>
      </table>
</body>
</html>
