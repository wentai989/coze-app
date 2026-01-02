<script setup>
import { onMounted, ref } from "vue";
import { http } from "@/api/http";
import EmptyState from "@/components/EmptyState.vue";
import LoadingSpinner from "@/components/LoadingSpinner.vue";

const powerLogs = ref(null);
const loading = ref(true);

onMounted(() => {
  loading.value = true;
  // Assuming the endpoint for power consumption logs is /api/power/logs
  http
    .get("/api/power-deduction-logs")
    .then((res) => {
      powerLogs.value = res;
    })
    .finally(() => {
      loading.value = false;
    });
});
</script>

<template>
  <!-- Content area uses system background, no extra background layer -->
  <div class="mx-auto max-w-[var(--safe-max)] px-4 pb-24 text-white">
    <div class="mt-4 space-y-3">
      <LoadingSpinner v-if="loading" overlay="true" />
      <div
        v-for="log in powerLogs"
        v-else
        :key="log.id"
        class="flex items-center justify-between rounded-xl bg-white/10 p-3 ring-1 ring-white/15">
        <div class="min-w-0">
          <p class="truncate text-sm font-medium">
            {{ log.name }}
          </p>
          <p class="mt-0.5 text-xs text-white/70">
            {{ log.created_at.split("T")[0] }}
          </p>
        </div>
        <div class="text-right">
          <p class="text-sm">
            消耗算力：
            <span class="font-semibold text-red-400"
              >-{{ log.power_value }}</span
            >
          </p>
          <!-- <p class="mt-0.5 text-xs text-white/70">
            {{ log.chat_id }}
          </p> -->
        </div>
      </div>
      <EmptyState
        v-if="powerLogs?.length === 0 && !loading"
        class="mt-3"
        text="暂无算力消耗记录" />
    </div>
  </div>
</template>
