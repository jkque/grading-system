import store from '~/store'

export default async (to, from, next) => {
  if (!store.getters['auth/check'] && store.getters['auth/token']) {
    try {
      await store.dispatch('auth/fetchUser')
      if(!store.getters['auth/roles'].includes('admin')){
        await store.dispatch('auth/fetchSchool')
      }
    } catch (e) { }
  }

  next()
}
