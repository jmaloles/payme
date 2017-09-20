<?php

namespace App\Http\Controllers\Backend\Product;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Product\Product;
use App\Models\Inventory\Inventory;
use App\Models\ProductSize\ProductSize;

use App\Http\Requests\Backend\Product\ManageRequest;
use Illuminate\Support\Facades\Input;

class ProductController extends Controller
{
    public function index(){
    	return view('backend.product.index');
    }

    public function create(){
    	$ingredients = Inventory::all();
        $selections  = $ingredients->pluck('name', 'id');

    	return view('backend.product.create', compact('ingredients', 'selections'));
    }

    public function store(ManageRequest $request){
    	$filename    = 'no_image.png';
        $products = json_decode($request->product_ingredients);

    	if($request->hasFile('image')){
    		$file 		= $request->file('image');
    		$filename 	= $file->getClientOriginalName();
    		$file->move(public_path().'/img/product/', $filename);
    	}

    	$product           = new Product();
    	$product->name 	   = $request->name;
        $product->code     = $request->code;
    	$product->image    = $filename;
        $product->category = $request->category;
    	$product->save();


        foreach ($products as $prod) {
            $prod_size              = new ProductSize();
            $prod_size->size        = $prod->size;
            $prod_size->price       = $prod->price;
            $prod_size->product_id  = $product->id;
            $prod_size->save();

            //attach product size ingredients
            foreach ($prod->ingredient as $item) {
               $ingredient = Inventory::findOrFail($item->id);

               $ingredient->product_size()->attach($prod_size, ['quantity' => $item->quantity]);
            }
        }

    	return redirect()->route('admin.product.index'); 
    }

    public function show(Product $product){
        return view('backend.product.show', compact('product'));
    }

    public function edit(Product $product){
        $ingredients = Inventory::all();
        $selections  = $ingredients->pluck('name', 'id');

    	return view('backend.product.edit', compact('product', 'ingredients', 'selections'));
    }

    public function update(Product $product, ManageRequest $request){
    	$filename    = $product->image;
		$products    = json_decode($request->product_ingredients);

    	if($request->hasFile('image')){
    		$file 		= $request->file('image');
    		$filename 	= $file->getClientOriginalName();
    		$file->move(public_path().'/img/product/', $filename);
    	}

    	$product->name     = $request->name;
        $product->code     = $request->code;
        $product->image    = $filename;
        $product->category = $request->category;
        $product->save();


    	foreach ($products as $prod) 
        {
            $exist_ingredient       = [];
            $not_exist_ingredient   = [];

            $prod_size              = $product->product_size->where('size', $prod->size)->first();
            $prod_size->size        = $prod->size;
            $prod_size->price       = $prod->price;
            $prod_size->product_id  = $product->id;
            $prod_size->save();

            for($i = 0; $i < count($prod->ingredient); $i++)
            {
                $exist_ingredient[$i] = (int)$prod->ingredient[$i]->id;
            }

            //
            // get existing product ingredients
            //
            $ingredients = $prod_size->ingredients->whereIn('id', $exist_ingredient);

            foreach ($ingredients as $ingredient) 
            {
                for($i = 0; $i < count($prod->ingredient); $i++)
                {
                    if($prod->ingredient[$i]->id == $ingredient->id)
                    {
                        $ingredient->pivot->quantity = $prod->ingredient[$i]->quantity;
                        $ingredient->save();
                    }
                }
            }


            $remove_ingredients = $prod_size->ingredients->whereNotIn('id', $exist_ingredient);
            
            foreach ($remove_ingredients as $ingredient) {
                $ingredient->product_size()->detach($prod_size);
            }

            $ingredient_ids = $ingredients->pluck('id');

            $not_exist_ingredient = array_diff_assoc($exist_ingredient, $ingredient_ids->toArray());
            
            foreach ($not_exist_ingredient as $id) {
                $ingredient = Inventory::findOrFail($id);
                $qty        = 0;

                foreach ($prod->ingredient as $item) 
                {
                    if($id == $item->id)
                    {
                        $qty = $item->quantity;
                        $ingredient->product_size()->attach($prod_size, ['quantity' => $qty]);
                    }
                }
            }
        }

    	return redirect()->route('admin.product.index')->withFlashSuccess('Product has been updated!'); 
    }

    public function destroy(Product $product){
    	$product->delete();

    	return redirect()->route('admin.product.index')->withFlashSuccess('Product has been deleted!');
    }

    public function unit_type($id){
        $inventory = Inventory::findOrFail($id);
        return $inventory->unit_type;
    }
}
