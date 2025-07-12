import { defineBoot } from '#q-app/wrappers'
import { Dialog, Notify } from 'quasar'
import { useSessionStore } from 'stores/session.js'
import axios from 'axios'

export const baseURL = 'http://localhost:3000'

export const api = axios.create({
  baseURL,
  timeout: 60000,
  timeoutErrorMessage: 'timeout'
})

const mostrarMensagem = (mensagem, dialog = false) => {
  if (dialog) Dialog.create({title: 'Ops', message: mensagem, html: true})
  else Notify.create({type: 'negative', message: mensagem})
}

const sessionStore = useSessionStore()

api.interceptors.request.use(conf => {
  if (sessionStore.isLoggedIn) conf.headers.Authorization = `Bearer ${sessionStore.token}`
  return conf
}, error => Promise.reject(error))

api.interceptors.response.use(value => value, async error => {
  if (error.response?.status === 410) sessionStore.logout(true)
  if (navigator && navigator.onLine !== true) mostrarMensagem('Parece que você está sem conexão com a internet.')
  else if (error.message === 'timeout') mostrarMensagem('O processamento está tomando mais tempo do que o permitido. Sua conexão pode estar ruim.')
  else if (error.response?.data) {
    if (typeof error.response.data === 'object' && error.response.data instanceof Blob && error.response.data.type === 'application/json') {
      const fileReader = new FileReader() //Erroneamente recebeu um JSON mas interpretou como Blob, bugfix.
      fileReader.onload = () => {
        const data = JSON.parse(fileReader.result)
        if (data?.mensagem) mostrarMensagem(data.mensagem, error.response?.status === 403)
      }
      fileReader.readAsText(error.response.data)
    } else if (error.response.data?.mensagem) {
      mostrarMensagem(error.response.data.mensagem, error.response?.status === 403)
    }
  } else if (error.code === 'ECONNABORTED') mostrarMensagem('O processamento foi cancelado pelo dispositivo, parece haver um problema de conexão.')
  else if (error.code === 'ERR_NETWORK') mostrarMensagem('Seu dispositivo fracassou ao tentar estabelecer conexão. Você pode estar com a conexão ruim ou o servidor rejeitou a comunicação após perceber alguma falha.')
  else if (error.message) mostrarMensagem(error.message)
  else mostrarMensagem('Ocorreu uma falha ao tentar realizar o processamento, a conexão pode estar ruim ou o serviço pode estar indisponível.')
  return Promise.reject(error)
})

export default defineBoot(({ app }) => {
  app.config.globalProperties.$axios = axios
  app.config.globalProperties.$api = api
})
