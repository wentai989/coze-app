<script setup>
import { ref, watch } from "vue";
import { onLoad, onUnload } from "@dcloudio/uni-app";
import { http } from "@/api/http";
import { useFileUpload } from "@/composables/use-file-upload";
import LoadingSpinner from "@/components/LoadingSpinner.vue";
import { showToast, goBack } from "@/composables/common";
import Custom from "@/components/Custom.vue";
import shareMixin from "@/composables/shareMixin";
import RechargeModal from "@/components/RechargeModal.vue";
import VipRechargeModal from "@/components/VipRechargeModal.vue";
import LoginModal from "@/components/LoginModal.vue";

defineOptions({
  mixins: [shareMixin],
});

const showLoginModal = ref(false);
const app = ref({});
const loading = ref(true);
const formData = ref({});
const currentUploadKey = ref("");
const showRechargeModal = ref(false);
const showVipRechargeModal = ref(false);
// 文件上传相关
const { chooseFile, fileData } = useFileUpload();

const startUpload = (key, type) => {
  currentUploadKey.value = key;
  // 根据类型调用 chooseFile
  // useFileUpload 的 chooseFile 第二个参数是 sourceType，这里默认传 album
  chooseFile(type, "album");
};

const handleSubmit = () => {
  // 校验必填项
  if (app.value.launch_params) {
    for (const param of app.value.launch_params) {
      if (param.required && !formData.value[param.name]) {
        showToast(`请填写${param.label}`);
        return;
      }
    }
  }

  loading.value = true;

  http
    .post(`/api/app/${app.value.id}/work`, {
      workflow_id: app.value.bot_id,
      parameters: formData.value,
      is_vip: app.value.is_vip,
    })
    .then((res) => {
      if (res.code == 305) {
        showToast(res.message);
        showVipRechargeModal.value = true;
        return;
      }

      if (res.code == 304) {
        showToast(res.message);
        showRechargeModal.value = true;
        return;
      }

      if (res.code == 500) {
        showToast(res.message);
        return;
      }

      uni.navigateTo({
        url: `/pages/app/result?id=${res.app_log_id}`,
      });
    })
    .catch((err) => {})
    .finally(() => {
      loading.value = false;
    });
};

onLoad((options) => {
  http
    .get(`/api/app/${options.id}`)
    .then((res) => {
      app.value = res;
      // 初始化 formData
      if (res.launch_params) {
        res.launch_params.forEach((param) => {
          formData.value[param.name] = param.value || "";
        });
      }
    })
    .finally(() => {
      loading.value = false;
    });

  uni.$on("loginExpired", () => {
    showLoginModal.value = true;
  });
});

onUnload(() => {
  uni.$off("loginExpired");
});
</script>

