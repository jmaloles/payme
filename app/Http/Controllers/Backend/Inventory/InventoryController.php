<?php

namespace App\Http\Controllers\Backend\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory\Inventory;
use App\Models\Category\Category;
use App\Models\Commissary\Product\Product as Commissary;
use App\Http\Requests\Backend\Inventory\StoreInventoryRequest;
use App\Repositories\Backend\Inventory\InventoryRepository;

use Carbon\Carbon;

class InventoryController extends Controller
{

    public function index(){
    	return view('backend.Inventory.index');
    }

    public function create(){
        $categories = Category::pluck('name', 'id');

        $inventories = Inventory::pluck('name');

        $commissaries = Commissary::whereNotIn('name', $inventories)->pluck('name', 'name');

    	return view('backend.Inventory.create', compact('categories', 'commissaries'));
    }

    public function store(StoreInventoryRequest $request){
        if($request->fromCommissary == "Other")
        {
            $request['name'] = $request->name2;
        }
        
    	Inventory::create($request->all());

    	return redirect()->route('admin.inventory.index')->withFlashSuccess('Inventory Added Successfully!');
    }

    public function edit(Inventory $inventory){
        $categories = Category::pluck('name', 'id');

    	return view('backend.Inventory.edit', compact('inventory', 'categories'));
    }

    public function update(Inventory $Inventory, StoreInventoryRequest $request){
    	$Inventory->update($request->all());

    	return redirect()->route('admin.inventory.index')->withFlashSuccess('Inventory Updated Successfully!');
    }

    public function destroy(Inventory $inventory){
    	$inventory->delete();

    	return redirect()->route('admin.inventory.index')->withFlashDanger('Inventory Deleted Successfully!');
    }
}
