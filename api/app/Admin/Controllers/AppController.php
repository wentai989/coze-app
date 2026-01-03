<?php

namespace App\Admin\Controllers;

use App\Services\AppService;
use Slowlyo\OwlAdmin\Controllers\AdminController;

/**
 * 应用设置
 *
 * @property AppService $service
 */
class AppController extends AdminController
{
	protected string $serviceName = AppService::class;

	public function list()
	{



		$crud = $this->baseCRUD()
			->filterTogglable(false)
			->filter($this->baseFilter()->body([
				amis()->TextControl('name', '应用名称')
					->placeholder('输入应用名称搜索')
					->size('md')
					->clearable(),
			]))
			->headerToolbar([
				$this->createButton('drawer', 'lg'),
				...$this->baseHeaderToolBar()
			])
			->columns([
				amis()->TableColumn('id', 'ID')->widith(100)->sortable(),
				amis()->TableColumn('mch.name', '商户名称')->sortable(),
				amis()->TableColumn('name', '应用名称'),
				amis()->TableColumn('image', '应用Logo')
					->type('image')
					->height(50)
					->src('${image}'),
				amis()->TableColumn('app_type', '应用类型')
					->type('mapping')
					->map([
						'1' => '<span class=" text-blue-600 text-sm font-bold">智能体应用</span>',
						'2' => '<span class=" text-green-600 text-sm font-bold">工作流应用</span>',
						'*' => '未知',
					]),
				amis()->TableColumn('categorie.name', '应用分类'),
				amis()->TableColumn('sort', '排序'),
				amis()->TableColumn('is_status', '状态')
					->type('switch')
					->value(1),
				amis()->TableColumn('updated_at', admin_trans('admin.updated_at'))->type('datetime')->sortable()->width(180),
				amis()->TableColumn('操作')->width(160)->type('operation')
					->buttons([
						$this->rowEditButton('drawer', 'lg'),
						$this->rowDeleteButton(),
					]),
			]);

		return $this->baseList($crud);
	}

	public function form($isEdit = false)
	{
		return $this->baseForm()->body([
			amis()->Tabs()->tabs([
				[
					'title' => '应用设置',
					'body'  => [
						amis()->SelectControl('mch_id', '商户名称')
							->source('/select_mch_options')
							->searchable(true)
							->required(true),
						amis()->RadiosControl('app_type', '应用类型')
							->options([
								['label' => '智能体应用', 'value' => 1],
								['label' => '工作流应用', 'value' => 2],
							])
							->required(true),
						amis()->SelectControl('kont_id', '扣子空间')
							->source('/select_kont_options')
							->searchable(true)
							->required(true),
						amis()->TextControl('bot_id', '智能体ID')
							->visibleOn('${app_type === 1}')
							->required(),
						amis()->TextControl('bot_id', '工作流ID')
							->visibleOn('${app_type === 2}')
							->required(),
						amis()->TextControl('name', '应用名称')->required(true),
						amis()->ImageControl('image', '应用Logo')->required(true),
						amis()->SelectControl('categorie_id', '应用分类')
							->source('select_categorie_options')
							->searchable(true)
							->required(true),
						amis()->RadiosControl('is_vip', '是否为VIP应用')
							->options([
								['label' => '是', 'value' => 1],
								['label' => '否', 'value' => 0],
							])
							->required(true),

						amis()->RadiosControl('power_type', '算力扣点类型')
							->options([
								['label' => '固定消耗算力', 'value' => 2],
								['label' => '倍数消耗算力', 'value' => 1],
							])
							->required(true),
						amis()->NumberControl('use_power', '固定消耗算力')
							->step(0.01)
							->min(0.01)
							->visibleOn('${power_type === 2}')
							->required(true),
						amis()->NumberControl('multiple_power', '倍数消耗算力')
							->description('根据扣子api返回的token数 X 该倍数 ,工作流不支持倍数消耗算力')
							->step(0.01)
							->min(0.01)
							->visibleOn('${power_type === 1}')
							->required(true),

						amis()->TextareaControl('description', '应用简介')->maxLength(155)
							->require(true),
						amis()->NumberControl('used_num', '已用人数')
							->value(0)
							->required(true),
						amis()->NumberControl('sort', '排序')
							->value(0)
							->description('排序越大越靠前')
							->required(true),


						amis()->SwitchControl('is_status', '状态')
							->width(100)
							->type('switch')
							->onText('启用')
							->offText('禁用')
							->value(1),

					],
				],
				[
					'title' => '输入参数设置',
					'visibleOn' => '${app_type === 2}',
					'body'  => [
						amis()->ComboControl('launch_params', '')
							->multiple(true)
							->multiLine(true)
							->mode('horizontal')
							->items([
								amis()->TextControl('name', '变量')
									->required(true),
								amis()->TextControl('label', '提示')
									->required(true),
								amis()->TextControl('value', '默认值'),
								amis()->SelectControl('type', '格式')
									->options([
										['label' => '纯数字', 'value' => 'number'],
										['label' => '短文本', 'value' => 'text'],
										['label' => '长文本', 'value' => 'textarea'],
										['label' => '图片', 'value' => 'image'],
										['label' => '文件', 'value' => 'file'],
									])
									->required(true),
								//是否必须
								amis()->SwitchControl('required', '是否必选')
									->onText('是')
									->offText('否')
									->value(1),
							])->required(true),
					]
				],
				[
					'title' => '输出参数设置',
					'visibleOn' => '${app_type === 2}',
					'body'  => [
						amis()->ComboControl('output_params', '')
							->multiple(true)
							->multiLine(true)
							->mode('horizontal')
							->items([
								amis()->TextControl('name', '变量')
									->required(true),
								// amis()->TextControl('label', '提示')
								// 	->required(true),
								amis()->SelectControl('type', '格式')
									->options([
										['label' => '文本', 'value' => 'text'],
										['label' => '图片', 'value' => 'image'],
										['label' => '文件', 'value' => 'file'],
									])
									->required(true),

								amis()->TextControl('tip', '提示')
									->description('用于输出结果提示语，非必填'),
								//是否必须
								// amis()->SwitchControl('required', '是否必选')
								// 	->onText('是')
								// 	->offText('否')
								// 	->value(1),
							])->required(true),
					]
				],

			]),


		]);
	}
}
