<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductLoss;
use App\Models\ProductReturn;
use App\Models\ProductSale;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function index()
    {
        $totals = [
            'total_stock'   => ProductCategory::sum('total_quantity'),
            'current_stock' => ProductCategory::sum('quantity'),
            'sell_qty'      => ProductSale::sum('quantity'),
            'loss_qty'      => ProductLoss::sum('quantity'),
            'return_qty'    => ProductReturn::sum('quantity'),
            'revenue'       => ProductSale::sum('amount'),
            'loss'          => ProductLoss::sum('loss_amount'),
        ];

        return view('products.summary', compact('totals'));
    }
    
// Product Category Management
    public function category()
    {
        $categories = ProductCategory::all();
        return view('products.category', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate(['name' => 'required|unique:product_categories']);
        ProductCategory::create([
            'name' => $request->name,
            'total_quantity' => 0, // Default quantity for new category
            'quantity' => 0, // Default quantity for new category
        ]);
        return back()->with('success', 'Category created successfully.');
    }

    public function editCategory($id)
    {
        $category = ProductCategory::findOrFail($id);
        return response()->json($category); // return data for modal
    }

    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:product_categories,name,' . $id,
        ]);

        $category = ProductCategory::findOrFail($id);
        $category->update([
            'name' => $request->name,
        ]);

        return back()->with('success', 'Category updated successfully.');
    }

    public function destroyCategory($id)
    {
        $category = ProductCategory::findOrFail($id);
        $category->delete();

        return back()->with('success', 'Category deleted successfully.');
    }

// Product Management
    public function create()
    {
        $categories = ProductCategory::all();
        
        $products = Product::where('status','pending')->with('category')->get();
        return view('products.add', compact('categories','products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_category_id' => 'required|exists:product_categories,id',
            'quantity' => 'required|integer|min:1',
            'amount' => 'nullable|numeric|min:0',
        ]);

        Product::create([
            'product_category_id' => $request->product_category_id,
            'quantity' => $request->quantity,
            'amount_type' => $request->amount_type ?? 'dollar', // default to 'dollar' if not provided
            'amount' => $request->amount ?? 0, // default to 0 if not provided
            'status' => 'pending', // default status
        ]);

        return back()->with('success', 'Product added successfully. Pending approval.');
    }

    public function editProduct($id)
    {
        $product = Product::with('category')->findOrFail($id);
        return response()->json($product);
    }

    public function updateProduct(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'amount' => 'nullable|numeric|min:0',
        ]);

        $product = Product::findOrFail($id);
        $product->update([
            'quantity' => $request->quantity,
            'amount_type' => $request->amount_type ?? 'dollar', // default to 'dollar' if not provided
            'amount' => $request->amount ?? 0, // default to 0 if not provided
            'status' => 'pending', // default status
        ]);

        return back()->with('success', 'Product updated successfully.');
    }

    public function approveProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['status' => 'approved']);

        // increment category stock after approval
        $category = ProductCategory::findOrFail($product->product_category_id);
        $category->increment('total_quantity', $product->quantity);
        $category->increment('quantity', $product->quantity);

        return back()->with('success', 'Product approved successfully.');
    }

    public function destroyProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        
        return back()->with('success', 'Product deleted successfully.');
    }

