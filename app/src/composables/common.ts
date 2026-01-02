import { http } from "@/api/http";
export function navigateBack() {
  uni.navigateBack();
}

export function isLogin() {
  const token = uni.getStorageSync("token");
  return token !== "";
}

export function navigateTo(
  url: string,
  data: any = {},
  type: string = "navigateTo"
) {
  // uni.navigateTo({url: url});
  if (type === "navigateTo") {
    uni.navigateTo({
      url,
      data,
    });
  } else if (type === "switchTab") {
    uni.switchTab({
      url,
      data,
    });
  } else if (type === "redirectTo") {
    uni.redirectTo({
      url,
      data,
    });
  } else if (type === "navigateBack") {
    navigateBack();
  }
}

export function showToast(title: string) {
  uni.showToast({
    title,
    icon: "none",
    duration: 2000,
  });
}
export function setUserToken(token: string) {
  // showToast("登录成功")
  uni.setStorageSync("token", token);
}
// 预览图片
export function previewImage(url: string) {
  uni.previewImage({
    urls: [url],
    current: 0,
  });
}

export function goBack() {
  const pages = getCurrentPages();
  if (pages.length <= 1) {
    // 没有上一页，跳转到首页
    // uni.switchTab({
    //   url: '/pages/index/index'
    // });
    uni.redirectTo({
      url: "/pages/app/index",
    });
  } else {
    // 有上一页，正常返回
    uni.navigateBack();
  }
}

export function handleLogin() {
  navigateTo("/pages/login");
}
