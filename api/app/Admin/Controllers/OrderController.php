<?php

namespace App\Admin\Controllers;

use App\Services\OrderService;
use Slowlyo\OwlAdmin\Controllers\AdminController;

/**
 * 订单记录
 *
 * @property OrderService $service
 */
class OrderController extends AdminController
{
	protected string $serviceName = OrderService::class;

	public function list()
	{
		$crud = $this->baseCRUD()
			->filterTogglable(false)
			->headerToolbar([
				...$this->baseHeaderToolBar()
			])
			->filter(
				$this->baseFilter()->body(
					[
						amis()->TextControl('order_no', '订单号')
							->placeholder('输入订单号搜索')
							->size('md')
							->clearable(),
					]
				)
			)
			->columns([
				amis()->TableColumn('id', 'ID')->width(100)->sortable(),
				amis()->TableColumn('mch.name', '商户名称'),
				amis()->TableColumn('order_no', '订单号'),
				amis()->TableColumn('user.phone', '用户手机号'),
				amis()->TableColumn('name', '充值名称'),
				amis()->TableColumn('order_type', '订单类型')
					->type('mapping')
					->map([
						'1' => '<span class="text-blue-600 text-sm font-bold">算力订单</span>',
						'2' => '<span class="text-green-600 text-sm font-bold">会员订单</span>',
						'*' => '未知',
					]),
				amis()->TableColumn('amount', '充值金额')->tpl('¥${amount}'),
				amis()->TableColumn('updated_at', admin_trans('admin.updated_at'))->type('datetime')->sortable(),
				amis()->TableColumn('操作')->width(160)->type('operation')
					->buttons([
						$this->rowDeleteButton(),
					]),
			]);

		return $this->baseList($crud);
	}

	public function form($isEdit = false)
	{
		return $this->baseForm()->body([
			amis()->TextControl('user_id', '用户ID (假设存在 users 表)'),
			amis()->TextControl('name', '充值名称'),
			amis()->TextControl('amount', '充值金额'),
			amis()->TextControl('order_no', '订单号'),
			amis()->TextControl('invoiced', '是否开票'),
			amis()->TextControl('order_type', '1是算力订单，2会员订单'),
		]);
	}

	public function detail()
	{
		return $this->baseDetail()->body([
			amis()->TextControl('id', 'ID')->static(),
			amis()->TextControl('user_id', '用户ID (假设存在 users 表)')->static(),
			amis()->TextControl('name', '充值名称')->static(),
			amis()->TextControl('amount', '充值金额')->static(),
			amis()->TextControl('order_no', '订单号')->static(),
			amis()->TextControl('invoiced', '是否开票')->static(),
			amis()->TextControl('order_type', '1是算力订单，2会员订单')->static(),
			amis()->TextControl('created_at', admin_trans('admin.created_at'))->static(),
			amis()->TextControl('updated_at', admin_trans('admin.updated_at'))->static(),
		]);
	}
}
