import { defineConfig } from "vite";
import uni from "@dcloudio/vite-plugin-uni";
import { UnifiedViteWeappTailwindcssPlugin as uvwt } from 'weapp-tailwindcss/vite';

export default defineConfig({
  plugins: [uni(), uvwt()],

  css: {
    preprocessorOptions: {
      scss: {
        api: 'modern-compiler',
        sassOptions: {
          outputStyle: 'compressed'
        }
      }
    },
    postcss: {
      plugins: [
        require('tailwindcss'),
        require('autoprefixer')
      ],
    }
  },
  resolve: {
    alias: {
      crypto: 'crypto-js'
    }
  }
});
