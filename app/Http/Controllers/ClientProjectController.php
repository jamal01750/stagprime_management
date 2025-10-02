<?php

namespace App\Http\Controllers;

use App\Models\ClientProject;
use App\Models\ClientProjectDebit;
use App\Models\ClientProjectTransaction;
use Illuminate\Http\Request;

class ClientProjectController extends Controller
{
    public function index()
    {
        $projects = ClientProject::all();
        return view('client_project', compact('projects'));
    }

    public function create()
    {
        return view('client_project.create_client_project');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'project_name' => 'required|string|max:255',
            'currency' => 'required|in:Dollar,Taka',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'contract_amount' => 'required|numeric|min:0',
            'advance_amount' => 'required|numeric|min:0',
        ]);
        ClientProject::create([
            'project_name' => $validatedData['project_name'],
            'currency' => $validatedData['currency'],
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
            'contract_amount' => $validatedData['contract_amount'],
            'advance_amount' => $validatedData['advance_amount'],
            'due_amount' => $validatedData['contract_amount'] - $validatedData['advance_amount'],
            'status' => $validatedData['advance_amount'] == $validatedData['contract_amount'] ? 'paid' : 'unpaid',
        ]);

        return redirect()->back()->with('success', 'Client project created successfully!');
    }

    public function transaction()
    {
        $projects = ClientProject::all();
        return view('client_project.add_transaction', compact('projects'));
    }

    public function storeTransaction(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'project_id' => 'required|exists:client_projects,id',
            'date' => 'required|date',
            'type' => 'required|in:invest,profit,loss',
            'amount' => 'required|numeric|min:0',
            'description' => ['nullable', 'string', 'not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
        ]);

        // Create the transaction
        ClientProjectTransaction::create([
            'project_id' => $validatedData['project_id'],
            'date' => $validatedData['date'],
            'type' => $validatedData['type'],
            'amount' => $validatedData['amount'],
            'description' => $validatedData['description'],
        ]);

        return redirect()->back()->with('success', 'Transaction recorded successfully!');
    }

    public function createClientDebit()
    {
        // Fetch all projects with their latest due_amount
        $projects = ClientProject::all(['id', 'project_name', 'currency', 'due_amount']);

        return view('client_project.add_client_debit', compact('projects'));
    }
    
    public function storeClientDebit(Request $request)
    {
        $validated = $request->validate([
            'project_id'   => 'required|exists:client_projects,id',
            'currency'     => 'required|in:Dollar,Taka',
            'pay_amount'   => 'required|numeric|min:0',
            'due_amount'   => 'required|numeric|min:0',
            'pay_date'     => 'required|date',
            // 'next_date'    => 'nullable|date',
        ]);

        $projectDebit = ClientProjectDebit::create([
            'project_id'  => $validated['project_id'],
            'currency'    => $validated['currency'],
            'pay_amount'  => $validated['pay_amount'],
            'due_amount'  => $validated['due_amount'],
            'pay_date'    => $validated['pay_date'],
            // 'next_date'   => $validated['next_date'],
            'status'      =>  'unpaid', // Default status is 'unpaid'
        ]);

        if ($projectDebit->due_amount > 0) {
            $project = ClientProject::findOrFail($projectDebit->project_id);
            $project->due_amount = $projectDebit->due_amount;
            $project->status = 'unpaid';
            $project->save();
        } 

        return redirect()->back()->with('success', 'Client Debit added successfully!');
    }

    public function list()
    {
        $projects = ClientProject::all();
        $clientDebits = ClientProjectDebit::all();
        foreach ($projects as $project) {
            $project->paid_amount = $project->advance_amount + $clientDebits->where('project_id', $project->id)->sum('pay_amount');
            $project->due_amount = $project->contract_amount - $project->paid_amount;
            $project->currency = $project->currency == 'Dollar' ? '$ ' : '৳ ' ;
        }
        return view('client_project.list_client_project', compact('projects'));
    }

    public function getClientDebits($projectId)
    {
        $project = ClientProject::findOrFail($projectId);
        $debits = ClientProjectDebit::where('project_id', $projectId)->get();

        return response()->json([
            'project_name' => $project->project_name,
            'debits' => $debits,
        ]);
    }

    public function toggleDebitStatus($id)
    {
        $debit = ClientProjectDebit::findOrFail($id);
        $debit->status = $debit->status === 'paid' ? 'unpaid' : 'paid';
        $debit->save();

        if ($debit->due_amount == 0) {
            $project = ClientProject::findOrFail($debit->project_id);
            $project->due_amount = 0;
            $project->status = 'paid';
            $project->save();
        } 
        
        return response()->json([
            'success' => true,
            'status' => $debit->status
        ]);
    }


    public function getTransactions(Request $request, $projectId)
    {
        $project = ClientProject::findOrFail($projectId);

        $query = ClientProjectTransaction::where('project_id', $projectId);

        if ($request->year) {
            $query->whereYear('date', $request->year);
        }

        if ($request->month) {
            $query->whereMonth('date', $request->month);
        }

        $transactions = $query->orderBy('date', 'asc')->get();

        return response()->json([
            'project_name' => $project->project_name,
            'transactions' => $transactions,
        ]);
    }

    public function showprojects(Request $request, $id)
    {
        $projects = ClientProject::all();
        $showproject = ClientProject::findOrFail($id);

        // Filtering
        $year = $request->input('year');
        $month = $request->input('month');

        $transactionsQuery = $showproject->transactions();

        if ($year) {
            $transactionsQuery->whereYear('date', $year);
        }

        if ($month) {
            $transactionsQuery->whereMonth('date', $month);
        }

        $transactions = $transactionsQuery->orderBy('date', 'asc')->get();

        // Totals
        $totalCost   = $transactions->where('type', 'cost')->sum('amount');
        $totalProfit = $transactions->where('type', 'profit')->sum('amount') + $showproject->advance_amount;

        return view('client_project', compact(
            'projects',
            'showproject',
            'transactions',
            'year',
            'month',
            'totalCost',
            'totalProfit'
        ));
    }

    public function deleteProject($id)
    {
        $project = ClientProject::findOrFail($id);
        $project->delete();
        return redirect()->back()->with('success', 'Client project deleted successfully!'); 
    }
}
