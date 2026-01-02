<script setup>
import { ref, onMounted } from "vue";
import { http } from "@/api/http";
import { onShow } from "@dcloudio/uni-app";
import BottomNav from "@/components/BottomNav.vue";
import ConfirmModal from "@/components/ConfirmModal.vue";
import Custom from "@/components/Custom.vue";
import LoadingSpinner from "@/components/LoadingSpinner.vue";
import LoginModal from "@/components/LoginModal.vue";
import RechargeModal from "@/components/RechargeModal.vue";
import SignRewardModal from "@/components/SignRewardModal.vue";
import VipRechargeModal from "@/components/VipRechargeModal.vue";
import { navigateTo } from "@/composables/common";
import shareMixin from "@/composables/shareMixin";

defineOptions({
  mixins: [shareMixin],
});
const loading = ref(true);
const showSignModal = ref(false);
const showLoginModal = ref(false);
const showRechargeModal = ref(false);
const showVipRechargeModal = ref(false);
const mch = ref({});

const showAboutModal = ref(false);
const signReward = ref({});

const user = ref(null);
function getMemberInfo() {
  http
    .get("/api/user")
    .then((res) => {
      user.value = res;
    })
    .finally(() => {
      loading.value = false;
    });
}

function handleSign() {
  if (!user.value) {
    showLoginModal.value = true;
    return;
  }

  http
    .get("/api/user/sign", {}, true)
    .then((res) => {
      showSignModal.value = true;
      signReward.value = res;
    })
    .finally(() => {
      loading.value = false;
    });
}

function handleVipRecharge() {
  if (!user.value) {
    showLoginModal.value = true;
    return;
  }
  showVipRechargeModal.value = true;
}

function handleRecharge() {
  if (!user.value) {
    showLoginModal.value = true;
    return;
  }
  showRechargeModal.value = true;
}

function handlePay() {
  getMemberInfo();
}
onMounted(() => {
  http.get("/api/mch").then((res) => {
    mch.value = res;
  });
});
onShow(() => {
  getMemberInfo();
});

function closeSignModal() {
  // 领取回调：关闭弹窗并刷新用户信息（可替换为实际接口）
  showSignModal.value = false;
  getMemberInfo();
}

// 当需要登录时，调用此函数
function requireLogin() {
  showLoginModal.value = true;
}

// 登录成功后的回调
function onLoginSuccess() {
  // console.log('登录成功，可以刷新用户信息了')
  // // 在这里重新获取用户信息
  getMemberInfo();
}
</script>

