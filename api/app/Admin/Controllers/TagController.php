<?php

namespace App\Admin\Controllers;

use App\Services\TagService;
use Slowlyo\OwlAdmin\Controllers\AdminController;

/**
 * 标签管理
 *
 * @property TagService $service
 */
class TagController extends AdminController
{
	protected string $serviceName = TagService::class;

	public function list()
	{
		$crud = $this->baseCRUD()
			->filterTogglable(false)
			->filter($this->baseFilter()->body([
				amis()->TextControl('name', '标签名称')
					->placeholder('输入标签名称搜索')
					->size('md')
					->clearable(),
			]))
			->headerToolbar([
				$this->createButton('dialog'),
				...$this->baseHeaderToolBar()
			])
			->columns([
				amis()->TableColumn('id', 'ID')->widith(100)->sortable(),
				amis()->TableColumn('name', '标签名称'),
				amis()->TableColumn('background_color', '背景色')->type('color'),
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
			amis()->TextControl('name', '标签名称')->required(),
			amis()->InputColorControl('background_color', '标签背景色'),
		]);
	}

	public function detail()
	{
		return $this->baseDetail()->body([
			amis()->TextControl('id', 'ID')->static(),
			amis()->TextControl('name', '标签名称')->static(),
			amis()->TextControl('background_color', '标签背景色')->static()->type('color'),
			amis()->TextControl('created_at', admin_trans('admin.created_at'))->static(),
			amis()->TextControl('updated_at', admin_trans('admin.updated_at'))->static(),
		]);
	}
}
