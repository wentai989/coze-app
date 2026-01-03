<script setup>
import {
  onLoad,
  onUnload,
  onShareAppMessage,
  onShareTimeline,
} from "@dcloudio/uni-app";
import { onUnmounted, onMounted, ref, watch } from "vue";
import { http } from "@/api/http";
import Custom from "@/components/Custom.vue";
import LoadingSpinner from "@/components/LoadingSpinner.vue";
import uaMarkdown from "@/components/ua-markdown/ua-markdown.vue";
import shareMixin from "@/composables/shareMixin";
import RechargeModal from "@/components/RechargeModal.vue";
import VipRechargeModal from "@/components/VipRechargeModal.vue";
import LoginModal from "@/components/LoginModal.vue";

import {
  sendMessage,
  handleRefresh,
  copyContent,
  isLastMessage,
  textToObj,
  handleNewChat,
  handlePay,
  formatResponse,
} from "@/composables/chat";
import { goBack } from "@/composables/common";
import { useFileUpload } from "@/composables/use-file-upload";
import { useShare } from "@/composables/useShare";
import { useChatStore } from "@/stores/chat";
// defineOptions({
//   mixins: [shareMixin],
// });

const app = ref({});

useShare({
  title: () => app.value?.name || "",
  imageUrl: () => app.value?.image || "",
  path: () => `/pages/app/chat?id=${app.value?.id || ""}`,
  query: () => `id=${app.value?.id || ""}`,
});

const { chooseFile, fileData } = useFileUpload();
const chatStore = useChatStore();
const showRechargeModal = ref(false);
const showVipRechargeModal = ref(false);
const showLoginModal = ref(false);
const inputText = ref("");
const inputHeight = ref(0);
const isInput = ref(false);

const loading = ref(true);

// watch(
//   () => userStore.isLoginExpired,
//   (isExpired) => {
//     console.log("isLoginExpired:", isExpired);
//     if (isExpired) {
//       showLoginModal.value = true;
//       // 重置状态，避免重复提示
//       userStore.setLoginExpired(false);
//     }
//   },
//   { deep: true }
// );

// watch(
//   () => userStore.isPowerPay,
//   (isPay) => {
//     if (isPay) {
//       showRechargeModal.value = true;
//       // 重置状态，以便下次可以再次触发
//       userStore.setPowerPay(false);
//     }
//   },
//   { deep: true }
// );

// watch(
//   () => userStore.isVipPay,
//   (isPay) => {
//     if (isPay) {
//       showVipRechargeModal.value = true;
//       // 重置状态，以便下次可以再次触发
//       userStore.setVipPay(false);
//     }
//   },
//   { deep: true }
// );

// 监听输入法高度变化
try {
  uni.onKeyboardHeightChange((res) => {
    inputHeight.value = res.height;
    console.log("inputHeight.value", inputHeight.value);
  });
} catch (error) {
  // console.error("Error in onMounted:", error);
  console.log("Error in onMounted:", error);
}

watch(
  () => [
    chatStore.currentMessage,
    chatStore.reasoningContent,
    chatStore.linkEds,
    chatStore.isResponsing,
    chatStore.messageHistory,
    inputHeight.value > 0,
  ],
  (newVal) => {
    // console.log(chatStore.currentMessage);
    setTimeout(() => {
      uni.pageScrollTo({
        scrollTop: 999999999999999,
        duration: 0,
      });
    }, 100);
  }
);

watch(inputText, (newVal, oldVal) => {
  if (newVal === oldVal) {
    return;
  }
  isInput.value = newVal.length > 0;
});

function handleSendMessage() {
  console.log("inputText.value.trim()", inputText.value.trim());
  if (chatStore.isResponsing) {
    return;
  }

  if (!inputText.value.trim()) {
    return;
  }
  try {
    sendMessage(inputText.value.trim());
    inputText.value = "";
  } catch (error) {
    console.error("Error in handleSendMessage:", error);
  }
}

function previewLink(link) {
  if (chatStore.isResponsing) {
    return;
  }
  sendMessage(link.trim());
}

function handleFocus(e) {
  // inputHeight.value = e.detail.height;
  console.log("focus", e);
}

function handleSelectFile() {
  uni.showActionSheet({
    itemList: ["选择图片", "选择文件"],
    success: (res) => {
      if (res.tapIndex === 0) {
        // 从相册选择图片
        chooseFile("image", "album");
      } else if (res.tapIndex === 1) {
        // 选择文件
        chooseFile("file");
      }
    },
    fail: (res) => {
      console.log(res.errMsg);
    },
  });
}

onLoad((options) => {
  chatStore.clearAll(); // 清理所有记录
  http
    .get(`/api/app/${options.id}`)
    .then((res) => {
      app.value = res;
      chatStore.init(res);
    })
    .finally(() => {
      loading.value = false;
    });

  uni.$on("loginExpired", () => {
    showLoginModal.value = true;
  });

  uni.$on("isVipPay", () => {
    showVipRechargeModal.value = true;
  });

  uni.$on("isPowerPay", () => {
    showRechargeModal.value = true;
  });
});

