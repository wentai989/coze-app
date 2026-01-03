<?php

namespace App\Http\Services;

use App\Models\PowerDeductionLog;
use App\Models\App;
use App\Models\User;
use App\Services\Service;
use Slowlyo\OwlAdmin\Services\AdminService;

/**
 * 算力消耗记录
 *
 * @method Order getModel()
 * @method Order|\Illuminate\Database\Query\Builder query()
 */
class PowerDeductionLogService
{

    public function getPowerDeductionLogs($request)
    {
        $orders = PowerDeductionLog::where('user_id', $request->auth_user->id)->orderBy('id', 'desc')->get();
        return $orders;
    }

    public function createPowerDeductionLog($id, $user_id, $tokens)
    {


        $app = App::findOrFail($id);

        if ($app->power_type == 1) {
            $powerValue = round($app->multiple_power * $tokens, 2);
        } else {
            $powerValue = $app->use_power;
        }




        if (!$powerValue) {
            return;
        }
        $powerDeductionLog = PowerDeductionLog::create([
            'user_id' => $user_id,
            'name' => $app->name,
            'power_value' => $powerValue,
            'deduction_type' => $app->power_type,
            'chat_id' => $id,
            'mch_id' => $app->mch_id,
        ]);
        User::where('id', $user_id)->decrement('power_value', $powerValue);
        return $powerDeductionLog;
    }
}
