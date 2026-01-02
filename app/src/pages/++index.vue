<script setup>
import { onMounted, ref, watch } from 'vue'
import { http } from '@/api/http'
import BottomNav from '@/components/BottomNav.vue'
import Custom from '@/components/Custom.vue'

import LoadingSpinner from '@/components/LoadingSpinner.vue'
import Waterfall from '@/components/Waterfall.vue'

import { navigateTo } from '@/composables/common'

// const cubes = ref([])
// http.get('/api/cubes').then((res) => {
//   cubes.value = res
// })
const apps = ref([])
const categories = ref([])
const categoryId = ref(0)
const searchKey = ref('')
const loading = ref(true)
const banners = ref([])
const bannerHeight = ref(0)
function getApps() {
  apps.value = []
  loading.value = true
  http.get('/api/apps', {
    page: 1,
    page_size: 10000,
    categorie_id: categoryId.value,
    search_key: searchKey.value,
  }).then((res) => {
    apps.value = res
  }).finally(() => {
    // 请求结束后，无论成功失败都关闭加载动画
    loading.value = false
  })

  http.get('/api/banners').then((res) => {
    banners.value = res
  })
}

onMounted(() => {
  getApps()

  http.get('/api/categories', {
  }, { is_admin: true }).then((res) => {
    // 追加个发现
    categories.value = [
      { id: 0, name: '发现' },
      ...res,
    ]
  })
  const { screenWidth } = uni.getSystemInfoSync()
  bannerHeight.value = Math.round(screenWidth * 0.5625) // 16:9
})
function handleCategory(id) {
  categoryId.value = id
  getApps()
}

function debounce(fn, delay = 400) {
  let timer
  return (...args) => {
    clearTimeout(timer)
    timer = setTimeout(() => fn(...args), delay)
  }
}

const debouncedGetApps = debounce(() => {
  getApps()
}, 400)

watch(searchKey, (newVal, oldVal) => {
  if (newVal === oldVal) { return }
  debouncedGetApps()
})
</script>

<template>
  <div class="min-h-screen w-full bg-[#0f1216] text-white">
    <Custom>
      <div class="px-4">
        <div
          class="
            flex flex-grow items-center rounded-full bg-white/15 px-4 py-2
            ring-1 ring-white/20 backdrop-blur-md
          "
        >
          <img src="@/static/icons/search-2-line.svg" class="mr-2 h-4 w-4">
          <input
            v-model="searchKey"
            class="flex-1 text-sm text-gray-500 outline-none"
            placeholder="搜索应用"
            type="text"
          >
        </div>
      </div>
    </Custom>

    <div class="mx-auto max-w-[var(--safe-max)]">
      <div :style="{ height: `${bannerHeight}px` }" class="mt-3 w-full">
        <swiper
          class="h-full"
          indicator-dots
          autoplay
          circular
          interval="3000"
          duration="500"
        >
          <swiper-item v-for="(banner, index) in banners" :key="index">
            <image :src="banner.image" class="h-full w-full" mode="aspectFill" />
          </swiper-item>
        </swiper>
      </div>

      <!-- 顶部 Tab 栏 -->
      <header class="sticky top-0 z-40 bg-[#14171c]/80 backdrop-blur-md">
        <div class="flex items-center justify-between px-4 py-3">
          <nav class="flex-1 overflow-x-auto">
            <ul class="flex min-w-max items-center gap-5">
              <li
                v-for="(category, index) in categories"
                :key="index"
                @click="handleCategory(category.id)"
              >
                <div
                  class="inline-flex items-center gap-1 font-semibold"

                  :class="category.id === categoryId ? 'text-lg' : `text-sm`"
                >
                  {{ category.name }}
                </div>
              </li>
            </ul>
          </nav>
        </div>
      </header>

      <!-- 瀑布流内容区 -->
      <main class="px-3 pb-20">
        <!-- Masonry：两列，随内容自适应高度 -->
        <div v-if="loading" class="mt-20">
          <LoadingSpinner />
        </div>

        <!-- 使用封装好的瀑布流组件 -->
        <Waterfall
          :items="apps"
          :columns="2"
          gapClass="gap-3"
          itemWrapperClass="mb-3 overflow-hidden rounded-xl bg-black/20 ring-1 ring-white/10"
        >
          <template #default="{ item }">
            <article @click="navigateTo(`/pages/app/detail?id=${item.id}`)">
              <div class="relative">
                <image
                  :src="item.image"
                  class="w-full"
                  mode="widthFix"
                />
                <div
                  class="
                    absolute bottom-2 left-2 rounded-full bg-black/50 px-2 py-1
                    text-sm font-medium text-white
                  "
                >
                  {{ item.name }}
                </div>
                <!-- <div class="absolute bottom-1 left-0 right-0">
                  <div class="rounded-t-lg bg-black/60 px-3 py-2 text-white">
                    <p class="text-xs font-semibold">
                      {{ item.name }}
                    </p>
                  </div>
                </div> -->
              </div>
            </article>
          </template>
        </Waterfall>
      </main>

      <div
        v-if="apps?.length === 0 && !loading"
        class="
          mt-3 flex h-[50vh] w-full items-center justify-center gap-2 text-sm
        "
      >
        <img src="@/static/icons/error-warning-line.svg" class="h-5 w-5">
        <p class="text-gray-500">
          没有更多内容了
        </p>
      </div>
    </div>
    <BottomNav />
  </div>
</template>
