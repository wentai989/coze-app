const { VITE_APP_API_URL: baseURL } = import.meta.env

// //获取轮播图
// export const getBanners = async (params:any) => {
//     return await http.get('/api/banners', params)
// }
// 获取分享设置

export function uploadFile(filePath: string): Promise<any> {
  const token = uni.getStorageSync('token')
  uni.showLoading({
  })

  return new Promise((resolve, reject) => {
    uni.uploadFile({
      url: `${baseURL}/api/upload`,
      filePath,
      header: {
        Authorization: `Bearer ${token}`,
      },
      name: 'file',
      success: (res) => {
        uni.hideLoading()
        const data = JSON.parse(res.data)
        if (data.code === 200) {
          console.log(data.data)
          resolve(data.data) // 返回上传成功的数据
        }
        else {
          uni.showToast({
            title: data.message,
            icon: 'none',
          })
          reject(data.message) // 返回错误信息
        }
      },
      fail: (err) => {
        uni.showToast({
          title: '图片上传失败',
          icon: 'none',
        })
        reject(err) // 返回错误信息
      },
    })
  })
}
