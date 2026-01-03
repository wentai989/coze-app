<?php

namespace App\Admin\Controllers;

use App\Services\AgentService;
use Slowlyo\OwlAdmin\Controllers\AdminController;

/**
 * 智能体设置
 *
 * @property AgentService $service
 */
class AgentController extends AdminController
{
	protected string $serviceName = AgentService::class;

	public function list()
	{
		$crud = $this->baseCRUD()
			->filterTogglable(false)
			->filter($this->baseFilter()->body([
				amis()->TextControl('name', '智能体名称')
					->placeholder('输入智能体名称搜索')
					->size('md')
					->clearable(),
			]))
			->headerToolbar([
				$this->createButton('dialog'),
				...$this->baseHeaderToolBar()
			])
			->columns([
				amis()->TableColumn('id', 'ID')->widith(100)->sortable(),
				amis()->TableColumn('name', '智能体名称'),
				amis()->TableColumn('icon', '图标')
					->type('avatar')
					->src('${icon}'),
				amis()->TableColumn('introduction', '介绍'),
				amis()->SwitchControl('is_status', '状态')
					->width(100)
					->type('switch')
					->value(1),
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
			amis()->TextControl('name', '智能体名称')->required(),
			amis()->ImageControl('icon', '图标')->required(),
			amis()->TextControl('introduction', '介绍')->maxLength(30)->required(),
			amis()->TextareaControl('opening_remark', '开场白')->required(),
			amis()->TextareaControl('prompt_settings', '提示词设置')->maxLength(19000)->required(),
			amis()->NumberControl('sort', '排序')->value(0)->description('排序值越大越靠前')->required(),
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
