<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductLoss;
use App\Models\ProductSale;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function index()
    {
        $totals = [
            'current_stock' => ProductCategory::sum('quantity'),
            'total_stock'   => Product::sum('quantity'),
            'sell_qty'      => ProductSale::sum('quantity'),
            'loss_qty'      => ProductLoss::sum('quantity'),
            'revenue'       => ProductSale::sum('amount'),
            'loss'          => ProductLoss::sum('loss_amount'),
        ];

        return view('products.summary', compact('totals'));
    }
    
    public function create()
    {
        $categories = ProductCategory::all();
        return view('products.add', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate(['name' => 'required|unique:product_categories']);
        ProductCategory::create([
            'name' => $request->name,
            'quantity' => 0, // Default quantity for new category
        ]);
        return back()->with('success', 'Category created successfully.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_category_id' => 'required|exists:product_categories,id',
            'product_name' => 'required',
            'quantity' => 'required|integer|min:1',
        ]);
        Product::create($request->all());
        // Increment the quantity in the product category
        if ($request->quantity < 0) {
            return back()->with('error', 'Quantity cannot be negative.');
        }
        $category = ProductCategory::findOrFail($request->product_category_id);
        $category->increment('quantity', $request->quantity);

        return back()->with('success', 'Product added successfully.');
    }

    public function sell()
    {
        $categories = ProductCategory::all();
        return view('products.sell', compact('categories'));
    }

    public function storeSoldProduct(Request $request)
    {
        $request->validate([
            'product_category_id' => 'required|exists:product_categories,id',
            'quantity'   => 'required|integer|min:1',
            'amount'     => 'required|numeric|min:0',
            'description' => ['nullable', 'string', 'not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
        ]);

        ProductSale::create([
            'product_category_id' => $request->product_category_id,
            'quantity' => $request->quantity,
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        // Decrement the quantity in the product category
        $category = ProductCategory::findOrFail($request->product_category_id);
        if ($category->quantity < $request->quantity) {
            return back()->with('error', 'Not enough stock available.');
        }
        $category->decrement('quantity', $request->quantity);

        return back()->with('success', 'Sale recorded successfully.');
    }

    public function loss()
    {
        $categories = ProductCategory::all();
        return view('products.loss', compact('categories'));
    }

    public function storeLossProduct(Request $request)
    {
        $request->validate([
            'product_category_id' => 'required|exists:product_categories,id',
            'quantity'   => 'required|integer|min:1',
            'loss_amount'     => 'required|numeric|min:0',
            'description' => ['nullable', 'string', 'not_regex:/<[^>]*>|<script\b[^>]*>(.*?)<\/script>/i'],
        ]);

        ProductLoss::create([
            'product_category_id' => $request->product_category_id,
            'quantity' => $request->quantity,
            'loss_amount' => $request->loss_amount,
            'description' => $request->description,
        ]);

        // Decrement the quantity in the product category
        $category = ProductCategory::findOrFail($request->product_category_id);
        if ($category->quantity < $request->quantity) {
            return back()->with('error', 'Not enough stock available.');
        }
        $category->decrement('quantity', $request->quantity);

        return back()->with('success', 'Loss recorded successfully.');
    }


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

        // --- Summary and details logic stays the same ---
        // (same as in my last version, only difference is date range)
        
        // Main summary
        $categories = \App\Models\ProductCategory::with('products')->get()->map(function ($cat) use ($start, $end) {
            $sales = \App\Models\ProductSale::where('product_category_id', $cat->id)
                ->whereBetween('created_at', [$start, $end])->get();
            $losses = \App\Models\ProductLoss::where('product_category_id', $cat->id)
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

            $sales = \App\Models\ProductSale::where('product_category_id', $categoryId)
                ->whereBetween('created_at', [$start, $end])->get();
            $losses = \App\Models\ProductLoss::where('product_category_id', $categoryId)
                ->whereBetween('created_at', [$start, $end])->get();
            $products = \App\Models\Product::where('product_category_id', $categoryId)->get();

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