<template>
  <div class="min-h-screen text-white p-4">
    <LoadingSpinner v-if="loading" :overlay="true" />

    <Custom>
      <div class="flex flex-1 items-center px-4 z-10">
        <img
          src="@/static/icons/back.svg"
          class="mr-2 h-8 w-8"
          @click.stop="goBack" />
        <!-- <div class="mr-2 h-10 w-10">
          <image
            :src="app.image"
            class="h-10 w-10 rounded-full"
            mode="aspectFill" />
        </div> -->
        <div class="flex flex-col items-start justify-start gap-1">
          <div class="mr-1 truncate text-sm font-medium max-w-[10rem]">
            {{ app.name }}
          </div>
          <div class="text-xs text-gray-300 max-w-[10rem] truncate">
            {{ app.description }}
          </div>
        </div>
      </div>
    </Custom>

    <div
      class="space-y-6 mt-4 overflow-y-auto flex flex-col flex-1 flex-grow w-full">
      <!-- 顶部封面图位置，如果有的话 -->
      <!-- <div v-if="app.cover" class="w-full h-40 bg-gray-800 rounded-lg mb-4"></div> -->

      <div v-if="app.name" class="hidden">{{ app.name }}</div>

      <!-- 使用 Grid 布局替代 Flex 布局，修复样式错乱问题 -->
      <div class="flex flex-col gap-4 w-full">
        <div
          v-for="(param, index) in app.launch_params"
          :key="index"
          class="flex flex-col gap-2">
          <label class="text-sm font-medium text-gray-300 flex items-center">
            <span v-if="param.required" class="text-red-500 mr-1">*</span>
            {{ param.label }}
          </label>
          <!-- Text Input -->
          <input
            v-if="param.type === 'text'"
            v-model="formData[param.name]"
            type="text"
            :placeholder="'请输入' + param.label"
            class="rounded-xl bg-[#222222] px-4 py-4 text-white outline-none placeholder-gray-500 text-sm" />
          <input
            v-if="param.type === 'number'"
            v-model="formData[param.name]"
            type="number"
            :placeholder="'请输入' + param.label"
            class="rounded-xl bg-[#222222] px-4 py-4 text-white outline-none placeholder-gray-500 text-sm" />

          <!-- Textarea Input -->
          <div
            v-else-if="param.type === 'textarea'"
            class="rounded-xl bg-[#222222] px-4 py-4 w-full box-border">
            <textarea
              v-model="formData[param.name]"
              :placeholder="'请输入' + param.label"
              maxlength="9999999999"
              rows="4"
              class="w-full text-white outline-none placeholder-gray-500 resize-none text-sm block"></textarea>
          </div>

          <!-- Number Input -->

          <div v-else-if="param.type === 'image'" class="flex flex-col gap-2">
            <div
              class="w-32 h-32 bg-[#222222] rounded-xl flex items-center justify-center cursor-pointer border border-white/5 overflow-hidden relative"
              @click="startUpload(param.name, 'image')">
              <img
                v-if="formData[param.name]"
                :src="formData[param.name]"
                class="object-cover"
                mode="aspectFill" />
              <div
                v-else
                class="flex flex-col items-center justify-center text-gray-400">
                <!-- 上传图标 -->
                <img src="/static/icons/file-upload-line.svg" class="w-6 h-6" />
              </div>
            </div>
          </div>

          <!-- File Upload -->
          <div v-else-if="param.type === 'file'" class="flex flex-col gap-2">
            <div
              class="w-24 h-24 bg-[#222222] rounded-xl flex items-center justify-center cursor-pointer border border-white/5 overflow-hidden relative"
              @click="startUpload(param.name, 'file')">
              <div
                v-if="formData[param.name]"
                class="p-2 text-center overflow-hidden">
                <span class="text-xs text-white break-all line-clamp-3">{{
                  formData[param.name].split("/").pop()
                }}</span>
              </div>
              <div
                v-else
                class="flex flex-col items-center justify-center text-gray-400">
                <!-- 上传图标 -->
                <img src="/static/icons/file-upload-line.svg" class="w-6 h-6" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 底部区域 -->
    </div>
    <div class="h-[10rem]"></div>

    <div class="pt-4 pb-8 px-4 fixed bottom-0 left-0 right-0 bg-black z-10">
      <button
        class="w-full rounded-xl bg-gradient-to-r from-[#165DFF] via-[#3BA1FF] to-[#3CD5A7] py-4 text-base font-bold text-white shadow-lg transition-transform active:scale-95"
        @click="handleSubmit">
        立即生成
        {{ app.power_type == 2 ? app.use_power + "算力" : "" }}
      </button>
      <div class="text-center text-gray-500 text-xs mt-4">
        内容由AI生成，仅供参考
      </div>
    </div>
  </div>

  <RechargeModal v-model="showRechargeModal" v-if="showRechargeModal" />
  <VipRechargeModal
    v-model="showVipRechargeModal"
    v-if="showVipRechargeModal" />
  <LoginModal v-model="showLoginModal" v-if="showLoginModal" />
</template>
