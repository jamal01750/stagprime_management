@extends('layouts.app')

@section('title', 'Student Payment | Student Management')
@section('heading', 'Student Payment Entry')

@section('content')
<div class="w-full flex flex-col space-y-6 md:space-y-8">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded shadow p-4 md:p-6">
        <h2 class="text-lg md:text-xl font-semibold mb-4">Add Student Payment</h2>
        <form method="POST" action="{{ route('student.payment.store') }}" class="space-y-4" id="paymentForm">
            @csrf

            {{-- Student Dropdown --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Student Name</label>
                <select name="student_id" id="studentSelect" class="w-full border-2 border-blue-600 rounded px-3 py-1" required>
                    <option value="">-- Select Student --</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}"
                            data-total="{{ $student->total_fee }}"
                            data-due="{{ $student->due_amount }}">
                            {{ $student->student_name }} ({{ $student->student_id }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Total Fee --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Total Fee</label>
                <input type="number" id="totalFee" name="total_fee" class="w-full border-2 border-blue-600 rounded px-3 py-1 bg-gray-100" readonly>
            </div>

            {{-- Pay Amount --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Pay Amount</label>
                <input type="number" name="pay_amount" id="payAmount" step="0.01" class="w-full border-2 border-blue-600 rounded px-3 py-1" required>
            </div>

            {{-- Due Amount --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Due Amount</label>
                <input type="number" id="dueAmount" name="due_amount" step="0.01" class="w-full border-2 border-blue-600 rounded px-3 py-1 bg-gray-100" readonly>
            </div>

            {{-- Pay Date --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Pay Date</label>
                <input type="date" name="pay_date" class="w-full border-2 border-blue-600 rounded px-3 py-1" required>
            </div>

            {{-- Next Date --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Next Date</label>
                <input type="date" name="next_date" class="w-full border-2 border-blue-600 rounded px-3 py-1">
            </div>

            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">
                Save Payment
            </button>
        </form>
    </div>
</div>

{{-- JavaScript for Dynamic Calculation --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const studentSelect = document.getElementById("studentSelect");
        const totalFee = document.getElementById("totalFee");
        const payAmount = document.getElementById("payAmount");
        const dueAmount = document.getElementById("dueAmount");

        let baseDue = 0;

        studentSelect.addEventListener("change", function () {
            const selected = this.options[this.selectedIndex];
            const total = parseFloat(selected.getAttribute("data-total")) || 0;
            const due = parseFloat(selected.getAttribute("data-due")) || 0;

            totalFee.value = total;
            dueAmount.value = due;
            baseDue = due;
            payAmount.value = "";
        });

        payAmount.addEventListener("input", function () {
            const pay = parseFloat(this.value) || 0;
            dueAmount.value = (baseDue - pay).toFixed(2);
        });
    });
</script>
@endsection
