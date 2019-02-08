<template>
    <AppSidebar fixed v-if="user">
      <SidebarHeader/>
      <SidebarForm/>
      <SidebarNav :navItems="nav"></SidebarNav>
      <SidebarFooter/>
      <SidebarMinimizer/>
    </AppSidebar>
</template>

<script>
import nav from '~/layouts/nav'
import { mapGetters } from 'vuex'
import { Sidebar as AppSidebar, SidebarFooter, SidebarForm, SidebarHeader, SidebarMinimizer, SidebarNav} from '@coreui/vue'

export default {
  mixins: [nav],
  middleware: 'admin',
  components: {
    AppSidebar,
    SidebarForm,
    SidebarFooter,
    SidebarHeader,
    SidebarNav,
    SidebarMinimizer
  },
  data: () => ({
    appName: window.config.appName,
  }),
  computed: {
    ...mapGetters({
      user: 'auth/user',
      school: 'auth/school',
      roles: 'auth/roles',
    }),
    nav: function () {
      if(this.roles.includes('admin')){
        return this.adminNav;
      }
      if(this.roles.includes('school_admin')){
        return this.schoolAdminNav;
      }
      if(this.roles.includes('teacher')){
        return this.teacherNav;
      }
    },
    adminNav: function () {
      return this.navs.slice().filter(nav => 
        nav.hasOwnProperty('middleware') && nav.middleware.includes('admin')
      );
    },
    schoolAdminNav: function () {
      return this.navs.slice().filter(nav => 
        nav.hasOwnProperty('middleware') && nav.middleware.includes('school_admin')
      );
    },
    teacherNav: function () {
      return this.navs.slice().filter(nav => 
        nav.hasOwnProperty('middleware') && nav.middleware.includes('teacher')
      );
    },
  }
}
</script>

<style scoped>
</style>
