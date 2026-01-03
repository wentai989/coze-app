<?php

namespace App\Services;

use App\Models\KontCalculate;
use App\Models\KontWorkExecute;
use Illuminate\Support\Facades\Http;
use Firebase\JWT\JWT;
use Illuminate\Support\Str;
use App\Models\KontToken;
use App\Http\Services\KontCalculateService;
use App\Models\KontLog;
use App\Models\KontWork;
use Illuminate\Support\Facades\App;
use App\Models\AppLog;

class KontApiService
{
    public $app_id = '';
    public $app_secret = '';
    public $app_key = '';
    public $time = 86399; //过期时间
    public $is_log = true;

    private function getPrivateKey(): string
    {
        return str_replace(['\n', '\r', '\\n', '\\r'], "\n", $this->app_key);
    }

    public function token(): array
    {
        try {
            $tokenModel = KontToken::where('app_id', $this->app_id)->first();
            if ($tokenModel && $tokenModel->expires_in > time()) {
                return [
                    'access_token' => $tokenModel->token,
                    'expires_in' => $tokenModel->expires_in,
                ];
            }
            $jwt = $this->generateJWT();
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $jwt
            ])->post('https://api.coze.cn/api/permission/oauth2/token', [
                'duration_seconds' => 86399,
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer'
            ]);
            $data = $response->json();
            if (isset($data['error'])) {
                throw new \Exception('获取token失败,请检查配置信息: ' . $data['error']);
            }
            KontToken::create([
                'app_id' => $this->app_id,
                'token' => $data['access_token'],
                'expires_in' => $data['expires_in'],
            ]);
            return $data;
        } catch (\Exception $e) {
            throw new \Exception('获取token失败: ' . $e->getMessage());
        }
    }

    private function generateJWT(): string
    {
        $now = time();


        $payload = [
            'iss' => $this->app_id,
            'aud' => 'api.coze.cn',
            'jti' => (string) Str::uuid(),
            'iat' => $now,
            'exp' => $now + (86399),
        ];

        $headers = [
            'typ' => 'JWT',
            'alg' => 'RS256',
            'kid' => $this->app_secret
        ];


        $jwt = JWT::encode($payload, $this->getPrivateKey(), 'RS256', null, $headers);
        return $jwt;
    }

    // 获取扣子异步结果
    public function asyncWorkOutput(string $workflow_id, string $execute_id, $appLog)
    {

        $token = $this->token();

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token['access_token']
        ])->get('https://api.coze.cn/v1/workflows/' . $workflow_id . '/run_histories/' . $execute_id);
        $data = $response->json();
        if (isset($data['error'])) {
            throw new \Exception('获取异步结果失败,请检查配置信息: ' . $data['error']);
        }


        $isStatus = 0;
        if (isset($data['data']) && $data['code'] == 0) {
            $collection = collect($data['data']);
            if ($collection->contains('execute_status', 'Fail')) {
                // 存在失败项
                $isStatus = 2;
            } elseif ($collection->contains(fn($item) => $item['execute_status'] !== 'Running')) {
                // 存在非运行中项（即已完成），且无失败项
                $isStatus = 1;
            }
        }
        // 状态为完成或失败时，更新数据库
        if ($isStatus != 0) {
            $appLog->is_status = $isStatus;
            $appLog->outputs = $data['data'];
            $appLog->save();
        }

        return  $appLog;
    }

    public function workRun($request, $app): array
    {


        $token = $this->token();


        // dd([
        //     'token' => $token,
        //     'workflow_id' => $request->workflow_id,
        //     'parameters' => json_encode($request->parameters),
        // ]);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token['access_token']
        ])->post('https://api.coze.cn/v1/workflow/run', [
            'workflow_id' => $request->workflow_id,
            'parameters' => $request->parameters,
            'is_async' => true,
        ]);
        $data = $response->json();

        if (($data['msg'])) {
            throw new \Exception('开始异步工作流失败 code: ' . $data['msg']);
        }
        //开始记录
        $appLog =  AppLog::create([
            'mch_id' => $request->mch->id,
            'app_id' => $app->id,
            'user_id' => $request->auth_user->id,
            'contents' =>  $request->parameters,
            'execute_id' => $data['execute_id'],
            'is_status' => 0,
            'log_type' => 'work'
        ]);
        return [
            'app_log_id' => $appLog->id,
        ];
    }
}
