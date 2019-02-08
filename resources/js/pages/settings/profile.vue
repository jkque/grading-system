<template>
  <card :title="$t('your_info')">
    <form @submit.prevent="update" @keydown="form.onKeydown($event)">
      <alert-success :form="form" :message="$t('info_updated')"/>

      <!-- First Name -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">First Name</label>
        <div class="col-md-7">
          <input v-model="form.first_name" :class="{ 'is-invalid': form.errors.has('first_name') }" class="form-control" type="text" name="first_name">
          <has-error :form="form" field="first_name"/>
        </div>
      </div>

      <!-- Last Name -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">Last Name</label>
        <div class="col-md-7">
          <input v-model="form.last_name" :class="{ 'is-invalid': form.errors.has('last_name') }" class="form-control" type="text" name="last_name">
          <has-error :form="form" field="last_name"/>
        </div>
      </div>

      <!-- Mobile Number -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">Mobile Number</label>
        <div class="col-md-7">
          <input v-model="form.mobile_number" :class="{ 'is-invalid': form.errors.has('mobile_number') }" class="form-control" type="text" name="mobile_number">
          <has-error :form="form" field="mobile_number"/>
        </div>
      </div>

      <!-- Address -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">Address</label>
        <div class="col-md-7">
          <input v-model="form.address" :class="{ 'is-invalid': form.errors.has('address') }" class="form-control" type="text" name="address">
          <has-error :form="form" field="address"/>
        </div>
      </div>

      <!-- Email -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">{{ $t('email') }}</label>
        <div class="col-md-7">
          <input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }" class="form-control" type="email" name="email">
          <has-error :form="form" field="email" />
        </div>
      </div>

      <!-- Submit Button -->
      <div class="form-group row">
        <div class="col-md-9 ml-md-auto">
          <v-button :loading="form.busy" type="success">{{ $t('update') }}</v-button>
        </div>
      </div>
    </form>
  </card>
</template>

<script>
import Form from 'vform'
import { mapGetters } from 'vuex'

export default {
  scrollToTop: false,
  middleware: 'auth',
  metaInfo () {
    return { title: this.$t('settings') }
  },
  data: () => ({
    form: new Form({
      first_name: '',
      last_name: '',
      mobile_number: '',
      address: '',
      email: ''
    })
  }),
  computed: mapGetters({
    user: 'auth/user'
  }),
  created () {
    // Fill the form with user data.
    this.form.keys().forEach(key => {
      this.form[key] = this.user[key]
    })
  },
  methods: {
    async update () {
      const { data } = await this.form.patch('/api/settings/profile')

      this.$store.dispatch('auth/updateUser', { user: data })
    }
  }
}
</script>
