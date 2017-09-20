<?php

namespace App\Http\Controllers\Backend\Report\Commissary;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order\Order;
use App\Models\OrderList\OrderList;
use App\Models\ProductSize\ProductSize;
use App\Models\Commissary\Product\Product as CommissaryProduct;

class ReportController extends Controller
{
    public function index(){
        $arr = $this->fetchReport(date('Y-m-d'));
        
    	return view('backend.report.commissary.daily.index', $arr);
    }

    public function show($id){
    	$order = Order::findOrFail($id);

    	return view('backend.report.commissary.sale.show', compact('order'));
    }

    public function store(Request $request){
        $arr = $this->fetchReport($request->date);

        return view('backend.report.commissary.daily.index', $arr);
    }

    public function destroy($id){
    	$order = Order::findOrFail($id);

    	foreach ($order->order_list as $list) {
    		$list->delete();
    	}

    	$order->delete();

    	return redirect()->route('admin.report.sale.index');
    }


    public function fetchReport($date){
        $inventory  = [];
        $commissary = CommissaryProduct::select('name', 'price')->get();

        $orders     = Order::with(
                        [
                            'order_list.product_size',
                            'order_list.product.product_size.ingredients' => function($q) use ($commissary) {
                                $q->whereIn('name', $commissary->pluck('name'));
                            }
                        ])
                    ->whereHas('order_list.product.product_size.ingredients', function($q) use($commissary) {
                        $q->whereIn('name', $commissary->pluck('name'));
                    })
                    ->whereRaw('date(created_at) = "'.$date.'"')
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
                    $qty     = (int)$list->quantity;
                    $qty_use = $ingredient->pivot->quantity;
                    $inventory[$ingredient->name] = (int)$inventory[$ingredient->name] + ($qty * $qty_use);
                }

            }            
        }

        return compact('commissary', 'inventory');
    }
}
