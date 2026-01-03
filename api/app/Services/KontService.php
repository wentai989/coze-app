<?php

namespace App\Services;

use App\Models\App;
use App\Models\Kont;
use Slowlyo\OwlAdmin\Services\AdminService;
use Illuminate\Support\Facades\Http;

/**
 * 扣子设置
 *
 * @method Kont getModel()
 * @method Kont|\Illuminate\Database\Query\Builder query()
 */
class KontService extends AdminService
{
	protected string $modelName = Kont::class;

	public function getAgentBySpaceId($request)
	{
		set_time_limit(0);
		ini_set('memory_limit', '512M'); // 设置内存限制为 512MB


		$space = Kont::findOrFail($request->input('id'));

		if (!$space) {
			throw new \Exception('空间不存在');
		}



		$kontAtuhService = new KontApiService();
		$kontAtuhService->app_id = $space->app_id;
		$kontAtuhService->app_secret = $space->app_secret;
		$kontAtuhService->app_key = $space->app_key;
		$token = $kontAtuhService->token();

		if (!$token['access_token']) {
			throw new \Exception('token 鉴权失败，请检查空间配置');
		}



		try {
			$response = Http::withHeaders([
				'Authorization' => 'Bearer ' . $token['access_token'],
			])->get('https://api.coze.cn/v1/space/published_bots_list?space_id', [
				'page_size' => 9999,
				'space_id' => $space->space_id,
			]);

			$data = $response->json();


			if (!isset($data['data']['space_bots'])) {
				throw new \Exception('获取智能体列表失败：' . json_encode($data));
			}

			$i = 0;
			$processedCount = 0;
			$maxProcess = 199; // 最大处理数量

			foreach ($data['data']['space_bots'] as $item) {
				$model = App::where('bot_id', $item['bot_id'])->first();
				if ($model && $model->publish_time == date('Y-m-d H:i:s', $item['publish_time'])) {
					continue;
				}
				// if ($model && $model->updated_at > $model->publish_time) {
				// 	continue;
				// }
				if ($i > $maxProcess) {
					break;
				}
				$response = Http::withHeaders([
					'Authorization' => 'Bearer ' . $token['access_token'],
				])->get('https://api.coze.cn/v1/bot/get_online_info', [
					'bot_id' => $item['bot_id'],
				]);

				$detail = $response->json();

				$kontConfigs = [];
				if ($detail && $detail['code'] == 0) {
					//	$onboarding_info = $detail['data']['onboarding_info'];
					$kontConfigs = [
						'onboarding_info' => $detail['data']['onboarding_info'],
						'voice_info_list' => $detail['data']['voice_info_list'],
					];
				}
				if (!$model) {
					$model = new App();
					$model->image = $item['icon_url'];
				}


				$model->mch_id = $space->mch_id;
				$model->bot_id = $item['bot_id'];
				$model->name = $item['bot_name'];
				// $model->onboarding_info = $onboarding_info;
				$model->description = $item['description'];
				$model->publish_time = date('Y-m-d H:i:s', $item['publish_time']);
				$model->kont_id = $space->id;
				$model->app_type = 1;
				$model->configs = $kontConfigs;
				$model->save();

				$processedCount++;
				$i++;
				// 每处理10条记录，释放一次内存
				// if ($processedCount % 10 === 0) {
				// 	gc_collect_cycles();
				// }

			}

			return [
				'code' => 200,
				'message' => "同步 {$processedCount} 个智能体"
			];
		} catch (\Exception $e) {
			throw new \Exception('同步失败: ' . $e->getMessage());
		}
	}
}
