<script setup>
defineProps({
  // 是否全屏遮罩（居中显示）
  overlay: { type: Boolean, default: false },
  // 圆环尺寸（px）
  size: { type: Number, default: 40 },
  // 圆环边框粗细（px）
  thickness: { type: Number, default: 2 },
  // 可选文案
  text: { type: String, default: '' },
})
</script>

<template>
  <div
    v-if="overlay" class="
      fixed inset-0 z-50 grid place-items-center bg-black/40 backdrop-blur-sm
    "
  >
    <div
      class="
        box-border animate-spin rounded-full border-white/20 border-t-white
      "
      :style="{ width: `${size}px`, height: `${size}px`, borderWidth: `${thickness}px` }"
    />
    <p v-if="text" class="mt-3 text-sm text-white/80">
      {{ text }}
    </p>
  </div>

  <div v-else class="grid place-items-center">
    <div
      class="
        box-border animate-spin rounded-full border-white/20 border-t-white
      "
      :style="{ width: `${size}px`, height: `${size}px`, borderWidth: `${thickness}px` }"
    />
    <p v-if="text" class="mt-3 text-sm text-white/80">
      {{ text }}
    </p>
  </div>
</template>
