<script setup>
import { onMounted, ref } from 'vue'
import { http } from '@/api/http'
import EmptyState from '@/components/EmptyState.vue'
import LoadingSpinner from '@/components/LoadingSpinner.vue'

const orders = ref(null)
const loading = ref(true)

onMounted(() => {
  loading.value = true
  http.get('/api/orders').then((res) => {
    orders.value = res
  }).finally(() => {
    loading.value = false
  })
})

const resolveStatus = order => (Number(order?.order_type) === 1 ? 1 : 0)
const statusDotClass = s => (s === 1 ? 'bg-emerald-400' : 'bg-amber-400')
const statusTextClass = s => (s === 1 ? 'text-emerald-300' : 'text-amber-300')
</script>

<template>
  <!-- 内容区使用系统背景，不添加额外背景层 -->
  <div class="mx-auto max-w-[var(--safe-max)] px-4 pb-24 text-white">
    <div class="mt-4 space-y-3">
      <LoadingSpinner v-if="loading" overlay="true" />
      <div
        v-for="order in orders"
        v-else
        :key="order.id"
        class="
          flex items-center justify-between rounded-xl bg-white/10 p-3 ring-1
          ring-white/15
        "
      >
        <div class="min-w-0">
          <p class="truncate text-sm font-medium">
            {{ order.name }}
          </p>
          <p class="mt-0.5 text-xs text-white/70">
            {{ order.created_at.split('T')[0] }}
          </p>
        </div>
        <div class="text-right">
          <!-- 新增：右侧状态行（无角标，无描边） -->
          <div class="mb-1 flex items-center justify-end gap-1">
            <span
              class="h-1.5 w-1.5 rounded-full"
              :class="statusDotClass(resolveStatus(order))"
            />
            <span
              class="text-[11px]"
              :class="statusTextClass(resolveStatus(order))"
            >
              {{ order.order_type === 1 ? '算力充值' : '会员充值' }}
            </span>
          </div>

          <p v-if="order.order_type === 1" class="text-sm">
            充值算力：
            <span class="font-semibold text-emerald-300">{{ order.power_value }}</span>
          </p>
          <p v-if="order.order_type === 2" class="text-sm">
            充值天数：
            <span class="font-semibold text-emerald-300">{{ order.day_number }}天</span>
          </p>
          <p v-if="order.order_type === 2" class="text-sm">
            赠送算力：
            <span class="font-semibold text-emerald-300">{{ order.power_value }}</span>
          </p>
          <p v-if="order.pay" class="mt-0.5 text-xs text-white/70">
            支付 ¥{{ order.amount }}
          </p>
        </div>
      </div>
      <EmptyState
        v-if="orders?.length === 0 && !loading"
        class="mt-3"
        text="暂无订单记录"
      />
    </div>
  </div>
</template>
