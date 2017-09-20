<?php

namespace App\Http\Controllers\Backend\Commissary\Product;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Commissary\Inventory\Inventory;
use App\Models\Commissary\Product\Product;

class ProductController extends Controller
{
    public function index(){
    	return view('backend.commissary.product.index');
    }

    public function show(Product $product){
        
        return view('backend.commissary.product.show', compact('product'));
    }

    public function create(){
    	$ingredients = Inventory::all();
    	$selections = $ingredients->pluck('name', 'id');
    	return view('backend.commissary.product.create', compact('ingredients', 'selections'));
    }

    public function store(Request $request){
    	$ingredients = json_decode($request->ingredients);

    	$product = new Product();
    	$product->name = $request->name;
        $product->price = $request->price;
    	$product->category = $request->category;
    	$product->save();


    	foreach ($ingredients as $item) {
    		$ingredient = Inventory::findOrFail($item->id);

    		$ingredient->products()->attach($product);
    	}

    	return redirect()->route('admin.commissary.product.index');
    }

    public function destroy(Product $product){
        $product->delete();

        return redirect()->route('admin.commissary.product.index')->withFlashDanger('Product has been deleted!');
    }
}
