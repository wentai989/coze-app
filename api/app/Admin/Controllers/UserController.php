<?php

namespace App\Admin\Controllers;

use App\Services\UserService;
use Slowlyo\OwlAdmin\Controllers\AdminController;

/**
 * 用户管理
 *
 * @property UserService $service
 */
class UserController extends AdminController
{
	protected string $serviceName = UserService::class;

	public function list()
	{
		$crud = $this->baseCRUD()
			->filterTogglable(false)
			->filter($this->baseFilter()->body([
				amis()->TextControl('phone', '用户手机号')
					->placeholder('输入用户手机号搜索')
					->size('md')
					->clearable(),
			]))
			->headerToolbar([
				// $this->createButton('dialog'),
				...$this->baseHeaderToolBar()
			])
			->columns([
				amis()->TableColumn('id', 'ID')->width(100)->sortable(),
				// amis()->TableColumn('ask_id', 'AskId'),
				amis()->TableColumn('mch.name', '商户名称'),
				amis()->TableColumn('phone', '手机号'),
				amis()->TableColumn('power_value', '算力值'),
				amis()->TableColumn('vip_expire_time', '会员过期时间'),
				amis()->TableColumn('is_status', '状态')
					->width(100)
					->type('switch')
					->value(1),
				amis()->TableColumn('updated_at', admin_trans('admin.updated_at'))->width(180)->type('datetime')->sortable(),
				// amis()->TableColumn('created_at', admin_trans('admin.created_at'))->width(160)->type('datetime')->sortable(),
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
				->required(true),
			amis()->NumberControl('power_value', '算力值')
				->description('用户算力值，用于抵扣应用调用成本')
				->step(0.01),
			amis()->DateControl('vip_expire_time', '会员过期时间')
				->format('YYYY-MM-DD'),
		]);
	}

	public function detail()
	{
		return $this->baseDetail()->body([
			amis()->TextControl('id', 'ID')->static(),
			amis()->TextControl('ask_id', 'AskId')->static(),
			amis()->TextControl('invite_count', 'InviteCount')->static(),
			amis()->TextControl('is_status', 'IsStatus')->static(),
			amis()->TextControl('mch_id', 'MchId')->static(),
			amis()->TextControl('name', 'Name')->static(),
			amis()->TextControl('openid', 'Openid')->static(),
			amis()->TextControl('phone', 'Phone')->static(),
			amis()->TextControl('power_value', 'PowerValue')->static(),
			amis()->TextControl('remember_token', 'RememberToken')->static(),
			amis()->TextControl('sign_at', 'SignAt')->static(),
			amis()->TextControl('vip_expire_time', '会员过期时间')->static(),
			amis()->TextControl('created_at', admin_trans('admin.created_at'))->static(),
			amis()->TextControl('updated_at', admin_trans('admin.updated_at'))->static(),
		]);
	}
}
