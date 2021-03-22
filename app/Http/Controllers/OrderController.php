<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    public function __construct($model = Order::class, $resource = null)
    {
        parent::__construct($model, $resource);
    }

    public function store(Request $request)
    {
        $rawOrder = $request->order;

        if (isset($rawOrder['id'])){
            $order = Order::find($rawOrder['id']);
        }else{
            $order = new Order();
        }

        $order->fill((array)$rawOrder);
        $order->save();

        if ($request->has('items')){

            OrderDetail::where('order_id', $order->id)->delete();
            foreach ($request->items as $item){
                $newItem = new OrderDetail();
                $newItem->product_id = $item['id'];
                $newItem->order_id = $order->id;
                $newItem->save();
            }
        }
    }

    public function destroy($id)
    {
        try{
            $model = $this->model::find($id);
            OrderDetail::where('order_id', $model->id)->delete();
            $model->delete();
            return response()->json([
                'message' => $this->model.' deleted'
            ], 200);
        }catch (\Exception $exception){
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }


    public function products($id)
    {
        return $this->response($this->model::find($id)->products()->get());
    }
}
