<?php

namespace App\Models;

class Order extends BaseModel
{
    public function products()
    {
        return $this->hasManyThrough(
            Product::class,
            OrderDetail::class,
            'order_id',
            'id',
        );
    }
}
