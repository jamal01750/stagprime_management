<?php

namespace App\Http\Controllers;

use App\Models\PriorityNotification;
use App\Models\PriorityProduct;
use App\Models\PriorityProductBudget;
use Illuminate\Http\Request;

class PriorityController extends Controller
{
    public function index()
    {
        $priorities = PriorityProduct::latest()->paginate(10); // Paginated list
        return view('priority.list', compact('priorities'));
    }

    public function create()
    {
        return view('priority.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'amount' => 'required|numeric',
            'description' => ['nullable', 'string', 'not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
        ]);

        PriorityProduct::create($request->only(['name','quantity','amount','description']));

        return redirect()->route('priority.list')->with('success', 'Priority Product/Project added successfully.');
    }

    public function edit(PriorityProduct $priority)
    {
        return view('priority.edit', compact('priority'));
    }

    public function update(Request $request, PriorityProduct $priority)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'amount' => 'required|numeric',
            'description' => ['nullable', 'string', 'not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
        ]);

        $priority->update($request->only(['name','quantity','amount','description']));

        return redirect()->route('priority.list')->with('success', 'Priority Product/Project updated successfully.');
    }

    public function destroy(PriorityProduct $priority)
    {
        $priority->delete();
        return redirect()->route('priority.list')->with('success', 'Priority Product/Project deleted successfully.');
    }

    
    public function purchase(PriorityProduct $priority)
    {
        $priority->update(['is_purchased' => true]);

        // Get current month's budget
        $budget = PriorityProductBudget::where('year', now()->year)
            ->where('month', now()->month)
            ->first();

        if ($budget) {
            $budget->extra_budget -= $priority->amount;
            $budget->save();

            // Recheck product notifications
            $extraBudget = $budget->extra_budget;
            $this->updateProductNotifications($extraBudget);
        }

        return redirect()->route('priority.list')
            ->with('success', 'Priority product purchased. Budget and notifications updated.');
    }

    protected function updateProductNotifications($extraBudget)
    {
        $priorityProducts = PriorityProduct::where('is_purchased', false)->get();

        foreach ($priorityProducts as $product) {
            $required = $product->amount * 2;

            if ($extraBudget >= $required) {
                PriorityNotification::updateOrCreate(
                    ['priority_product_id' => $product->id],
                    ['is_active' => true]
                );
            } else {
                PriorityNotification::where('priority_product_id', $product->id)->delete();
            }
        }
    }

}