<template>
  <div
    class="relative min-h-screen w-full bg-gradient-to-b from-[#0f1216] via-[#0f1216] to-[#0b0f12] text-white">
    <Custom>
      <div class="px-4 text-lg font-bold text-white">我的</div>
    </Custom>

    <LoadingSpinner v-if="loading" overlay="true" />

    <div class="relative mx-auto max-w-[var(--safe-max)] px-4">
      <!-- 头像与手机号、签到 -->
      <section class="my-6 flex items-center justify-between">
        <template v-if="user">
          <div class="flex items-center gap-4">
            <img
              src="@/static/user.png"
              alt="用户头像"
              class="h-16 w-16 rounded-2xl object-cover ring-1 ring-white/30" />
            <div>
              <p class="text-xl font-semibold">
                {{ user.phone }}
              </p>

              <div class="mt-2 flex items-center gap-2">
                <span
                  v-if="user.is_vip"
                  class="rounded-md bg-emerald-700/40 px-2 py-0.5 text-xs text-emerald-200"
                  >VIP会员</span
                >
                <span
                  v-else
                  class="rounded-md bg-slate-600/60 px-2 py-0.5 text-xs text-slate-200"
                  >普通会员</span
                >
              </div>
            </div>
          </div>
          <!-- <div
            class="
              inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm
              font-semibold text-gray-900 shadow
              disabled:cursor-not-allowed
            "
            :class="user?.is_sign ? 'bg-gray-400' : 'bg-amber-400'"
            @click="handleSign"
          >
            {{ user?.is_sign ? '已签到' : '签到' }}
          </div> -->
        </template>
        <template v-else>
          <div class="flex items-center gap-4">
            <img
              src="@/static/user.png"
              alt="用户头像"
              class="h-16 w-16 rounded-2xl object-cover ring-1 ring-white/30" />
            <div>
              <p
                class="text-xl font-semibold text-green-400"
                @click="requireLogin">
                立即登录
              </p>
            </div>
          </div>
        </template>
      </section>
      <!-- 钱包与会员卡片 -->
      <section class="mb-6 grid grid-cols-[2fr_1fr] gap-4">
        <!-- 钱包 -->
        <div
          class="rounded-2xl bg-gradient-to-tr from-teal-500 to-green-400 p-3 text-gray-900 shadow-xl ring-1 ring-black/10"
          @click="handleRecharge">
          <p class="text-2xl font-bold">Ƀ {{ user?.power_value || 0.0 }}</p>
          <p class="mt-1 text-xs opacity-80">算力钱包</p>
          <div class="flex justify-end">
            <div
              class="mt-2 inline-flex items-center gap-2 rounded-xl bg-white/60 px-3 py-1.5 text-gray-900 shadow">
              充值
            </div>
          </div>
        </div>

        <!-- 会员权益 -->
        <div
          class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-300 to-rose-400 p-3 text-gray-900 shadow-xl ring-1 ring-black/10"
          @click="handleVipRecharge">
          <p class="text-base font-semibold">尊享会员</p>
          <p class="text-xs opacity-80">享会员权益</p>
          <div
            class="absolute bottom-3 right-3 grid h-8 w-8 place-items-center rounded-xl bg-white/60 shadow">
            <img src="@/static/icons/vip-fill.svg" class="h-6 w-6" />
          </div>
          <div
            class="absolute bottom-3 left-3 grid h-7 w-7 place-items-center rounded-full bg-black/10 text-black">
            <img src="@/static/icons/arrow-right-line.svg" class="h-5 w-5" />
          </div>
        </div>
      </section>

      <!-- 日常任务 -->
      <section class="mb-6">
        <div class="mb-3 flex items-center justify-between">
          <p class="text-lg font-semibold text-white">日常任务</p>
          <p class="text-xs text-white/60">完成任务获得奖励</p>
        </div>

        <ul class="space-y-3">
          <!-- 任务1: 每日登录 -->
          <li class="rounded-2xl bg-black/20 p-4 ring-1 ring-white/10">
            <div class="flex items-start justify-between">
              <div class="flex items-start gap-4">
                <div>
                  <p class="text-sm font-semibold text-white">每日签到</p>
                  <p class="mt-1 text-xs text-white/60">每日签到即可获得算力</p>
                </div>
              </div>
              <div class="flex flex-shrink-0 flex-col items-end">
                <p class="font-bold text-cyan-400">
                  +{{ mch?.power?.add_value || 0 }}
                </p>
                <div
                  v-if="user?.is_sign"
                  class="mt-2 w-20 cursor-default rounded-lg bg-white/10 py-1.5 text-center text-xs text-white/70 ring-1 ring-white/20 backdrop-blur-sm">
                  已完成
                </div>
                <div
                  v-else
                  class="mt-2 w-20 cursor-pointer rounded-lg bg-cyan-500/20 py-1.5 text-center text-xs font-semibold text-cyan-300 ring-1 ring-cyan-500/50 backdrop-blur-sm transition-colors hover:bg-cyan-500/30"
                  @click="handleSign">
                  签到
                </div>
              </div>
            </div>
            <!-- 进度条 -->
            <div class="mt-3 flex items-center gap-3">
              <div class="h-1.5 flex-1 rounded-full bg-black/30">
                <div
                  class="h-full rounded-full bg-cyan-400"
                  :style="{
                    width: `${((user?.is_sign ? 1 : 0) / 1) * 100}%`,
                  }" />
              </div>
              <p class="text-xs text-white/60">{{ user?.is_sign ? 1 : 0 }}/1</p>
            </div>
          </li>

          <!-- 任务2: 邀请好友 -->
          <li class="rounded-2xl bg-black/20 p-4 ring-1 ring-white/10">
            <div class="flex items-start justify-between">
              <div class="flex items-start gap-4">
                <div>
                  <div class="flex items-center gap-2">
                    <p class="text-sm font-semibold text-white">邀请好友</p>
                    <span
                      class="rounded-md bg-rose-500 px-1.5 py-0.5 text-xs font-semibold"
                      >HOT</span
                    >
                  </div>
                  <p class="mt-1 text-xs text-white/60">
                    邀请好友注册即可获得算力
                  </p>
                </div>
              </div>
              <div class="flex flex-shrink-0 flex-col items-end">
                <p class="font-bold text-cyan-400">
                  +{{ mch?.power?.ask_value || 0 }}
                </p>
                <button
                  class="mt-2 w-20 cursor-pointer rounded-lg bg-cyan-500/20 py-1.5 text-center text-xs font-semibold text-cyan-300 ring-1 ring-cyan-500/50 backdrop-blur-sm transition-colors hover:bg-cyan-500/30"
                  open-type="share">
                  去邀请
                </button>
              </div>
            </div>
            <!-- 进度条 -->
            <div class="mt-3 flex items-center gap-3">
              <div class="h-1.5 flex-1 rounded-full bg-gray-800/60">
                <div
                  class="h-full rounded-full bg-cyan-400"
                  :style="{
                    width: `${((user?.referral_count || 0) / 9999) * 100}%`,
                  }" />
              </div>
              <p class="text-xs text-white/60">
                {{ user?.referral_count || 0 }}/9999
              </p>
            </div>
          </li>

          <!-- 任务3: 观看广告 -->
          <li class="rounded-2xl bg-black/20 p-4 ring-1 ring-white/10">
            <div class="flex items-start justify-between">
              <div class="flex items-start gap-4">
                <div>
                  <p class="text-sm font-semibold text-white">观看激励广告</p>
                  <p class="mt-1 text-xs text-white/60">观看广告获得算力奖励</p>
                </div>
              </div>
              <div class="flex flex-shrink-0 flex-col items-end">
                <p class="font-bold text-cyan-400">
                  +{{ mch?.power?.video_value || 0 }}
                </p>
                <div
                  class="mt-2 w-20 cursor-default rounded-lg bg-white/10 py-1.5 text-center text-xs text-white/70 ring-1 ring-white/20 backdrop-blur-sm"
                  @click="handleWatchAd">
                  即将上线
                </div>
              </div>
            </div>
          </li>
        </ul>
      </section>

      <!-- 功能列表卡片 -->
      <section class="rounded-2xl bg-black/30 p-3 ring-1 ring-white/10">
        <ul class="divide-y divide-white/10">
          <li
            class="flex items-center justify-between py-3"
            @click="
              () =>
                !user
                  ? (showLoginModal = true)
                  : navigateTo('/pages/log', {}, 'switchTab')
            ">
            <div class="flex items-center gap-3">
              <span
                class="grid h-8 w-8 place-items-center rounded-lg bg-emerald-500/15 text-emerald-300 ring-1 ring-emerald-400/30">
                <img src="@/static/icons/file-list-fill.svg" class="h-5 w-5" />
              </span>
              <span class="text-sm">使用记录</span>
            </div>
            <i class="fa-solid fa-angle-right text-white/60" />
          </li>

          <!-- <li class="flex items-center justify-between py-3">
            <div class="flex items-center gap-3">
              <span
                class="
                  grid h-8 w-8 place-items-center rounded-lg bg-emerald-500/15
                  text-emerald-300 ring-1 ring-emerald-400/30
                "
              >
                <i class="fa-solid fa-ticket" />
              </span>
              <span class="text-sm">优惠券</span>
            </div>
            <div class="flex items-center gap-3">
              <span class="text-xs text-white/70">2张</span>
              <i class="fa-solid fa-angle-right text-white/60" />
            </div>
          </li>

          <li class="flex items-center justify-between py-3">
            <div class="flex items-center gap-3">
              <span
                class="
                  grid h-8 w-8 place-items-center rounded-lg bg-emerald-500/15
                  text-emerald-300 ring-1 ring-emerald-400/30
                "
              >
                <i class="fa-solid fa-star" />
              </span>
              <span class="text-sm">我的关注</span>
            </div>
            <i class="fa-solid fa-angle-right text-white/60" />
          </li>
 -->

          <li
            v-if="mch?.is_spread === 1"
            class="flex items-center justify-between py-3"
            @click="
              () =>
                !user
                  ? (showLoginModal = true)
                  : navigateTo('/pages/extend/index')
            ">
            <div class="flex items-center gap-3">
              <span
                class="grid h-8 w-8 place-items-center rounded-lg bg-emerald-500/15 text-emerald-300 ring-1 ring-emerald-400/30">
                <img
                  src="@/static/icons/bar-chart-box-ai-fill.svg"
                  class="h-5 w-5" />
              </span>
              <span class="text-sm">分销中心</span>
            </div>
            <i class="fa-solid fa-angle-right text-white/60" />
          </li>

          <li
            class="flex items-center justify-between py-3"
            @click="
              () =>
                !user ? (showLoginModal = true) : navigateTo('/pages/code')
            ">
            <div class="flex items-center gap-3">
              <span
                class="grid h-8 w-8 place-items-center rounded-lg bg-emerald-500/15 text-emerald-300 ring-1 ring-emerald-400/30">
                <img src="@/static/icons/bank-card-line.svg" class="h-5 w-5" />
              </span>
              <span class="text-sm">卡密兑换</span>
            </div>
            <i class="fa-solid fa-angle-right text-white/60" />
          </li>

          <li
            class="flex items-center justify-between py-3"
            @click="
              () =>
                !user
                  ? (showLoginModal = true)
                  : navigateTo('/pages/user/power')
            ">
            <div class="flex items-center gap-3">
              <span
                class="grid h-8 w-8 place-items-center rounded-lg bg-emerald-500/15 text-emerald-300 ring-1 ring-emerald-400/30">
                <img src="@/static/icons/wallet-2-line.svg" class="h-5 w-5" />
              </span>
              <span class="text-sm">算力消耗</span>
            </div>
            <div class="flex items-center gap-3">
              <span class="text-xs text-white/70" />
              <i class="fa-solid fa-angle-right text-white/60" />
            </div>
          </li>
          <li
            class="flex items-center justify-between py-3"
            @click="
              () =>
                !user
                  ? (showLoginModal = true)
                  : navigateTo('/pages/user/order')
            ">
            <div class="flex items-center gap-3">
              <span
                class="grid h-8 w-8 place-items-center rounded-lg bg-emerald-500/15 text-emerald-300 ring-1 ring-emerald-400/30">
                <img src="@/static/icons/wechat-pay-line.svg" class="h-5 w-5" />
              </span>
              <span class="text-sm">订单记录</span>
            </div>
            <i class="fa-solid fa-angle-right text-white/60" />
          </li>

          <li
            class="flex items-center justify-between py-3"
            @click="showAboutModal = true">
            <div class="flex items-center gap-3">
              <span
                class="grid h-8 w-8 place-items-center rounded-lg bg-emerald-500/15 text-emerald-300 ring-1 ring-emerald-400/30">
                <img src="@/static/icons/wechat-2-line.svg" class="h-5 w-5" />
              </span>
              <span class="text-sm">联系我们</span>
            </div>
            <i class="fa-solid fa-angle-right text-white/60" />
          </li>
        </ul>
      </section>

      <!-- 底部导航 -->

      <BottomNav />
    </div>
  </div>

  <!-- 将原来的“签到 ConfirmModal”替换为新的奖励弹窗 -->
  <SignRewardModal
    v-model="showSignModal"
    :title="signReward.title"
    :tag="signReward.tag"
    :description="signReward.description"
    buttonText="我知道了"
    @claim="closeSignModal"
    @close="closeSignModal" />

  <ConfirmModal
    v-model="showLoginModal"
    title="登录"
    message="登录后即可使用全部功能"
    confirmText="确认"
    cancelText="取消"
    @confirm="requireLogin" />

  <ConfirmModal
    v-model="showAboutModal"
    :imageSrc="mch?.contact_qrcode || '/static/logo.png'"
    imageClass="h-[15rem] w-[15rem] rounded-full" />

  <LoginModal
    v-if="showLoginModal"
    v-model="showLoginModal"
    @success="onLoginSuccess" />

  <RechargeModal
    v-if="showRechargeModal"
    v-model="showRechargeModal"
    @pay="handlePay" />
  <VipRechargeModal
    v-if="showVipRechargeModal"
    v-model="showVipRechargeModal"
    @pay="handlePay" />
</template>
