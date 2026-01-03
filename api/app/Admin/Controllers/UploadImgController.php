<?php

namespace App\Admin\Controllers;

use App\Services\UploadImgService;
use Slowlyo\OwlAdmin\Controllers\AdminController;

/**
 * 文件上传日志
 *
 * @property UploadImgService $service
 */
class UploadImgController extends AdminController
{
	protected string $serviceName = UploadImgService::class;

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
				amis()->TableColumn('file', '分成'),
				amis()->TableColumn('mch_id', '商户ID'),
				amis()->TableColumn('url', '推广人'),
				amis()->TableColumn('created_at', admin_trans('admin.created_at'))->type('datetime')->sortable(),
				amis()->TableColumn('updated_at', admin_trans('admin.updated_at'))->type('datetime')->sortable(),
				$this->rowActions('dialog')
			]);

		return $this->baseList($crud);
	}

	public function form($isEdit = false)
	{
		return $this->baseForm()->body([
			amis()->TextControl('file', '分成'),
			amis()->TextControl('mch_id', '商户ID'),
			amis()->TextControl('url', '推广人'),
		]);
	}

	public function detail()
	{
		return $this->baseDetail()->body([
			amis()->TextControl('id', 'ID')->static(),
			amis()->TextControl('file', '分成')->static(),
			amis()->TextControl('mch_id', '商户ID')->static(),
			amis()->TextControl('url', '推广人')->static(),
			amis()->TextControl('created_at', admin_trans('admin.created_at'))->static(),
			amis()->TextControl('updated_at', admin_trans('admin.updated_at'))->static(),
		]);
	}
}