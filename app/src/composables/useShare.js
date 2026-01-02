import { onShareAppMessage, onShareTimeline } from "@dcloudio/uni-app";

/**
 * 组合式分享 Hook
 * @param {Object} options 分享配置
 * @param {Function} options.title 获取标题的函数
 * @param {Function} options.path 获取路径的函数 (如果不传则默认当前页面)
 * @param {Function} options.imageUrl 获取图片的函数
 * @param {Function} options.query 获取查询参数的函数 (用于朋友圈分享)
 */
export function useShare(options = {}) {
  const {
    title = () => "",
    path = () => {
      const pages = getCurrentPages();
      const page = pages[pages.length - 1];
      return page ? `/${page.route}` : "/pages/app/index";
    },
    imageUrl = () => "",
    query = () => "",
  } = options;

  onShareAppMessage((res) => {
    if (res.from === "button") {
      console.log("来自页面内分享按钮", res.target);
    }

    const userId = uni.getStorageSync("user_id");
    let sharePath = path();

    // 自动附加 ask_id
    if (userId) {
      sharePath = `${sharePath}${
        sharePath.includes("?") ? "&" : "?"
      }ask_id=${userId}`;
    }

    return {
      title: title(),
      path: sharePath,
      imageUrl: imageUrl(),
    };
  });

  onShareTimeline(() => {
    const userId = uni.getStorageSync("user_id");
    let queryStr = query();

    // 自动附加 ask_id
    if (userId) {
      queryStr = queryStr ? `${queryStr}&ask_id=${userId}` : `ask_id=${userId}`;
    }

    return {
      title: title(),
      query: queryStr,
      imageUrl: imageUrl(),
    };
  });
}
