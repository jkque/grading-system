<template>
  <div class="app">
    <navbar/>
    <div class="app-body">
      <sidebar/>
      <main class="main">
        <Breadcrumb :list="list"/>
        <div class="container-fluid">
          <child/>
        </div>
      </main>
      <AppAside fixed>
        <!--aside-->
        <DefaultAside/>
      </AppAside>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Navbar from '~/components/Navbar'
import Sidebar from '~/components/Sidebar'
import { Breadcrumb, Aside as AppAside } from '@coreui/vue'
import DefaultAside from '~/components/DefaultAside'

export default {
  name: 'MainLayout',
  components: {
    Sidebar,
    Navbar,
    Breadcrumb,
    AppAside,
    DefaultAside
  },
  computed: {
    list () {
      return this.$route.matched.filter((route) => route.name || route.meta.label )
    }
  }
}
</script>
