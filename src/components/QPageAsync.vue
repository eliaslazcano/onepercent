<script setup>
defineProps({
  loading: { type: Boolean, required: true },
  padding: { type: Boolean, default: false },
  color: { type: String, default: 'primary' },
  text: { type: String, default: null },
  size: { type: String, default: '32px' }
})
</script>

<template>
  <q-page class="flex flex-center column" v-if="loading" :key="1">
    <q-spinner v-if="loading" :size="size" :color="color" class="flex-center"/>
    <p class="q-mb-none q-mt-sm" v-if="text">{{ text }}</p>
  </q-page>
  <q-page :padding="padding" v-else :key="2">
    <slot></slot>
  </q-page>
</template>
