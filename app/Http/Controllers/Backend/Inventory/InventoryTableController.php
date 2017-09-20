<?php

namespace App\Http\Controllers\Backend\Inventory;

use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Repositories\Backend\Inventory\InventoryRepository;
use App\Http\Requests\Backend\Inventory\StoreInventoryRequest;
use App\Models\Inventory\Inventory;
use Illuminate\Http\Request;

/**
 * Class UserTableController.
 */
class InventoryTableController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $inventories;

    /**
     * @param UserRepository $users
     */
    public function __construct(InventoryRepository $inventories)
    {
        $this->inventories = $inventories;
    }

    /**
     * @param ManageUserRequest $request
     *
     * @return mixed
     */
    public function __invoke(Request $request)
    {
        return Datatables::of($this->inventories->getForDataTable())
                ->escapeColumns(['id', 'sort'])
                ->addColumn('stocks', function($inventory) {
                    $stock = $inventory->stock;
                    $unit  = $inventory->unit_type;

                    if($stock > 1){
                        $unit = $unit.'s';
                    }

                    return $stock.' '.$unit;

                })
                ->addColumn('category', function($inventory) {
                    return $inventory->category->name;
                })
                ->addColumn('actions', function($inventory) {
                    return $inventory->action_buttons;
                })
                ->make();
    }
}
