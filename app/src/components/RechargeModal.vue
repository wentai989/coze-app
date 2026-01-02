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
  http.get("/api/compute-powers").then((res) => {
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
    .post(`/api/compute-power/${packages.value[selectedIndex.value].id}/pay`, {
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
    <!-- 深色遮罩 -->
    <div
      class="absolute inset-0 bg-black/70 backdrop-blur-sm"
      @click="onBackdrop" />
    <!-- 主体卡片：深色玻璃态，内置固定内容 -->

    <LoadingSpinner v-if="loading" :overlay="true" />
    <div
      class="relative z-10 w-[80%] rounded-2xl bg-gradient-to-b from-[#1a1f24]/95 to-[#0b0f12]/95 p-5 text-white shadow-xl ring-1 ring-white/10 sm:max-w-sm sm:p-6 md:max-w-md"
      role="dialog"
      aria-modal="true">
      <!-- 标题栏 -->
      <div class="flex items-center justify-between">
        <p class="text-base font-semibold sm:text-lg">算力充值</p>
        <!-- <span class="text-xs text-emerald-300">选择套餐 ></span> -->
      </div>
      <!-- 金额展示卡 -->
      <div
        class="ring-white/12 mt-4 flex flex-col items-center gap-1 rounded-2xl bg-black/30 p-4 ring-1">
        <span class="text-sm text-white/70">¥</span>
        <span class="text-4xl font-bold tracking-wide sm:text-5xl">
          {{ packages[selectedIndex]?.amount }}
        </span>
        <div
          class="mt-3 w-full rounded-xl bg-emerald-500/15 px-3 py-2 text-center text-sm text-emerald-200 ring-1 ring-emerald-400/25">
          获得算力：{{ packages[selectedIndex]?.power_value }}
        </div>
      </div>
      <!-- 套餐选择（胶囊形状 + 折扣角标） -->
      <div class="mt-6 flex items-center justify-between gap-3">
        <div
          v-for="(pkg, i) in packages"
          :key="i"
          class="relative flex-1 rounded-full bg-gray-800 px-4 py-2 text-sm text-white ring-1 ring-white/15 transition-all duration-150"
          :class="
            i === selectedIndex
              ? `
            bg-emerald-400/25 ring-2 ring-emerald-300/70
          `
              : ''
          "
          @click="selectPackage(i)">
          <!-- <span
            class="
              absolute -top-3 right-1 rounded-full bg-rose-500/90 px-1.5
              text-[10px] shadow ring-1 ring-rose-300/40
            "
          >
            {{ pkg.discount }}
          </span> -->
          <span class="block text-center">{{ pkg.amount }}元</span>
        </div>
      </div>

      <!-- 底部支付按钮：蓝绿渐变 -->
      <div
        type="button"
        class="mt-5 inline-flex h-11 w-full items-center justify-center rounded-full bg-gradient-to-r from-[#165DFF] via-[#3BA1FF] to-[#3CD5A7] text-sm font-semibold text-white shadow-md transition-all duration-150 hover:shadow-lg hover:brightness-105 focus:outline-none focus:ring-2 focus:ring-sky-300/60 active:scale-[0.98]"
        @click="onPay">
        支付 {{ packages[selectedIndex]?.amount }} 元
      </div>
    </div>
  </div>
</template>
