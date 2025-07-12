<template>
  <q-layout view="hHh Lpr fFf" :class="{'bg-grey-1': !$q.dark.isActive}">
    <q-header
      :elevated="!$q.dark.isActive"
      :bordered="$q.dark.isActive"
      :class="{'bg-dark':$q.dark.isActive}"
    >
      <q-toolbar>
        <q-btn icon="menu" aria-label="Menu" @click="toggleLeftDrawer" flat dense round />

        <q-toolbar-title>Quasar App</q-toolbar-title>

        <q-btn flat rounded dense icon="more_vert">
          <q-menu>
            <q-list style="min-width: 100px">
              <q-item clickable v-close-popup>
                <q-item-section avatar>
                  <q-icon name="account_circle" />
                </q-item-section>
                <q-item-section>Conta</q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="$q.dark.toggle()">
                <q-item-section avatar>
                  <q-icon v-if="$q.dark.isActive" name="wb_incandescent" />
                  <q-icon v-else name="dark_mode" />
                </q-item-section>
                <q-item-section v-if="$q.dark.isActive">Tema claro</q-item-section>
                <q-item-section v-else>Tema escuro</q-item-section>
              </q-item>
              <q-separator />
              <q-item clickable v-close-popup @click="sessionStore.logout(true)">
                <q-item-section avatar>
                  <q-icon color="red" name="logout" />
                </q-item-section>
                <q-item-section>Desconectar</q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>
      </q-toolbar>
    </q-header>

    <q-drawer v-model="leftDrawerOpen" show-if-above bordered>
      <q-list>
        <q-item>
          <q-item-section avatar>
            <q-avatar color="primary" text-color="white">
              <q-icon name="person" size="28px" />
            </q-avatar>
          </q-item-section>
          <q-item-section>
            <q-item-label lines="1">Quasar App</q-item-label>
            <q-item-label caption lines="1">Starter Kit</q-item-label>
          </q-item-section>
        </q-item>
        <q-separator/>
        <q-item-label header>Menu</q-item-label>
        <QItemLink v-for="link in linksList" :key="link.title" v-bind="link" />
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view v-slot="{ Component }">
        <transition
          appear
          mode="out-in"
          enter-active-class="animated fadeIn faster"
          leave-active-class="animated fadeOut faster"
        >
          <component :is="Component" />
        </transition>
      </router-view>
    </q-page-container>

    <q-footer bordered :class="{'bg-dark':$q.dark.isActive}" class="q-pa-xs">
      <div class="flex justify-between text-caption">
        <div>SID 0</div>
        <div>v{{ Package.version }}</div>
      </div>
    </q-footer>
  </q-layout>
</template>

<script setup>
import { ref } from 'vue'
import { useSessionStore } from 'stores/session.js'
import Package from '../../package.json'
import QItemLink from 'components/QItemLink.vue'

const sessionStore = useSessionStore()

const leftDrawerOpen = ref(false)
const toggleLeftDrawer = () => leftDrawerOpen.value = !leftDrawerOpen.value

const linksList = [
  {
    title: 'Docs',
    caption: 'quasar.dev',
    icon: 'school',
    href: 'https://quasar.dev'
  },
  {
    title: 'Github',
    caption: 'github.com/quasarframework',
    icon: 'code',
    href: 'https://github.com/quasarframework'
  },
  {
    title: 'Discord Chat Channel',
    caption: 'chat.quasar.dev',
    icon: 'chat',
    href: 'https://chat.quasar.dev'
  },
  {
    title: 'Forum',
    caption: 'forum.quasar.dev',
    icon: 'record_voice_over',
    href: 'https://forum.quasar.dev'
  },
  {
    title: 'Twitter',
    caption: '@quasarframework',
    icon: 'rss_feed',
    href: 'https://twitter.quasar.dev'
  },
  {
    title: 'Facebook',
    caption: '@QuasarFramework',
    icon: 'public',
    href: 'https://facebook.quasar.dev'
  },
  {
    title: 'Quasar Awesome',
    caption: 'Community Quasar projects',
    icon: 'favorite',
    href: 'https://awesome.quasar.dev'
  }
]
</script>
