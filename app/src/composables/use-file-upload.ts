import { ref } from "vue";
import { isLogin, showToast } from "./common";

const {
  VITE_APP_API_URL: baseURL,
  VITE_APP_ID: appId,
  VITE_APP_TYPE: appType,
} = import.meta.env;

const url = "/api/upload";

export function useFileUpload() {
  const fileId = ref("");
  const imagePath = ref("");
  const isUploading = ref(false);
  const spaceId = ref(0);
  const fileData = ref({
    // id: "",
    // file_name: "",
    file_url: "",
    file_type: "",
  });
  const fileType = ref("file");
  const chooseImage = () => {
    if (isUploading.value) {
      return;
    }
    uni.chooseImage({
      count: 1,
      sizeType: ["original", "compressed"],
      sourceType: ["album", "camera"],
      success: (res) => {
        const tempFilePath = res.tempFilePaths[0];
        imagePath.value = tempFilePath;
        // console.log('tempFilePath', tempFile, tempFilePath);
        // uploadKouzi(isH5 ? tempFile : tempFilePath);
        // uploadKouzi(isH5 ? tempFile : tempFilePath);
        uploadOss(imagePath.value);
      },
    });
  };

  const uploadOss = async (filePath: string) => {
    // if (!isLogin()) {
    //   return
    // }
    // 上传到我的api里

    // 加载动画
    uni.showLoading({
      // title: '',
    });
    const token = uni.getStorageSync("token");
    uni.uploadFile({
      url: `${baseURL}${url}`,
      filePath,
      header: {
        Authorization: `Bearer ${token}`,
        "App-Id": appId,
        "app-type": appType,
      },
      name: "file",
      success: (res) => {
        uni.hideLoading();
        const data = JSON.parse(res.data);
        if (data.code === 200) {
          fileData.value = {
            // id: result.id,
            // file_name: result.file_name,
            file_url: data.data,
            file_type: fileType.value,
          };

          return fileData.value;
        }

        if (data.code === 401) {
          uni.removeStorageSync("token");
          uni.removeStorageSync("user");
          uni.$emit("loginExpired");
          console.log("登录已过期，清除 token");
          return Promise.reject(new Error("登录已过期"));
        }

        showToast("上传失败");
      },
      fail: (err) => {
        uni.hideLoading();
        console.error("文件上传失败：", err);
        showToast("上传失败");
      },
    });
  };

  const chooseFile = (
    type: "image" | "video" | "file" = "file",
    sourceType: "album"
  ) => {
    if (isUploading.value) {
      return;
    }
    if (type === "video") {
      fileType.value = "video";
      uni.chooseVideo({
        sourceType: ["camera", "album"],
        success(res) {
          console.log("tempFilePath", res.tempFilePath);
          uploadOss(res.tempFilePath);
        },
      });
    } else if (type === "image") {
      fileType.value = "image";
      uni.chooseImage({
        count: 1,
        sizeType: ["original", "compressed"],
        sourceType: [sourceType],
        extension: ["jpg", "png", "jpeg", "gif", "bmp"],
        success: (res) => {
          const tempFilePath = res.tempFilePaths[0];
          const tempFile = (res.tempFiles as File[])[0];

          imagePath.value = tempFilePath;
          console.log("tempFilePath", tempFile, tempFilePath);
          uploadOss(imagePath.value);
        },
      });
    } else {
      fileType.value = "file";
      // 根据平台使用不同的选择文件 API
      if (appType === "h5") {
        uni.chooseFile({
          count: 1,
          type: "all",
          success: (res) => {
            const tempFilePath = res.tempFilePaths[0];
            imagePath.value = tempFilePath;
            uploadOss(imagePath.value);
          },
        });
      } else {
        // 小程序环境使用 chooseMessageFile
        uni.chooseMessageFile({
          count: 1,
          type: "file", // 改为 file，这样就只能选择文档类型的文件
          success: (res) => {
            const tempFilePath = res.tempFiles[0].path;
            console.log("tempFilePath", res.tempFiles[0]);
            imagePath.value = tempFilePath;
            uploadOss(imagePath.value);
          },
          fail: (err) => {
            console.error("Choose file failed:", err);
            uni.showToast({
              title: "Choose file failed",
              icon: "none",
            });
          },
        });
      }
    }
  };

  return {
    fileId,
    imagePath,
    isUploading,
    fileData,
    spaceId,
    chooseFile, // 导出新的方法
    chooseImage, // 保留原来的方法以保持兼容性
  };
}
