<script setup lang="ts">
import CmpToast from '../vue/Components/CmpToast.vue'
</script>

<template>
  <UApp :toaster="{ position: 'top-right' }">
    <router-view />
    <CmpToast />
  </UApp>
</template>