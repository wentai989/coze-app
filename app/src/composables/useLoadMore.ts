import { onReachBottom } from '@dcloudio/uni-app'
import { ref } from 'vue'

export function useLoadMore(loadData: (page: number) => Promise<any[]>) {
  const currentPage = ref(1)
  const loadMoreStatus = ref('more')
  const list = ref<any[] | null>(null)
  const isLoading = ref(false) // 添加加载锁

  const loadMore = async (page = 1) => {
    if (isLoading.value) { return } // 防止重复加载
    try {
      isLoading.value = true
      loadMoreStatus.value = 'loading'
      console.log('开始加载第', page, '页')

      const result = await loadData(page)
      console.log('加载结果:', result)

      if (result.length === 0) {
        loadMoreStatus.value = 'noMore'
        return
      }

      if (page === 1) {
        list.value = result
      }
      else if (list.value) {
        list.value = [...list.value, ...result]
      }

      loadMoreStatus.value = 'more'
    }
    catch (error) {
      console.error('加载数据失败:', error)
      loadMoreStatus.value = 'more'
    }
    finally {
      isLoading.value = false
    }
  }

  // 监听页面触底事件
  onReachBottom(() => {
    console.log('触发触底事件', loadMoreStatus.value)
    if (loadMoreStatus.value === 'more') {
      currentPage.value++
      loadMore(currentPage.value)
    }
  })

  const refresh = () => {
    list.value = null
    currentPage.value = 1
    loadMore(1)
  }

  return {
    list,
    loadMoreStatus,
    currentPage,
    isLoading,
    refresh,
    loadMore,
  }
}
