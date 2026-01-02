import { ChatEventType, RoleType } from "@coze/api";
import { AbortController, CozeAPI } from "@coze/uniapp-api";
import { ref } from "vue";
import {
  careteKontClaculate,
  careteModelClaculate,
  getApiKontToken,
} from "@/api/public";
import { navigateTo, showToast } from "@/composables/common";
import { useChatStore } from "@/stores/chat";
import { useCozeStore } from "@/stores/coze";
// 这行代码没起作用
const {
  VITE_APP_API_URL: baseURL,
  VITE_APP_KEY: appKey,
  VITE_APP_TYPE: appType,
} = import.meta.env;

export function useChatLogic() {
  // 定义音频播放状态枚举
  const AudioState = {
    NORMAL: "NORMAL", // 正常状态
    LOADING: "LOADING", // 加载中
    PLAYING: "PLAYING", // 播放中
  };

  // 将布尔值改为状态值
  const audioState = ref(AudioState.NORMAL);

  // 移除原来的状态变量
  // const isPlaying = ref(false);
  // const isPlayLoading = ref(false);
  const chatStore = useChatStore();
  const controller = ref(null);
  const requestTask = ref(null); // 添加这一行
  const total_tokens = ref(0);
  const innerAudioContext = ref(null);
  async function sendKouzi() {
    chatStore.linkEds = [];
    controller.value = null;
    controller.value = new AbortController();

    chatStore.currentMessage = "";
    chatStore.isResponsing = true;
    const client = await useCozeStore().getClient(
      chatStore.agentData?.kont_space_id
    );
    if (!client) {
      return;
      // throw new Error('Coze API client is not initialized.')
    }

    try {
      //   const token = await getApiKontToken(chatStore.agentData?.kont_space_id);
      //   console.log(token);

      // console.log(client);

      const response = client.chat.stream(
        {
          bot_id: chatStore.agentData?.bot_id,
          user_id: "",
          additional_messages: chatStore.messageHistory,
          timeout: 1000 * 60 * 5,
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
          // 更新消息内容
          const newContent = chunk.data.content || "";
          chatStore.currentMessage += newContent;
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
          careteKontClaculate({
            bot_id: chunk.data.bot_id,
            kont_agent_id: chatStore.agentData?.id,
            conversation_id: chunk.data.conversation_id,
            agent_type: chatStore.agentData?.agent_type,
            kont_space_id: chatStore.agentData?.kont_space_id,
            chat_id: chunk.data.id,
            token: chunk.data?.usage?.token_count || 0,
          });
          // console.log(chunk.data);
        }
      }
      // chatStore.isResponsing = false;
    } catch (error) {
      console.log(error);
    } finally {
      if (chatStore.currentMessage) {
        chatStore.messageHistory.push({
          role: RoleType.Assistant,
          content: chatStore.currentMessage,
          content_type: "text",
        });
      }
      chatStore.isResponsing = false;
      controller.value = null;
    }
  }
  // 发送
  async function sendMessage(content) {
    if (chatStore.isResponsing) {
      return;
    }
    if (!content) {
      showToast("请输入内容");
      return;
    }
    stopMp3();
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

    if (chatStore.isModel) {
      sendModel(content);
      return;
    }
    sendKouzi();
  }
  // 中断
  const handleAbort = () => {
    showToast("中断");

    chatStore.isResponsing = false;
    controller.value?.abort();
    requestTask.value?.abort(); // 添加这一行
  };

  // 刷新当条数据
  const handleRefresh = async () => {
    chatStore.messageHistory.pop(); // 删除最后一条数据
    if (chatStore.isModel) {
      sendModel();
      return;
    }
    sendKouzi();
  };
  function handleStreamData(text) {
    text
      .trim()
      .split("\n")
      .forEach((line) => {
        // console.log(line)
        if (line.startsWith("data: ")) {
          const content = line.replace(/^data: /, "").trim();
          if (content === "[DONE]") {
            return;
          }
          try {
            const data = JSON.parse(content);
            if (data?.error) {
              showToast(data?.error?.message);
              if (data?.error?.status == 304) {
                setTimeout(() => {
                  navigateTo("/pages/user/index");
                }, 2000);
              }
              return;
            }
            if (data.usage && data.usage?.total_tokens) {
              careteModelClaculate({
                gmodel_id: chatStore.agentData?.id,
                token: data?.usage?.total_tokens || 0,
              });
            }
            if (data.choices && data.choices[0]?.delta?.content) {
              //  data.choices[0].delta.content = data.choices[0].delta.content.replace(/\\n/g, '\n');

              chatStore.currentMessage += data.choices[0].delta.content;
              // console.log(chatStore.currentMessage);
            }
          } catch (error) {
            console.error("解析数据失败:", error);
          }
        } else {
          try {
            const data = JSON.parse(text);
            if (data?.error) {
              showToast(data?.error?.message || "未知错误");
            }
          } catch (error) {
            // showToast(error?.message || '未知错误');
          }
        }
      });
  }

  // 添加 StreamRequest 函数
  function StreamRequest(data) {
    let isAborted = false;
    // let requestTask = null; // 删除这一行
    controller.value = null;

    controller.value = new AbortController();

    // 判断是否为 H5 环境
    if (process.env.UNI_PLATFORM === "h5") {
      // H5 环境使用 fetch API
      fetch(data.url, {
        method: data.method,
        headers: {
          Accept: "text/event-stream",
          "Content-Type": "application/json;charset=UTF-8",
          ...data.header,
        },
        body: JSON.stringify(data.data),
        signal: controller.value.signal, // 添加 signal
      })
        .then(async (response) => {
          const reader = response.body.getReader();
          const decoder = new TextDecoder();
          try {
            while (!isAborted) {
              const { done, value } = await reader.read();
              if (done) {
                break;
              }

              const chunk = decoder.decode(value, { stream: true });
              handleStreamData(chunk);
            }
          } catch (error) {
            console.error("数据处理错误:", error);
          }
        })
        .catch((err) => {
          console.error("请求失败:", err);
        })
        .finally(() => {
          total_tokens.value = 0;
          chatStore.isResponsing = false;
          if (chatStore.currentMessage) {
            chatStore.messageHistory.push({
              role: RoleType.Assistant,
              content: chatStore.currentMessage,
              content_type: "text",
            });
          }
        });
    } else {
      // 非 H5 环境使用原有的 uni.request

      requestTask.value = uni.request({
        // 修改这一行
        url: data.url,
        method: data.method,
        data: data.data,
        header: {
          "Content-Type": "application/json;charset=UTF-8",
          ...data.header,
        },
        // responseType: "arraybuffer",
        responseType: "text",
        enableChunked: true,
        signal: controller.value.signal, // 添加 signal
        success: (res) => {
          // showToast("接收到返回值")

          // 处理 500 错误
          //   if (res.statusCode === 500) {
          //     uni.showToast({
          //         title: '服务异常，请是后再试',  // 使用服务器返回的错误信息或默认信息
          //         icon: 'none',
          //         duration: 3000
          //       });
          //     chatStore.isResponsing = false;
          //     return;
          //   }
          if (!chatStore.isResponsing) {
            console.log("请求已被终止");
          }
        },
        fail: (err) => {
          console.error("请求失败:", err);
          chatStore.isResponsing = false;
        },
        complete: () => {
          console.log("请求完成");
          console.log("请求完成");
          console.log("请求完成");
          console.log("请求完成");
          console.log("请求完成");
          console.log("请求完成");

          chatStore.isResponsing = false;
          if (chatStore.currentMessage) {
            chatStore.messageHistory.push({
              role: RoleType.Assistant,
              content: chatStore.currentMessage,
              content_type: "text",
            });
          }
        },
      });

      requestTask.value.onChunkReceived((response) => {
        // 修改这一行
        try {
          if (!chatStore.isResponsing) {
            console.log("请求已被终止");
            return;
          }
          // 直接处理为字符串，避免 ArrayBuffer 转换
          const uint8Array = new Uint8Array(response.data);
          let text = String.fromCharCode.apply(null, uint8Array);
          text = decodeURIComponent(escape(text));
          handleStreamData(text);
        } catch (err) {
          showToast(err?.message || "未知错误");
        }
      });
    }
  }

  const sendModel = async () => {
    chatStore.isResponsing = true;
    chatStore.linkEds = [];
    chatStore.currentMessage = "";
    total_tokens.value = 0;
    // 兼容大模型鉴权
    //  await getApiKontToken(0)
    StreamRequest({
      url: `${baseURL}/api/gmodel-chat`,
      method: "POST",
      data: {
        gmodel_id: chatStore.agentData?.id,
        content: chatStore.messageHistory,
      },
      header: {
        "app-key": appKey,
        "app-type": appType,
        Authorization: `Bearer ${uni.getStorageSync("token")}`,
      },
    });
  };
  async function getValidToken() {
    let token = await useCozeStore().getKontToken();
    if (!token && chatStore.agentData?.kont_space_id) {
      await useCozeStore().getClient(chatStore.agentData.kont_space_id);
      token = await useCozeStore().getKontToken();
    }
    return token;
  }

  async function handleMp3(content) {
    // const platform = uni.getSystemInfoSync().platform;

    if (!content) {
      return;
    }
    audioState.value = AudioState.LOADING;

    const token = await getValidToken();
    if (!token) {
      showToast("获取token失败");
      return;
    }
    let chunks = [];
    // if (content.indexOf("。") > -1) {
    //   chunks = content.split("。").filter((chunk) => chunk.trim() !== "");
    // }
    // if (chunks.length === 0) {
    // }
    // content = content.replace(/！/g, "").replace(/\*/g, "");

    // chunks = content.split("，").filter((chunk) => chunk.trim() !== "");

    // 去除content里的

    // 按照字符长度分割

    content = content.replace(/[*\s—]/g, "");

    chunks = content.match(/.{1,500}/g) || [];

    // console.log(chunks);

    // chunks[0]=content;

    // 将文本按逗号分割
    // if(chunks.length > 10){
    //   showToast('文字长度超过1000字符，无法播报');
    //   return;
    // }

    let audioQueue = [];
    let isRequesting = true;
    let currentIndex = 0;

    // 创建音频上下文
    if (innerAudioContext.value) {
      stopMp3();
    }
    innerAudioContext.value = uni.createInnerAudioContext({
      useWebAudioImplement: true, // 使用 WebAudio 作为底层音频驱动
    });

    // 音频播放完成事件
    innerAudioContext.value.onEnded(() => {
      currentIndex++;
      if (currentIndex < audioQueue.length) {
        // 播放下一段
        innerAudioContext.value.src = audioQueue[currentIndex];
        innerAudioContext.value.play();
      } else if (!isRequesting) {
        // 所有音频播放完成
        audioState.value = AudioState.NORMAL;
        console.log("全部播放完成");
        innerAudioContext.value.destroy();
        innerAudioContext.value = null;
      }
    });

    // 错误处理
    innerAudioContext.value.onError((res) => {
      console.error("播放错误:", res);
      showToast("音频播放失败");
      audioState.value = AudioState.NORMAL;
      if (innerAudioContext.value) {
        innerAudioContext.value.destroy();
        innerAudioContext.value = null;
      }
    });

    // 开始请求所有音频片段
    for (let i = 0; i < chunks.length; i++) {
      try {
        const res = await new Promise((resolve, reject) => {
          uni.request({
            url: "https://api.coze.cn/v1/audio/speech",
            method: "POST",
            data: {
              // response_format: "mp3",
              input: chunks[i],
              voice_id:
                chatStore?.agentData?.kont_configs?.voice_info_list?.[0]
                  .voice_id,
              response_format: "wav",
            },
            header: {
              Authorization: `Bearer ${token}`,
              "Content-Type": "application/json",
              Accept: "audio/mpeg",
            },
            responseType: "arraybuffer",
            success: resolve,
            fail: reject,
          });
        });

        if (res.data) {
          const base64 = uni.arrayBufferToBase64(res.data);
          const audioSrc = `data:audio/mpeg;base64,${base64}`;
          audioQueue.push(audioSrc);

          // 如果是第一段音频，立即开始播放
          if (i === 0) {
            audioState.value = AudioState.PLAYING;
            innerAudioContext.value.src = audioSrc;
            innerAudioContext.value.play();

            // 记录token使用
            careteKontClaculate({
              bot_id: chatStore.agentData?.bot_id,
              kont_agent_id: chatStore.agentData?.id,
              conversation_id: 1,
              agent_type: chatStore.agentData?.agent_type,
              kont_space_id: chatStore.agentData?.kont_space_id,
              is_mp3: 1,
              chat_id: 1,
              token: content.length,
            });
          }
        }
      } catch (err) {
        console.error("请求失败:", err);
        showToast("语音合成请求失败");
        audioState.value = AudioState.NORMAL;
        if (innerAudioContext.value) {
          innerAudioContext.value.destroy();
          innerAudioContext.value = null;
        }
        return;
      }
    }

    isRequesting = false;
  }

  const stopMp3 = () => {
    if (innerAudioContext.value) {
      innerAudioContext.value.stop();
      innerAudioContext.value.pause();
      innerAudioContext.value.destroy(); // 销毁实例释放资源
      innerAudioContext.value = null;
      audioState.value = AudioState.NORMAL;
    }
  };

  return {
    stopMp3,
    handleMp3,
    sendMessage,
    handleAbort,
    audioState,
    AudioState,
    handleRefresh,
  };
}
