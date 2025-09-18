@extends('layouts.app')

@section('title', 'Product Summary | Product Management')
@section('heading', 'Summary Dashboard | Product Management')

@section('content')
<div class="p-6 space-y-6">

    <h1 class="text-2xl font-bold mb-4">Summary Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-6">

        {{-- Current Stock --}}
        <div class="bg-white shadow rounded-xl p-6 text-center">
            <h2 class="text-gray-600">Current Stock</h2>
            <p class="text-3xl font-bold text-blue-600">{{ $totals['current_stock'] }}</p>
        </div>

        {{-- Total Stock --}}
        <div class="bg-white shadow rounded-xl p-6 text-center">
            <h2 class="text-gray-600">Total Stock</h2>
            <p class="text-3xl font-bold text-green-600">{{ $totals['total_stock'] }}</p>
        </div>

        {{-- Sell Quantity --}}
        <div class="bg-white shadow rounded-xl p-6 text-center">
            <h2 class="text-gray-600">Total Sold</h2>
            <p class="text-3xl font-bold text-purple-600">{{ $totals['sell_qty'] }}</p>
        </div>

        {{-- Loss Quantity --}}
        <div class="bg-white shadow rounded-xl p-6 text-center">
            <h2 class="text-gray-600">Total Loss Quantity</h2>
            <p class="text-3xl font-bold text-red-600">{{ $totals['loss_qty'] }}</p>
        </div>

        {{-- Revenue --}}
        <div class="bg-white shadow rounded-xl p-6 text-center">
            <h2 class="text-gray-600">Total Revenue</h2>
            <p class="text-3xl font-bold text-emerald-600">
                {{ number_format($totals['revenue'],2) }}
            </p>
        </div>

        {{-- Loss Amount --}}
        <div class="bg-white shadow rounded-xl p-6 text-center">
            <h2 class="text-gray-600">Total Loss Amount</h2>
            <p class="text-3xl font-bold text-rose-600">
                {{ number_format($totals['loss'],2) }}
            </p>
        </div>

    </div>
</div>
@endsection
