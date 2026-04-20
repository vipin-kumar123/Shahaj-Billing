<?php

namespace App\Services\AI;

use App\Models\SaleItem;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AIPurchaseHelper
{
    public function getSuggestions()
    {
        $products = Product::where('user_id', auth()->id())->get();
        $data = [];

        foreach ($products as $product) {

            /* Avg monthly sale */
            $last6MonthsSales = SaleItem::where('product_id', $product->id)
                ->where('created_at', '>', Carbon::now()->subMonths(6))
                ->sum('quantity');

            $avgMonthlySale = round($last6MonthsSales / 6, 2);

            /* Current stock (REMOVE user_id filter) */
            $stock = StockMovement::where('product_id', $product->id)
                ->sum(DB::raw("
                            CASE
                                WHEN type = 'increase' THEN quantity
                                WHEN type = 'decrease' THEN -quantity
                                ELSE 0
                            END
                        "));

            /* Recommended purchase = demand - stock */
            $recommended = max(0, ($avgMonthlySale * 1.2) - $stock);

            /* Avg last 5 purchase price */
            $avgRate = PurchaseItem::where('product_id', $product->id)
                ->orderBy('id', 'DESC')
                ->take(5)
                ->avg('unit_cost');

            $data[$product->id] = [
                'product' => $product->name,
                'avg_sale' => $avgMonthlySale,
                'stock' => $stock,
                'recommended' => round($recommended),
                'avg_rate' => round($avgRate),
            ];
        }

        return $data;
    }
}
