<script setup>
import { onMounted, ref } from "vue";
import { http } from "@/api/http";
import LoadingSpinner from "@/components/LoadingSpinner.vue";
import WithdrawModal from "@/components/WithdrawModal.vue";
import { navigateTo } from "@/composables/common";
import shareMixin from "@/composables/shareMixin";

defineOptions({
  mixins: [shareMixin],
});
const showWithdrawModal = ref(false);
const spread = ref({});

const loading = ref(true);

function handleWithdrawSuccess() {
  // 可以在这里刷新用户信息或佣金数据
  fetchUserCommissions();
}

async function fetchUserCommissions() {
  try {
    const res = await http.get("/api/spread");
    spread.value = res;
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  // 页面加载时获取用户佣金数据
  fetchUserCommissions();
});
</script>

<template>
  <div class="min-h-screen text-white">
    <!-- Custom Navigation Bar -->

    <LoadingSpinner v-if="loading" overlay="true" />

    <div
      class="relative mx-auto max-w-[var(--safe-max)] flex-1 overflow-y-auto px-4 py-4">
      <!-- Top Card: 算力 -->
      <div class="rounded-2xl bg-black/20 p-4 ring-1 ring-white/10">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-white/60">余额</p>
            <p class="mt-1 text-2xl font-bold">
              {{ spread.amount || 0 }}
            </p>
            <div
              class="mt-2 flex items-center justify-center"
              @click="() => navigateTo('/pages/extend/log')">
              <p class="cursor-pointer text-sm font-medium text-green-400/60">
                佣金明细
              </p>
              <img
                src="/static/icons/arrow-right-s-fill.svg"
                alt="arrow-right-s-fill"
                class="h-5 w-5" />
            </div>
          </div>
          <div
            class="cursor-pointer rounded-lg bg-cyan-500/20 px-6 py-2 text-sm font-semibold text-cyan-300 ring-1 ring-cyan-500/50 backdrop-blur-sm transition-colors"
            @click="showWithdrawModal = true">
            提现
          </div>
        </div>
        <div class="mt-6 grid grid-cols-3 divide-x divide-gray-700 text-center">
          <div>
            <p class="text-sm text-white/60">累计获得</p>
            <p class="mt-1 text-lg font-semibold">
              {{ spread.total_spread || 0 }}
            </p>
          </div>
          <div>
            <p class="text-sm text-white/60">最近30天获得</p>
            <p class="mt-1 text-lg font-semibold">
              {{ spread.spread_30_days || 0 }}
            </p>
          </div>
          <div>
            <p class="text-sm text-white/60">邀请用户</p>
            <p class="mt-1 text-lg font-semibold">
              {{ spread.invite_count || 0 }}
            </p>
          </div>
        </div>
      </div>

      <!-- Bottom Card: 邀请说明 -->
      <div class="mt-6 rounded-2xl bg-black/20 p-4 ring-1 ring-white/10">
        <h2 class="text-center text-lg font-semibold">【邀请好友 共享收益】</h2>
        <div class="mt-4 space-y-4 text-sm text-white/80">
          <div class="flex items-start gap-2">
            <span class="font-bold">1.</span>
            <p>
              一键复制专属邀请链接发送给好友，好友成功注册后立即与您建立绑定关系。
            </p>
          </div>
          <div class="flex items-start gap-2">
            <span class="font-bold">2.</span>
            <p>
              通过您链接注册的好友，在平台每笔消费都将为您带来：<span
                class="font-semibold text-amber-400"
                >现金佣金分成</span
              >
            </p>
          </div>
          <div class="flex items-start gap-2">
            <span class="font-bold">3.</span>
            <p>佣金提现后，请在用户中心，联系客服及时处理、确认到账。</p>
          </div>
        </div>
      </div>

      <!-- Spacer -->
      <div class="h-24" />
    </div>

    <!-- Bottom Button -->
    <div
      class="fixed bottom-0 left-0 z-10 w-full bg-gradient-to-t from-black/80 via-black/50 to-transparent p-4 backdrop-blur-lg">
      <button
        class="w-full rounded-full bg-gradient-to-r from-[#165DFF] via-[#3BA1FF] to-[#3CD5A7] py-3 text-lg font-semibold text-white transition-opacity"
        open-type="share">
        立即推广
      </button>
    </div>

    <!-- Withdraw Modal -->
    <WithdrawModal
      v-model="showWithdrawModal"
      :amount="spread.amount"
      :qrCode="spread.amount_img"
      @success="handleWithdrawSuccess" />
  </div>
</template>
