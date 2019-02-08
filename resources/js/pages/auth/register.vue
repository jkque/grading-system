<template>
  <b-col md="8" sm="8">
    <b-card no-body class="mx-4" header="Register">
      <b-card-body class="p-4">
        <form @submit.prevent="register" @keydown="form.onKeydown($event)">
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
              <has-error :form="form" field="email"/>
            </div>
          </div>

          <!-- Password -->
          <div class="form-group row">
            <label class="col-md-3 col-form-label text-md-right">{{ $t('password') }}</label>
            <div class="col-md-7">
              <input v-model="form.password" :class="{ 'is-invalid': form.errors.has('password') }" class="form-control" type="password" name="password">
              <has-error :form="form" field="password"/>
            </div>
          </div>

          <!-- Password Confirmation -->
          <div class="form-group row">
            <label class="col-md-3 col-form-label text-md-right">{{ $t('confirm_password') }}</label>
            <div class="col-md-7">
              <input v-model="form.password_confirmation" :class="{ 'is-invalid': form.errors.has('password_confirmation') }" class="form-control" type="password" name="password_confirmation">
              <has-error :form="form" field="password_confirmation"/>
            </div>
          </div>

          <div class="form-group row">
            <div class="col-md-3 offset-md-3 d-flex">
              <!-- Submit Button -->
              <v-button :loading="form.busy">
                {{ $t('register') }}
              </v-button>
              
              <!-- GitHub Register Button -->
              <login-with-github/>
            </div>
            <b-col cols="4" class="text-right">
              <b-button variant="link" class="px-0" :to="{ name: 'login' }">Go to Login</b-button>
            </b-col>
          </div>
        </form>
      </b-card-body>
    </b-card>
  </b-col>
  <!-- <div class="row">
    <div class="col-lg-8 m-auto">
      <card :title="$t('register')">
      </card>
    </div>
  </div> -->
</template>

<script>
import Form from 'vform'
import LoginWithGithub from '~/components/LoginWithGithub'

export default {
  layout: 'basic',
  middleware: 'guest',

  components: {
    LoginWithGithub
  },

  metaInfo () {
    return { title: this.$t('register') }
  },

  data: () => ({
    form: new Form({
      first_name: '',
      last_name: '',
      mobile_number: '',
      email: '',
      password: '',
      password_confirmation: ''
    })
  }),

  methods: {
    async register () {
      // Register the user.
      const { data } = await this.form.post('/api/register')

      // Log in the user.
      const { data: { token } } = await this.form.post('/api/login')

      // Save the token.
      this.$store.dispatch('auth/saveToken', { token })

      // Update the user.
      await this.$store.dispatch('auth/updateUser', { user: data })

      // Redirect home.
      this.$router.push({ name: 'school.register' })
    }
  }
}
</script>
