<?php

namespace App\Http\Controllers\Backend\Stock;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Inventory\Inventory;
use App\Models\Stock\Stock;
use App\Models\Product\Product;
use App\Models\Commissary\Product\Product as Commissary;

use App\Http\Requests\Backend\Stock\ManageRequest;

class StockController extends Controller
{
    
	public function index(){
		return view('backend.stock.index');
	}

	public function create(){
		$inventories = Inventory::pluck('name', 'id');
		
		return view('backend.stock.create', compact('inventories'));
	}

	public function store(ManageRequest $request){
		$inventory 	= Inventory::find($request->inventory_id);

		//
		// check if ingredient is from commissary
		//
		$commissary = Commissary::where('name', $inventory->name)->first();

		if(count($commissary))
		{
			if($commissary->produce >= $request->quantity)
			{
				$commissary->produce = $commissary->produce - $request->quantity;
				$commissary->save();
			}
			else
			{
				return redirect()->route('admin.stock.create')->withFlashDanger('Stock quantity not match from commissary!');
			}
		}

		Stock::create($request->all());
		
		$inventory->AddStock($request->quantity);
		$inventory->save();

		return redirect()->route('admin.stock.index')->withFlashSuccess('Stock Added Successfully!');
	}

	public function edit(Stock $stock){

		return view('backend.stock.edit', compact('stock'));
	}

	public function update(Stock $stock, ManageRequest $request){
		$stock->update([
			'inventory_id'	=> $request->inventory_id,
			'quantity'		=> $request->quantity,
			'price'			=> $request->price,
			'received'		=> $request->received,
			'expiration'	=> $request->expiration
		]);

		$stock = Stock::selectRaw('sum(quantity) as "quantity"')->where('inventory_id', $request->inventory_id)->first();

		$inventory = Inventory::find($request->inventory_id);
		$inventory->stock = $stock->quantity;
		$inventory->save();

		return redirect()->route('admin.stock.index')->withFlashSuccess('Stock Updated Successfully!');
	}

	public function destroy(Stock $stock){
		$inventory 	= Inventory::findOrFail($stock->inventory_id);

		$commissary = Commissary::where('name', $inventory->name)->first();

		if(count($commissary))
		{
			$commissary->produce = $commissary->produce + $stock->quantity;
			$commissary->save();
		}

		$inventory->stock = $inventory->stock - $stock->quantity;
		$inventory->save();

		$stock->delete();

		return redirect()->route('admin.stock.index')->withFlashDanger('Stock has Been Deleted Successfully!');
	}

	public function updateProductCost(){
		$products = Product::all();

		foreach ($products as $product) {
			$product_cost  = 0;
			$inventories   = $product->inventories;

			if(count($inventories))
			{
				foreach ($inventories as $inventory) {
					$stock = $inventory->stocks->last();

					if(count($stock)){
						$product_cost += $stock->price;
					}
				}
			}

			$product->cost = $product_cost;
			$product->save();
		}
	}

}
