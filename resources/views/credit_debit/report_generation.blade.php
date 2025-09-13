@extends('layouts.app')

@section('title', 'Report Generation | Daily Credit & Debit')
@section('heading', 'Report Generation | Daily Credit & Debit')

@section('content')
    <div class="w-full flex flex-col space-y-6 md:space-y-8">
        
        <div class="bg-white rounded shadow p-4 md:p-6 flex flex-col md:flex-row items-start">
            <div class="w-1/5 flex items-center">
                <h2 class="text-lg md:text-xl font-semibold mb-2 md:mb-4">Reports</h2>
            </div>
            <div class="w-4/5">
                <form method="POST" action="{{ route('credit.debit.report.show') }}" class="space-y-3 md:space-y-4 w-full md:w-auto">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Choose Starting Date</label>
                        <input type="date" name="start_date" class="mt-1 block w-full border-2 border-blue-600 focus:border-blue-700 focus:ring-blue-700 rounded bg-white text-gray-900" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Choose Ending Date</label>
                        <input type="date" name="end_date" class="mt-1 block w-full border-2 border-blue-600 focus:border-blue-700 focus:ring-blue-700 rounded bg-white text-gray-900" required>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">Show Reports</button>
                </form>
                @if(isset($start_date) && isset($end_date))
                    <div class="mt-4">
                        <p>Showing reports from <strong>{{ $start_date }}</strong> to <strong>{{ $end_date }}</strong></p>
                        <form action="{{ route('credit.debit.download.pdf') }}" method="POST" target="_blank" class="mt-4">
                            @csrf
                            <input type="hidden" name="start_date" value="{{ $start_date }}">
                            <input type="hidden" name="end_date" value="{{ $end_date }}">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">Download PDF</button>
                        
                            <div class="overflow-x-auto w-full">
                                <table class="min-w-full text-sm">
                                    <thead>
                                        <tr>
                                            <th class="px-2 py-1 text-left cursor-pointer" id="toggleCheck">Mark All</th>
                                            <th class="px-2 py-1 text-left">Date</th>
                                            <th class="px-2 py-1 text-left">Credit (TK.)</th>
                                            <th class="px-2 py-1 text-left">Debit (TK.)</th>
                                            <th class="px-2 py-1 text-left">Comment</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transactions as $transaction)
                                            <tr>
                                                <td class="border px-4 py-2">
                                                    <input type="checkbox" name="transaction_ids[]" value="{{ $transaction->id }}" class="transaction-checkbox">
                                                </td>
                                                <td class="px-2 py-1">{{$transaction -> date}}</td>
                                                <td class="px-2 py-1 text-green-600">
                                                    @if($transaction->type == 'credit')
                                                    {{ number_format($transaction->amount, 2) }}
                                                    @else
                                                    {{ number_format(0, 2) }}
                                                    @endif
                                                </td>
                                                <td class="px-2 py-1 text-red-600">
                                                    @if($transaction->type == 'debit')
                                                        {{ number_format($transaction->amount, 2) }}
                                                        @else
                                                        {{ number_format(0, 2) }}
                                                    @endif
                                                </td>
                                                <td class="px-2 py-1">{{$transaction -> description}}</td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td class="px-2 py-1 font-bold"></td>
                                            <td class="px-2 py-1 font-bold">Total</td>
                                            <td class="px-2 py-1 text-green-600 font-bold">{{ $totalCredits }}</td>
                                            <td class="px-2 py-1 text-red-600 font-bold">{{ $totalDebits }}</td>
                                            <td class="px-2 py-1 text-blue-600 font-bold">Balance: {{ $balance }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <script>
        const toggleCheckBtn = document.getElementById('toggleCheck');
        const checkboxes = document.querySelectorAll('.transaction-checkbox');
        let allChecked = false;

        toggleCheckBtn.addEventListener('click', () => {
            allChecked = !allChecked;
            checkboxes.forEach(cb => cb.checked = allChecked);
            toggleCheckBtn.textContent = allChecked ? 'Unmark All' : 'Mark All';
        });
    </script>
@endsection




      