<?php

namespace App\Models;

class Supplier extends BaseModel
{
    public function products()
    {
        return $this->hasManyThrough(
            Product::class,
            SupplierProduct::class,
            'supplier_id',
            'id',
        );
    }
}
