<template>
  <b-col md="8">
    <b-card-group>
      <b-card no-body class="p-4">
        <b-card-body>
          <b-form @submit.prevent="login" @keydown="form.onKeydown($event)">
            <h1>Login</h1>
            <p class="text-muted">Sign In to your account</p>
            <b-input-group class="mb-3">
              <b-input-group-prepend><b-input-group-text><i class="icon-user"></i></b-input-group-text></b-input-group-prepend>
              <b-form-input v-model="form.email" type="email" class="form-control" placeholder="Email" autocomplete="email" name="email" :class="{ 'is-invalid': form.errors.has('email') }"/>
              <has-error :form="form" field="email"/>
            </b-input-group>
            <b-input-group class="mb-4">
              <b-input-group-prepend><b-input-group-text><i class="icon-lock"></i></b-input-group-text></b-input-group-prepend>
              <b-form-input v-model="form.password" :class="{ 'is-invalid': form.errors.has('password') }" type="password" class="form-control" placeholder="Password" autocomplete="current-password" />
              <has-error :form="form" field="password"/>
            </b-input-group>
            <b-row>
              <b-col cols="6">
                
                <v-button :loading="form.busy">
                  {{ $t('login') }}
                </v-button>
              </b-col>
              <b-col cols="6" class="text-right">
                <b-button variant="link" class="px-0" :to="{ name: 'password.request' }">Forgot password?</b-button>
              </b-col>
            </b-row>
          </b-form>
        </b-card-body>
      </b-card>
      <b-card no-body class="text-white bg-primary py-5 d-md-down-none" style="width:44%">
        <b-card-body class="text-center">
          <div>
            <h2>Sign up</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
            <b-button variant="primary" class="active mt-3" :to="{ name: 'register' }">Register Now!</b-button>
          </div>
        </b-card-body>
      </b-card>
    </b-card-group>
  </b-col>
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
    return { title: this.$t('login') }
  },

  data: () => ({
    form: new Form({
      email: '',
      password: ''
    }),
    remember: false
  }),

  methods: {
    async login () {
      // Submit the form.
      const { data } = await this.form.post('/api/login')

      // Save the token.
      this.$store.dispatch('auth/saveToken', {
        token: data.token,
        remember: this.remember
      })

      // Fetch the user.
      await this.$store.dispatch('auth/fetchUser')

      // Fetch the achool.
      await this.$store.dispatch('auth/fetchSchool')
      
      if(this.$store.getters['auth/roles'].includes('school_admin')){
        if(this.$store.getters['auth/school']){
          this.$router.push({ name: `Home` })
        }else{
          this.$router.push({ name: `school.register` })
        }
      }

      // Redirect home.
      this.$router.push({ name: 'Home' })
    }
  }
}
</script>
