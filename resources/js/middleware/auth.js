import store from '~/store'

export default async (to, from, next) => {
  if (!store.getters['auth/check']) {
    next({ name: 'login' })
  } else {
    if (store.getters['auth/roles'].includes('school_admin')) {
      if(store.getters['auth/school']){
        next()
      }else{
        next({ name: 'school.register' })
      }
    }
    next()
  }
}
