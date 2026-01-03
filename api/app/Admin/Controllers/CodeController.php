<?php

namespace App\Admin\Controllers;

use App\Services\CodeService;
use Slowlyo\OwlAdmin\Controllers\AdminController;

/**
 * 卡密管理
 *
 * @property CodeService $service
 */
class CodeController extends AdminController
{
	protected string $serviceName = CodeService::class;

	public function list()
	{
		$crud = $this->baseCRUD()
			->filterTogglable(false)
			->filter($this->baseFilter()->body([
				amis()->TextControl('code', '卡密')
					->placeholder('输入卡密搜索')
					->size('md')
					->clearable(),
			]))
			->headerToolbar([
				$this->createButton('dialog'),
				$this->exportAction(),
				...$this->baseHeaderToolBar()
			])
			->columns([
				amis()->TableColumn('id', 'ID')->widith(100)->sortable(),
				amis()->TableColumn('mch.name', '商户名称'),
				amis()->TableColumn('code', '卡密'),
				amis()->TableColumn('code_type', '卡密类型')
					->type('mapping')
					->map([
						'1' => '<span class="text-blue-600 text-sm font-bold">算力卡密</span>',
						'2' => '<span class="text-green-600 text-sm font-bold">会员卡密</span>',
						'*' => '未知',
					]),
				amis()->TableColumn('value', '卡密面值')->sortable(),
				amis()->TableColumn('name', '批次名称')->maxLength(50),
				amis()->TableColumn('is_status', '使用状态')
					->type('mapping')
					->map([
						'0' => '<span class="text-red-600 text-sm font-bold">未使用</span>',
						'1' => '<span class="text-green-600 text-sm font-bold">已使用</span>',
						'*' => '未知',
					]),
				amis()->TableColumn('user.phone', '用户手机号'),
				amis()->TableColumn('invoke_at', '激活时间')->width(180)->type('datetime')->sortable(),
				amis()->TableColumn('created_at', admin_trans('admin.created_at'))->width(180)->type('datetime')->sortable(),


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
			amis()->RadiosControl('code_type', '卡密类型')
				->options([
					['label' => '算力', 'value' => 1],
					['label' => '会员', 'value' => 2],
				])->value(1),
			amis()->TextControl('name', '批次名称')->required(true),


			amis()->NumberControl('value', '卡密面值')
				->description('算力：单位为整数，会员：单位为天')
				->required(true)
				->min(1),

			amis()->NumberControl('number', '生成数量')->min(1)->max(1000)
				->required(true),
		]);
	}

	public function detail()
	{
		return $this->baseDetail()->body([
			amis()->TextControl('id', 'ID')->static(),
			amis()->TextControl('code', '卡密')->static(),
			amis()->TextControl('code_type', '类型1算力，2会员')->static(),
			amis()->TextControl('is_status', '状态')->static(),
			amis()->TextControl('mch_id', 'MchId')->static(),
			amis()->TextControl('name', '生成名称')->static(),
			amis()->TextControl('user_id', 'UserId')->static(),
			amis()->TextControl('value', '金额')->static(),
			amis()->TextControl('created_at', admin_trans('admin.created_at'))->static(),
			amis()->TextControl('updated_at', admin_trans('admin.updated_at'))->static(),
		]);
	}
	protected function exportFileName()
	{
		return '卡密导出';
	}

	protected function exportMap($row)
	{
		$data = $row instanceof \Illuminate\Database\Eloquent\Model ? $row->toArray() : (array) $row;

		return [
			'卡密' => data_get($data, 'code'),
			'创建时间' => date('Y-m-d H:i:s', strtotime((string) data_get($data, 'created_at'))),
		];
	}
}
