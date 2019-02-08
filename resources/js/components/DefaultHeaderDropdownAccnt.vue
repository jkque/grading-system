<template>
  <AppHeaderDropdown right no-caret v-if="user">
    <template slot="header">
      <img
        :src="user.photo_url"
        class="img-avatar"
        alt="admin@bootstrapmaster.com" />
    </template>
    <template slot="dropdown">
      <b-dropdown-header tag="div" class="text-center"><strong>Account</strong></b-dropdown-header>
      <b-dropdown-item :to="{ name: 'Profile' }"><i class="fa fa-user" /> Profile</b-dropdown-item>
      <b-dropdown-item :to="{ name: 'Password' }"><i class="fa fa-wrench" /> Password</b-dropdown-item>
      <b-dropdown-divider />
      <b-dropdown-item  @click.prevent="logout"><i class="fa fa-lock" /> Logout</b-dropdown-item>
    </template>
  </AppHeaderDropdown>
</template>

<script>
import { mapGetters } from 'vuex'
import { HeaderDropdown as AppHeaderDropdown } from '@coreui/vue'
export default {
  name: 'DefaultHeaderDropdownAccnt',
  components: {
    AppHeaderDropdown
  },
  data: () => {
    return { itemsCount: 42 }
  },
  computed: mapGetters({
    user: 'auth/user'
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
