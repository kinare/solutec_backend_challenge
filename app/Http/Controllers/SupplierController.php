<?php

namespace App\Http\Controllers;

use App\Models\supplier;
use Illuminate\Http\Request;

class SupplierController extends BaseController
{
    public function __construct($model = Supplier::class, $resource = null)
    {
        parent::__construct($model, $resource);
    }

    public function products($id)
    {
        return $this->response($this->model::find($id)->products()->get());
    }
}
