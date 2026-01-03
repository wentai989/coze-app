<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\SpreadService;


class SpreadController extends Controller
{

    protected $spreadService;

    public function __construct(SpreadService $spreadService)
    {
        $this->spreadService = $spreadService;
    }

    //推广员数据
    public function show(Request $request)
    {
        $data = $this->spreadService->getSpread($request);
        return response()->json([
            'code' => 200,
            'data' => $data,
        ]);
    }

    //推广员提现
    public function withdraw(Request $request)
    {
        try {

            $request->validate([
                'amount' => 'required|numeric|min:1',
            ], [
                'amount.required' => '请输入提现金额',
                'amount.numeric' => '提现金额必须是数字',
                'amount.min' => '提现金额不能小于1元',
            ]);

            $this->spreadService->handleWithdraw($request);
            return response()->json([
                'code' => 200,
                'message' => '提现成功',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    //获取推广员提现记录
    public function index(Request $request)
    {
        $data = $this->spreadService->getSpreads($request);
        return response()->json([
            'code' => 200,
            'data' => $data,
        ]);
    }
}
