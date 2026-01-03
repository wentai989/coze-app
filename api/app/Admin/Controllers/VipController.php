<?php

namespace App\Admin\Controllers;

use App\Services\VipService;
use Slowlyo\OwlAdmin\Controllers\AdminController;

/**
 * 会员管理
 *
 * @property VipService $service
 */
class VipController extends AdminController
{
	protected string $serviceName = VipService::class;

	public function list()
	{
		$crud = $this->baseCRUD()
			->filterTogglable(false)
			->filter($this->baseFilter()->body([
				amis()->TextControl('name', '套餐名称')
					->placeholder('输入套餐名称搜索')
					->size('md')
					->clearable(),
			]))
			->headerToolbar([
				$this->createButton('dialog'),
				...$this->baseHeaderToolBar()
			])
			->columns([
				amis()->TableColumn('id', 'ID')->widith(100)->sortable(),
				amis()->TableColumn('mch.name', '商户名称'),
				amis()->TableColumn('name', '套餐名称'),
				amis()->TableColumn('amount', '金额'),
				amis()->TableColumn('power_value', '赠送算力值')->sortable(),
				amis()->TableColumn('day_number', '充值天数')->sortable(),
				amis()->TableColumn('sort', '排序')->sortable(),
				amis()->TableColumn('is_status', '状态')
					->width(100)
					->type('switch')
					->value(1),
				// amis()->TableColumn('created_at', admin_trans('admin.created_at'))->type('datetime')->sortable()->width(180),
				amis()->TableColumn('updated_at', admin_trans('admin.updated_at'))->type('datetime')->sortable()->width(180),
				amis()->TableColumn('操作')->width(160)->type('operation')
					->buttons([
						$this->rowEditButton('dialog', 'md'),
						$this->rowDeleteButton(),
					]),
			]);

		return $this->baseList($crud);
	}

	public function form($isEdit = false)
	{
		return $this->baseForm()->body([
			amis()->SelectControl('mch_id', '商户名称')
				->source('/select_mch_options')
				->searchable()
				->required(),
			amis()->TextControl('name', '套餐名称')->required(),

			amis()->NumberControl('amount', '充值金额')
				->step(0.01)
				->min(0.01)
				->required(),
			amis()->NumberControl('day_number', '充值天数')
				->step(1)
				->min(1)
				->required(),
			amis()->NumberControl('power_value', '赠送算力值')
				->step(1)
				->min(1)
				->required(),
			amis()->TextareaControl('description', '会员介绍'),
			amis()->NumberControl('sort', '排序')
				->value(0)
				->description('排序值，数值越大越靠前')
				->required(),
			amis()->SwitchControl('is_status', '状态')
				->width(100)
				->type('switch')
				->onText('启用')
				->offText('禁用')
				->value(1),
		]);
	}

	public function detail()
	{
		return $this->baseDetail()->body([
			amis()->TextControl('id', 'ID')->static(),
			amis()->SelectControl('mch_id', '商户名称')
				->source('/select_mch_options')
				->searchable()
				->required(),
			amis()->TextControl('name', '套餐名称')->static(),
			amis()->TextControl('amount', '金额')->static(),
			amis()->TextControl('description', '会员介绍')->static(),
			amis()->TextControl('is_status', '状态')->static(),
			amis()->TextControl('sort', '排序')->static(),
			amis()->TextControl('power_value', '赠送算力值')->static(),
			amis()->TextControl('day_number', '充值天数')->static(),
			amis()->TextControl('created_at', admin_trans('admin.created_at'))->static(),
			amis()->TextControl('updated_at', admin_trans('admin.updated_at'))->static(),
		]);
	}
}
