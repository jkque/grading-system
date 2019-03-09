<template>
  <b-col md="8" sm="8">
    <b-card no-body class="mx-4" header="Register School">
      <b-card-body class="p-4">
        <form @submit.prevent="register" @keydown="form.onKeydown($event)">
          <!-- Name -->
          <div class="form-group row">
            <label class="col-md-3 col-form-label text-md-right">Name</label>
            <div class="col-md-7">
              <input v-model="form.name" :class="{ 'is-invalid': form.errors.has('name') }" class="form-control" type="text" name="name">
              <has-error :form="form" field="name"/>
            </div>
          </div>

          <!-- Ccontact Number -->
          <div class="form-group row">
            <label class="col-md-3 col-form-label text-md-right">Contact Number</label>
            <div class="col-md-7">
              <input v-model="form.contact_number" :class="{ 'is-invalid': form.errors.has('contact_number') }" class="form-control" type="text" name="contact_number">
              <has-error :form="form" field="contact_number"/>
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

          <!-- Start -->
          <div class="form-group row">
            <label class="col-md-3 col-form-label text-md-right">School Year Start</label>
            <div class="col-md-7">
              <datePicker name="start" v-model="form.start" :class="{ 'is-invalid': form.errors.has('start') }" :config="datePickerOptions" @dp-change="startDateOnChange"></datePicker>
              <has-error :form="form" field="start"/>
            </div>
          </div>

          <!-- End -->
          <div class="form-group row">
            <label class="col-md-3 col-form-label text-md-right">School Year End</label>
            <div class="col-md-7">
              <datePicker name="end" v-model="form.end" :class="{ 'is-invalid': form.errors.has('end') }" :config="datePickerOptions" ref="endDatePicker"></datePicker>
              <has-error :form="form" field="end"/>
            </div>
          </div>

          <div class="form-group row">
            <div class="col-md-3 offset-md-3 d-flex">
              <!-- Submit Button -->
              <v-button :loading="form.busy">
                {{ $t('register') }}
              </v-button>

            </div>
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
import moment from 'moment'
import datePicker from 'vue-bootstrap-datetimepicker'
import 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css';

export default {
  layout: 'basic',
  middleware: 'school-admin',
  components: {
    datePicker,
  },

  metaInfo () {
    return { title: 'Register School' }
  },

  data: () => ({
    form: new Form({
      name: '',
      contact_number: '',
      address: '',
      start: moment().format('YYYY-MM-DD'),
      end: moment().add(1,'year').format('YYYY-MM-DD')
    }),
    datePickerOptions: {
      format: 'YYYY-MM-DD',
      useCurrent: false,
      showClear: true,  
      showClose: true,
      minDate: moment().format('YYYY-MM-DD')
    }
  }),

  methods: {
    async register () {
      // Register the user.
      const { data } = await this.form.post('/api/school/register')
      
      // Update the school.
      await this.$store.dispatch('auth/updateSchool', { school: data })

      // Redirect home.
      this.$router.push({ name: 'Home' })
    },
    startDateOnChange: function() {
      this.$refs.endDatePicker.dp.minDate(this.form.start);
    }
  },
}
</script>
