import store from '~/store'

export default (to, from, next) => {
  if (store.getters['auth/check']) {
    if (store.getters['auth/roles'].includes('admin') || store.getters['auth/roles'].includes('school_admin')) {
      if(store.getters['auth/school']){
        next({ name: 'Home' })
      }
      next()
    } else {
      next({ name: 'Home' })
    }
  }else{
    next({ name: 'login' })
  }
}
