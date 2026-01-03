<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Services\OrderService;
use App\Http\Services\ComputePowerService;
use App\Http\Services\VipService;

use App\Http\Services\SpreadService;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    //订单回调
    public  function notify(Request $request)
    {

        $order = Order::where('order_no', $request->order_no)->first();

        if (!$order) {
            return response()->json([
                'code' => 500,
                'message' => '订单不存在',
            ], 500);
        }

        if ($order->is_status == 1) {
            return response()->json([
                'code' => 500,
                'message' => '订单已支付',
            ], 500);
        }

        return DB::transaction(function () use ($order) {

            if ($order->order_type == 1) {

                (new ComputePowerService())->payNotify($order);
            }

            if ($order->order_type == 2) {
                (new VipService())->payNotify($order);
            }

            //开始返佣

            (new SpreadService())->processCommission($order);


            return response()->json([
                'code' => 200,
                'message' => '支付成功',
            ]);
        });
    }


    public function index(Request $request)
    {
        $orders = $this->orderService->orders($request);
        try {
            return response()->json([
                'code' => 200,
                'data' => $orders,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
