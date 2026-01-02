import { defineStore } from "pinia";
import { ref } from "vue";

export const useUserStore = defineStore("user", () => {
  const isLoginExpired = ref(false);
  const isPowerPay = ref(false);
  const isVipPay = ref(false);

  function setLoginExpired(expired: boolean) {
    isLoginExpired.value = expired;
  }

  function setPowerPay(expired: boolean) {
    isPowerPay.value = expired;
    console.log("isPowerPay:", isPowerPay.value);
  }

  function setVipPay(expired: boolean) {
    isVipPay.value = expired;
  }

  return {
    isLoginExpired,
    setLoginExpired,
    isPowerPay,
    setPowerPay,
    isVipPay,
    setVipPay,
  };
});
