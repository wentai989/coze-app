<script setup>
import { ref } from "vue";
defineProps({
  bgColor: {
    type: String,
    default: "0f1216",
  },
});
const statusBarHeight = ref("");
const navHeight = ref("");

const isWx = ref(false);
const contentStyle = ref({});

// #ifdef H5
statusBarHeight.value = 10;
navHeight.value = 55;
isWx.value = false;
// #endif

// #ifdef MP
// 获取手机系统的信息 里面有状态栏高度和设备型号
const { statusBarHeight: sysStatusBarHeight, system } = uni.getSystemInfoSync();
const menuButton = uni.getMenuButtonBoundingClientRect();
// 注意返回的单位是px 不是rpx
statusBarHeight.value = sysStatusBarHeight;
// 胶囊按钮的宽度
// const menuButtonWidth = menuButton.width
// 而导航栏的高度则 = 状态栏的高度 + 判断机型的值（是ios就+40，否则+44）
// 这个高度刚好和胶囊对齐
navHeight.value = sysStatusBarHeight + (system.includes("iOS") ? 40 : 44);
contentStyle.value = { width: `${menuButton.left}px` };

// #endif
</script>

<template>
  <div
    :style="{
      height: `${navHeight}px`,
      paddingTop: `${statusBarHeight}px`,
      backgroundColor: `#${bgColor}`,
    }"
    class="fixed left-0 right-0 top-0 z-40 flex flex-1 items-center">
    <div :style="contentStyle">
      <slot name="default" />
    </div>
  </div>
  <div :style="{ height: `${navHeight}px` }" />
</template>
