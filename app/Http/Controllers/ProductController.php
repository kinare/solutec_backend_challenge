<?php

namespace App\Http\Controllers;


use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
   public function __construct($model = Product::class, $resource = null)
   {
       parent::__construct($model, $resource);
   }

   public function store(Request $request)
   {
       return parent::store($request); // TODO: Change the autogenerated stub
   }
}
