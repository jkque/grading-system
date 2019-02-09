import Vue from 'vue'
import store from '~/store'
import router from '~/router'
import i18n from '~/plugins/i18n'
import App from '~/components/App'
import BootstrapVue from 'bootstrap-vue'
import Echo from 'laravel-echo'

import '~/plugins'
import '~/components'

window.Pusher = require('pusher-js');

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: '0835bdf80a267479ddd7',
  cluster: 'ap1',
  encrypted: true
});

Vue.config.productionTip = false
Vue.use(BootstrapVue)


/* eslint-disable no-new */
new Vue({
  Echo,
  i18n,
  store,
  router,
  ...App
})

