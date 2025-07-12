<template>
  <q-item :target="href ? '_blank': null" :href="href" :to="to" clickable>
    <q-item-section v-if="icon" avatar>
      <q-icon :name="icon" />
    </q-item-section>

    <q-item-section>
      <q-item-label>{{ title }}</q-item-label>
      <q-item-label caption>{{ caption }}</q-item-label>
    </q-item-section>
  </q-item>
</template>

<script setup>
defineProps({
  title: { type: String, required: true },
  caption: { type: String, default: '' },
  href: { type: String },
  to: { type: String },
  icon: { type: String, default: '' }
})
</script>
