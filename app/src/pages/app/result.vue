<script setup>
import { ref, computed } from "vue";
import { onLoad } from "@dcloudio/uni-app";
import { http } from "@/api/http";
import { previewImage } from "@/composables/common";
import { copyContent } from "@/composables/chat";
import LoadingSpinner from "@/components/LoadingSpinner.vue";

const id = ref("");
const isGenerating = ref(false);
const outputs = ref(null);
const outputParams = ref([]);
const isStatus = ref(null);
const loading = ref(true);

const cleanValue = (val) => {
  if (typeof val === "string") {
    // Remove surrounding backticks and whitespace
    return val.replace(/`/g, "").trim();
  }
  return val;
};

const parsedContent = computed(() => {
  if (!outputs.value || !outputs.value.length || !outputParams.value) return [];

  return outputs.value
    .map((outputItem) => {
      try {
        const rawOutput = outputItem.output;
        if (!rawOutput) return [];

        const json1 = JSON.parse(rawOutput);
        let finalOutput = {};

        if (json1.Output) {
          if (typeof json1.Output === "string") {
            finalOutput = JSON.parse(json1.Output);
          } else {
            finalOutput = json1.Output;
          }
        } else {
          finalOutput = json1;
        }

        return outputParams.value.map((param) => {
          return {
            ...param,
            value: cleanValue(finalOutput[param.name]),
          };
        });
      } catch (e) {
        console.error("Parsing error:", e);
        return [];
      }
    })
    .filter((group) => group.length > 0);
});

const pollForResult = () => {
  const poll = () => {
    http
      .get(`/api/app-log/${id.value}/work`)
      .then((res) => {
        loading.value = false;
        if (res.is_status === 1) {
          isGenerating.value = false;
          isStatus.value = 1;
          outputs.value = res.outputs;
          outputParams.value = res.output_params || [];
          // You can handle the successful result here, e.g., display it
          console.log("Task result:", res.outputs);
        } else if (res.is_status === 2) {
          isGenerating.value = false;
          isStatus.value = 2;

          outputs.value = res.outputs;
        } else {
          isGenerating.value = true;
          isStatus.value = 0;
          // If status is 'pending' or 'running', continue polling
          setTimeout(poll, 3000);
        }
      })
      .catch((err) => {
        // Continue polling even on error, or handle appropriately
        setTimeout(poll, 3000);
        console.error("Polling error:", err);
      });
  };

  // Start polling immediately
  poll();
};

onLoad((options) => {
  if (options.id) {
    id.value = options.id;
    pollForResult();
  }
});
</script>

<template>
  <div class="min-h-screen text-white p-4">
    <LoadingSpinner v-if="loading" :overlay="true" />

    <!-- Generating Animation State -->
    <div
      v-if="isStatus === 0"
      class="flex flex-col items-center justify-center flex-1 h-[80vh] animate-fade-in">
      <div class="relative w-40 h-40 mb-8">
        <!-- Outer Ring -->
        <div
          class="absolute inset-0 border-4 border-cyan-500/20 rounded-full animate-[spin_3s_linear_infinite]"></div>
        <div
          class="absolute inset-0 border-t-4 border-cyan-500 rounded-full animate-[spin_2s_linear_infinite]"></div>

        <!-- Middle Ring -->
        <div
          class="absolute inset-4 border-4 border-purple-500/20 rounded-full"></div>
        <div
          class="absolute inset-4 border-r-4 border-purple-500 rounded-full animate-[spin_3s_linear_infinite_reverse]"></div>

        <!-- Inner Core -->
        <div
          class="absolute inset-10 bg-gradient-to-tr from-cyan-500/20 to-purple-500/20 rounded-full animate-pulse flex items-center justify-center backdrop-blur-sm">
          <div
            class="w-2 h-2 bg-white rounded-full shadow-[0_0_10px_rgba(255,255,255,0.8)] animate-ping"></div>
        </div>

        <!-- Scanning Effect -->
        <div
          class="absolute inset-0 bg-gradient-to-b from-transparent via-cyan-500/10 to-transparent animate-[scan_2s_ease-in-out_infinite] rounded-full pointer-events-none"></div>
      </div>

      <div class="space-y-3 text-center z-10">
        <div
          class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-cyan-400 to-purple-400 animate-pulse">
          AI 正在生成中
        </div>
        <div class="flex items-center justify-center gap-1">
          <span
            class="w-1.5 h-1.5 bg-cyan-400 rounded-full animate-[bounce_1s_infinite_0ms]"></span>
          <span
            class="w-1.5 h-1.5 bg-cyan-400 rounded-full animate-[bounce_1s_infinite_200ms]"></span>
          <span
            class="w-1.5 h-1.5 bg-cyan-400 rounded-full animate-[bounce_1s_infinite_400ms]"></span>
        </div>
        <div
          class="text-xs text-gray-400 font-mono tracking-widest uppercase mt-4 opacity-70">
          Processing Data...
        </div>
      </div>
    </div>

    <!-- Result Display -->
    <div
      v-else-if="isStatus === 1"
      class="mt-4 p-4 bg-[#222222] rounded-xl overflow-y-auto">
      <div v-if="parsedContent.length > 0" class="space-y-8">
        <div
          v-for="(group, groupIndex) in parsedContent"
          :key="groupIndex"
          class="space-y-6 border-b border-gray-700 pb-6 last:border-0 last:pb-0">
          <!-- Optional: Header for each result group if needed -->
          <!-- <div class="text-xs text-gray-500 uppercase tracking-wider">Result {{ groupIndex + 1 }}</div> -->

          <div v-for="(item, index) in group" :key="index">
            <!-- <div class="text-sm text-gray-400 mb-2">{{ item.name }}</div> -->
            <!-- Text & Number -->
            <div
              v-if="item.type === 'text' || item.type === 'number'"
              class="text-white whitespace-pre-wrap break-words">
              {{ item.value }}
            </div>

            <!-- Image -->
            <div
              v-else-if="item.type === 'image'"
              class="rounded-lg overflow-hidden">
              <image
                :src="item.value"
                mode="widthFix"
                class="w-full"
                @click="previewImage(item.value)" />
            </div>

            <!-- Video -->
            <!-- <div
              v-else-if="item.type === 'video'"
              class="rounded-lg overflow-hidden">
              <video
                :src="item.value"
                class="w-full aspect-video"
                controls></video>
            </div> -->

            <!-- File -->
            <div
              v-else-if="item.type === 'file'"
              class="flex flex-col items-start gap-2">
              <div class="i-carbon-document text-2xl text-gray-400"></div>
              <a
                :href="item.value"
                target="_blank"
                class="text-blue-400 hover:underline break-all flex-1">
                {{ item.value }}
              </a>
              <div
                class="flex items-center gap-2 bg-green-600 hover:bg-green-500 active:scale-95 text-white px-4 py-2 rounded-lg text-xs font-medium transition-all cursor-pointer shadow-lg shadow-green-900/20"
                @click="copyContent(item.value)">
                <img
                  src="@/static/icons/file-copy-fill.svg"
                  alt="复制"
                  class="h-3.5 w-3.5 brightness-0 invert" />
                <span>复制链接</span>
              </div>
            </div>

            <!-- Fallback -->
            <div v-else class="bg-[#333] p-3 rounded-lg text-white break-words">
              {{ item.value }}
            </div>
            <div class="text-xs text-gray-200 my-2">
              {{ item.tip }}
            </div>
            <div class="h-px bg-gray-700/50 w-full"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-4 p-4 bg-[#222222] rounded-xl" v-if="isStatus === 2">
      <div class="text-center text-red-500">
        ai 生成失败,请稍后再试.
        <div
          v-for="(error, index) in outputs"
          :key="index"
          class="text-red-500">
          {{ error?.error_message || error }}
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Add any specific styles here if needed */
</style>
