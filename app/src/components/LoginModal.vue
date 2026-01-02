<script setup>
import { ref } from "vue";
import { http } from "@/api/http";
import SignRewardModal from "@/components/SignRewardModal.vue";
import { navigateTo, showToast } from "@/composables/common";

// 定义 props 和 emits, 以支持 v-model 和事件通信
defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
});
const emit = defineEmits(["update:modelValue", "close", "success"]);

// --- 从 login.vue 迁移并适配的逻辑 ---
const isAgree = ref(false);
const showSignModal = ref(false);
const description = ref("");

// 检查是否同意协议
function handleButtonClick() {
  if (!isAgree.value) {
    showToast("请先勾选用户协议和隐私政策");
  }
}

// 处理微信登录
async function handleWechatLogin(e) {
  if (e.detail.errMsg !== "getPhoneNumber:ok") {
    showToast("授权失败");
    return;
  }
  try {
    const loginRes = await uni.login({ provider: "weixin" });
    const res = await http.post("/api/wechat-login", {
      code: e.detail.code,
      encryptedData: e.detail.encryptedData,
      iv: e.detail.iv,
      js_code: loginRes.code,
      ask_id: uni.getStorageSync("ask_id") || "",
    });
    if (res.token) {
      uni.setStorageSync("isLoginExpired", "false");
      uni.setStorageSync("user_id", res.user_id);
      uni.setStorageSync("token", res.token);
      showToast("登录成功");
      emit("success", res); // 登录成功，发出 success 事件
      closeModal(); // 关闭弹窗
      if (res.add_value) {
        description.value = `送您${res.add_value}算力`;
        showSignModal.value = true;
      }
    } else {
      throw new Error("登录失败，请重试");
    }
  } catch (error) {
    console.error("Login error:", error);
    showToast(error.message || "登录时发生错误");
  }
}
// --- 逻辑迁移结束 ---

// 关闭弹窗
function closeModal() {
  emit("update:modelValue", false);
  emit("close");
}
</script>

<template>
  <div
    v-if="modelValue"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur"
    @click="closeModal">
    <div
      class="relative w-[85vw] max-w-sm rounded-2xl bg-white p-6 text-center shadow-xl dark:bg-gray-800"
      @click.stop>
      <!-- 关闭按钮 -->
      <div class="absolute right-4 top-4" @click="closeModal">
        <img src="@/static/icons/close-line.svg" class="h-6 w-6 opacity-40" />
      </div>

      <h2 class="mb-4 text-xl font-bold text-gray-800 dark:text-white">
        欢迎登录
      </h2>
      <p class="mb-8 text-sm text-gray-500 dark:text-gray-400">
        登录后体验更多精彩内容
      </p>

      <!-- 登录按钮 -->
      <button
        class="mb-4 flex w-full items-center justify-center gap-2 rounded-full bg-gradient-to-r from-green-500 to-teal-500 py-3 text-base font-medium text-white shadow-lg"
        :open-type="isAgree ? 'getPhoneNumber' : ''"
        @click="handleButtonClick"
        @getphonenumber="handleWechatLogin">
        <img src="@/static/icons/wechat-fill.svg" class="h-6 w-6" />
        <span>手机号一键登录</span>
      </button>

      <!-- 协议勾选 -->
      <div
        class="mt-6 flex items-center justify-center gap-1 text-xs text-gray-400">
        <div class="flex h-4 w-4 items-center" @click="isAgree = !isAgree">
          <div
            class="flex h-4 w-4 items-center justify-center rounded-full border-2"
            :class="{
              'border-teal-500 bg-teal-500': isAgree,
              'border-gray-300': !isAgree,
            }">
            <!-- <img
              v-if="isAgree" src="@/static/icons/check-line.svg" class="h-3 w-3"
            > -->
          </div>
        </div>
        <div class="leading-tight">
          <text>我已阅读并同意</text>
          <text
            class="text-teal-600"
            @click="navigateTo('/pages/user/agreement')">
            《用户协议》
          </text>
          <text>和</text>
          <text
            class="text-teal-600"
            @click="navigateTo('/pages/user/privacy')">
            《隐私政策》
          </text>
        </div>
      </div>
    </div>
  </div>

  <SignRewardModal
    v-model="showSignModal"
    title="恭喜您"
    tag="新人注册"
    :description="description"
    buttonText="我知道了"
    @claim="showSignModal = false"
    @close="showSignModal = false" />
</template>
