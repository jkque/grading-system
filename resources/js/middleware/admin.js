import store from '~/store'

export default (to, from, next) => {
  if (!store.getters['auth/roles'].includes('admin')) {
    next({ name: 'Home' })
  } else {
    next()
  }
}
