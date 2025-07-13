<script setup>
import { reactive, ref } from 'vue'
import { useQuasar } from 'quasar'
import { validarEmail } from '@eliaslazcano/utils'

const $q = useQuasar()

const emit = defineEmits(['submit'])
const formValues = reactive({email: '', password: '', password2: ''})
const formEmailRules = [
  v => (!!v && !!v.trim()) || 'Insira seu email',
  v => validarEmail(v) || 'Digite um email válido'
]
const formPasswordRules = [
  v => (!!v && !!v.trim()) || 'Insira sua nova senha',
  v => !/\s/.test(v) || 'A senha não deve conter espaços',
  v => v.length >= 6 || 'A senha deve ter pelo menos 6 caracteres',
]
const formShowPassword = ref(false)
const formLoading = defineModel('loading', {type: Boolean, default: false})
const formSubmit = async () => {
  if (formValues.password !== formValues.password2) return $q.notify({type: 'negative', message: 'A repetição da senha está incorreta.'})
  emit('submit', formValues.email, formValues.password)
}
</script>

<template>
  <q-card>
    <q-form @submit.prevent="formSubmit">
      <q-card-section>
        <div class="text-h6">Registrar-se</div>
      </q-card-section>
      <q-card-section class="q-gutter-y-md q-py-none">
        <q-input
          label="Email"
          type="email"
          autocomplete="email"
          v-model="formValues.email"
          :disable="formLoading"
          :rules="formEmailRules"
          lazy-rules filled
        />
        <q-input
          label="Nova senha"
          :type="formShowPassword ? 'text' : 'password'"
          maxlength="16"
          autocomplete="new-password"
          v-model="formValues.password"
          :disable="formLoading"
          :rules="formPasswordRules"
          lazy-rules filled
        >
          <template #append>
            <q-btn
              :icon="formShowPassword ? 'visibility' : 'visibility_off'"
              @click="formShowPassword = !formShowPassword"
              flat round dense
            />
          </template>
        </q-input>
        <q-input
          label="Repetir a senha"
          type="password"
          maxlength="16"
          autocomplete="off"
          v-model="formValues.password2"
          :disable="formLoading"
          :rules="[v => (!!v && !!v.trim()) || 'Repita sua nova senha']"
          lazy-rules filled
        />
      </q-card-section>
      <q-card-actions align="right">
        <q-btn flat label="Cancelar" color="primary" :disable="formLoading" v-close-popup />
        <q-btn flat label="Confirmar" color="primary" type="submit" :loading="formLoading" />
      </q-card-actions>
    </q-form>
  </q-card>
</template>
