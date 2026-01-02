<script setup lang="ts">
import { computed, ref } from "vue";
import { onShow } from "@dcloudio/uni-app";
import BottomNav from "@/components/BottomNav.vue";
import { http } from "@/api/http";
import { navigateTo } from "@/composables/common";
import LoadingSpinner from "@/components/LoadingSpinner.vue";
import EmptyState from "@/components/EmptyState.vue";

const tabs = ["全部", "工作流", "智能体"];
const activeTab = ref("全部");
const logs = ref<any>([]);
const loading = ref(false);

const getStatusText = (status: number) => {
  switch (status) {
    case 2:
      return "调用失败";
    default:
      return "调用成功";
  }
};

const fetchLogs = async () => {
  if (logs.value.length === 0) {
    loading.value = true;
  }
  try {
    const res: any = await http.get("/api/app-logs");
    logs.value = res;
  } catch (error) {
    console.error("获取日志失败:", error);
  } finally {
    loading.value = false;
  }
};

const handleLogClick = (log: any) => {
  if (log.app?.app_type === 1) {
    navigateTo(`/pages/app/chat?id=${log.app?.id}`);
    return;
  }
  navigateTo(`/pages/app/result?id=${log.id}`);
};

onShow(() => {
  fetchLogs();
});

const filteredLogs = computed(() => {
  const sortedLogs = [...logs.value].sort(
    (a, b) =>
      new Date(b.created_at).getTime() - new Date(a.created_at).getTime()
  );

  if (activeTab.value === "全部") {
    return sortedLogs;
  }

  return sortedLogs.filter((log) => {
    const type = log.app?.app_type === 1 ? "智能体" : "工作流";
    return type === activeTab.value;
  });
});
</script>

<template>
  <div class="min-h-screen px-4 pb-6 text-white">
    <!-- Loading Spinner -->
    <LoadingSpinner v-if="loading" overlay="true" />

    <!-- Segmented Control Tabs -->
    <div class="mb-4 mt-2">
      <div class="inline-flex w-full rounded-lg bg-gray-800/60 p-1">
        <div
          v-for="tab in tabs"
          :key="tab"
          class="w-full rounded-md py-1.5 text-center text-sm font-semibold transition-all"
          :class="
            activeTab === tab
              ? 'bg-gray-700/70 text-white shadow-sm'
              : `
            text-gray-400
            hover:bg-gray-700/30
          `
          "
          @click="activeTab = tab">
          {{ tab }}
        </div>
      </div>
    </div>

    <EmptyState v-if="filteredLogs?.length === 0 && !loading" class="mt-3" />

    <!-- Log List -->
    <ul v-else class="space-y-3">
      <li
        v-for="log in filteredLogs"
        :key="log.id"
        class="rounded-2xl bg-black/20 p-4 ring-1 ring-white/10 active:scale-[0.98] transition-transform"
        @click="handleLogClick(log)">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <img
              :src="log.app?.image || '/static/logo.png'"
              class="h-10 w-10 flex-shrink-0 rounded-lg object-cover" />
            <div>
              <p class="font-semibold text-gray-100">
                {{ log.app?.name || "未知应用" }}
              </p>
              <p class="mt-1 text-xs text-gray-400">
                {{ log.created_at }}
              </p>
            </div>
          </div>
          <div
            class="rounded-full px-3 py-1 text-xs font-medium"
            :class="{
              'bg-green-500/10 text-green-400 ring-1 ring-green-500/20':
                log.is_status === 1 || log.is_status === 0,
              'bg-red-500/10 text-red-400 ring-1 ring-red-500/20':
                log.is_status === 2,
            }">
            {{ getStatusText(log.is_status) }}
          </div>
        </div>
        <div
          class="mt-3 flex items-center justify-between border-t border-white/5 pt-3 text-xs text-gray-500">
          <span>类型: {{ log.app?.app_type === 1 ? "智能体" : "工作流" }}</span>
          <span>ID: {{ log.id }}</span>
        </div>
      </li>
    </ul>
  </div>
  <BottomNav />
</template>
