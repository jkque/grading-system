import store from '~/store'

export default (to, from, next) => {
  if (store.getters['auth/roles'].includes('admin') || store.getters['auth/roles'].includes('school_admin')  || store.getters['auth/roles'].includes('teacher')) {
    next()
  } else {
    next({ name: 'Home' })
  }
}
