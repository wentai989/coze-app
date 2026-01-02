<script setup>
const props = defineProps({
  modelValue: { type: Boolean, required: true },
  title: { type: String, default: '' },
  message: { type: String, default: '' },
  confirmText: { type: String, default: '' },
  cancelText: { type: String, default: '' },
  closeOnBackdrop: { type: Boolean, default: true },
  // 新增：图片
  imageSrc: { type: String, default: '' },
  imageClass: { type: String, default: '' },
})

const emit = defineEmits(['update:modelValue', 'confirm', 'cancel'])

const close = () => emit('update:modelValue', false)
function onConfirm() { emit('confirm'); close() }
function onCancel() { emit('cancel'); close() }
function onBackdrop() {
  if (props.closeOnBackdrop) { close() }
}
</script>

<template>
  <!-- 居中弹窗 + 暗色卡片风格 -->
  <div
    v-if="modelValue" class="
      fixed inset-0 z-50 flex items-center justify-center
    "
  >
    <!-- 遮罩更深，增强与背景的层次 -->
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="onBackdrop" />
    <!-- 卡片改为暗色渐变 + 浅色描边，与项目背景一致 -->
    <div
      class="
        relative z-10 mx-4 w-[80%] rounded-2xl bg-gradient-to-b
        from-[#12161a]/95 to-[#0b0f12]/95 p-5 px-4 text-white shadow-xl ring-1
        ring-white/10
        sm:max-w-sm sm:p-6
        md:max-w-md
      "
    >
      <!-- 新增：可选图片 -->
      <div v-if="imageSrc" class="mb-3 grid place-items-center">
        <img
          :src="imageSrc"
          class="mt-5 rounded-xl object-cover ring-1 ring-white/20"
          :class="imageClass"
        >
      </div>
      <!-- 标题与说明 -->
      <p v-if="title" class="text-center text-lg font-semibold">
        {{ title }}
      </p>
      <p
        v-if="message" class="
          mt-2 text-center text-sm leading-relaxed text-white/80
        "
      >
        {{ message }}
      </p>

      <!-- 操作区：左弱化暗色，右琥珀主题保持一致 -->
      <div class="mt-6 grid grid-cols-2 gap-3">
        <div
          v-if="cancelText"
          class="
            bg-white/12 inline-flex h-11 items-center justify-center
            rounded-full text-sm font-medium text-white ring-1 ring-white/15
            transition-colors duration-150
            hover:bg-white/18
            focus:outline-none focus:ring-2 focus:ring-amber-300/30
            active:scale-[0.98]
          "
          @click="onCancel"
        >
          {{ cancelText }}
        </div>
        <div
          v-if="confirmText"
          class="
            inline-flex h-11 items-center justify-center rounded-full
            bg-gradient-to-r from-amber-400 to-orange-500 text-sm font-semibold
            text-gray-900 shadow-md shadow-amber-900/10 transition-all
            duration-150
            hover:shadow-lg hover:brightness-105
            focus:outline-none focus:ring-2 focus:ring-amber-300/60
            active:scale-[0.98]
          "
          @click="onConfirm"
        >
          {{ confirmText }}
        </div>
      </div>
    </div>
  </div>
</template>
