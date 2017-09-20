<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order\Order;
use App\Models\OrderList\OrderList;
use App\Models\Product\Product;
use App\Models\Notification\Notification;
use App\Models\Request\RequestMessage;
use App\Models\Access\User\User;
use App\Models\Response\ResponseMessage;
use App\Models\Commissary\Product\Product as CommissaryProduct;


/**
 * Class DashboardController.
 */
class DashboardController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
    	$months 	= [];
    	$monthNames = [];
    	$tops 		= [];
        $requests   = RequestMessage::orderBy('id', 'desc')->take(5)->get();

    	for($i = 1; $i <= 12; $i++){
    		$date = strlen($i) > 1 ? $i.'/01/2017' : '0'.$i.'/01/2017';

    		$str = date('M', strtotime($date));
    		$monthNames[$i -1 ] = $str;
    	}

    	for($i = 1; $i <= 12; $i++){
    		$i = $i < 10 ? '0'.$i : $i;
    		$total = Order::selectRaw('sum(payable) as "total"')
    				->whereBetween('created_at', [date('Y-'.$i.'-01'), date('Y-'.$i.'-31')])
    				->first()->total;

    	 	$total = $total > 0 ? $total : 0;
    	 	$months[$i - 1] = $total;
    	}

		$products = Product::with([
						'order_list' => function($q) 
						{
							$q->whereBetween('created_at', [date('Y-m-01'), date('Y-m-31')])
						  	  ->orderBy('product_id');
						}
					])->get();


		for($i = 0; $i < count($products); $i++)
		{
			$count   = 0;
			$orders  = count($products[$i]->order_list);

			//
			// if product has orders
			// sum all product sold quantity
			//
			if(count($orders))
			{
				foreach ($products[$i]->order_list as $order) 
				{
					$count += $order->quantity;
				}
			}

			$tops[$i] = (object)[ 'name' => $products[$i]->name, 'count' => $count];
		}


		$temp = 0;

		for($i=0; $i < count($tops); $i++)
		{  
			for($j=1; $j < (count($tops) - $i); $j++)
			{  
				if($tops[$j-1]->count < $tops[$j]->count)
				{  
					//swap elements  
					$temp = $tops[$j-1];  
					$tops[$j-1] = $tops[$j];  
					$tops[$j] = $temp;  
				}  
			}  
		}  

        $commissaries = $this->fetchCommissary();
		
        return view('backend.dashboard', compact('months', 'monthNames', 'tops', 'requests', 'commissaries'));
    }

    public function getRequest($id) {
    	$request  = RequestMessage::findOrFail($id);
    	$user     = User::findOrFail($request->user_id);
    	$response = $request->response;

    	return [$request, $user, $response];
    }

    public function getAllRequest(){
    	$requests = RequestMessage::with('user')->orderBy('id', 'desc')->take(50)->get();

    	return $requests;
    }

    public function storeResponse(Request $request)
    {
    	ResponseMessage::updateOrCreate(
    		['request_id' => $request->request_id],
    		[
    			'message' => $request->message,
    			'status'  => $request->status
    		]
    	);

    	return 'success';
    }


    public function fetchCommissary(){
        $months     = [];
        $monthNames = [];
        $commissary = CommissaryProduct::select('name')->get();

        for($i = 1; $i <= 12; $i++){
            $date = strlen($i) > 1 ? $i.'/01/2017' : '0'.$i.'/01/2017';

            $str = date('M', strtotime($date));
            $monthNames[$i -1 ] = $str;
        }
    
        
        for($i = 1; $i <= 12; $i++){
            $total      = null;

            $orders = Order::with(
                        [
                            'order_list.product_size',
                            'order_list.product.product_size.ingredients' => function($q) use ($commissary) {
                                $q->whereIn('name', $commissary);
                            }
                        ])
                    ->whereHas('order_list.product.product_size.ingredients', function($q) use($commissary) {
                        $q->whereIn('name', $commissary);
                    })
                    ->whereRaw('date(created_at) between "'.date('Y-'.$i.'-01').'" and "'.date('Y-'.$i.'-31').'"')
                    ->get();


            foreach ($orders as $order) {
                $qty   = 0;
                $lists = $order->order_list;

                foreach ($lists as $list) {
                    $size         = $list->product_size->size;
                    $product      = $list->product;
                    $product_size = $product->product_size->where('size', $size)->first();
                    $ingredients  = $product_size->ingredients;

                    foreach ($ingredients as $ingredient) {
                        $price     = CommissaryProduct::where('name', $ingredient->name)->first()->price;
                        $qty       = (int)$list->quantity;
                        $qty_use   = $ingredient->pivot->quantity;
                        $total     = $total + (($qty * $qty_use) * $price);
                    }
                }            
            }

            $months[$i] = number_format($total, 2);
        }

        return $months;
    }
}
