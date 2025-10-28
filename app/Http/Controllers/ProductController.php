<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductLoss;
use App\Models\ProductReturn;
use App\Models\ProductSale;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductController extends Controller
{
    public function index()
    {
        $exchangeRate = session('result', 1); // Default to 1 if not set
        $saleQty  = ProductSale::where('status', 'paid')->sum('quantity');
        $returQty = ProductReturn::where('status', 'approved')->sum('quantity');
        $saleQuantity = $saleQty - $returQty;
        $loosQty = ProductLoss::where('status', 'approved')->sum('quantity');
        
        $salesRev = 0;
        $sales = ProductSale::where('status', 'paid')->get();
        foreach($sales as $sale) {
            if ($sale->amount_type === 'dollar') {
                $salesRev += $sale->amount * $exchangeRate; 
            } else {
                $salesRev += $sale->amount; 
            }
        }

        $lossAmount = 0;
        $losses = ProductLoss::where('status', 'approved')->get();
        foreach($losses as $loss) {
            if ($loss->amount_type === 'dollar') {
                $lossAmount += $loss->loss_amount * $exchangeRate;
            } else {
                $lossAmount += $loss->loss_amount;
            }
        }

        $returnRev = 0;
        $returns = ProductReturn::where('status', 'approved')->get();
        foreach($returns as $return) {
            if ($return->amount_type === 'dollar') {
                $returnRev += $return->amount * $exchangeRate;
            } else {
                $returnRev += $return->amount;
            }
        }
        $totalRevenue = $salesRev - $returnRev;
        
        $totals = [
            'total_stock'   => ProductCategory::sum('total_quantity'),
            'current_stock' => ProductCategory::sum('quantity'),
            'sell_qty'      => $saleQuantity,
            'loss_qty'      => $loosQty,
            'return_qty'    => $returQty,
            'revenue'       => $totalRevenue,
            'loss'          => $lossAmount,
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

    public function downloadCategoryPdf()
    {
        $categories = ProductCategory::all();
        $pdf = PDF::loadView('pdf.product_categories', compact('categories'));
        return $pdf->stream('product_categories.pdf');
    }

// Product Management
    public function create()
    {
        $categories = ProductCategory::all();
        
        $products = Product::where('status','pending')->with('category')->paginate(5);
        $totalAmount = $products->sum(function ($product) {
            return $product->amount_type === 'dollar' ? $product->amount * session('result', 1) : $product->amount;
        });
        $totalQuantity = $products->sum('quantity');
        return view('products.add', compact('categories','products','totalAmount', 'totalQuantity'));
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

    public function downloadProductPdf()
    {
        $products = Product::where('status', 'pending')->with('category')->get();
        $totalAmount = $products->sum(function ($product) {
            return $product->amount_type === 'dollar' ? $product->amount * session('result', 1) : $product->amount;
        });
        $totalQuantity = $products->sum('quantity');
        $pdf = PDF::loadView('pdf.pending_products', compact('products', 'totalAmount', 'totalQuantity'));
        return $pdf->stream('pending_products.pdf');
    }

// Product Sale Management
    public function sell()
    {
        $categories = ProductCategory::all();
        // Show only pending sales
        $sales = ProductSale::where('status', 'unpaid')->with('category')->paginate(20);
        $totalAmount = $sales->sum(function ($product) {
            return $product->amount_type === 'dollar' ? $product->amount * session('result', 1) : $product->amount;
        });
        $totalQuantity = $sales->sum('quantity');
        
        return view('products.sell', compact('categories','sales','totalAmount', 'totalQuantity'));
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

    public function downloadProductSalesPdf()
    {
        $sales = ProductSale::where('status', 'unpaid')->with('category')->get();
        $totalAmount = $sales->sum(function ($sale) {
            return $sale->amount_type === 'dollar' ? $sale->amount * session('result', 1) : $sale->amount;
        });
        $totalQuantity = $sales->sum('quantity');
        
        $pdf = PDF::loadView('pdf.pending_sales', compact('sales', 'totalAmount', 'totalQuantity'));
        return $pdf->stream('pending_sales.pdf');
    }

// Product Loss Management
    public function loss()
    {
        $categories = ProductCategory::all();
        $losses = ProductLoss::where('status', 'pending')->with('category')->paginate(20);
        $totalAmount = $losses->sum(function ($loss) {
            return $loss->amount_type === 'dollar' ? $loss->loss_amount * session('result', 1) : $loss->loss_amount;
        });
        $totalQuantity = $losses->sum('quantity');
        return view('products.loss', compact('categories','losses', 'totalAmount', 'totalQuantity'));
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

    public function downloadLossPdf()
    {
        $losses = ProductLoss::where('status', 'pending')->with('category')->get();
        $totalAmount = $losses->sum(function ($loss) {
            return $loss->amount_type === 'dollar' ? $loss->loss_amount * session('result', 1) : $loss->loss_amount;
        });
        $totalQuantity = $losses->sum('quantity');
        
        $pdf = PDF::loadView('pdf.pending_losses', compact('losses', 'totalAmount', 'totalQuantity'));
        return $pdf->stream('pending_losses.pdf');
    }

// Product Return Management
    public function return()
    {
        $categories = ProductCategory::all();
        $returns = ProductReturn::where('status', 'pending')->with('category')->paginate(20);
        $totalAmount = $returns->sum(function ($return) {
            return $return->amount_type === 'dollar' ? $return->amount * session('result', 1) : $return->amount;
        });
        $totalQuantity = $returns->sum('quantity');
        return view('products.return', compact('categories','returns', 'totalAmount', 'totalQuantity'));
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

    public function downloadReturnPdf()
    {
        $returns = ProductReturn::where('status', 'pending')->with('category')->get();
        $totalAmount = $returns->sum(function ($return) {
            return $return->amount_type === 'dollar' ? $return->amount * session('result', 1) : $return->amount;
        });
        $totalQuantity = $returns->sum('quantity');
        $pdf = PDF::loadView('pdf.pending_returns', compact('returns', 'totalAmount', 'totalQuantity'));
        return $pdf->stream('pending_returns.pdf');
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
        $exchangeRate = session('result', 1); // Default to 1 if not set

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
            $stocks = Product::where('product_category_id', $cat->id)
                ->where('status', 'approved') 
                ->whereBetween('updated_at', [$start, $end])->get();
            $sales = ProductSale::where('product_category_id', $cat->id)
                ->where('status', 'paid') 
                ->whereBetween('paid_date', [$start, $end])->get();
            $losses = ProductLoss::where('product_category_id', $cat->id)
                ->where('status', 'approved') 
                ->whereBetween('updated_at', [$start, $end])->get();
            $returns = ProductReturn::where('product_category_id', $cat->id)
                ->where('status', 'approved') 
                ->whereBetween('updated_at', [$start, $end])->get();

            $saleQty = $sales->sum('quantity') - $returns->sum('quantity');
            $currentStock = $stocks->sum('quantity') - $sales->sum('quantity') - $losses->sum('quantity') + $returns->sum('quantity');

            $exchangeRate = session('result', 1);
            $saleAmount = 0;
            foreach ($sales as $sale) {
                if ($sale->amount_type === 'dollar') {
                    $saleAmount += $sale->amount * $exchangeRate; // Use exchange rate
                } else {
                    $saleAmount += $sale->amount;
                }
            }
            foreach ($returns as $return) {
                if ($return->amount_type === 'dollar') {
                    $saleAmount -= $return->amount * $exchangeRate; // Use exchange rate
                } else {
                    $saleAmount -= $return->amount;
                }
            }

            $lossAmount = $losses->sum(function ($loss) use ($exchangeRate) {
                return $loss->amount_type === 'dollar' ? $loss->loss_amount * $exchangeRate : $loss->loss_amount;
            });

            $returnAmount = $returns->sum(function ($return) use ($exchangeRate) {
                return $return->amount_type === 'dollar' ? $return->amount * $exchangeRate : $return->amount;
            });

            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'current_stock' => $currentStock,
                'total_stock' => $stocks->sum('quantity'),
                'sell_qty' => $saleQty,
                'loss_qty' => $losses->sum('quantity'),
                'return_qty' => $returns->sum('quantity'),
                'revenue' => $saleAmount,
                'loss' => $lossAmount,
                'return' => $returnAmount,
            ];
        });

        $totals = [
            'current_stock' => $categories->sum('current_stock'),
            'total_stock'   => $categories->sum('total_stock'),
            'sell_qty'      => $categories->sum('sell_qty'),
            'loss_qty'      => $categories->sum('loss_qty'),
            'return_qty'    => $categories->sum('return_qty'),
            'revenue'       => $categories->sum('revenue'),
            'loss'          => $categories->sum('loss'),
            'return'        => $categories->sum('return'),
        ];

        // Details (same as before, uses $start/$end)
        $details = null;
        $categoryName = null;
        if ($categoryId) {
            $category = ProductCategory::findOrFail($categoryId);
            $categoryName = $category->name;
            
            $stocks = Product::where('product_category_id', $categoryId)
                ->where('status', 'approved') 
                ->whereBetween('updated_at', [$start, $end])->get();
            $sales = ProductSale::where('product_category_id', $categoryId)
                ->where('status', 'paid') 
                ->whereBetween('paid_date', [$start, $end])->get();
            $losses = ProductLoss::where('product_category_id', $categoryId)
                ->where('status', 'approved') 
                ->whereBetween('updated_at', [$start, $end])->get();
            $returns = ProductReturn::where('product_category_id', $categoryId)
                ->where('status', 'approved') 
                ->whereBetween('updated_at', [$start, $end])->get();

            $details = collect();

            foreach ($sales as $s) {
                $details->push([
                    'date' => $s->updated_at,
                    'stock_qty' => null,
                    's_type' => null,
                    'stock_amount' => null,
                    'sell_qty' => $s->quantity,
                    'sa_type' => $s->amount_type,
                    'amount' => $s->amount,
                    'sell_desc' => $s->description,
                    'loss_qty' => null,
                    'la_type' => null,
                    'loss_amount' => null,
                    'loss_desc' => null,
                    'return_qty' => null,
                    'ra_type' => null,
                    'return_amount' => null,
                    'return_desc' => null,
                ]);
            }

            foreach ($losses as $l) {
                $details->push([
                    'date' => $l->updated_at,
                    'stock_qty' => null,
                    's_type' => null,
                    'stock_amount' => null,
                    'sell_qty' => null,
                    'sa_type' => null,
                    'amount' => null,
                    'sell_desc' => null,
                    'loss_qty' => $l->quantity,
                    'la_type' => $l->amount_type,
                    'loss_amount' => $l->loss_amount,
                    'loss_desc' => $l->description,
                    'return_qty' => null,
                    'ra_type' => null,
                    'return_amount' => null,
                    'return_desc' => null,
                ]);
            }

            foreach ($returns as $r) {
                $details->push([
                    'date' => $r->updated_at,
                    'stock_qty' => null,
                    's_type' => null,
                    'stock_amount' => null,
                    'sell_qty' => null,
                    'sa_type' => null,
                    'amount' => null,
                    'sell_desc' => null,
                    'loss_qty' => null,
                    'la_type' => null,
                    'loss_amount' => null,
                    'loss_desc' => null,
                    'return_qty' => $r->quantity,
                    'ra_type' => $r->amount_type,
                    'return_amount' => $r->amount,
                    'return_desc' => $r->description,
                ]);
            }

            foreach ($stocks as $s) {
                $details->push([
                    'date' => $s->updated_at,
                    'stock_qty' => $s->quantity,
                    's_type' => $s->amount_type,
                    'stock_amount' => $s->amount,
                    'sell_qty' => null,
                    'sa_type' => null,
                    'amount' => null,
                    'sell_desc' => null,
                    'loss_qty' => null,
                    'la_type' => null,
                    'loss_amount' => null,
                    'loss_desc' => null,
                    'return_qty' => null,
                    'ra_type' => null,
                    'return_amount' => null,
                    'return_desc' => null,
                ]);
            }
            
            $details = $details->sortByDesc('date');
        }

        return view('products.report', compact(
            'categories','totals','filterType','date','month','year','startDate','endDate',
            'categoryId','details','categoryName'
        ));
    }

    public function stockReport(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $selected_status = $request->input('status');
        $selected_category = $request->input('category_id');

        $categories = ProductCategory::orderBy('name')->get();

        $query = Product::with('category');

        if ($start_date) {
            $query->whereDate('created_at', '>=', $start_date);
        }

        if ($end_date) {
            $query->whereDate('created_at', '<=', $end_date);
        }

        if ($selected_status) {
            $query->where('status', $selected_status);
        }

        if ($selected_category) {
            $query->where('product_category_id', $selected_category);
        }

        $products = $query->latest()->paginate(10);

        $totalQuantity = $products->sum('quantity');
        $totalAmount = $products->sum(function ($product) {
            return $product->amount_type === 'dollar' ? $product->amount * session('result', 1) : $product->amount;
        });

        return view('products.stock_report', compact(
            'products',
            'categories',
            'start_date',
            'end_date',
            'selected_status',
            'selected_category',
            'totalQuantity',
            'totalAmount'
        ));
    }

    public function updateStockStatus(Request $request)
    {
        $product = Product::find($request->id);
        if ($product) {
            $product->status = $request->status;
            $product->save();
            return response()->json(['message' => 'Status updated successfully!']);
        }
        return response()->json(['message' => 'Product not found!'], 404);
    }

    public function downloadProductFilterPdf(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $selected_status = $request->input('status');
        $selected_category = $request->input('category_id');

        // $categories = ProductCategory::orderBy('name')->get();

        $query = Product::with('category');

        if ($start_date) {
            $query->whereDate('created_at', '>=', $start_date);
        }

        if ($end_date) {
            $query->whereDate('created_at', '<=', $end_date);
        }

        if ($selected_status) {
            $query->where('status', $selected_status);
        }

        if ($selected_category) {
            $query->where('product_category_id', $selected_category);
        }

        $products = $query->orderBy('created_at')->get();

        $totalQuantity = $products->sum('quantity');
        $totalAmount = $products->sum(function ($product) {
            return $product->amount_type === 'dollar' ? $product->amount * session('result', 1) : $product->amount;
        });

        $pdf = PDF::loadView('pdf.pending_products', compact('products', 'totalAmount', 'totalQuantity'));
        return $pdf->stream('pending_products.pdf');
    }

    public function sellReport(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $selected_status = $request->input('status');
        $selected_category = $request->input('category_id');

        $categories = ProductCategory::orderBy('name')->get();

        $query = ProductSale::with('category');

        if ($start_date) {
            $query->whereDate('created_at', '>=', $start_date);
        }

        if ($end_date) {
            $query->whereDate('created_at', '<=', $end_date);
        }

        if ($selected_status) {
            $query->where('status', $selected_status);
        }

        if ($selected_category) {
            $query->where('product_category_id', $selected_category);
        }

        $products = $query->latest()->paginate(10);

        $totalQuantity = $products->sum('quantity');
        $totalAmount = $products->sum(function ($product) {
            return $product->amount_type === 'dollar' ? $product->amount * session('result', 1) : $product->amount;
        });

        return view('products.sell_report', compact(
            'products',
            'categories',
            'start_date',
            'end_date',
            'selected_status',
            'selected_category',
            'totalQuantity',
            'totalAmount'
        ));
    }

    public function updateSellStatus(Request $request)
    {
        $product = ProductSale::find($request->id);
        if ($product) {
            $product->status = $request->status;
            $product->save();
            return response()->json(['message' => 'Status updated successfully!']);
        }
        return response()->json(['message' => 'Product not found!'], 404);
    }

    public function downloadSellFilterPdf(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $selected_status = $request->input('status');
        $selected_category = $request->input('category_id');

        $query = ProductSale::with('category');

        if ($start_date) {
            $query->whereDate('created_at', '>=', $start_date);
        }

        if ($end_date) {
            $query->whereDate('created_at', '<=', $end_date);
        }

        if ($selected_status) {
            $query->where('status', $selected_status);
        }

        if ($selected_category) {
            $query->where('product_category_id', $selected_category);
        }

        $sales = $query->orderBy('created_at')->get();

        $totalQuantity = $sales->sum('quantity');
        $totalAmount = $sales->sum(function ($product) {
            return $product->amount_type === 'dollar' ? $product->amount * session('result', 1) : $product->amount;
        });

        $pdf = PDF::loadView('pdf.pending_sales', compact('sales', 'totalAmount', 'totalQuantity'));
        return $pdf->stream('pending_sales.pdf');
    }

    public function lossReport(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $selected_status = $request->input('status');
        $selected_category = $request->input('category_id');

        $categories = ProductCategory::orderBy('name')->get();

        $query = ProductLoss::with('category');

        if ($start_date) {
            $query->whereDate('created_at', '>=', $start_date);
        }

        if ($end_date) {
            $query->whereDate('created_at', '<=', $end_date);
        }

        if ($selected_status) {
            $query->where('status', $selected_status);
        }

        if ($selected_category) {
            $query->where('product_category_id', $selected_category);
        }

        $products = $query->latest()->paginate(10);

        $totalQuantity = $products->sum('quantity');
        $totalAmount = $products->sum(function ($product) {
            return $product->amount_type === 'dollar' ? $product->loss_amount * session('result', 1) : $product->loss_amount;
        });

        return view('products.loss_report', compact(
            'products',
            'categories',
            'start_date',
            'end_date',
            'selected_status',
            'selected_category',
            'totalQuantity',
            'totalAmount'
        ));
    }

    public function updateLossStatus(Request $request)
    {
        $product = ProductLoss::find($request->id);
        if ($product) {
            $product->status = $request->status;
            $product->save();
            return response()->json(['message' => 'Status updated successfully!']);
        }
        return response()->json(['message' => 'Product not found!'], 404);
    }

    public function downloadLossFilterPdf(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $selected_status = $request->input('status');
        $selected_category = $request->input('category_id');

        $query = ProductLoss::with('category');

        if ($start_date) {
            $query->whereDate('created_at', '>=', $start_date);
        }

        if ($end_date) {
            $query->whereDate('created_at', '<=', $end_date);
        }

        if ($selected_status) {
            $query->where('status', $selected_status);
        }

        if ($selected_category) {
            $query->where('product_category_id', $selected_category);
        }

        $losses = $query->orderBy('created_at')->get();

        $totalQuantity = $losses->sum('quantity');
        $totalAmount = $losses->sum(function ($product) {
            return $product->amount_type === 'dollar' ? $product->loss_amount * session('result', 1) : $product->loss_amount;
        });

        $pdf = PDF::loadView('pdf.pending_losses', compact('losses', 'totalAmount', 'totalQuantity'));
        return $pdf->stream('pending_losses.pdf');
    }

    public function returnReport(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $selected_status = $request->input('status');
        $selected_category = $request->input('category_id');

        $categories = ProductCategory::orderBy('name')->get();

        $query = ProductReturn::with('category');

        if ($start_date) {
            $query->whereDate('created_at', '>=', $start_date);
        }

        if ($end_date) {
            $query->whereDate('created_at', '<=', $end_date);
        }

        if ($selected_status) {
            $query->where('status', $selected_status);
        }

        if ($selected_category) {
            $query->where('product_category_id', $selected_category);
        }

        $products = $query->latest()->paginate(10);

        $totalQuantity = $products->sum('quantity');
        $totalAmount = $products->sum(function ($product) {
            return $product->amount_type === 'dollar' ? $product->amount * session('result', 1) : $product->amount;
        });

        return view('products.return_report', compact(
            'products',
            'categories',
            'start_date',
            'end_date',
            'selected_status',
            'selected_category',
            'totalQuantity',
            'totalAmount'
        ));
    }

    public function updateReturnStatus(Request $request)
    {
        $product = ProductReturn::find($request->id);
        if ($product) {
            $product->status = $request->status;
            $product->save();
            return response()->json(['message' => 'Status updated successfully!']);
        }
        return response()->json(['message' => 'Product not found!'], 404);
    }

    public function downloadReturnFilterPdf(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $selected_status = $request->input('status');
        $selected_category = $request->input('category_id');

        $query = ProductReturn::with('category');

        if ($start_date) {
            $query->whereDate('created_at', '>=', $start_date);
        }

        if ($end_date) {
            $query->whereDate('created_at', '<=', $end_date);
        }

        if ($selected_status) {
            $query->where('status', $selected_status);
        }

        if ($selected_category) {
            $query->where('product_category_id', $selected_category);
        }

        $returns = $query->orderBy('created_at')->get();

        $totalQuantity = $returns->sum('quantity');
        $totalAmount = $returns->sum(function ($product) {
            return $product->amount_type === 'dollar' ? $product->amount * session('result', 1) : $product->amount;
        });

        $pdf = PDF::loadView('pdf.pending_returns', compact('returns', 'totalAmount', 'totalQuantity'));
        return $pdf->stream('pending_returns.pdf');
    }


}


