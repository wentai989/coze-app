const {
  VITE_APP_API_URL: baseURL,
  VITE_APP_ID: appId,
  VITE_APP_TYPE: appType,
} = import.meta.env;

// 请求计数器
let requestCount = 0;

// 写一个请求

// 显示加载动画
function showLoading() {
  if (requestCount === 0) {
    uni.showLoading({
      mask: true,
    });
  }
  requestCount++;
}

// 隐藏加载动画
function hideLoading() {
  requestCount--;
  if (requestCount === 0) {
    uni.hideLoading();
  }
}

// 手动重置并隐藏加载动画
function resetLoading() {
  requestCount = 0;
  uni.hideLoading();
}

// 扩展请求配置接口
type ExtendedRequestOptions = UniApp.RequestOptions & {
  hideLoading?: boolean;
};

// 请求拦截器
function requestInterceptor(config: ExtendedRequestOptions) {
  // 只有当 hideLoading 为 false 或未定义时才显示加载动画
  if (config.hideLoading === true) {
    showLoading();
  }
  //const token = "47|O0DTsiWZrtW49MTBsCmvYxTo8XpLDZQHNff03Muu7ccabd1f"; // uni.getStorageSync('token');
  const token = uni.getStorageSync("token");

  if (token) {
    config.header = {
      ...config.header,
      Authorization: `Bearer ${token}`,
    };
  }
  config.url = `${baseURL}${config.url}`;
  config.header = {
    ...config.header,
    "App-Id": appId,
    "App-Type": appType,
  };
  return config;
}

// 响应拦截器
function responseInterceptor(response: any, config: ExtendedRequestOptions) {
  // 只有当 hideLoading 为 false 或未定义时才隐藏加载动画
  if (config.hideLoading !== true) {
    hideLoading();
  }

  const { statusCode, data } = response;

  if (statusCode === 200) {
    if (data.message) {
      // 白色弹窗
      uni.showToast({
        title: data.message,
        icon: "none",
        style: {
          backgroundColor: "#ffffff", // 将背景设置为白色
        },
      });
    }
    return data.data;
  }

  if (statusCode === 401) {
    uni.$emit("loginExpired");
    uni.removeStorageSync("token");
    uni.removeStorageSync("user");

    console.log("登录已过期，清除 token");
    return Promise.reject(new Error("登录已过期"));
  }
  // 其他错误
  const error = data.message || "请求失败";
  uni.showToast({
    title: error,
    icon: "none",
  });
  return Promise.reject(error);
}

// 请求函数
function request<T = any>(options: ExtendedRequestOptions): Promise<T> {
  const config = requestInterceptor(options);

  return new Promise((resolve, reject) => {
    uni.request({
      ...config,
      success: (res) => {
        resolve(responseInterceptor(res, options));
      },
      fail: (err) => {
        // 请求失败时也需要隐藏加载动画
        if (!options.hideLoading) {
          hideLoading();
        }
        uni.showToast({
          title: "网络错误",
          icon: "none",
        });
        reject(err);
      },
    });
  });
}

// 导出请求方法
export const http = {
  get: <T = any>(url: string, params?: any, hideLoading?: boolean) => {
    return request<T>({
      url,
      method: "GET",
      data: params,
      hideLoading,
    });
  },
  post: <T = any>(url: string, data?: any, hideLoading?: boolean) => {
    return request<T>({
      url,
      method: "POST",
      data: { ...data },
      hideLoading,
      header: {
        "content-type": "application/json",
      },
    });
  },
  put: <T = any>(url: string, data?: any, hideLoading?: boolean) => {
    return request<T>({
      url,
      method: "PUT",
      data: { ...data },
      hideLoading,
      header: {
        "content-type": "application/json",
      },
    });
  },
  delete: <T = any>(url: string, params?: any, hideLoading?: boolean) => {
    return request<T>({
      url,
      method: "DELETE",
      data: params,
      hideLoading,
    });
  },
  upload: <T = any>(url: string, data?: any, hideLoading?: boolean) => {
    return request<T>({
      url,
      method: "POST",
      data: { ...data },
      hideLoading,
      header: {
        "Content-Type": "multipart/form-data",
      },
    });
  },
  // 手动隐藏加载动画
  resetLoading,
};
