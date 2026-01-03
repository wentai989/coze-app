<?php

namespace App\Admin\Controllers;

use App\Services\CategorieService;
use Slowlyo\OwlAdmin\Controllers\AdminController;

/**
 * 分类管理
 *
 * @property CategorieService $service
 */
class CategorieController extends AdminController
{
	protected string $serviceName = CategorieService::class;

	public function list()
	{
		$crud = $this->baseCRUD()
			->filterTogglable(false)
			->filter($this->baseFilter()->body([
				amis()->TextControl('name', '分类名称')
					->placeholder('输入分类名称搜索')
					->size('md')
					->clearable(),
			]))
			->headerToolbar([
				$this->createButton('dialog'),
				...$this->baseHeaderToolBar()
			])
			->columns([
				amis()->TableColumn('id', 'ID')->widith(100)->sortable(),
				amis()->TableColumn('mch.name', '商户名称')->sortable(),
				amis()->TableColumn('name', '分类名称'),
				amis()->TableColumn('icon', '图标')
					->type('image')
					->height(50)
					->src('${icon}'),
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
			amis()->TextControl('name', '分类名称')->required(),
			amis()->ImageControl('icon', '分类图标')->receiver($this->uploadImagePath()),
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
}
