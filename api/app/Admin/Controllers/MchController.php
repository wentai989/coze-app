<?php

namespace App\Admin\Controllers;

use App\Services\MchService;
use Slowlyo\OwlAdmin\Controllers\AdminController;

/**
 * 商户名称管理
 *
 * @property MchService $service
 */
class MchController extends AdminController
{
	protected string $serviceName = MchService::class;

	public function list()
	{
		$crud = $this->baseCRUD()
			->filterTogglable(false)
			->filter($this->baseFilter()->body([
				amis()->TextControl('name', '商户名称')
					->placeholder('输入商户名称搜索')
					->size('md')
					->clearable(),
			]))
			->headerToolbar([
				$this->createButton('dialog'),
				...$this->baseHeaderToolBar()
			])
			->columns([
				amis()->TableColumn('id', 'ID')->widith(100)->sortable(),
				amis()->TableColumn('name', '商户名称')->sortable(),
				amis()->ImageControl('logo', 'Logo')->width(50)->height(50),
				amis()->TableColumn('app_key', 'v3密钥')->sortable(),
				amis()->TableColumn('service_at', '服务到期时间')->sortable(),
				amis()->SwitchControl('is_status', '状态')->sortable(),
				amis()->TableColumn('created_at', admin_trans('admin.created_at'))->width(160)->type('datetime')->sortable(),
				// amis()->TableColumn('updated_at', admin_trans('admin.updated_at'))->width(160)->type('datetime')->sortable(),
				amis()->TableColumn('操作')->width(260)->type('operation')
					->buttons([
						amis()->DialogAction()
							->label('系统配置')
							->level('primary')
							->className('text-while bg-green-600 border-green-600')
							->dialog(
								amis()->Dialog()->title('系统配置')->body($this->systemConfigForm())->size('md')
							),
						$this->rowEditButton('dialog', 'md'),
						$this->rowDeleteButton(),
					]),
			]);

		return $this->baseList($crud);
	}

	// 系统配置表单
	public function systemConfigForm()
	{
		return $this->baseForm()
			->mode('horizontal')
			->title('系统配置')
			->form(false)->canAccessSuperData(true)->api($this->getUpdatePath())
			->body([
				amis()->Tabs()->tabs([
					[
						'title' => '基础设置',
						'body'  => [

							amis()->TextControl('mini.appid', '小程序AppID')->required(),
							amis()->TextControl('mini.appsecret', '小程序AppSecret')->required(),
							amis()->ImageControl('contact_qrcode', '联系我们')->required(),
							amis()->TextControl('share.title', '分享标题')->required()->max(15),
							amis()->ImageControl('share.poster', '分享海报')->required(),


						],
					],
					[
						'title' => '微信支付',
						'body'  => [
							amis()->TextControl('pay.mch_id', '微信商户名称号')->required(),
							amis()->TextControl('pay.secret_key', '商户名称APIV3密钥')->required(),
							// amis()->TextControl('pc_pay_url', 'ios支付地址')->required(),
							amis()->TextareaControl('pay.cert', '证书文件')->description('复制cert文件内容到这里'),
							amis()->TextareaControl('pay.key', '密钥文件')->description('复制key文件内容到这里'),
						],
					],

					[
						'title' => '营销设置',
						'body'  => [
							amis()->NumberControl('power.add_value', '注册赠送算力')
								->value(0)
								->required(),
							amis()->NumberControl('power.ask_value', '邀请注册赠送算力')
								->value(0)
								->required(),
							amis()->NumberControl('power.sign_value', '签到赠送算力')
								->value(0)
								->required(),
							amis()->NumberControl('power.video_value', '视频赠送算力')
								->value(0)
								->description('微信广告联盟观看广告赠送算力， 有需要联系客服开通')
								->required(),
							amis()->SwitchControl('is_spread', '是否开启推广员')
								->description('开启后，用户推广的用户注册后，产生消费返佣')
								->type('switch')
								->onText('开启')
								->offText('关闭')
								->value(0),
							amis()->NumberControl('spread_value', '推广员返佣比例')
								->value(0)
								->description('例如：10%，则输入10')
								->required(),
						],
					],
					// [
					// 	'title' => '短信设置',
					// 	'body'  => [
					// 		amis()->TextControl('sms_account', '短信宝账户名')->description('H5,PC必填'),
					// 		amis()->TextControl('sms_api_key', '短信宝APIKEY')->description('H5,PC必填'),
					// 		amis()->TextControl('sms_sign', '短信宝签名')->description('H5,PC必填'),
					// 	],
					// ],
					// [
					// 	'title' => '赠送设置',
					// 	'body'  => [
					// 		amis()->NumberControl('calculate_value', '新注册用户赠送算力')
					// 			->value(0)
					// 			->required(),
					// 		amis()->NumberControl('vip_time', '新注册用户赠送天数')
					// 			->value(0)
					// 			->required(),
					// 		amis()->NumberControl('ask_calculate_value', '邀请注册送算力')
					// 			->value(0)
					// 			->required(),
					// 	],
					// ],
				])
			]);
	}



	public function form($isEdit = false)
	{
		return $this->baseForm()->body([

			amis()->TextControl('name', '商户名称')->required(true),
			amis()->ImageControl('logo', 'Logo'),

			amis()->SwitchControl('is_h5', '手机端网页')
				->type('switch')
				->onText('开启')
				->offText('关闭')
				->value(1),
			amis()->SwitchControl('is_mini', '微信小程序')
				->type('switch')
				->onText('开启')
				->offText('关闭')
				->value(1),
			amis()->SwitchControl('is_pc', '电脑端网页')
				->type('switch')
				->onText('开启')
				->offText('关闭')
				->value(1),
			amis()->DateControl('service_at', '服务到期时间')->format('YYYY-MM-DD')->minDate(date('Y-m-d'))->required(true),

			amis()->TextareaControl('remark', '备注'),

			amis()->SwitchControl('is_status', '状态')
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
			amis()->TextControl('is_h5', 'IsH5')->static(),
			amis()->TextControl('is_mini', 'IsMini')->static(),
			amis()->TextControl('is_pc', 'IsPc')->static(),
			amis()->TextControl('is_status', 'IsStatus')->static(),
			amis()->TextControl('logo', 'Logo')->static(),
			amis()->TextControl('mini_config', 'MiniConfig')->static(),
			amis()->TextControl('name', 'Name')->static(),
			amis()->TextControl('remark', 'Remark')->static(),
			amis()->TextControl('service_at', 'ServiceAt')->static(),
			amis()->TextControl('web_config', 'WebConfig')->static(),
			amis()->TextControl('created_at', admin_trans('admin.created_at'))->static(),
			amis()->TextControl('updated_at', admin_trans('admin.updated_at'))->static(),
		]);
	}
}
