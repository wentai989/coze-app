<?php

namespace App\Admin\Controllers;

use App\Services\PowerDeductionLogService;
use Slowlyo\OwlAdmin\Controllers\AdminController;

/**
 * 算力使用记录
 *
 * @property PowerDeductionLogService $service
 */
class PowerDeductionLogController extends AdminController
{
	protected string $serviceName = PowerDeductionLogService::class;

	public function list()
	{
		$crud = $this->baseCRUD()
			->filterTogglable(false)
			->headerToolbar([
				$this->createButton('dialog'),
				...$this->baseHeaderToolBar()
			])
			->columns([
				amis()->TableColumn('id', 'ID')->sortable(),
				amis()->TableColumn('mch.name', '商户名称'),
				amis()->TableColumn('user.phone', '用户手机号'),
				amis()->TableColumn('name', '扣点名称'),
				amis()->TableColumn('deduction_type', '订单类型')
					->type('mapping')
					->map([
						'1' => '<span class="text-blue-600 text-sm font-bold">智能体</span>',
						'2' => '<span class="text-green-600 text-sm font-bold">工作流</span>',
						'*' => '未知',
					]),

				amis()->TableColumn('power_value', '扣除算力值')->sortable(),
				amis()->TableColumn('created_at', admin_trans('admin.created_at'))->type('datetime')->sortable(),

			]);

		return $this->baseList($crud);
	}

	public function form($isEdit = false)
	{
		return $this->baseForm()->body([
			amis()->TextControl('deduction_name', '扣点名称'),
			amis()->TextControl('deduction_type', '扣点类型'),
			amis()->TextControl('power_value', '扣除算力值'),
			amis()->TextControl('user_id', '用户ID (假设存在 users 表)'),
		]);
	}

	public function detail()
	{
		return $this->baseDetail()->body([
			amis()->TextControl('id', 'ID')->static(),
			amis()->TextControl('deduction_name', '扣点名称')->static(),
			amis()->TextControl('deduction_type', '扣点类型')->static(),
			amis()->TextControl('power_value', '扣除算力值')->static(),
			amis()->TextControl('user_id', '用户ID (假设存在 users 表)')->static(),
			amis()->TextControl('created_at', admin_trans('admin.created_at'))->static(),
			amis()->TextControl('updated_at', admin_trans('admin.updated_at'))->static(),
		]);
	}
}
