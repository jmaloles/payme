<?php

namespace App\Http\Controllers\Backend\Commissary\Produce;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Commissary\Produce\Produce;
use App\Models\Commissary\Product\Product;
use App\Models\Commissary\Inventory\Inventory;

class ProduceController extends Controller
{
    public function index(){
    	return view('backend.commissary.produce.index');
    }

    public function create(){
    	$products = Product::pluck('name', 'id');

    	return view('backend.commissary.produce.create', compact('products'));
    }

    public function store(Request $request) {
        $canProduce = 0;
        $product    = Product::findOrFail($request->product_id);
        $ingredients= $product->ingredients;

        foreach ($ingredients as $ingredient) {
            if($request->quantity <= $ingredient->stock)
                $canProduce++;
        }
        
        if(count($ingredients) == $canProduce)
        {
            $produce = Produce::updateOrCreate(
                        [
                            'product_id' => $request->product_id,
                            'created_at' => date('Y-m-d h:i:s')
                        ],
                        [
                            'date'      => $request->date,
                            'quantity'  => $request->quantity
                        ]
                    );


            $product = $produce->product;
            $product->produce = $product->produce + $request->quantity;
            $product->save();


            foreach ($ingredients as $ingredient) {
               $ingredient->stock = $ingredient->stock - $request->quantity;
               $ingredient->save();
            }

            return redirect()->route('admin.commissary.produce.index')->withFlashSuccess('Record Saved!');
        }
        else
        {
            return redirect()->back()->withFlashDanger('Check item stock!');
        }
    }


    public function destroy(Produce $produce){
    	$product = $produce->product;
    	$product->produce = $product->produce - $produce->quantity;
    	$product->save();

        $ingredients = $product->ingredients;

        foreach ($ingredients as $ingredient) {
            $ingredient->stock = $ingredient->stock + $produce->quantity;
            $ingredient->save();
        }

        $produce->delete();

    	return redirect()->route('admin.commissary.produce.index')->withFlashDanger('Produce Product Deleted Successfully!');
    }

}
