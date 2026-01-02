<script setup lang="ts">
import { navigateTo } from "@/composables/common";
import { ref } from "vue";

const pages = getCurrentPages();
const currentPage = pages[pages.length - 1];
const currentPath = ref(currentPage.route);
console.log(currentPath.value);
</script>

<template>
  <div class="h-16" />
  <nav
    class="fixed bottom-4 left-1/2 z-30 w-[60%] max-w-[var(--safe-max)] -translate-x-1/2">
    <div
      class="relative flex items-center justify-between rounded-full bg-white/25 px-4 py-3 text-gray-900 shadow-xl ring-1 ring-white/40 backdrop-blur-md">
      <!-- 左：首页 -->
      <div
        class="flex items-center gap-1 text-white"
        @click="navigateTo('/pages/app/index', {}, 'switchTab')">
        <img
          v-if="currentPath === 'pages/app/index'"
          src="@/static/icons/home-2-fill.svg"
          class="h-5 w-5" />
        <img v-else src="@/static/icons/home-2-line.svg" class="h-5 w-5" />

        <span class="text-sm font-medium">首页</span>
      </div>

      <!-- 中：我的创作 -->
      <div
        class="absolute -top-4 left-1/2 flex h-14 w-14 -translate-x-1/2 flex-col items-center justify-center gap-0.5 rounded-full bg-gradient-to-br from-blue-600 to-teal-500 text-white shadow-lg"
        @click="navigateTo('/pages/log', {}, 'switchTab')">
        <img src="@/static/icons/duihua.svg" class="h-8 w-8" />
        <span class="text-[8px]">最近</span>
      </div>

      <!-- 右：我的 -->
      <div
        class="flex items-center gap-1 text-white"
        @click="navigateTo('/pages/user/index', {}, 'switchTab')">
        <img
          v-if="currentPath === 'pages/user/index'"
          src="@/static/icons/user-3-fill.svg"
          class="h-5 w-5" />
        <img v-else src="@/static/icons/user-3-line.svg" class="h-5 w-5" />
        <span class="text-sm font-medium">我的</span>
      </div>
    </div>
  </nav>
</template>
