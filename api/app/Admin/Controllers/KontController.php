<?php

namespace App\Admin\Controllers;

use App\Services\KontService;
use Slowlyo\OwlAdmin\Controllers\AdminController;
use Illuminate\Http\Request;

/**
 * 扣子设置
 *
 * @property KontService $service
 */
class KontController extends AdminController
{
	protected string $serviceName = KontService::class;

	public function list()
	{
		$crud = $this->baseCRUD()
			->headerToolbar([
				$this->createButton('dialog'),
				...$this->baseHeaderToolBar()
			])
			//搜索
			->filter(
				$this->baseFilter()->body(
					[
						// amis()->SelectControl('agent_id', ' 渠道')
						// 	->options(Agent::getAgentListData())
						// 	->clearable()
						// 	->searchable()
						// 	->size('md'),

						amis()->TextControl('name', '扣子名称')
							->placeholder('输入扣子名称搜索')
							->size('md')
							->clearable(),
					]
				)
			)
			->columns([
				amis()->TableColumn('id', 'ID')->sortable(),
				amis()->TableColumn('mch.name', '商户'),
				amis()->TableColumn('name', '扣子名称'),
				amis()->TableColumn('space_id', '空间ID'),
				amis()->TableColumn('app_id', '应用ID'),
				amis()->TableColumn('created_at', admin_trans('admin.created_at'))->type('datetime')
					->sortable(),
				amis()->TableColumn('updated_at', admin_trans('admin.updated_at'))->type('datetime')
					->sortable(),
				amis()->TableColumn('操作')->type('operation')
					->buttons([
						amis()->AjaxAction()
							->label('同步智能体')
							->level('primary')
							->className('text-while bg-green-600 border-green-600')
							->api('post:' . admin_url('knot-agent/get-agent-by-space-id'))
							->confirmText('您确定要从该空间同步智能体吗？')
							->data(['kont_space_id' => '${space_id}']),
						$this->rowEditButton('dialog'),
						$this->rowDeleteButton(),
					]),
			]);

		return $this->baseList($crud);
	}

	public function form($isEdit = false)
	{
		return $this->baseForm()->body([
			amis()->SelectControl('mch_id', '商户')
				->searchable()
				->source('/select_mch_options')
				->required(),
			amis()->TextControl('name', '扣子名称')
				->required(),
			amis()->TextControl('space_id', '空间ID')
				->required(),
			amis()->TextControl('app_id', '应用ID')
				->required(),
			amis()->TextControl('app_secret', '应用密钥')
				->required(),
			amis()->TextareaControl('app_key', '应用公钥')
				->maxRows(10)
				->required(),
			amis()->SwitchControl('is_status', '状态')
				->value(1)
				->onText('启用')
				->offText('禁用')
				->required(),

		]);
	}

	//获取智能体
	public function getAgentBySpaceId(Request $request)
	{


		try {


			$this->service->getAgentBySpaceId($request);
			return $this->response()->success([
				'message' => '获取智能体成功',
			]);
		} catch (\Exception $e) {
			return $this->response()->fail($e->getMessage());
		}
	}
}
