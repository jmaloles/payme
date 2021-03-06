<?php

namespace App\Models\Product\Traits\Attribute;

trait ProductAttribute
{
	public function getEditButtonAttribute(){
		return '<a href="'.route('admin.product.edit', $this).'" class="btn btn-xs btn-primary"><i class="fa fa-pencil" data-toggle="tooltip" data-placement="top" title="'.trans('buttons.general.crud.edit').'"></i></a> ';
	}

	public function getViewButtonAttribute(){
		return '<a href="'.route('admin.product.show', $this).'" class="btn btn-xs btn-primary"><i class="fa fa-search" data-toggle="tooltip" data-placement="top" title="'.trans('buttons.general.crud.view').'"></i></a> ';
	}

	public function getDeleteButtonAttribute(){
		return '<a href="'.route('admin.product.destroy', $this).'"
                 data-method="delete"
                 data-trans-button-cancel="'.trans('buttons.general.cancel').'"
                 data-trans-button-confirm="'.trans('buttons.general.crud.delete').'"
                 data-trans-title="'.trans('strings.backend.general.are_you_sure').'"
                 class="btn btn-xs btn-danger"><i class="fa fa-trash" data-toggle="tooltip" data-placement="top" title="'.trans('buttons.general.crud.delete').'"></i></a> ';
	}

	public function getActionButtonsAttribute(){
		return $this->view_button.$this->edit_button.$this->delete_button;
	}

	public function getIngredientListAttribute(){
		return $this->inventories->pluck('name');
	}

	public function getProductIngredientsAttribute(){
		return $this->inventories;
	}
}