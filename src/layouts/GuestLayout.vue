<script setup>
import Package from '../../package.json'
</script>

<template>
  <q-layout class="app-background-color">
    <q-page-container>
      <router-view v-slot="{ Component }">
        <transition
          appear
          mode="out-in"
          enter-active-class="animated fadeIn fast"
          leave-active-class="animated fadeOut fast"
        >
          <component :is="Component" />
        </transition>
      </router-view>
    </q-page-container>

    <q-footer bordered :class="{'bg-dark':$q.dark.isActive}" class="q-pa-xs">
      <div class="flex justify-between text-caption">
        <div>&copy; Elias Lazcano</div>
        <div>v{{ Package.version }}</div>
      </div>
    </q-footer>

    <q-page-sticky position="top-right" :offset="[16,16]" class="z-fab">
      <q-btn
        color="primary"
        :icon="$q.dark.isActive ? 'wb_incandescent' : 'dark_mode'"
        @click="$q.dark.toggle()"
        push round
      >
        <q-tooltip anchor="center left" self="center right" :offset="[10, 10]">
          TEMA {{ $q.dark.isActive ? 'CLARO' : 'ESCURO' }}
        </q-tooltip>
      </q-btn>
    </q-page-sticky>
  </q-layout>
</template>

<style scoped>
body:not(.body--dark) .app-background-color:not(.dark) {
  background: linear-gradient(90deg, #0072ff 0%, #00d4ff 100%);
}
</style>
