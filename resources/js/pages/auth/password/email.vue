<template>
  <b-col md="8" sm="8">
    <b-card no-body class="mx-4" header="Reset Password">
      <b-card-body class="p-4">
        <form @submit.prevent="send" @keydown="form.onKeydown($event)">
          <alert-success :form="form" :message="status"/>

          <!-- Email -->
          <div class="form-group row">
            <label class="col-md-3 col-form-label text-md-right">{{ $t('email') }}</label>
            <div class="col-md-7">
              <input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }" class="form-control" type="email" name="email">
              <has-error :form="form" field="email"/>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="form-group row">
            <div class="col-md-5 offset-md-3 d-flex">
              <v-button :loading="form.busy">
                {{ $t('send_password_reset_link') }}
              </v-button>
            </div>
            <b-col cols="2" class="text-right">
              <b-button variant="link" class="px-0" :to="{ name: 'login' }">Go to Login</b-button>
            </b-col>
          </div>
        </form>
      </b-card-body>
    </b-card>
  </b-col>
  <!-- <div class="row">
    <div class="col-lg-8 m-auto">
      <card :title="$t('reset_password')">
      </card>
    </div>
  </div> -->
</template>

<script>
import Form from 'vform'

export default {
  layout: 'basic',
  middleware: 'guest',

  metaInfo () {
    return { title: this.$t('reset_password') }
  },

  data: () => ({
    status: '',
    form: new Form({
      email: ''
    })
  }),

  methods: {
    async send () {
      const { data } = await this.form.post('/api/password/email')

      this.status = data.status

      this.form.reset()
    }
  }
}
</script>
