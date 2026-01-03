<?php

namespace App\ApiTemplates;

use Slowlyo\OwlAdmin\Support\Apis\AdminBaseApi;

class OptionsApi extends AdminBaseApi
{
    // 请求方式
    public string $method = 'get';
    
    // 模板的名称
    public string $title = '获取选项列表';

    // api 的处理逻辑
    public function handle()
    {
        $query = $this->service()->query();
        
        // 处理GET参数查询条件
        foreach (request()->query() as $field => $value) {
            // 跳过空值
            if ($value === null || $value === '') {
                return [];
            }
            // 跳过分页参数
            if (in_array($field, ['page', 'perPage'])) {
                continue;
            }
            $query->where($field, $value);
        }
        
        // 获取结果
        return $query->get([
            $this->getArgs('value_field') . ' as value',
            $this->getArgs('label_field') . ' as label',
        ]);
    }

    // api 的参数配置, 返回数组格式的 amis 结构
    // 动态 api 的表单中, 将会把 argsSchema 返回的表单放到 Combo 组件中
    public function argsSchema()
    {
        return [
            // 让用户选择从哪个模型获取数据
            amis()->SelectControl('model', __('admin.relationships.model'))
                ->required()
                ->menuTpl('${label} <span class="text-gray-300 pl-2">${table}</span>')
                ->source('/dev_tools/relation/model_options')
                ->searchable(),
            amis()->TextControl('value_field', 'Value字段')->required(),
            amis()->TextControl('label_field', 'Label字段')->required(),
        ];
    }

    protected function service()
    {
        // blankService 方法由父类提供, 返回了一个空白的 AdminService 实例
        $service = $this->blankService();

        // 读取参数, 给 service 设置模型
        $service->setModelName($this->getArgs('model'));

        return $service;
    }
}
