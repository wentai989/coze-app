<script setup>
import { onMounted, ref, watch } from "vue";
import { http } from "@/api/http";
import { navigateTo, showToast } from "@/composables/common";

// 手机号登录相关变量
const phone = ref("");
const code = ref("");
const countdown = ref(0);
const isWechat = ref(false);
isWechat.value = true;

// #ifdef MP-WEIXIN
// #endif

// 用户协议勾选状态
const isAgree = ref(false);

// 登录相关逻辑
async function handleWechatLogin(e) {
  if (e.detail.errMsg !== "getPhoneNumber:ok") {
    return;
  }
  const loginRes = await uni.login({ provider: "weixin" });
  const res = await http.post("/api/wechat-login", {
    code: e.detail.code,
    encryptedData: e.detail.encryptedData,
    iv: e.detail.iv,
    js_code: loginRes.code,
    ask_id: uni.getStorageSync("ask_id") || "",
  });
  if (res.token) {
    uni.setStorageSync("user_id", res.user_id);
    uni.setStorageSync("token", res.token);
    if (res.add_value) {
      uni.setStorageSync("add_value", res.add_value);
      console.log("add_value", res.add_value);
    }
    navigateTo("/pages/app/index", {}, "switchTab");
  }
}
watch(isAgree, (newVal) => {
  if (newVal === true) {
    // if (getUserToken()) {
    //   navigateTo('/pages/index')
    // }
  }
});
onMounted(async () => {
  uni.removeStorageSync("token");
});

function handleButtonClick() {
  if (!isAgree.value) {
    showToast("请先勾选用户协议");
  }
}
</script>

<template>
  <div class="relative p-4">
    <!-- Logo区域 -->
    <div class="mb-[3rem] mt-[7rem] text-center">
      <div
        class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-2xl bg-white shadow-lg">
        <img src="@/static/logo.png" class="h-16 w-16" alt="Logo" />
      </div>
      <h2 class="mb-2 text-2xl font-bold text-white">欢迎登录</h2>
      <p class="text-sm text-white/80">登录后体验更多精彩内容</p>
    </div>

    <!-- 登录卡片 -->
    <div class="w-full max-w-sm">
      <!-- 主要登录按钮 -->
      <button
        v-if="isWechat"
        class="mb-4 mt-[5rem] flex w-[80%] items-center justify-center gap-2 rounded-full bg-[#007AFF] py-3 text-sm font-medium text-white shadow-sm"
        :open-type="isAgree ? 'getPhoneNumber' : ''"
        @click="handleButtonClick"
        @getphonenumber="handleWechatLogin">
        <i class="fab fa-weixin text-lg" />
        <span>手机号快捷登录</span>
      </button>

      <!-- 手机号验证码登录 -->
      <div v-if="!isWechat" class="flex flex-col gap-4">
        <div class="relative">
          <input
            v-model="phone"
            type="text"
            placeholder="请输入手机号"
            maxlength="11"
            class="rounded-xl bg-white px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:outline-none" />
        </div>
        <div class="relative flex gap-3">
          <input
            v-model="code"
            type="text"
            placeholder="请输入验证码"
            maxlength="6"
            class="flex-1 rounded-xl bg-white px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:outline-none" />
          <div
            :disabled="countdown > 0"
            class="whitespace-nowrap rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-600"
            @click="sendCode">
            {{ countdown > 0 ? `${countdown}秒后重试` : "发送验证码" }}
          </div>
        </div>
        <div
          class="rounded-xl bg-[#007AFF] px-4 py-3 text-center text-sm font-medium text-white shadow-sm"
          @click="handlePhoneLogin">
          手机号登录
        </div>
      </div>
    </div>
    <!-- 隐私政策和用户协议 -->
    <div class="fixed bottom-[20px] left-0 right-0 px-4">
      <div class="flex items-center justify-center gap-2 text-xs text-gray-500">
        <div class="flex items-center" @click="isAgree = !isAgree">
          <div
            class="flex h-4 w-4 items-center justify-center rounded border border-gray-300"
            :class="{ 'border-[#007AFF] bg-[#007AFF]': isAgree }">
            <view v-if="!isAgree" class="h-2.5 w-2.5 rounded-sm bg-gray-500" />
          </div>
        </div>
        <div>
          <text>我已阅读并同意</text>
          <text
            class="text-[#007AFF]"
            @click="navigateTo('/pages/user/agreement')">
            《用户协议》
          </text>
          <text>和</text>
          <text
            class="text-[#007AFF]"
            @click="navigateTo('/pages/user/privacy')">
            《隐私政策》
          </text>
        </div>
      </div>
    </div>
  </div>
</template>
