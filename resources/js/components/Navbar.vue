<template>
    <AppHeader fixed v-if="user">
      <SidebarToggler class="d-lg-none" display="md" mobile />
      <b-link class="navbar-brand" to="#">
        <img class="navbar-brand-full" src="/images/logo.png" width="150" alt="Logo">
        <img class="navbar-brand-minimized" src="/images/logo-symbol.png" width="30" height="30" alt="Logo">
      </b-link>
      <SidebarToggler class="d-md-down-none" display="lg" />
      <b-navbar-nav class="d-md-down-none">
        <template v-if="school">
          <b-nav-item class="px-3" to="/dashboard">{{school.name.toUpperCase()}}</b-nav-item>
        </template>
        <!-- <b-nav-item class="px-3" to="/users" exact>Users</b-nav-item>
        <b-nav-item class="px-3">Settings</b-nav-item> -->
      </b-navbar-nav>
      <b-navbar-nav class="ml-auto">
        <template v-if="school">
          <b-nav-item class="d-md-down-none" :to="{ path: `school/${school.id}/calendar` }"><i class="icon-calendar"></i></b-nav-item>
        </template>
        <!-- <b-nav-item class="d-md-down-none">
          <NotificationDropDown/>
        </b-nav-item> -->
        <DefaultHeaderDropdownAccnt/>
        <b-nav-item class="px-3 user-name d-md-down-none">{{ user.name }}</b-nav-item>
      </b-navbar-nav>
      <AsideToggler class="d-none d-lg-block" />
    </AppHeader>
</template>

<script>
import { mapGetters } from 'vuex'
import LocaleDropdown from './LocaleDropdown'
import { Header as AppHeader, SidebarToggler, AsideToggler } from '@coreui/vue'
import DefaultHeaderDropdownAccnt from '~/components/DefaultHeaderDropdownAccnt'
import NotificationDropDown from '~/components/NotificationDropDown'

export default {
  components: {
    LocaleDropdown,
    AppHeader,
    DefaultHeaderDropdownAccnt,
    NotificationDropDown,
    SidebarToggler,
    AsideToggler
  },

  data: () => ({
    appName: window.config.appName
  }),

  computed: mapGetters({
    user: 'auth/user',
    school: 'auth/school'
  }),

  methods: {
    async logout () {
      // Log out the user.
      await this.$store.dispatch('auth/logout')

      // Redirect to login.
      this.$router.push({ name: 'login' })
    }
  }
}
</script>

<style scoped>
.user-name {
  padding-left: initial !important;
  padding-right: initial !important;
}
</style>
