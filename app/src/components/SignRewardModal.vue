<script setup>
defineProps({
  modelValue: { type: Boolean, default: false },
  title: { type: String, default: '恭喜您' },
  tag: { type: String, default: '新人赠送' },
  description: { type: String, default: '您已成功签到，获得100积分' },
  buttonText: { type: String, default: '我知道了' },
  //   imageSrc: { type: String, default: '@/static/logo.png' },
  closeOnBackdrop: { type: Boolean, default: true },
})
const emit = defineEmits(['update:modelValue', 'claim'])

function close() {
  emit('close')
}
function handleClaim() {
  emit('claim')
//   emit('update:modelValue', false)
}
</script>

<template>
  <div
    v-if="modelValue"
    class="fixed inset-0 z-[1000]"
    @click.self="closeOnBackdrop && close()"
  >
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" />
    <div class="relative z-[1001] grid h-full place-items-center p-4">
      <div
        class="
          relative w-[58vw] max-w-[300px] rounded-2xl bg-gradient-to-br
          from-emerald-200/70 via-teal-200/70 to-amber-200/70 p-5 shadow-lg
          ring-1 ring-black/10
        "
      >
        <!-- 标题：新人赠送 -->
        <div class="flex justify-center">
          <span
            class="
              inline-flex items-center rounded-full bg-orange-500/90 px-3 py-1
              text-xs font-semibold text-white
            "
          >
            {{ tag }}
          </span>
        </div>
        <!-- <div class="grid place-items-center">
          <img :src="imageSrc" alt="奖励图标" class="h-14 w-14 object-contain">
        </div> -->

        <div class="mt-2 text-center">
          <p class="text-lg font-extrabold text-gray-900">
            {{ title }}
          </p>
          <p class="mt-1 text-lg font-extrabold text-gray-900">
            {{ description }}
          </p>
        </div>

        <button
          class="
            mt-4 w-[10rem] rounded-full bg-gradient-to-r from-orange-400
            to-rose-400 px-6 py-2 text-sm font-bold text-white shadow-lg
            shadow-rose-500/30 transition-all
            hover:-translate-y-0.5 hover:shadow-xl hover:shadow-rose-500/40
          "
          @click.stop="handleClaim"
        >
          {{ buttonText }}
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* 极简版本无需额外样式 */
</style>
