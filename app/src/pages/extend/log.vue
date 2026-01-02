<script setup>
import { onLoad } from "@dcloudio/uni-app";
import { ref } from "vue";
import { http } from "@/api/http";
import EmptyState from "@/components/EmptyState.vue";
import LoadingSpinner from "@/components/LoadingSpinner.vue";

const logs = ref([]);
const loading = ref(true);

onLoad(() => {
  http
    .get("/api/spread/logs")
    .then((res) => {
      logs.value = res;
    })
    .catch(() => {})
    .finally(() => {
      loading.value = false;
    });
});

// For withdrawals (type === 2)
function resolveStatus(status) {
  switch (status) {
    case 0:
      return { text: "审核中", class: "text-gray-400" };
    case 1:
      return { text: "已到账", class: "text-emerald-400" };
    case 2:
      return { text: "已驳回", class: "text-red-400" };
    default:
      return { text: "未知", class: "text-gray-400" };
  }
}
</script>

<template>
  <div class="mx-auto max-w-[var(--safe-max)] px-4 pb-24 text-white">
    <LoadingSpinner v-if="loading" overlay="true" />
    <div v-else class="mt-4 space-y-4">
      <div
        v-for="log in logs"
        :key="log.id"
        class="flex items-center rounded-xl bg-white/5 p-4 ring-1 ring-white/10 transition-all hover:bg-white/10 hover:ring-cyan-500/50">
        <div class="flex-grow">
          <p class="text-sm font-semibold text-white">
            {{ log.remark }}
          </p>
          <p class="text-xs text-white/50">
            {{ log.created_at }}
          </p>
        </div>

        <div class="text-right">
          <p
            class="text-sm font-bold"
            :class="
              log.spread_type === 1
                ? `
              text-emerald-400
            `
                : `text-red-400`
            ">
            {{ log.spread_type === 1 ? "+" : "-" }}{{ log.amount }}
          </p>
          <p
            v-if="log.spread_type === 2"
            class="text-xs"
            :class="resolveStatus(log.is_status).class">
            {{ resolveStatus(log.is_status).text }}
          </p>
          <p v-else class="text-xs text-green-400">已到账</p>
          <p v-if="log.audit_remark" class="text-xs text-white/50">
            {{ log.audit_remark }}
          </p>
        </div>
      </div>
      <EmptyState
        v-if="logs?.length === 0 && !loading"
        class="mt-8 pt-16"
        text="暂无佣金记录" />
    </div>
  </div>
</template>
