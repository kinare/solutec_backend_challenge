<?php

namespace App\Http\Controllers;

use App\Models\orderDetail;
use Illuminate\Http\Request;

class OrderDetailController extends BaseController
{
    public function __construct($model = OrderDetail::class, $resource = null)
    {
        parent::__construct($model, $resource);
    }
}
