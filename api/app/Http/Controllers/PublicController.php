<?php

namespace App\Http\Controllers;

use App\Http\Services\PublicService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\UploadImg;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\App;
use Illuminate\Http\JsonResponse;


class PublicController extends Controller
{
    protected PublicService $PublicService;

    public function __construct(PublicService $PublicService)
    {
        $this->PublicService = $PublicService;
    }

    /**
     * 获取所有魔方
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function banners(Request $request)
    {
        try {
            $banners = $this->PublicService->getBanners($request);
            return response()->json([
                'code' => 200,
                'data' => $banners,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    //获取分享设置
    public function share(Request $request)
    {
        try {
            $shareSetting = $this->PublicService->getShareSetting($request);
            return response()->json([
                'code' => 200,
                'data' => $shareSetting,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    //获取商户名称配置
    public function mch(Request $request)
    {
        try {
            return response()->json([
                'code' => 200,
                'data' => [
                    'power' => $request->mch->power,
                    'contact_qrcode' => $request->mch->contact_qrcode,
                    'is_spread' => $request->mch->is_spread,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    //获取所有应用分类
    public function categories(Request $request)
    {
        try {
            $categories = $this->PublicService->getCategories($request);
            return response()->json([
                'code' => 200,
                'data' => $categories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function upload(Request $request)
    {


        $path = $request->file('file')->store('app/uploads', 's3');
        $url = Storage::disk('s3')->url($path);


        UploadImg::create([
            'url' => $url,
            'path' => $path,
            'mch_id' => $request->mch->id,
        ]);

        return response()->json([
            'code' => 200,
            'message' => '图片上传成功',
            'data' => $url,
        ]);
    }


    public function iconChange(Request $request): JsonResponse
    {
        $apps = App::where('icon_change', 0)
            ->where('app_type', 1)
            ->orderBy('id', 'desc')
            ->get();



        foreach ($apps as $app) {
            try {
                // 使用 Laravel HTTP Client 下载图片
                $response = Http::timeout(10)
                    ->withoutVerifying() // 忽略SSL验证
                    ->get($app->image);



                // 检查是否成功下载且为图片
                if ($response->successful() && str_starts_with($response->header('Content-Type'), 'image/')) {
                    $iconPath = 'app/icon/' . $app->bot_id . '.png';


                    // 尝试上传到OSS
                    if (Storage::disk('s3')->put($iconPath, $response->body())) {

                        $icon_url = Storage::disk('s3')->url($iconPath);
                        $app->image = $icon_url;
                        $app->icon_change = 1;
                        $app->save();
                    }
                } else {
                    Log::warning('Failed to download image', [
                        'url' => $app->image,
                        'status' => $response->status(),
                        'content_type' => $response->header('Content-Type')
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Image processing failed', [
                    'url' => $app->image,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return response()->json([
            'code' => 200,
            'message' => 'success',
        ]);
    }
}
