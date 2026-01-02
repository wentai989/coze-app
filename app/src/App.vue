<script setup lang="ts">
// import { onLaunch, onShow, onHide } from "@dcloudio/uni-app";

// onLaunch(() => {
//   console.log("App Launch");
// });
// onShow(() => {
//   console.log("App Show");
// });
// onHide(() => {
//   console.log("App Hide");
// });
</script>

<style lang="scss">
@use "tailwindcss/base";
@use "tailwindcss/components";
@use "tailwindcss/utilities";

// @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css');
/*  #ifdef  H5  */
svg {
  display: initial;
}
/*  #endif  */
page {
  background-color: #0f1216;
}
@layer components {
  .raw-btn {
    // 注意 after: 后面不能加任何空格，有些格式化工具可能会在这里自动加一个空格
    @apply after:border-none inline-flex items-center gap-2 rounded text-sm font-semibold transition-all;
  }
  .btn {
    // 使用上面定义的 raw-btn
    @apply raw-btn bg-gradient-to-r from-[#9e58e9] to-blue-500 px-2 py-1 text-white;
  }
}
uni-textarea {
  width: 100% !important;
}
wx-textarea {
  width: 100% !important;
}
</style>
