<script setup>
import { onLoad } from "@dcloudio/uni-app";
import { ref } from "vue";
import { http } from "@/api/http";
import EmptyState from "@/components/EmptyState.vue";
import LoadingSpinner from "@/components/LoadingSpinner.vue";
import { showToast } from "@/composables/common";

const cardCode = ref("");
const activationHistory = ref([]);
const loading = ref(false);
const pageLoading = ref(true);

async function handleActivate() {
  if (!cardCode.value) {
    showToast("请输入卡密");
    return;
  }
  loading.value = true;
  try {
    await http.get(`/api/code/${cardCode.value}/activate`, {}, true);

    cardCode.value = ""; // 清空输入框

    showToast("兑换成功");
    fetchHistory();
  } finally {
    loading.value = false;
  }
}

async function fetchHistory() {
  await http
    .get("/api/code/history")
    .then((res) => {
      activationHistory.value = res;
      pageLoading.value = false;
    })
    .catch(() => {
      pageLoading.value = false;
    });
}

onLoad(() => {
  fetchHistory();
});
</script>

<template>
  <div class="b min-h-screen p-4 text-white">
    <div class="mx-auto max-w-md">
      <!-- Activation Card -->
      <div
        class="rounded-xl bg-gray-800/50 p-6 shadow-2xl ring-1 ring-white/10 backdrop-blur-md">
        <h2 class="text-center text-lg font-bold text-white">卡密兑换</h2>
        <p class="mt-2 text-center text-sm text-white/60">
          输入您的卡密来兑换会员时长
        </p>
        <div class="mt-6">
          <div class="flex items-center justify-between">
            <input
              v-model="cardCode"
              type="text"
              placeholder="请输入卡密"
              class="w-full rounded-lg bg-black/30 px-2 py-3 ring-white/10 transition-all focus:ring-cyan-500" />
            <button
              class="ml-2 flex h-10 shrink-0 items-center justify-center rounded-lg bg-gradient-to-r from-[#165DFF] via-[#3BA1FF] to-[#3CD5A7] py-4 text-sm font-semibold text-white shadow-lg shadow-cyan-500/10"
              :disabled="loading"
              @click="handleActivate">
              <div v-if="loading" class="flex items-center">
                <span>兑换中...</span>
              </div>
              <span v-else>兑换</span>
            </button>
          </div>
        </div>
      </div>

      <!-- History Section -->
      <div class="mt-8">
        <h3 class="text-base font-semibold text-white/80">
          兑换记录
          <span class="mr-2 text-xs text-gray-400">只保留最近10条记录</span>
        </h3>
        <div v-if="pageLoading" class="mt-4 flex justify-center p-10">
          <LoadingSpinner />
        </div>
        <div v-else-if="activationHistory.length > 0" class="mt-4 space-y-3">
          <div
            v-for="item in activationHistory"
            :key="item.id"
            class="transform-gpu rounded-lg bg-gray-800/50 p-4 ring-1 ring-white/10 transition-transform">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="flex flex-col">
                  <span class="text-sm font-bold text-white/90">{{
                    item.code
                  }}</span>
                  <span class="mt-1 text-xs text-white/50">{{
                    item.invoke_at
                  }}</span>
                </div>
              </div>
              <span
                v-if="item.code_type === 1"
                class="rounded-full bg-green-500/10 px-3 py-1 text-sm font-semibold text-green-400"
                >+{{ item.value }} 算力</span
              >
              <span
                v-else
                class="rounded-full bg-blue-500/10 px-3 py-1 text-sm font-semibold text-blue-400"
                >+{{ item.value }} 天</span
              >
            </div>
          </div>
        </div>

        <EmptyState v-else class="mt-4 pt-16" text="暂无兑换记录" />
      </div>
    </div>
  </div>
</template>
