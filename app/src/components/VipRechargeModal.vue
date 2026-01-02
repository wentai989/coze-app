<script setup>
import { onMounted, ref, watch } from "vue";
import LoadingSpinner from "./LoadingSpinner.vue";
import { http } from "@/api/http";

const props = defineProps({
  modelValue: { type: Boolean, required: true },
  closeOnBackdrop: { type: Boolean, default: true },
});
const emit = defineEmits(["update:modelValue", "pay"]);
const selectedIndex = ref(0);
const packages = ref([]);
const loading = ref(true);
onMounted(() => {
  http.get("/api/vips").then((res) => {
    packages.value = res;
    loading.value = false;
  });
});

function close() {
  emit("update:modelValue", false);
}
function onBackdrop() {
  if (props.closeOnBackdrop) {
    close();
  }
}
function selectPackage(i) {
  selectedIndex.value = i;
}

function onPay() {
  http
    .post(`/api/vip/${packages.value[selectedIndex.value].id}/pay`, {
      id: packages.value[selectedIndex.value].id,
    })
    .then((res) => {
      uni.requestPayment({
        timeStamp: res.timeStamp,
        nonceStr: res.nonceStr,
        package: res.package,
        signType: res.signType,
        paySign: res.paySign,
        success: () => {
          close();
          uni.showToast({
            title: "支付成功",
            icon: "none",
            duration: 3000,
          });
          // 模拟通知后端支付成功
          http.post(`/api/order/${res.order_no}/notify`).then(() => {
            emit("pay");
          });
        },
        fail: (err) => {
          uni.showToast({
            icon: "none",
            title: err,
          });
        },
      });
    });
}

watch(
  () => props.modelValue,
  (v) => {
    if (v) {
      selectedIndex.value = 0;
    }
  }
);
</script>

<template>
  <div
    v-if="modelValue"
    class="fixed inset-0 z-50 flex items-center justify-center">
    <LoadingSpinner v-if="loading" :overlay="true" />

    <!-- 深色遮罩 -->
    <div
      class="absolute inset-0 bg-black/70 backdrop-blur-sm"
      @click="onBackdrop" />
    <!-- 主体卡片 -->
    <div
      class="relative z-10 w-[80%] rounded-2xl bg-gradient-to-b from-[#1a1f24]/95 to-[#0b0f12]/95 p-5 text-white shadow-xl ring-1 ring-white/10 sm:max-w-sm sm:p-6 md:max-w-md"
      role="dialog"
      aria-modal="true">
      <!-- 标题栏 -->
      <div class="flex items-center justify-between">
        <p class="text-base font-semibold sm:text-lg">会员套餐</p>
      </div>

      <!-- VIP名称、介绍、金额 -->
      <div
        class="ring-white/12 mt-4 flex flex-col items-center gap-2 rounded-2xl bg-black/30 p-4 ring-1">
        <h3 class="text-xl font-bold text-amber-300">
          {{ packages[selectedIndex]?.name }}
        </h3>
        <p class="text-xs text-white/60">
          {{ packages[selectedIndex]?.description }}
        </p>
        <div class="mt-2 flex items-baseline justify-center gap-1">
          <span class="text-lg text-white/70">¥</span>
          <span class="text-4xl font-bold tracking-wide sm:text-5xl">
            {{ packages[selectedIndex]?.amount }}
          </span>
        </div>
        <div
          class="mt-3 w-full rounded-xl bg-emerald-500/15 px-3 py-2 text-center text-sm text-emerald-200 ring-1 ring-emerald-400/25">
          赠送算力：{{ packages[selectedIndex]?.power_value }}
        </div>
      </div>

      <!-- 套餐选择 -->
      <div class="mt-6 grid grid-cols-3 gap-3">
        <div
          v-for="(pkg, i) in packages"
          :key="i"
          class="relative cursor-pointer rounded-lg bg-gray-800 px-2 py-3 text-center text-sm text-white ring-1 ring-white/15 transition-all duration-150"
          :class="
            i === selectedIndex
              ? `
            bg-emerald-400/25 ring-2 ring-emerald-300/70
          `
              : ''
          "
          @click="selectPackage(i)">
          <span class="block font-semibold">{{ pkg.name }}</span>
          <span class="mt-1 block text-xs text-white/70"
            >{{ pkg.amount }}元</span
          >
        </div>
      </div>

      <!-- 底部支付按钮 -->
      <button
        type="button"
        class="mt-6 inline-flex h-12 w-full items-center justify-center rounded-full bg-gradient-to-r from-[#165DFF] via-[#3BA1FF] to-[#3CD5A7] text-base font-semibold text-white shadow-md transition-all duration-150 hover:shadow-lg hover:brightness-105 focus:outline-none focus:ring-2 focus:ring-sky-300/60 active:scale-[0.98]"
        @click="onPay">
        立即支付 {{ packages[selectedIndex]?.amount }} 元
      </button>
    </div>
  </div>
</template>
