<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class InventoryController extends Controller
{
    public function currentStock(Request $request)
    {
        if ($request->ajax()) {

            $products = Product::leftJoin('stock_movements', 'products.id', '=', 'stock_movements.product_id')
                ->select(
                    'products.id',
                    'products.name',
                    'products.opening_stock',
                    DB::raw("
                    products.opening_stock +
                    COALESCE(SUM(
                        CASE
                            WHEN stock_movements.type = 'increase' THEN stock_movements.quantity
                            WHEN stock_movements.type = 'decrease' THEN -stock_movements.quantity
                        END
                    ),0) as stock
                ")
                )
                ->where('products.user_id', auth()->id())
                ->groupBy('products.id', 'products.name', 'products.opening_stock');

            return DataTables::of($products)
                ->addIndexColumn()

                ->editColumn('stock', function ($row) {

                    if ($row->stock <= 0) {
                        return '<span class="text-danger fw-bold">' . $row->stock . '</span>';
                    }

                    return '<span class="text-success">' . $row->stock . '</span>';
                })

                ->rawColumns(['stock'])
                ->make(true);
        }

        return view('backend.inventory.current-stock');
    }

    public function stockLedger(Request $request)
    {
        if ($request->ajax()) {

            $ledger = StockMovement::leftJoin('products', 'products.id', '=', 'stock_movements.product_id')
                ->select(
                    'stock_movements.id',
                    'products.name',
                    'stock_movements.type',
                    'stock_movements.quantity',
                    'stock_movements.created_at',
                    'stock_movements.purchase_id',
                    'stock_movements.sale_id'
                );

            return DataTables::of($ledger)

                ->addIndexColumn()

                ->addColumn('in', function ($row) {
                    return $row->type == 'increase' ? $row->quantity : '-';
                })

                ->addColumn('out', function ($row) {
                    return $row->type == 'decrease' ? $row->quantity : '-';
                })

                ->addColumn('reference', function ($row) {

                    if ($row->purchase_id) {
                        return 'PUR-' . $row->purchase_id;
                    }

                    if ($row->sale_id) {
                        return 'SAL-' . $row->sale_id;
                    }

                    return '-';
                })

                ->editColumn('created_at', function ($row) {
                    return formatDate($row->created_at);
                })

                ->make(true);
        }

        return view('backend.inventory.stock-ledger');
    }


    public function adjustmentForm()
    {
        $products = Product::select('id', 'name')->where('user_id', auth()->id())->get();

        return view('backend.inventory.stock-adjustment', compact('products'));
    }


    public function productStock(Request $request)
    {
        $stock = StockMovement::where('product_id', $request->productId)
            ->selectRaw("
            SUM(
                CASE
                    WHEN type='increase' THEN quantity
                    WHEN type='decrease' THEN -quantity
                END
            ) as stock
        ")
            ->value('stock');

        return response()->json([
            'stock' => $stock ?? 0
        ]);
    }


    public function storeAdjustment(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'new_stock' => 'required|numeric'
        ]);

        DB::transaction(function () use ($request) {

            $currentStock = StockMovement::where('product_id', $request->product_id)
                ->selectRaw("
                SUM(
                    CASE
                        WHEN type='increase' THEN quantity
                        WHEN type='decrease' THEN -quantity
                    END
                ) as stock
            ")
                ->value('stock') ?? 0;

            $newStock = $request->new_stock;

            $difference = $newStock - $currentStock;

            if ($difference == 0) {
                return redirect()->back()->with('warning', 'No stock change detected');
            }

            $type = $difference > 0 ? 'increase' : 'decrease';

            StockMovement::create([
                'product_id' => $request->product_id,
                'user_id' => auth()->id(),
                'type' => $type,
                'quantity' => abs($difference)
            ]);
        });

        return redirect()->back()->with('success', 'Stock Adjusted Successfully');
    }


    public function lowStock(Request $request)
    {
        if ($request->ajax()) {
            $query = Product::leftJoin('stock_movements', 'products.id', '=', 'stock_movements.product_id')
                ->where('products.user_id', auth()->id())
                ->select(
                    'products.id',
                    'products.name',
                    'products.low_stock_alert',
                    DB::raw("
                    COALESCE(
                        SUM(
                            CASE
                                WHEN stock_movements.type='increase' THEN quantity
                                WHEN stock_movements.type='decrease' THEN -quantity
                            END
                        ),0
                    ) as stock
                ")
                )
                ->groupBy('products.id', 'products.name', 'products.low_stock_alert')
                ->havingRaw('stock <= products.low_stock_alert');

            return DataTables::of($query)

                ->filterColumn('stock', function ($query, $keyword) {
                    $query->havingRaw("
                    SUM(
                        CASE
                            WHEN stock_movements.type='increase' THEN quantity
                            WHEN stock_movements.type='decrease' THEN -quantity
                        END
                    ) like ?", ["%{$keyword}%"]);
                })

                ->addColumn('status', function ($row) {
                    return '<span class="badge bg-danger">Low Stock</span>';
                })

                ->rawColumns(['status'])

                ->make(true);
        }

        return view('backend.inventory.low-stock');
    }
}
