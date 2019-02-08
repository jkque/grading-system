<template>

  <div class="animated fadeIn">
    <b-row>
      <b-col sm="6" md="12">
        <b-card header="Information">
        <form @submit.prevent="update" @keydown="form.onKeydown($event)">
            <alert-success :form="form" :message="'School information has been updated'"/>
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

            <div class="form-group row">
                <div class="col-md-9 ml-md-auto">
                    <v-button :loading="form.busy" type="success">Update</v-button>
                </div>
            </div>
        </form>
        </b-card>
      </b-col>
    </b-row><!--/.row-->
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Form from 'vform'

export default {
  middleware: 'school-admin',
  data: () => ({
    form: new Form({
      name: '',
      contact_number: '',
      address: ''
    }),
  }),
  metaInfo () {
    return { title: 'School Information' }
  },
  computed: {
    ...mapGetters({
      school: 'auth/school',
    }),
  },
  created () {
    // Fill the form with user data.
    this.form.keys().forEach(key => {
      this.form[key] = this.school[key]
    })
  },

  methods: {
    async update () {
      const { data } = await this.form.patch(`/api/school/${this.school.id}/update-info`)

      this.$store.dispatch('auth/updateSchool', { school: data })
    }
  },
}
</script>
