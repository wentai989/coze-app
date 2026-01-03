<?php

namespace App\Admin\Controllers;

use App\Services\SpreadService;
use Slowlyo\OwlAdmin\Controllers\AdminController;

/**
 * 推广记录
 *
 * @property SpreadService $service
 */
class SpreadController extends AdminController
{
	protected string $serviceName = SpreadService::class;

	public function list()
	{
		$crud = $this->baseCRUD()
			->filterTogglable(false)
			->headerToolbar([
				$this->createButton('dialog'),
				...$this->baseHeaderToolBar()
			])
			->filter(
				$this->baseFilter()->body(
					[
						amis()->TextControl('remark', '标题')
							->placeholder('输入标题搜索')
							->size('md')
							->clearable(),
					]
				)
			)
			->columns([
				amis()->TableColumn('id', 'ID')->sortable(),
				amis()->TableColumn('mch.name', '商户名称'),
				amis()->TableColumn('remark', '标题'),
				amis()->TableColumn('user.phone', '推广人'),
				amis()->TableColumn('amount', '金额')->sortable(),
				amis()->TableColumn('spread_type', '佣金类型')
					->type('mapping')
					->map([
						'1' => '<span class="text-blue-600 text-sm font-bold">到账</span>',
						'2' => '<span class="text-green-600 text-sm font-bold">提现</span>',
						'*' => '未知',
					]),
				amis()->TableColumn('is_status', '分销状态')
					->type('mapping')
					->map([
						'1' => '<span class="text-blue-600 text-sm font-bold">成功</span>',
						'2' => '<span class="text-red-600 text-sm font-bold">失败</span>',
						'0' => '<span class="text-yellow-600 text-sm font-bold">待处理</span>',
						'*' => '未知',
					]),
				amis()->TableColumn('created_at', admin_trans('admin.created_at'))->type('datetime')->sortable(),
				amis()->TableColumn('操作')->width(160)->type('operation')
					->buttons([
						$this->rowEditButton('dialog', 'md')->title('审核')
							->visibleOn('${is_status === 0 && spread_type === 2}'),
						$this->rowDeleteButton(),
					]),
			]);

		return $this->baseList($crud);
	}

	public function form($isEdit = false)
	{
		return $this->baseForm()->body([
			amis()->ImageControl('amount_img', '推广二维码')->static(),
			amis()->RadiosControl('is_status', '状态')
				->options([
					['label' => '同意', 'value' => 1],
					['label' => '拒绝', 'value' => 2],
				]),
			amis()->TextareaControl('audit_remark', '原因')
				->visibleOn('${is_status === 2}'),
		]);
	}

	public function detail()
	{
		return $this->baseDetail()->body([
			amis()->TextControl('id', 'ID')->static(),
			amis()->TextControl('amount', '分成')->static(),
			amis()->TextControl('tg_user_id', '推广人')->static(),
			amis()->TextControl('user_id', '推广员ID')->static(),
			amis()->TextControl('created_at', admin_trans('admin.created_at'))->static(),
			amis()->TextControl('updated_at', admin_trans('admin.updated_at'))->static(),
		]);
	}
}
