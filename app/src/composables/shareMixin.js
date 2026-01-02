//全局分享配置

import { http } from "@/api/http";
export default {
  data() {
    return {
      shareInfo: null,
      askId: "",
    };
  },

  async onLoad(query) {
    console.log("query", query);
    if (query?.ask_id) {
      uni.setStorageSync("ask_id", query.ask_id); //保存推荐人ID
    }
    // 检查缓存
    const cachedShareInfo = uni.getStorageSync("share_info");
    const cachedTime = uni.getStorageSync("share_info_time");
    const now = Date.now();
    const oneDayMs = 48 * 60 * 60 * 1000;

    // 如果缓存存在且未过期（小于2天），直接使用缓存
    if (cachedShareInfo && cachedTime && now - cachedTime < oneDayMs) {
      this.shareInfo = cachedShareInfo;
    } else {
      // 缓存不存在或已过期，重新获取并缓存
      const res = await http.get("/api/share", {});
      this.shareInfo = res || {};
      uni.setStorageSync("share_info", this.shareInfo);
      uni.setStorageSync("share_info_time", now);
    }
  },

  // 分享给朋友
  onShareAppMessage() {
    const path = this.shareInfo?.path;
    const ask_id = uni.getStorageSync("user_id");
    const sharePath = ask_id ? `${path}?ask_id=${ask_id}` : path;
    return {
      title: this.shareInfo?.title || "默认标题",
      path: sharePath,
      imageUrl: this.shareInfo?.image || "",
      success: () => {
        console.log("分享成功");
      },
      fail: () => {
        console.log("分享失败");
      },
    };
  },

  // 分享到朋友圈
  onShareTimeline() {
    const ask_id = uni.getStorageSync("user_id");
    return {
      title: this.shareInfo?.title || "默认标题",
      query: ask_id ? `ask_id=${ask_id}` : "",
      imageUrl: this.shareInfo?.image || "",
      success: () => {
        console.log("分享成功");
      },
      fail: () => {
        console.log("分享失败");
      },
    };
  },
};
