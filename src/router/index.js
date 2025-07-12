import { defineRouter } from '#q-app/wrappers'
import { createRouter, createMemoryHistory, createWebHistory, createWebHashHistory } from 'vue-router'
import { useSessionStore} from 'stores/session.js'
import routes from './routes'
import { Notify } from 'quasar'

/*
 * If not building with SSR mode, you can
 * directly export the Router instantiation;
 *
 * The function below can be async too; either use
 * async/await or return a Promise which resolves
 * with the Router instance.
 */

export default defineRouter(function (/* { store, ssrContext } */) {
  const createHistory = process.env.SERVER
    ? createMemoryHistory
    : (process.env.VUE_ROUTER_MODE === 'history' ? createWebHistory : createWebHashHistory)

  const Router = createRouter({
    scrollBehavior: () => ({ left: 0, top: 0 }),
    routes,

    // Leave this as is and make changes in quasar.conf.js instead!
    // quasar.conf.js -> build -> vueRouterMode
    // quasar.conf.js -> build -> publicPath
    history: createHistory(process.env.VUE_ROUTER_BASE)
  })

  Router.beforeEach((to) => {
    const session = useSessionStore()
    if (!session.token) session.loadFromStorage() // Garante que a sessão esteja carregada do localStorage
    if (session.isLoggedIn && session.isTokenExpired) {
      Notify.create({ type: 'warning', message: 'Sessão expirada. Faça login novamente.' })
      session.logout()
    }

    const requiresAuth = to.matched.some(record => !!record.meta?.requiresAuth)
    const requiresNoAuth = to.matched.some(record => !!record.meta?.requiresNoAuth)

    if (requiresAuth && !session.isLoggedIn) return { name: 'login', query: { redirect: to.fullPath } }
    if (requiresNoAuth && session.isLoggedIn) return { name: 'home' }
  })

  return Router
})
