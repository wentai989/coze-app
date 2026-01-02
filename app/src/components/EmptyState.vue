<script setup>
defineProps({
  text: {
    type: String,
    default: '没有更多内容了',
  },
  icon: {
    type: String,
    // uni-app 中 /static 目录会被映射为根目录
    default: '/static/icons/error-warning-line.svg',
  },
})
</script>

<template>
  <div
    class="
      flex h-[50vh] w-full flex-col items-center justify-center gap-4 text-sm
    "
  >
    <img :src="icon" class="h-8 w-8 opacity-30">
    <p class="text-sm text-gray-500">
      {{ text }}
    </p>
  </div>
</template>
