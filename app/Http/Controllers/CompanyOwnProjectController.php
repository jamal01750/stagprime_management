<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanyProject;
use App\Models\CompanyProjectTransaction;

class CompanyOwnProjectController extends Controller
{
    public function index()
    {
        $projects = CompanyProject::all();
        return view('company_own_project', compact('projects'));
        
    }

    public function create()
    {
        return view('company_project.create_company_project');
    }

    public function store(Request $request)
    {   
        $request->validate([
            'project_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'initial_amount' => 'required|numeric|min:0',
            'description' => ['nullable', 'string', 'not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
        ]);
    
        $project = new CompanyProject();
        $project->project_name = $request->input('project_name');
        $project->start_date = $request->input('start_date');
        $project->initial_amount = $request->input('initial_amount');
        $project->description = $request->input('description');
        $project->save();

        return redirect()->back()->with('success', 'Project created successfully!');
    }

    public function transaction()
    {
        $projects = CompanyProject::all();
        return view('company_project.add_transaction', compact('projects'));
    }

    public function storeTransaction(Request $request)
    {
        $request->validate([
            'project_id' => 'required|numeric',
            'date' => 'required|date',
            'type' => 'required|in:invest,profit,loss',
            'amount' => 'required|numeric|min:0',
            'description' => ['nullable', 'string', 'not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
        ]);

        $transaction = new CompanyProjectTransaction();
        $transaction->project_id = $request->input('project_id');
        $transaction->date = $request->input('date');
        $transaction->type = $request->input('type');
        $transaction->amount = $request->input('amount');   
        $transaction->description = $request->input('description');
        $transaction->save();

        return redirect()->back()->with('success', 'Transaction recorded successfully!');
    }
    
    public function list()
    {
        $projects = CompanyProject::all();
        $transactions = CompanyProjectTransaction::all();
        foreach ($projects as $project) {
            $project->initial_invest = $project->initial_amount;
            $project->total_invest = $transactions->where('project_id', $project->id)
                ->where('type', 'invest')
                ->sum('amount');
            $project->total_profit = $transactions->where('project_id', $project->id)
                ->where('type', 'profit')
                ->sum('amount');
            $project->total_loss = $transactions->where('project_id', $project->id)
                ->where('type', 'loss')
                ->sum('amount');
            $project->remaining_balance = ($project->initial_invest + $project->total_profit) - $project->total_invest;
        }
        return view('company_project.list_project', compact('projects'));
    }

    public function getTransactions(Request $request, $projectId)
    {
        $project = CompanyProject::findOrFail($projectId);

        $query = CompanyProjectTransaction::where('project_id', $projectId);

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


    public function deleteProject($id)
    {
        $project = CompanyProject::findOrFail($id);
        $project->delete();

        return redirect()->back()->with('success', 'Project deleted successfully!');
    }
}
