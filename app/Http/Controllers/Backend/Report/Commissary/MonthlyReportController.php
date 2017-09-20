<?php

namespace App\Http\Controllers\Backend\Report\Commissary;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order\Order;
use App\Models\OrderList\OrderList;
use App\Models\ProductSize\ProductSize;
use App\Models\Commissary\Product\Product as CommissaryProduct;
use Carbon\Carbon;
use DB;


class MonthlyReportController extends Controller
{
    public function index(){
        $arr = $this->fetchReport(date('Y-m-d'));      

    	return view('backend.report.commissary.monthly.index', $arr);
    }

    public function store(Request $request){
        $arr = $this->fetchReport(date('Y-m-d'));      

        return view('backend.report.commissary.monthly.index', $arr);
    }

    public function fetchReport($date){
        $inventory  = [];
        $commissary = CommissaryProduct::select('name', 'price')->get();

        $orders     = Order::with(
                        [
                            'order_list.product_size',
                            'order_list.product.product_size.ingredients' => function($q) use ($commissary) {
                                $q->whereIn('name', $commissary);
                            }
                        ])
                    ->whereHas('order_list.product.product_size.ingredients', function($q) use($commissary) {
                        $q->whereIn('name', $commissary);
                    })
                    ->whereRaw('date(created_at) between "'.$date.'" and "'.$date.'"')
                    ->get();


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
