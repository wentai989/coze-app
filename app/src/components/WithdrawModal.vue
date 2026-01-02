<script setup>
import { ref, watch } from "vue";
import { http } from "@/api/http";
import { useFileUpload } from "@/composables/use-file-upload";
import { showToast } from "@/composables/common";

const props = defineProps({
  modelValue: Boolean,
  amount: Number,
  qrCode: String,
});
const emit = defineEmits(["update:modelValue", "success"]);

const amount = ref(props.amount);
const qrCode = ref(props.qrCode);
const loading = ref(false);

watch(
  () => props.amount,
  (newValue) => {
    amount.value = newValue;
  }
);

watch(
  () => props.qrCode,
  (newValue) => {
    qrCode.value = newValue;
  }
);

const { chooseImage, fileData, isUploading } = useFileUpload();

async function handleUpload() {
  if (isUploading.value) {
    return;
  }
  chooseImage();
}

watch(
  () => fileData.value,
  (newValue) => {
    if (newValue && newValue.file_url) {
      qrCode.value = newValue.file_url;
    }
  }
);

function handleConfirm() {
  if (!amount.value) {
    uni.showToast({ title: "请输入提现金额", icon: "none" });
    return;
  }
  if (!qrCode.value) {
    uni.showToast({ title: "请上传收款二维码", icon: "none" });
    return;
  }

  loading.value = true;
  http
    .post(
      "/api/spread/withdraw",
      { amount: amount.value, qr_code: qrCode.value },
      true
    )
    .then(() => {
      showToast("申请成功，请等待打款");
      emit("success");
      closeModal();
    })
    .finally(() => {
      loading.value = false;
    });
}

function closeModal() {
  emit("update:modelValue", false);
}
</script>

<template>
  <div
    v-if="modelValue"
    class="fixed inset-0 z-50 flex items-center justify-center">
    <div
      class="absolute inset-0 bg-black/70 backdrop-blur-sm"
      @click="closeModal" />

    <div
      class="relative z-10 w-[80%] rounded-2xl bg-gradient-to-b from-[#1a1f24]/95 to-[#0b0f12]/95 p-5 text-white shadow-xl ring-1 ring-white/10 sm:max-w-sm sm:p-6 md:max-w-md">
      <h2 class="text-center text-lg font-semibold">申请提现</h2>

      <div class="mt-6 space-y-4">
        <div>
          <label for="amount" class="text-sm text-white/60">提现金额</label>
          <input
            id="amount"
            v-model="amount"
            type="number"
            placeholder="请输入提现金额"
            class="mt-2 rounded-lg bg-black/20 px-4 py-3 ring-1 ring-white/10 focus:ring-cyan-500" />
        </div>
        <div>
          <label class="text-sm text-white/60">收款二维码</label>
          <div
            class="mt-2 flex h-32 w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-black/20 ring-1 ring-white/10"
            @click="handleUpload">
            <img
              v-if="qrCode"
              :src="qrCode"
              class="h-full w-full object-contain" />
            <div
              v-else
              class="flex flex-col items-center text-sm text-white/60">
              <span v-if="isUploading">上传中...</span>
              <span v-else>点击上传</span>
            </div>
          </div>
        </div>
      </div>
      <button
        type="button"
        class="mt-6 inline-flex h-12 w-full items-center justify-center rounded-full bg-gradient-to-r from-[#165DFF] via-[#3BA1FF] to-[#3CD5A7] text-base font-semibold text-white shadow-md transition-all duration-150 hover:shadow-lg hover:brightness-105 focus:outline-none focus:ring-2 focus:ring-sky-300/60 active:scale-[0.98]"
        @click="handleConfirm">
        <span v-if="loading">确认提现中...</span>
        <span v-else>确认提现</span>
      </button>
    </div>
  </div>
</template>
