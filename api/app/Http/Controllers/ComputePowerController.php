<?php

namespace App\Http\Controllers;

use App\Http\Services\ComputePowerService;
use Illuminate\Http\Request;

class ComputePowerController extends Controller
{
	protected $computePowerService;

	public function __construct(ComputePowerService $computePowerService)
	{
		$this->computePowerService = $computePowerService;
	}

	/**
	 * 获取所有算力目录
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */

	public function index(Request $request)
	{
		try {
			$computePowers = $this->computePowerService->computePowers($request);
			return response()->json([
				'code' => 200,
				'data' => $computePowers,
			]);
		} catch (\Exception $e) {
			return response()->json([
				'code' => 500,
				'message' => $e->getMessage(),
			], 500);
		}
	}

	//算力充值
	public function pay(Request $request)
	{
		try {
			$result = $this->computePowerService->createComputePowerPay($request);
			return response()->json([
				'code' => 200,
				'message' => '正在唤起支付！',
				'data' => $result,
			]);
		} catch (\Exception $e) {
			return response()->json([
				'code' => 500,
				'message' => $e->getMessage(),
			], 500);
		}
	}
}
