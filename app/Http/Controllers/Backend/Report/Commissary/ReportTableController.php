<?php

namespace App\Http\Controllers\Backend\Report\Commissary;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Repositories\Backend\Report\ReportCommissaryRepository;
use App\Models\Order\Order;
use App\Models\ProductSize\ProductSize;
use App\Models\Commissary\Product\Product as CommissaryProduct;

class ReportTableController extends Controller
{
    protected $reports;

    public function __construct(ReportCommissaryRepository $reports){
    	$this->reports = $reports;
    }

    public function __invoke(Request $request){
        $inventory  = [];
        $commissary = CommissaryProduct::select('name')->get();

        $orders     = Order::with(
                        [
                            'order_list.product_size',
                            'order_list.product.product_size.ingredients' => function($q) use ($commissary) {
                                $q->whereIn('name', $commissary);
                            }
                        ])
                    ->whereHas('order_list.product.product_size.ingredients', function($q) use($commissary) {
                        $q->whereIn('name', $commissary);
                    })->get();


        foreach ($commissary as $product) {
            $inventory[$product->name] = [];
        }

        foreach ($orders as $order) {
            $qty   = 0;
            $lists = $order->order_list;

            foreach ($lists as $list) {
                $size         = $list->product_size->size;
                $product      = $list->product;
                $product_size = $product->product_size->where('size', $size)->first();
                $ingredients  = $product_size->ingredients;

                foreach ($ingredients as $ingredient) {
                    $qty = (int)$list->quantity;
                    $inventory[$ingredient->name] = (int)$inventory[$ingredient->name] + $qty;
                }

            }            
        }

        return compact('commissary', 'inventory');
    }
}
