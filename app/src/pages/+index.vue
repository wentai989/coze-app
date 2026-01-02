<script setup>
import { onMounted, ref } from 'vue'
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
// 新增：加载状态
const loading = ref(true)

onMounted(() => {
  http.get('/api/apps', {
    is_home: 1,
    page: 1,
    page_size: 6,
  }).then((res) => {
    apps.value = res
  }).finally(() => {
    // 请求结束后，无论成功失败都关闭加载动画
    loading.value = false
  })
})
</script>

<template>
  <div class="relative min-h-screen">
    <!-- <video
      class="fixed inset-0 h-full w-full object-cover"
      src="https://aiagent.s3.bitiful.net/bg.mp4"
      autoplay
      loop
      muted
      playsinline
      object-fit="cover"
      :controls="false"
      :show-fullscreen-btn="false"
      :show-play-btn="false"
      :show-center-play-btn="false"
    /> -->
    <image
      class="pointer-events-none fixed inset-0 h-full w-full object-cover"
      src="@/static/bj.gif"
      mode="aspectFill"
      lazy-load
    />
    <div
      class="
        fixed inset-0 z-10 bg-gradient-to-t from-black/50 via-black/30
        to-transparent/20
      "
    />
    <div
      class="
        relative z-20 mx-auto flex min-h-screen max-w-[var(--safe-max)] flex-col
        px-4 pb-24 pt-6
      "
    >
      <!-- 顶部问候与图标 -->

      <Custom>
        <div class="flex items-center justify-between px-4">
          <div class="text-lg font-bold text-white">
            糯米智创
          </div>
          <div class="flex items-center gap-3">
            <div
              class="
                grid h-9 w-9 place-items-center rounded-full border
                border-white/30 bg-white/15 text-white transition
              "
            >
              <img
                src="@/static/icons/notification-line.svg" alt="" class="
                  h-5 w-5
                "
              >
            </div>
          </div>
        </div>
      </custom>

      <div class="h-[20rem]" />
      <!-- 导航：居中，磨砂玻璃效果（取消固定定位） -->
      <!-- <div
        class="
          mx-auto mt-auto grid w-[100%] max-w-[var(--safe-max)] grid-cols-3
          gap-3
        "
      >

      <div
        v-for="(cube, index) in cubes"
        :key="index"
        class="
          flex flex-col items-center justify-center rounded-2xl border
          border-white/25 bg-white/15 px-4 py-3 text-white transition
        "
      >
        <div class="flex items-center justify-center gap-1">
          <div
            v-if="cube.icon"
            :style="{ backgroundImage: `url(${cube.icon})` }"
            class="h-5 w-5 bg-cover bg-center"
          />
          <span class="text-xs">{{ cube.name }}</span>
        </div>
      </div>
    </div>
-->

      <div class="z-20">
        <!-- 分区标题 -->
        <section class="mt-8 flex items-center justify-between py-3">
          <h2 class="text-lg font-bold text-white">
            热门推荐
          </h2>
          <div
            class="
              rounded-full border border-white/25 bg-white/15 px-3 py-1 text-xs
              text-white
            "
            @click="navigateTo(`/pages/app/index`, {}, 'switchTab')"
          >
            更多
          </div>
        </section>

        <!-- 加载转圈（组件） -->
        <LoadingSpinner v-if="loading" class="mt-20" />

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

        <!-- 底部导航 -->
        <BottomNav />
      </div>
    </div>
  </div>
</template>

<style>

</style>