onUnmounted(() => {
  console.log("卸载");
  chatStore.clearAll(); // 清理所有记录
});
//保存页面聊天数据
const saveChatHistory = () => {
  if (!chatStore.messageHistory || chatStore.messageHistory.length === 0) {
    return;
  }
  http.post(`/api/app/${chatStore.agentData?.id}/log`, {
    messages: chatStore.messageHistory,
    log_type: "agent",
  });
};

onMounted(() => {
  uni.onAppHide(saveChatHistory);
});

onUnload(() => {
  saveChatHistory();
  uni.offAppHide(saveChatHistory);
  uni.$off("loginExpired");
  uni.$off("isVipPay");
  uni.$off("isPowerPay");
});
</script>

<template>
  <div class="flex h-screen flex-col text-white">
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
    <!-- 聊天消息 -->
    <div class="flex-1 px-4 pt-4">
      <div class="flex flex-col items-center justify-center my-4">
        <div class="mr-2 h-32 w-32">
          <image
            :src="app.image"
            class="h-32 w-32 rounded-full"
            mode="aspectFill" />
          <!-- <img
            src="@/static/2.png"
            class="h-32 w-32 rounded-full"
            mode="aspectFill" /> -->
        </div>
        <div class="mt-4 text-xl font-semibold">{{ app.name }}</div>
      </div>
      <div
        v-for="(item, index) in chatStore.messageHistory"
        :key="item.id"
        class="mb-4">
        <!-- 时间戳 -->
        <div
          v-if="item.timestamp"
          class="mb-4 text-center text-xs text-gray-500">
          {{ item.timestamp }}
        </div>
        <!-- {{ item }} -->

        <!-- AI 消息 -->
        <div
          v-if="item.role === 'assistant'"
          class="flex items-start gap-3 flex-col">
          <div
            class="break-all rounded-lg text-gray-800 p-3 text-sm bg-gray-100 shadow-sm">
            <!-- <text class="">{{ item.content }}</text> -->

            <!-- <ua-markdown :source="item.content"> </ua-markdown> -->

            <ua-markdown
              :source="formatResponse(item.reasoning_content, item.content)">
            </ua-markdown>
          </div>
          <div class="ml-1 flex items-start gap-2" v-if="isLastMessage(index)">
            <div
              class="cursor-pointer flex items-center justify-center bg-gray-800 p-1.5 rounded-full">
              <img
                src="@/static/icons/file-copy-fill.svg"
                alt="复制"
                @click="copyContent(item.content)"
                class="h-4 w-4" />
            </div>

            <div
              class="cursor-pointer flex items-center justify-center bg-gray-800 p-1.5 rounded-full">
              <img
                src="@/static/icons/reset-left-line.svg"
                alt="刷新"
                @click="handleRefresh"
                class="h-4 w-4" />
            </div>
          </div>

          <div
            v-for="(link, linkIndex) in chatStore.linkEds"
            v-if="isLastMessage(index)"
            :key="linkIndex"
            class="cursor-pointer text-white/80 text-xs border-white/50 border-[1px] px-1.5 py-1 rounded-xl"
            @click="previewLink(link)">
            {{ link }}
          </div>
        </div>

        <div
          v-if="item.role === 'user'"
          v-for="(msgItem, msgIndex) in textToObj(item)"
          :key="msgIndex"
          class="flex items-start justify-end gap-3 mb-2">
          <image
            v-if="msgItem.type === 'image'"
            :src="msgItem.file_url"
            style="max-width: 50%; max-height: 1000px"
            class="rounded-lg flex justify-end"
            mode="widthFix" />
          <div v-else-if="msgItem.type === 'file'">
            <img
              src="@/static/icons/wenjian.svg"
              class="w-[4rem] h-[4rem] object-cover rounded-lg" />

            <!-- <span>文件</span>  -->
          </div>
          <div
            v-else
            class="rounded-lg bg-gradient-to-r from-orange-400 to-rose-400 p-3 text-sm text-white shadow-sm">
            <text class="break-words" v-if="msgItem.type === 'text'">{{
              msgItem.text
            }}</text>
          </div>
          <!-- 用户头像可以在这里添加 -->
        </div>
      </div>

      <div
        class="mb-4 flex flex-col items-start gap-3"
        v-if="chatStore.isResponsing">
        <!-- <div
          v-if="chatStore.reasoningContent"
          class="break-all rounded-lg bg-white p-3 text-sm text-gray-800 shadow-sm">
          <ua-markdown :source="chatStore.reasoningContent"> </ua-markdown>
        </div>

        <div
          v-if="chatStore.currentMessage"
          class="break-all rounded-lg bg-white p-3 text-sm text-gray-800 shadow-sm">
          <ua-markdown :source="chatStore.currentMessage"> </ua-markdown>
        </div> -->
        <div
          v-if="chatStore.reasoningContent || chatStore.currentMessage"
          class="break-all rounded-lg bg-white p-3 text-sm text-gray-800 shadow-sm">
          <ua-markdown
            :source="
              formatResponse(
                chatStore.reasoningContent,
                chatStore.currentMessage
              )
            ">
          </ua-markdown>
        </div>

        <div
          v-if="!chatStore.currentMessage"
          class="break-all rounded-lg bg-gray-800 p-3 text-sm text-gray-800 shadow-sm">
          <div class="flex items-center space-x-1.5">
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
          </div>
        </div>
      </div>

      <div
        id="zhanwei"
        class="h-[10rem]"
        :style="{
          marginBottom: `${inputHeight}px`,
        }">
        &nbsp;
      </div>
    </div>

    <!-- 输入区域 -->

    <div
      class="fixed left-0 right-0 z-50"
      :style="{ bottom: inputHeight + 'px' }">
      <div class="p-3 flex justify-start gap-3">
        <div
          class="flex items-center gap-2 rounded-lg bg-white p-2 shadow-sm"
          v-if="!chatStore.isResponsing && chatStore.messageHistory.length > 1"
          @click="handleNewChat">
          <img src="@/static/icons/chat-1-fill.svg" class="h-5 w-5" />
          <div class="text-xs text-gray-800 font-medium">开启新对话</div>
        </div>
        <!-- <div class="flex items-center gap-1 rounded-lg bg-white p-2 shadow-sm">
          <img src="@/static/icons/arrow-down-box-fill.svg" class="h-5 w-5" />
        </div> -->
        <div class="flex items-center gap-1 rounded-lg bg-white p-2 shadow-sm">
          <button open-type="share" class="share-button">
            <img src="@/static/icons/wechat-fill.svg" class="h-5 w-5" />
          </button>
          <!-- <div class="text-xs text-gray-800 font-medium">分享</div> -->
        </div>
      </div>
      <div class="border-t border-gray-800 bg-black p-3">
        <div
          class="flex flex-wrap gap-3"
          v-if="chatStore?.fileData?.length > 0">
          <div v-for="(item, index) in chatStore.fileData" class="relative">
            <template v-if="item.file_type === 'image'">
              <img
                :src="item.file_url"
                class="w-[4rem] h-[4rem] object-cover rounded-lg"
                mode="heightFix" />
            </template>
            <template v-else>
              <div class="flex items-center">
                <img
                  src="@/static/icons/wenjian.svg"
                  class="w-[4rem] h-[4rem] object-cover rounded-lg" />
              </div>
            </template>
            <img
              @click="chatStore.removeFile(index)"
              src="@/static/icons/close-fill.svg"
              class="w-5 h-5 absolute -top-2 -right-2 cursor-pointer" />
          </div>
        </div>

        <div
          class="flex items-center gap-2 rounded-lg bg-gray-100 px-2 shadow-sm">
          <div
            v-if="!isInput"
            class="grid h-10 w-10 place-items-center rounded-full text-gray-600"
            @click="() => chooseFile('image', 'camera')">
            <img src="/static/icons/img.svg" alt="拍照" class="h-6 w-6" />
          </div>

          <textarea
            v-model="inputText"
            auto-height
            maxlength="9999999999"
            placeholder="发送消息..."
            class="max-h-[5rem] flex-1 bg-transparent text-gray-800"
            show-confirm-bar="{{ false }}"
            :adjust-position="false"
            @focus="handleFocus" />
          <div
            v-if="!isInput"
            class="grid h-10 w-10 place-items-center rounded-full text-gray-600"
            @click="handleSelectFile">
            <img src="/static/icons/add.svg" alt="文件" class="h-6 w-6" />
          </div>

          <div
            v-if="isInput"
            class="grid h-10 w-10 place-items-center rounded-full text-gray-600"
            @click.stop="handleSendMessage">
            <img src="/static/icons/up.svg" alt="发送" class="h-6 w-6" />
          </div>
        </div>

        <div class="text-xs text-gray-300 text-center mt-2">
          内容由AI生成，仅供参考
        </div>
      </div>
    </div>
  </div>
  <RechargeModal
    v-model="showRechargeModal"
    v-if="showRechargeModal"
    @pay="handlePay" />
  <VipRechargeModal
    v-model="showVipRechargeModal"
    v-if="showVipRechargeModal"
    @pay="handlePay" />
  <LoginModal
    v-model="showLoginModal"
    v-if="showLoginModal"
    @success="handlePay" />
</template>

<style scoped>
.dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: #a3a3a3; /* a shade of gray */
  animation: wave 1.4s infinite ease-in-out both;
}

.dot:nth-child(1) {
  animation-delay: -0.32s;
}

.dot:nth-child(2) {
  animation-delay: -0.16s;
}

@keyframes wave {
  0%,
  80%,
  100% {
    transform: scale(0);
  }
  40% {
    transform: scale(1);
  }
}
.share-button {
  padding: 0;
  margin: 0;
  background-color: transparent;
  line-height: 1;
}
.share-button::after {
  border: none;
}
</style>
