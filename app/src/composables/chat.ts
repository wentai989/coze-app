import { ChatEventType, RoleType } from "@coze/api";
import { showToast } from "@/composables/common";
import { useChatStore } from "@/stores/chat";
import { useCozeStore } from "@/stores/coze";
import { AbortController } from "@coze/uniapp-api";
import { http } from "@/api/http";
import { ref } from "vue";
const chatStore = useChatStore();
const controller = ref<any>(null);

// 复制内容功能
export function copyContent(content: string) {
  uni.setClipboardData({
    data: content,
    success: () => {
      showToast("复制成功");
    },
    fail: () => {
      showToast("复制失败");
    },
  });
}
export function formatResponse(reasoning = "", message = "") {
  let content = "";
  if (reasoning) {
    content += `> <span style="font-size: 16px; color: #888;">深度思考</span>\n> \n${reasoning
      .split("\n")
      .map(
        (line) =>
          '> <span style="font-size: 14px; color: #666; display: block; width: 100%; margin-bottom: 4px;">' +
          line +
          "</span>"
      )
      .join("\n")}\n\n`;
  }
  if (message) {
    content += message;
  }
  return content;
}
export function isLastMessage(index: number) {
  return index == chatStore.messageHistory.length - 1 && index > 1;
}
//刷新当条数据
export function handleRefresh() {
  chatStore.messageHistory.pop(); // 删除最后一条数据
  sendKouzi();
}
export function handlePay() {
  sendKouzi();
}
export function textToObj(item: any) {
  if (item.content_type === "object_string") {
    return JSON.parse(item.content);
  }
  return [
    {
      text: item.content,
      type: "text",
    },
  ];
}

// 开启新对话
export function handleNewChat() {
  if (chatStore.isResponsing) {
    showToast("请在对话结束后再试");
    return;
  }

  chatStore.resetOtherData(); // 清理所有记录
  chatStore.openingRemarks(); // 添加开场白数据
}

export function sendMessage(content: string) {
  if (chatStore.isResponsing) {
    return;
  }
  if (!content) {
    showToast("请输入");
    return;
  }

  let kontContent = content;

  // 处理文件数据
  if (chatStore.fileData.length > 0) {
    let objectFileData = [];
    chatStore.fileData.forEach((item) => {
      objectFileData.push({
        type: item.file_type,
        //  file_id: item.id,
        // file_name: item.file_name,
        file_url: item.file_url,
      });
    });

    objectFileData.push({
      type: "text",
      text: content,
    });

    kontContent = JSON.stringify(objectFileData);
    chatStore.messageHistory.push({
      role: RoleType.User,
      content: kontContent,
      content_type: "object_string",
    });
    chatStore.fileData = []; // 清空
  } else {
    chatStore.messageHistory.push({
      role: RoleType.User,
      content: kontContent,
      content_type: "text",
    });
  }

  sendKouzi();
}

async function sendKouzi() {
  chatStore.linkEds = [];
  controller.value = null;
  controller.value = new AbortController();

  chatStore.currentMessage = "";
  chatStore.reasoningContent = "";
  chatStore.isResponsing = true;

  const client = await useCozeStore().getClient(
    chatStore.agentData?.kont_id,
    chatStore.agentData?.is_vip
  );

  if (!client) {
    chatStore.isResponsing = false;
    // throw new Error("Coze API client is not initialized.");
    return;
  }

  try {
    //   const token = await getApiKontToken(chatStore.agentData?.kont_space_id);
    //   console.log(token);

    // console.log(client);
    chatStore.linkEds = [];
    const messages = chatStore.messageHistory.map((item: any) => {
      const { reasoning_content, ...rest } = item;
      return rest;
    });

    const response = (client as any).chat.stream(
      {
        bot_id: chatStore.agentData?.bot_id,
        user_id: "",
        additional_messages: messages,
        // timeout: 1000 * 60 * 5,
      },
      {
        signal: controller.value?.signal,
      }
    );

    //  // 使用异步迭代器处理流
    for await (const chunk of response) {
      if (!chatStore.isResponsing) {
        break;
      }
      if (chunk.event === ChatEventType.CONVERSATION_CHAT_CREATED) {
        //    //异步注册，不等待结果
        // careateChatReg({
        //   bot_id: chunk.data.bot_id,
        //   kont_agent_id: chatStore.agentData?.id,
        //   conversation_id: chunk.data.conversation_id,
        //   agent_type: chatStore.agentData?.agent_type,
        //   kont_space_id: chatStore.agentData?.kont_space_id,
        //   chat_id: chunk.data.id
        // }).catch(error => {
        //   console.error('注册失败:', error);
        //   // 可以在这里添加错误处理逻辑
        // });
      }
      //  检查是否已中断
      // 处理消息
      if (chunk.event === ChatEventType.CONVERSATION_MESSAGE_DELTA) {
        chatStore.reasoningContent += chunk.data.reasoning_content || "";
        // 更新消息内容
        chatStore.currentMessage += chunk.data.content || "";
      }
      if (chunk.event === ChatEventType.CONVERSATION_MESSAGE_COMPLETED) {
        // putChatRegister({
        //   bot_id: chunk.data.bot_id,
        //   kont_agent_id: chatStore.agentData?.id,
        //   conversation_id: chunk.data.conversation_id,
        // });
        // 更新联想数据
        if (chunk.data.type === "follow_up" && chunk.data.content) {
          chatStore.linkEds.push(chunk.data.content);
        }
      }

      if (chunk.event === ChatEventType.CONVERSATION_CHAT_COMPLETED) {
        http.post("/api/power-deduction-log", {
          bot_id: chunk.data.bot_id,
          id: chatStore.agentData?.id,
          chat_id: chunk.data.id,
          token: chunk.data?.usage?.token_count || 0,
          deduction_type: "agent",
        });
        // console.log(chunk.data);
      }
    }
    // chatStore.isResponsing = false;
  } catch (error) {
    console.log(error);
  } finally {
    chatStore.isResponsing = false;

    if (chatStore.currentMessage) {
      chatStore.messageHistory.push({
        role: RoleType.Assistant,
        content: chatStore.currentMessage,
        reasoning_content: chatStore.reasoningContent,
        content_type: "text",
      });
    }
    controller.value = null;
  }
}
