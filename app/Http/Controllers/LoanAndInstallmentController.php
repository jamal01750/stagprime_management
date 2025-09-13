<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\Loan;
use Illuminate\Http\Request;

class LoanAndInstallmentController extends Controller
{
    public function index(Request $request)
    {
        $year   = (int)($request->query('year') ?? now('Asia/Dhaka')->year);
        $month = (int)($request->query('month') ?? now('Asia/Dhaka')->month);
        $loans = Loan::whereMonth('next_date', $month)
        ->whereYear('next_date', $year)
        ->orderBy('next_date', 'asc')
        ->get();
    
        return view('loan_and_installment', compact('loans', 'year', 'month'));
    }
    
    public function createLoan()
    {
        return view('loan_installment.create_loan');
    }

    public function storeLoan(Request $request)
    {
        $request->validate([
            'loan_name' => 'required|string|max:255',
            'loan_amount' => 'required|numeric|min:0',
            'installment_number' => 'required|integer|min:1',
            'installment_amount' => 'required|numeric|min:0',
            'due_amount' => 'required|numeric|min:0',
        ]);
        Loan::create([
            'loan_name' => $request->loan_name,
            'loan_amount' => $request->loan_amount,
            'installment_number' => $request->installment_number,
            'installment_type' => $request->installment_type,
            'installment_amount' => $request->installment_amount,
            'due_amount' => $request->due_amount,
        ]);
        return redirect()->back()->with('success', 'Loan data saved successfully.');
    }

    public function createInstallment()
    {
        $loans = Loan::all();
        return view('loan_installment.create_installment', compact('loans'));
    }

    public function storeInstallment(Request $request)
    {
        $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'pay_date' => 'required|date',
            'next_date' => 'nullable|date',
        ]);

        $loan = Loan::findOrFail($request->loan_id);

        // Insert new installment
        $installment = Installment::create([
            'loan_id' => $loan->id,
            'installment_amount' => $loan->installment_amount,
            'due_amount' => $loan->due_amount - $loan->installment_amount,
            'pay_date' => $request->pay_date,
            'next_date' => $request->next_date,
            'status' => 'unpaid',
        ]);

        // Update loan due amount
        $loan->update([
            'due_amount' => $loan->due_amount - $loan->installment_amount
        ]);

        return redirect()->back()->with('success', 'Installment added successfully.');
    }

    public function report()
    {
        $loans = Loan::all();
        return view('loan_installment.report_loan', compact('loans'));
    }

    public function showInstallments(Loan $loan)
    {
        $loans = Loan::all();
        $installments = $loan->installments()->orderBy('pay_date','asc')->get();
        return view('loan_installment.report_loan', compact('loans','loan','installments'));
    }

    public function toggleInstallmentStatus(Request $request, Installment $installment)
    {
        // Toggle the status
        $installment->status = $installment->status === 'unpaid' ? 'paid' : 'unpaid';
        $installment->save();

        return response()->json([
            'success' => true,
            'status' => $installment->status
        ]);
    }


    
}
