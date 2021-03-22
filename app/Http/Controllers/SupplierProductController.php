<?php

namespace App\Http\Controllers;

use App\Models\supplierProduct;
use Illuminate\Http\Request;

class SupplierProductController extends BaseController
{
    public function __construct($model = SupplierProduct::class, $resource = null)
    {
        parent::__construct($model, $resource);
    }
}
