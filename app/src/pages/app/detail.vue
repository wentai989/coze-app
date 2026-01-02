<script setup>
import { onLoad } from "@dcloudio/uni-app";
import { ref } from "vue";
import { http } from "@/api/http";
import Custom from "@/components/Custom.vue";
import LoadingSpinner from "@/components/LoadingSpinner.vue";
import { goBack } from "@/composables/common";

const id = ref(0);
const app = ref({});
const loading = ref(true);

onLoad((options) => {
  id.value = options.id;
  http
    .get(`/api/app/${id.value}`)
    .then((res) => {
      app.value = res;
    })
    .finally(() => {
      loading.value = false;
    });
});
</script>

<template>
  <!-- Use flexbox to create a layout where the bottom card is fixed at the bottom -->
  <div class="flex h-screen flex-col bg-[#0f1216] text-white">
    <!-- Scrollable main content area -->

    <LoadingSpinner v-if="loading" :overlay="true" />
    <Custom>
      <div class="flex flex-1 items-center px-4">
        <img
          src="@/static/icons/back.svg"
          class="mr-3 h-6 w-6"
          @click.stop="goBack" />
        <div class="mr-2 h-8 w-8">
          <image
            :src="app.image"
            class="h-8 w-8 rounded-full"
            mode="aspectFill" />
        </div>
        <div class="flex items-center justify-between">
          <div class="mr-2 max-w-[7.5rem] truncate text-sm">
            {{ app.name }}
          </div>
        </div>
      </div>
    </Custom>

    <main class="mt-4 overflow-auto">
      <!-- Image Display Section -->
      <section class="flex items-center justify-center">
        <img
          v-if="app.previews?.type === 'image'"
          :src="app.previews?.image"
          alt="作品预览"
          class="w-full"
          mode="widthFix" />

        <video
          v-else-if="app.previews?.type === 'video'"
          :src="app.previews?.video"
          alt="作品预览"
          class="w-full"
          mode="widthFix" />
      </section>
    </main>

    <!-- Bottom Info Card Section (will stay at the bottom) -->
    <!-- <section class="shrink-0 p-4"> -->
    <section class="shrink-0 p-4">
      <div class="rounded-2xl bg-[#14171c] p-4 shadow-xl">
        <!-- Header -->
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <span class="text-lg">{{ app.name }}</span>
          </div>
        </div>

        <!-- Description -->
        <div class="mt-4 rounded-xl bg-[#0f1216] p-3 ring-1 ring-white/10">
          <p class="text-xs leading-6 text-white/80">
            {{ app.description }}
          </p>
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 flex justify-between">
          <button
            class="flex h-11 items-center justify-center gap-2 rounded-xl bg-white/10 px-4 text-white ring-1 ring-white/20">
            <span class="font-semibold">Ƀ</span> {{ app.use_power }}
          </button>
          <button
            class="h-11 rounded-xl bg-white px-6 font-semibold text-gray-900 hover:brightness-95">
            做同款
          </button>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped></style>
