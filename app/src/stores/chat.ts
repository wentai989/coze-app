import type { EnterMessage } from "@coze/api";
import { RoleType } from "@coze/api";
import { defineStore } from "pinia";
import { ref } from "vue";

export const useChatStore = defineStore("chat", () => {
  // 联想数据
  const linkEds = ref<any[]>([]);
  // 文件数据
  const fileData = ref<any[]>([]);
  //联想内容
  const reasoningContent = ref("");
  // 智能体数据
  const agentData = ref<any>(null);
  // 状态定义
  const currentMessage = ref("");
  const isResponsing = ref(false);

  // const messageHistory = ref<EnterMessage[]>([]);
  const messageHistory = ref<any[]>([]);

  // 日志id
  const logId = ref("");

  // 清理数据
  function clearAll() {
    linkEds.value = [];
    fileData.value = [];
    agentData.value = null;
    currentMessage.value = "";
    reasoningContent.value = "";
    // 清空历史消息
    messageHistory.value = [];
    isResponsing.value = false;
  }
  //重置其他数据
  function resetOtherData() {
    linkEds.value = [];
    fileData.value = [];
    currentMessage.value = "";
    // 清空历史消息
    messageHistory.value = [];
    isResponsing.value = false;
  }

  function clearMessage() {
    currentMessage.value = "";
  }

  function clearLink() {
    linkEds.value = [];
  }

  function removeFile(index: number) {
    fileData.value.splice(index, 1);
  }

  // 保存上传文件
  function createFileData(data: any) {
    fileData.value.push(data);
  }

  // 添加对话到历史记录
  function addHistory(
    role: string,
    content: any,
    contentType: "text" | "object_string"
  ) {
    messageHistory.value.push({
      role: role === "user" ? RoleType.User : RoleType.Assistant,
      content,
      content_type: contentType,
    });
  }

  // 清理所有记录
  function clearHistory() {
    messageHistory.value = [];
  }

  // 添加开场白数据
  function openingRemarks() {
    // 添加开场白
    let content = "";
    if (agentData.value?.kont_configs?.onboarding_info?.prologue) {
      content = agentData.value?.kont_configs?.onboarding_info?.prologue;
      if (agentData.value?.kont_configs?.onboarding_info?.suggested_questions) {
        agentData.value?.kont_configs?.onboarding_info?.suggested_questions.forEach(
          (item: any) => {
            linkEds.value.push(item);
          }
        );
      }
    } else {
      // 默认开场白
      content = `你好！我是你的专属AI助手 ${agentData.value?.name}。今天想聊点什么？无论是天马行空的想法，还是生活中的小困惑，我都在这里倾听。😊`;
    }

    messageHistory.value.push({
      role: RoleType.Assistant,
      content,
      content_type: "text",
    });
    currentMessage.value = content;
  }
  function init(data: any) {
    agentData.value = data;
    if (agentData.value.agent_log?.contents?.length > 0) {
      messageHistory.value = agentData.value.agent_log?.contents;
    } else {
      openingRemarks(); // 添加开场白数据
    }
  }

  return {
    linkEds,
    // 状态
    currentMessage,
    isResponsing,
    messageHistory,
    agentData,
    addHistory,
    clearLink,
    init,
    clearAll,
    createFileData,
    // 开启新对话
    openingRemarks,
    // 方法
    removeFile,
    resetOtherData,
    fileData,
    clearMessage,
    clearHistory,
    logId,
    reasoningContent,
  };
});