// Product Sale Management
    public function sell()
    {
        $categories = ProductCategory::all();
        // Show only pending sales
        $sales = ProductSale::where('status', 'unpaid')->with('category')->get();
        return view('products.sell', compact('categories','sales'));
    }

    public function storeSoldProduct(Request $request)
    {
        $request->validate([
            'product_category_id' => 'required|exists:product_categories,id',
            'quantity'   => 'required|integer|min:1',
            'amount'     => 'required|numeric|min:0',
            'description'=> ['nullable','string','not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
        ]);

        ProductSale::create([
            'product_category_id' => $request->product_category_id,
            'quantity'   => $request->quantity,
            'amount_type' => $request->amount_type ?? 'dollar',
            'amount'     => $request->amount,
            'description'=> $request->description,
            'paid_date'  => null,
            'status'     => 'unpaid',
        ]);

        return back()->with('success', 'Sale recorded successfully. Pending approval.');
    }

    public function editSale($id)
    {
        $sale = ProductSale::with('category')->findOrFail($id);
        return response()->json($sale);
    }

    public function updateSale(Request $request, $id)
    {
        $request->validate([
            'quantity'   => 'required|integer|min:1',
            'amount'     => 'required|numeric|min:0',
            'description'=> ['nullable','string','not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
        ]);

        $sale = ProductSale::findOrFail($id);
        $sale->update([
            'quantity'   => $request->quantity,
            'amount_type'     => $request->amount_type ?? 'dollar', // default to 'dollar' if not provided
            'amount'     => $request->amount,
            'description'=> $request->description,
        ]);

        return back()->with('success', 'Sale updated successfully.');
    }

    public function approveSale($id)
    {
        $sale = ProductSale::findOrFail($id);

        // check stock before approving
        $category = ProductCategory::findOrFail($sale->product_category_id);
        if ($category->quantity < $sale->quantity) {
            return back()->with('error', 'Not enough stock to approve this sale.');
        }

        $sale->update([
            'status'    => 'paid',
            'paid_date' => now('Asia/Dhaka')->toDateString(),
        ]);

        // reduce stock
        $category->decrement('quantity', $sale->quantity);

        return back()->with('success', 'Sale approved successfully.');
    }

    public function destroySale($id)
    {
        $sale = ProductSale::findOrFail($id);
        $sale->delete();

        return back()->with('success', 'Sale deleted successfully.');
    }

// Product Loss Management
    public function loss()
    {
        $categories = ProductCategory::all();
        $losses = ProductLoss::where('status', 'pending')->with('category')->get();
        return view('products.loss', compact('categories','losses'));
    }

    public function storeLossProduct(Request $request)
    {
        $request->validate([
            'product_category_id' => 'required|exists:product_categories,id',
            'quantity'   => 'required|integer|min:1',
            'loss_amount'=> 'required|numeric|min:0',
            'description'=> ['nullable','string','not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
        ]);

        ProductLoss::create([
            'product_category_id' => $request->product_category_id,
            'quantity'   => $request->quantity,
            'amount_type'=> $request->amount_type ?? 'dollar',
            'loss_amount'=> $request->loss_amount,
            'description'=> $request->description,
            'status'     => 'pending',
        ]);

        return back()->with('success', 'Loss entry created. Pending approval.');
    }

    public function editLoss($id)
    {
        $loss = ProductLoss::with('category')->findOrFail($id);
        return response()->json($loss);
    }

    public function updateLoss(Request $request, $id)
    {
        $request->validate([
            'quantity'   => 'required|integer|min:1',
            'loss_amount'=> 'required|numeric|min:0',
            'description'=> ['nullable','string','not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
        ]);

        $loss = ProductLoss::findOrFail($id);
        $loss->update([
            'quantity'   => $request->quantity,
            'amount_type'=> $request->amount_type ?? 'dollar',
            'loss_amount'=> $request->loss_amount,
            'description'=> $request->description,
        ]);

        return back()->with('success', 'Loss updated successfully.');
    }

    public function approveLoss($id)
    {
        $loss = ProductLoss::findOrFail($id);

        $category = ProductCategory::findOrFail($loss->product_category_id);
        if ($category->quantity < $loss->quantity) {
            return back()->with('error', 'Not enough stock to approve this loss.');
        }

        $loss->update(['status' => 'approved']);
        $category->decrement('quantity', $loss->quantity);

        return back()->with('success', 'Loss approved successfully.');
    }

    public function destroyLoss($id)
    {
        $loss = ProductLoss::findOrFail($id);
        $loss->delete();

        return back()->with('success', 'Loss deleted successfully.');
    }

// Product Return Management
    public function return()
    {
        $categories = ProductCategory::all();
        $returns = ProductReturn::where('status', 'pending')->with('category')->get();
        return view('products.return', compact('categories','returns'));
    }

    public function storeReturnProduct(Request $request)
    {
        $request->validate([
            'product_category_id' => 'required|exists:product_categories,id',
            'quantity'   => 'required|integer|min:1',
            'amount'=> 'required|numeric|min:0',
            'description'=> ['nullable','string','not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
        ]);

        ProductReturn::create([
            'product_category_id' => $request->product_category_id,
            'quantity'   => $request->quantity,
            'amount_type'=> $request->amount_type ?? 'dollar',
            'amount'=> $request->amount,
            'description'=> $request->description,
            'status'     => 'pending',
        ]);

        return back()->with('success', 'Return entry created. Pending approval.');
    }

    public function editReturn($id)
    {
        $return = ProductReturn::with('category')->findOrFail($id);
        return response()->json($return);
    }

    public function updateReturn(Request $request, $id)
    {
        $request->validate([
            'quantity'   => 'required|integer|min:1',
            'amount'=> 'required|numeric|min:0',
            'description'=> ['nullable','string','not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
        ]);

        $return = ProductReturn::findOrFail($id);
        $return->update([
            'quantity'   => $request->quantity,
            'amount_type'=> $request->amount_type ?? 'dollar',
            'amount'=> $request->amount,
            'description'=> $request->description,
        ]);

        return back()->with('success', 'Return updated successfully.');
    }

    public function approveReturn($id)
    {
        $return = ProductReturn::findOrFail($id);
        
        $category = ProductCategory::findOrFail($return->product_category_id);
        
        $category->increment('quantity', $return->quantity);
        $return->update(['status' => 'approved']);

        return back()->with('success', 'Return approved successfully.');
    }

    public function destroyReturn($id)
    {
        $return = ProductReturn::findOrFail($id);
        $return->delete();

        return back()->with('success', 'Return deleted successfully.');
    }


// Product Report
    public function report(Request $request)
    {
        $filterType = $request->input('filter_type', 'month'); // day|month|year|range
        $date  = $request->input('date');
        $month = $request->input('month');
        $year  = $request->input('year');
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $categoryId = $request->input('category_id');

        $today = now();

        // --- Date range selection ---
        if ($filterType === 'day' && $date) {
            $start = \Carbon\Carbon::parse($date)->startOfDay();
            $end   = \Carbon\Carbon::parse($date)->endOfDay();
        } elseif ($filterType === 'month' && $month && $year) {
            $start = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $end   = \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth();
        } elseif ($filterType === 'year' && $year) {
            $start = \Carbon\Carbon::createFromDate($year, 1, 1)->startOfYear();
            $end   = \Carbon\Carbon::createFromDate($year, 12, 31)->endOfYear();
        } elseif ($filterType === 'range' && $startDate && $endDate) {
            $start = \Carbon\Carbon::parse($startDate)->startOfDay();
            $end   = \Carbon\Carbon::parse($endDate)->endOfDay();
        } else {
            // Default: current month
            $start = $today->copy()->startOfMonth();
            $end   = $today->copy()->endOfMonth();
            $filterType = 'month';
            $month = $today->month;
            $year  = $today->year;
        }

    
        // Main summary
        $categories = ProductCategory::with('products')->get()->map(function ($cat) use ($start, $end) {
            $sales = ProductSale::where('product_category_id', $cat->id)
                ->whereBetween('created_at', [$start, $end])->get();
            $losses = ProductLoss::where('product_category_id', $cat->id)
                ->whereBetween('created_at', [$start, $end])->get();

            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'current_stock' => $cat->quantity,
                'total_stock' => $cat->products->sum('quantity'),
                'sell_qty' => $sales->sum('quantity'),
                'loss_qty' => $losses->sum('quantity'),
                'revenue' => $sales->sum('amount'),
                'loss' => $losses->sum('loss_amount'),
            ];
        });

        $totals = [
            'current_stock' => $categories->sum('current_stock'),
            'total_stock'   => $categories->sum('total_stock'),
            'sell_qty'      => $categories->sum('sell_qty'),
            'loss_qty'      => $categories->sum('loss_qty'),
            'revenue'       => $categories->sum('revenue'),
            'loss'          => $categories->sum('loss'),
        ];

        // Details (same as before, uses $start/$end)
        $details = null;
        $categoryName = null;
        if ($categoryId) {
            $category = \App\Models\ProductCategory::findOrFail($categoryId);
            $categoryName = $category->name;

            $sales = ProductSale::where('product_category_id', $categoryId)
                ->whereBetween('created_at', [$start, $end])->get();
            $losses = ProductLoss::where('product_category_id', $categoryId)
                ->whereBetween('created_at', [$start, $end])->get();
            $products = Product::where('product_category_id', $categoryId)->get();

            $details = collect();

            foreach ($sales as $s) {
                $details->push([
                    'date' => $s->created_at,
                    'product_name' => null,
                    'stock_qty' => null,
                    'sell_qty' => $s->quantity,
                    'amount' => $s->amount,
                    'sell_desc' => $s->description,
                    'loss_qty' => null,
                    'loss_amount' => null,
                    'loss_desc' => null,
                ]);
            }

            foreach ($losses as $l) {
                $details->push([
                    'date' => $l->created_at,
                    'product_name' => null,
                    'stock_qty' => null,
                    'sell_qty' => null,
                    'amount' => null,
                    'sell_desc' => null,
                    'loss_qty' => $l->quantity,
                    'loss_amount' => $l->loss_amount,
                    'loss_desc' => $l->description,
                ]);
            }

            foreach ($products as $p) {
                $details->push([
                    'date' => $p->created_at,
                    'product_name' => $p->product_name,
                    'stock_qty' => $p->quantity,
                    'sell_qty' => null,
                    'amount' => null,
                    'sell_desc' => null,
                    'loss_qty' => null,
                    'loss_amount' => null,
                    'loss_desc' => null,
                ]);
            }

            $details = $details->sortByDesc('date');
        }

        return view('products.report', compact(
            'categories','totals','filterType','date','month','year','startDate','endDate',
            'categoryId','details','categoryName'
        ));
    }


}


