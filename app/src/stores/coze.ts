import { CozeAPI } from "@coze/uniapp-api";
import { defineStore } from "pinia";
import { ref } from "vue";
import { http } from "@/api/http";
// 本来是决定 持久化 扣子Cli的，避免重复加载，但是考虑到TOken问题，这边弃用了。
export const useCozeStore = defineStore("coze", () => {
  const cozeClient = ref<CozeAPI | null>(null);
  const kontId = ref<number>(0);
  const isVip = ref<boolean>(false);
  const access_token = ref<string>("");

  const getKontToken = () => {
    return access_token.value;
  };

  const getClient = async (id: number, vip: boolean = false) => {
    kontId.value = id;
    isVip.value = vip;
    try {
      const res: any = await http.post(`/api/kont/${kontId.value}/token`, {
        is_vip: isVip.value,
      });

      if (res.code === 304) {
        uni.showToast({
          title: res.message,
          icon: "none",
          duration: 3000,
        });
        uni.$emit("isPowerPay");
        cozeClient.value = null;
        return null;
      }

      if (res.code === 305) {
        uni.showToast({
          title: res.message,
          icon: "none",
          duration: 3000,
        });
        uni.$emit("isVipPay");
        cozeClient.value = null;
        return null;
      }

      access_token.value = res.access_token;
      cozeClient.value = new CozeAPI({
        baseURL: import.meta.env.VITE_COZE_BASE_URL,
        token: access_token.value || "",
        allowPersonalAccessTokenInBrowser: true, // only for test
        axiosOptions: {
          timeout: 300 * 1000, // 5 分钟 http 超时
        },
      });
    } catch (error) {
      // console.error("创建 CozeAPI 实例失败:", error);
      cozeClient.value = null;
    }

    return cozeClient.value;
  };

  const closeCoze = async () => {
    cozeClient.value = null;
  };

  return {
    cozeClient, // 可以选择不直接暴露 client ref
    getClient,
    kontId,
    getKontToken,
    closeCoze,
  };
});
