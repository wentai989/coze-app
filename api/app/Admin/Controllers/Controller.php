<?php

namespace App\Admin\Controllers;

use App\Services\Service;
use Slowlyo\OwlAdmin\Controllers\AdminController;

/**
 * 智能体设置
 *
 * @property Service $service
 */
class Controller extends AdminController
{
	protected string $serviceName = Service::class;

	public function list()
	{
		$crud = $this->baseCRUD()
			->filterTogglable(false)
			->headerToolbar([
				$this->createButton('dialog'),
				...$this->baseHeaderToolBar()
			])
			->columns([
				amis()->TableColumn('id', 'ID')->widith(100)->sortable(),
				amis()->TableColumn('name', '智能体名称'),
				amis()->TableColumn('icon', '图标URL'),
				amis()->TableColumn('introduction', '介绍'),
				amis()->TableColumn('opening_remark', '开场白'),
				amis()->TableColumn('prompt_settings', '提示词设置'),
				amis()->TableColumn('created_at', admin_trans('admin.created_at'))->type('datetime')->sortable(),
				amis()->TableColumn('updated_at', admin_trans('admin.updated_at'))->type('datetime')->sortable(),
				$this->rowActions('dialog')
			]);

		return $this->baseList($crud);
	}

	public function form($isEdit = false)
	{
		return $this->baseForm()->body([
			amis()->TextControl('name', '智能体名称'),
			amis()->TextControl('icon', '图标URL'),
			amis()->TextControl('introduction', '介绍'),
			amis()->TextControl('opening_remark', '开场白'),
			amis()->TextControl('prompt_settings', '提示词设置'),
		]);
	}

	public function detail()
	{
		return $this->baseDetail()->body([
			amis()->TextControl('id', 'ID')->static(),
			amis()->TextControl('name', '智能体名称')->static(),
			amis()->TextControl('icon', '图标URL')->static(),
			amis()->TextControl('introduction', '介绍')->static(),
			amis()->TextControl('opening_remark', '开场白')->static(),
			amis()->TextControl('prompt_settings', '提示词设置')->static(),
			amis()->TextControl('created_at', admin_trans('admin.created_at'))->static(),
			amis()->TextControl('updated_at', admin_trans('admin.updated_at'))->static(),
		]);
	}
}
