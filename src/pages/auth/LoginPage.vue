<script setup>
import { ref, reactive } from 'vue'
import { useSessionStore } from 'stores/session.js'
import { useRouter, useRoute } from 'vue-router'
import { useQuasar } from 'quasar'
//import { api } from 'boot/axios'

const sessionStore = useSessionStore()
const router = useRouter()
const route = useRoute()
const $q = useQuasar()

const loginFormIpt = reactive({ usuario: '', senha: '' })
const loginFormMostrarSenha = ref(false)
const loginFormProcessando = ref(false)
const loginFormSubmit = async () => {
  try {
    loginFormProcessando.value = true
    //const { data } = await api.post('/auth/login', loginFormIpt)
    //sessionStore.login(data.token)

    const fakeToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoiRWxpYXMgTGF6Y2FubyIsImV4cCI6MTk3MzIzNTAxOX0.Y4jcPBwP9_NWKHcoHqW8DgyXwJmemhbh67GAsrWpe3Q'
    await new Promise(resolve => setTimeout(resolve, 5000))
    sessionStore.login(fakeToken)

    await router.replace(route.query.redirect ? route.query.redirect : { name: 'home' })
  } finally {
    loginFormProcessando.value = false
  }
}
</script>

<template>
  <q-page padding class="content-center">
    <div style="width: 100%; max-width: 22rem" class="q-mx-auto">
      <q-card :flat="$q.dark.isActive" :bordered="$q.dark.isActive">
        <q-card-section>
          <div class="text-center q-pb-sm">
            <img alt="Logo" src="~assets/logo.png" style="width: 4rem" />
            <div class="text-h6 q-mb-none">Quasar</div>
            <div class="text-subtitle1 q-mb-sm">Starter Kit</div>
          </div>
          <q-form
            @submit.prevent="loginFormSubmit"
            autocorrect="off"
            autocapitalize="off"
            class="q-gutter-y-sm"
          >
            <q-input
              label="Email"
              autocomplete="username"
              v-model="loginFormIpt.usuario"
              :disable="loginFormProcessando"
              :rules="[v => (!!v && !!v.trim()) || 'Insira seu email']"
              lazy-rules filled
            />
            <q-input
              label="Senha"
              :type="loginFormMostrarSenha ? 'text' : 'password'"
              maxlength="16"
              autocomplete="current-password"
              v-model="loginFormIpt.senha"
              :disable="loginFormProcessando"
              :rules="[v => (!!v && !!v.trim()) || 'Insira sua senha']"
              lazy-rules filled
            >
              <template #append>
                <q-btn
                  :icon="loginFormMostrarSenha ? 'visibility' : 'visibility_off'"
                  @click="loginFormMostrarSenha = !loginFormMostrarSenha"
                  flat round dense
                />
              </template>
            </q-input>
            <q-btn
              label="Login"
              color="primary"
              class="full-width"
              type="submit"
              :loading="loginFormProcessando"
              unelevated rounded
            />
          </q-form>
        </q-card-section>
      </q-card>
    </div>
  </q-page>
</template>
