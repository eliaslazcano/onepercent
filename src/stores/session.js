import { defineStore, acceptHMRUpdate } from 'pinia'
import { jwtDecode } from 'jwt-decode'

export const useSessionStore = defineStore('session', {
  state: () => ({
    token: null,
    payload: null,
  }),
  getters: {
    isLoggedIn: state => !!state?.token,
    isTokenExpired: state => {
      const exp = state?.payload?.exp
      if (!exp) return true
      return Math.floor(Date.now() / 1000) >= exp
    },
  },
  actions: {
    login(token) {
      try {
        this.token = token
        this.payload = jwtDecode(token)
        localStorage.setItem('session', JSON.stringify({ token: this.token })) // Persistir no localStorage
      } catch (error) {
        console.error('Token inválido', error)
        this.logout()
      }
    },

    logout(redirect = false) {
      this.token = null
      localStorage.removeItem('session')
      if (redirect) this.router.push({name: 'login'})
    },

    loadFromStorage() {
      const session = localStorage.getItem('session')
      if (session) {
        try {
          const { token } = JSON.parse(session)
          this.token = token
          this.payload = jwtDecode(token)
        } catch (error) {
          console.error('Erro ao carregar sessão:', error)
          this.logout()
        }
      }
    },
  }
})

if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useSessionStore, import.meta.hot))
}
