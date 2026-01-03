<?php

namespace App\Services;

use App\Models\Code;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Slowlyo\OwlAdmin\Services\AdminService;

/**
 * 卡密管理
 *
 * @method Code getModel()
 * @method Code|\Illuminate\Database\Query\Builder query()
 */
class CodeService extends AdminService
{
	protected string $modelName = Code::class;


	/**
	 * 生成卡密
	 *
	 * @param $type
	 *
	 * @return string
	 */
	public function generateCode($type, $mchId)
	{
		$prefix = $type == 1 ? 'C' : 'V';

		do {
			// 使用更强大的随机字符串，并循环检查以确保唯一性
			// 格式: C-MCHID-RANDOM
			$randomPart = Str::upper(Str::random(20));
			$code       = "{$prefix}-{$mchId}-{$randomPart}";
		} while ($this->query()->where('code', $code)->exists());


		return $code;
	}

	/**
	 * 新增
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	public function store($data)
	{
		$result = false;
		DB::beginTransaction();
		try {
			$insertData = [];
			$numberToGenerate = (int)($data['number'] ?? 0);

			if ($numberToGenerate <= 0) {
				throw new \InvalidArgumentException('生成数量 (number) 必须大于 0');
			}

			for ($i = 0; $i < $numberToGenerate; $i++) {
				$insertData[] = [
					'mch_id'     => $data['mch_id'],
					'name'       => $data['name'],
					'code_type'  => $data['code_type'],
					'value'      => $data['value'],
					'is_status'  => 0,
					'code'       => $this->generateCode($data['code_type'], $data['mch_id']),
					'created_at' => now(),
					'updated_at' => now(),
				];
			}

			if (!empty($insertData)) {
				// 使用批量插入以提高性能
				$result = $this->getModel()->insert($insertData);
			}

			DB::commit();
		} catch (\Throwable $e) {
			DB::rollBack();

			admin_abort($e->getMessage());
		}

		return $result;
	}
}
