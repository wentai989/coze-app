# 项目介绍

基于 Coze (扣子) 平台能力，提供开箱即用的 AI 智能体与工作流 商业化变现解决方案

## 🚀 功能特性

- **小程序端**: AI 流式对话、异步工作流、Markdown 渲染、应用广场、用户/会员/卡密/分销/支付系统。
- **管理后台**: 用户/订单管理、AI 参数配置、系统设置、微信集成、Sanctum 认证。

## 📂 目录

- `api/`: Laravel 后端项目
- `app/`: UniApp 前端项目

## 🛠️ 快速部署

**环境要求**: PHP >= 8.2, Node.js >= 18.0, MySQL >= 5.7, Composer

### 1. 后端 (API)

```bash
cd api
cp .env.example .env            # 配置数据库连接
composer install                # 安装依赖
php artisan migrate             # 迁移数据库结构
php artisan db:seed             # 填充初始数据
php artisan serve               # 启动服务 (默认 http://127.0.0.1:8000)
```

> 管理后台: `/admin`

### 2. 前端 (小程序)

```bash
cd app
pnpm install                    # 安装依赖
# 请在 src/env.d.ts 或 .env 中配置 API 地址
npm run dev:mp-weixin           # 编译开发
```

**导入微信开发者工具**: 选择目录 `app/dist/dev/mp-weixin`，配置 AppID。

**构建生产**: `npm run build:mp-weixin`

## 🔄 维护指南

- **更新数据库结构**: `cd api && php artisan migrate`

## 📝 技术栈

Laravel 12, MySQL, Owl Admin, EasyWeChat, UniApp, Vue 3, TypeScript, Tailwind CSS

## 二维码演示

![小程序二维码](https://github.com/wentai989/coze-app/blob/main/gh_64f0e18948c7_258.jpg)