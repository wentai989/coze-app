<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  items: any[]
  columns?: number
  gapClass?: string
  containerClass?: string
  itemWrapperClass?: string
  itemKey?: (item: any, index: number, colIndex: number) => string | number
  distribute?: 'even' | 'balanced'
}

const props = withDefaults(defineProps<Props>(), {
  columns: 2,
  gapClass: 'gap-3',
  containerClass: '',
  itemWrapperClass: '',
  distribute: 'even',
})

function getKey(item: any, index: number, colIndex: number) {
  if (props.itemKey) { return props.itemKey(item, index, colIndex) }
  return item?.id ?? `${colIndex}-${index}`
}

// 简易分发：按索引奇偶分到各列；需要更均衡可切换为 balanced
const cols = computed(() => {
  const arr: any[][] = Array.from({ length: props.columns }, () => [])
  if (props.distribute === 'even') {
    props.items?.forEach((it, i) => arr[i % props.columns].push(it))
    return arr
  }
  // 预留 balanced（根据预估高度分配），默认先走 even
  props.items?.forEach((it, i) => arr[i % props.columns].push(it))
  return arr
})
</script>

<template>
  <div class="flex" :class="[gapClass, containerClass]">
    <div
      v-for="(col, cIndex) in cols"
      :key="cIndex"
      class="flex-1"
    >
      <div
        v-for="(item, index) in col"
        :key="getKey(item, index, cIndex)"
        :class="itemWrapperClass"
      >
        <slot name="default" :item="item" :index="index" :col-index="cIndex" />
      </div>
    </div>
  </div>
</template>
