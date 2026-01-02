/**
 * 设置本地存储，支持过期时间
 * @param {string} key 存储键名
 * @param {any} value 存储的值
 * @param {number} expire 过期时间（秒），默认0表示永久有效
 */
export function setStorage(key: any, value: any, expire = 0) {
  const obj = {
    data: value, // 存储的数据
    time: Date.now() / 1000, // 存储时的时间戳（秒）
    expire, // 过期时间（秒）
  }
  uni.setStorageSync(key, JSON.stringify(obj))
}

/**
 * 获取本地存储
 * @param {string} key 存储键名
 * @returns {any} 存储的值，如果已过期或不存在则返回 null
 */
export function getStorage(key: any) {
  let val = uni.getStorageSync(key)
  if (!val) { return null }

  val = JSON.parse(val)
  if (val.expire && Date.now() / 1000 - val.time > val.expire) {
    uni.removeStorageSync(key)
    return null
  }
  return val.data
}
