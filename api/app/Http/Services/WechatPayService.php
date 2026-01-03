<?php

namespace App\Http\Services;

use EasyWeChat\Pay\Application as PayApplication;
use App\Models\Order;

class WechatPayService
{
    // protected array $config;

    // public function __construct()
    // {
    //     $this->config = [
    //         // 'app_id'         => 'wx5b488afab2d98c9b',
    //         // 'mch_id'         => '1725719462',
    //         // 'secret'         => '26e62cdf0575cd01b3c0f1e672275b00',
    //         // 'mch_secret_key' => 'SDADASdsfdsfdfgd54dfg43d546d5f4g',
    //         // 'private_key'    => file_get_contents(storage_path('cert/apiclient_key.pem')),
    //         // 'certificate'    => file_get_contents(storage_path('cert/apiclient_cert.pem')),
    //     ];
    // }
    /**
     * 小程序支付下单
     * @param Order $order
     * @param string $openid
     * @param \Illuminate\Http\Request $request
     * @return array
     * @throws \Exception
     */
    public function miniProgramPay($order, $openid, $notify_url, $config = [])
    {
        // return  [
        //     'appid'        => $config['app_id'],
        //     'mchid'        => $config['mch_id'],
        //     'description'  => '商品购买',
        //     'out_trade_no' => $order->order_no,
        //     'notify_url'   => $config['notify_url'],
        //     'amount'       => [
        //         'total'    => intval($order->total_amount * 100),
        //         'currency' => 'CNY',
        //     ],
        //     'payer' => [
        //         'openid' => $openid,
        //     ],
        // ];

        try {
            $payment = new PayApplication($config);

            $response = $payment->getClient()->post(
                'v3/pay/transactions/jsapi',
                [
                    'json' => [
                        'appid'        => $config['app_id'],
                        'mchid'        => $config['mch_id'],
                        'description'  => $order->name,
                        'out_trade_no' => $order->order_no,
                        'notify_url'   => $notify_url,
                        'amount'       => [
                            'total'    => (int) ceil($order->amount * 100),
                            'currency' => 'CNY',
                        ],
                        'payer' => [
                            'openid' => $openid,
                        ],
                    ],
                ]
            );
            $result = $response->toArray();
            if (isset($result['prepay_id'])) {
                $utils = $payment->getUtils();
                $payConfig = $utils->buildMiniAppConfig($result['prepay_id'], $config['app_id']);
                return $payConfig;
            } else {
                throw new \Exception($result['message'] ?? '微信支付下单失败');
            }
        } catch (\Exception $e) {
            //  throw new \Exception($e->getMessage());
            throw new \Exception($e->getMessage() ?? '微信支付下单失败');
        }
    }

    /**
     * 扫码支付下单
     * @param Order $order
     * @param array $config
     * @return array
     * @throws \Exception
     */
    public function nativePay($order, $config)
    {
        $payment = new PayApplication($config);

        try {
            $response = $payment->getClient()->post(
                'v3/pay/transactions/native',
                [
                    'json' => [
                        'appid'        => $config['app_id'],
                        'mchid'        => $config['mch_id'],
                        'description'  => '订单支付',
                        'out_trade_no' => $order->order_no,
                        'notify_url'   => $config['notify_url'],
                        'amount'       => [
                            'total'    => intval($order->price * 100),
                            'currency' => 'CNY',
                        ],
                        'scene_info' => [
                            'payer_client_ip' => request()->ip(),
                        ],
                    ],
                ]
            );


            $result = $response->toArray();
            if (isset($result['code_url'])) {
                return [
                    'code_url' => $result['code_url'],  // 二维码链接
                    'order_no' => $order->order_no      // 订单号
                ];
            } else {
                throw new \Exception($result['message'] ?? '微信支付下单失败');
            }
        } catch (\Exception $e) {

            $raw = $response->getContent(false);
            throw new \Exception($raw);
        }
    }

    /**
     * H5支付下单
     * @param Order $order
     * @param array $config
     * @param bool $isServiceProvider 是否为服务商模式
     * @return array
     * @throws \Exception
     */
    public function h5Pay($order, $config)
    {
        $payment = new PayApplication($config);


        try {
            $requestData = [
                'sp_appid' => $config['app_id'],
                'sp_mchid' => $config['mch_id'],
                'sub_mchid' => $config['sub_mchid'],
                'description'  => '订单支付',
                'out_trade_no' => $order->order_no,
                'notify_url'   => $config['notify_url'],
                'amount'       => [
                    'total'    => intval($order->price * 100),
                    'currency' => 'CNY',
                ],
                'scene_info' => [
                    'payer_client_ip' => request()->ip(),
                    'h5_info' => [
                        'type' => 'Wap'
                    ]
                ],
            ];



            $response = $payment->getClient()->post(
                'v3/pay/partner/transactions/h5',

                [
                    'json' => $requestData,
                ]
            );


            $result = $response->toArray();


            if (isset($result['h5_url'])) {
                return [
                    'h5_url' => $result['h5_url'],  // H5支付链接
                    'order_no' => $order->order_no   // 订单号
                ];
            } else {
                throw new \Exception($result['message'] ?? '微信支付H5下单失败');
            }
        } catch (\Exception $e) {
            $raw = $response->getContent(false);
            throw new \Exception($raw);
        }
    }

    // /**
    //  * 支付回调
    //  * @return mixed
    //  */
    // public function notify()
    // {
    //     $config = $this->config;
    //     $payment = new PayApplication($config);
    //     $server = $payment->getServer();
    //     $server->handlePaid(function ($message, $fail) {
    //         $order = Order::where('order_no', $message['out_trade_no'])->first();
    //         if (!$order) {
    //             return $fail('订单不存在');
    //         }
    //         if ($message['trade_state'] === 'SUCCESS') {
    //             $order->is_status = 2;
    //             $order->paid_at = now();
    //             $order->save();
    //         }
    //         return true;
    //     });
    //     return $server->serve();
    // }
}
