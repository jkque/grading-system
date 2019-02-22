<template>
<div class="wrapper" v-if="user">
    <div class="animated fadeIn">
        <b-row>
            <b-col sm="12" md="12">
                <b-card header="School Year">
                    <b-col md="2">
                        <b-button variant="primary" @click.stop="showAdd()">
                            <i class="icon-plus"></i>&nbsp;Add
                        </b-button>
                    </b-col>
                    <b-card-body>
                        <b-table striped hover :items="school.school_years" :fields="fields" :responsive="true">
                            <template slot="start" slot-scope="data">
                                {{ formatDate(data.item.start,'LL') }}
                            </template>
                            <template slot="end" slot-scope="data">
                                {{ formatDate(data.item.end,'LL') }}
                            </template>
                            <template slot="status" slot-scope="data">
                                <b-badge :variant="getBadge(data.item.status)">{{data.item.status ? 'Active' : 'Inactive'}}</b-badge>
                            </template>
                            <template slot="action" slot-scope="row">
                                <b-button size="sm" variant="primary" @click.stop="info(row.item, row.index, $event.target)">
                                    <i class="fa fa-pencil-square-o"></i>&nbsp;Update
                                </b-button>
                                <b-button size="sm" variant="danger" @click.stop="remove(row.item, row.index, $event.target)" >
                                    <i class="fa fa-trash-o"></i>&nbsp;Delete
                                </b-button>
                                <b-button size="sm" variant="warning" @click.stop="readyForEnrollment(row.item)" v-if="row.index > 0 && !row.item.status">
                                    <i class="fa fa-pencil-square-o"></i>&nbsp;Ready for enrollment
                                </b-button>
                            </template>
                        </b-table>
                    </b-card-body>
                </b-card>
            </b-col>
        </b-row><!--/.row-->
    </div>

    <!-- Info modal -->
    <b-modal id="modalInfo" :title="modalInfo.title" v-model="modalInfoShow">
        <form @submit.prevent="update" @keydown="form.onKeydown($event)">
            <alert-success :form="form" :message="'School year has been updated'"/>
            <alert-error :form="form" message="There were some problems with your input."></alert-error>
            <!-- Start -->
            <div class="form-group row">
                <label class="col-md-3 col-form-label text-md-right">Start</label>
                <div class="col-md-7">
                <datePicker :class="{ 'is-invalid': form.errors.has('start') }" name="start" v-model="form.start" :config="datePickerOptions" @dp-change="startDateOnChange"></datePicker>
                <has-error :form="form" field="start"/>
                </div>
            </div>

            <!-- End -->
            <div class="form-group row">
                <label class="col-md-3 col-form-label text-md-right">End</label>
                <div class="col-md-7">
                    <datePicker :class="{ 'is-invalid': form.errors.has('end') }" name="end" v-model="form.end" :config="datePickerOptions" ref="endDatePicker"></datePicker>
                    <has-error :form="form" field="end"/>
                </div>
            </div>

            <!-- Status -->
            <!-- <div class="form-group row">
                <label class="col-md-3 col-form-label text-md-right">Status</label>
                <div class="col-md-7">
                    <c-switch class="mx-1" color="success" variant="pill" :checked="form.status" v-model="form.status"/>
                </div>
            </div> -->

            <div class="form-group row">
                <div class="col-md-9 ml-md-auto">
                    <v-button :loading="form.busy" type="success">Update</v-button>
                </div>
            </div>
        </form>
        <div slot="modal-footer">
            <b-btn class="float-right" variant="secondary" @click="modalInfoShow=false">Close</b-btn>
       </div>
    </b-modal>

    <!-- Add modal -->
    <b-modal id="modalAdd" :title="'Add new school year'" v-model="modalAddShow">
        <form @submit.prevent="create" @keydown="form.onKeydown($event)">
            <alert-success :form="form" :message="'School year has been added'"/>
            <alert-error :form="form" message="There were some problems with your input."></alert-error>
            <!-- Start -->
            <div class="form-group row">
                <label class="col-md-3 col-form-label text-md-right">Start</label>
                <div class="col-md-7">
                <datePicker name="start" v-model="form.start" :config="datePickerOptions" @dp-change="startDateOnChange"></datePicker>
                <has-error :form="form" field="start"/>
                </div>
            </div>

            <!-- End -->
            <div class="form-group row">
                <label class="col-md-3 col-form-label text-md-right">End</label>
                <div class="col-md-7">
                    <datePicker name="end" v-model="form.end" :config="datePickerOptions" ref="endDatePicker"></datePicker>
                    <has-error :form="form" field="end"/>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-9 ml-md-auto">
                    <v-button :loading="form.busy" type="primary">Add</v-button>
                </div>
            </div>
        </form>
        <div slot="modal-footer">
            <b-btn class="float-right" variant="secondary" @click="modalAddShow=false">Close</b-btn>
       </div>
    </b-modal>
</div>
</template>

<script>
import { mapGetters } from 'vuex'
import { Switch as cSwitch } from '@coreui/vue'
import swal from 'sweetalert2'
import axios from 'axios'
import Form from 'vform'
import moment from 'moment'
import datePicker from 'vue-bootstrap-datetimepicker'
import 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css';

export default {
    components: {
        cSwitch,
        datePicker
    },
    middleware: 'auth',
    name: 'schoolYear',
    computed: {
        ...mapGetters({
            school: 'auth/school',
            user: 'auth/user',
        }),
    },
    data: function () {
        return {
            form: new Form({
                start: null,
                end: null,
                status: false,
                id: null
            }),
            datePickerOptions: {
                format: 'YYYY-MM-DD',
                useCurrent: false,
                showClear: true,
                showClose: true,
            },
            modalInfo: { title: '', content: '' },
            fields: [
                {
                    key: 'start',
                    sortable: true
                },
                {
                    key: 'end',
                    sortable: true
                },
                {
                    key: 'status',
                },
                {
                    key: 'action',
                }
            ],
            modalInfoShow: false,
            modalAddShow: false,
        }
    },
    methods:{
        formatDate (date,format) {
            return moment(date).format(format);
        },
        startDateOnChange () {
            this.$refs.endDatePicker.dp.minDate(this.form.start);
        },
        getBadge (status) {
            return status ? 'success' : 'secondary';
        },
        info (item, index, button) {
            this.modalInfo.title = `${item.start} - ${item.end}`
            this.form.start = moment(item.start).format('YYYY-MM-DD');
            this.form.end = moment(item.end).format('YYYY-MM-DD');
            this.form.status = item.status;
            this.form.id = item.id;
            this.$root.$emit('bv::show::modal', 'modalInfo', button)
        },
        showAdd () {
            this.form.reset();
            this.form.clear();
            this.form.start = moment().format('YYYY-MM-DD');
            this.form.end = moment().add(1,'year').format('YYYY-MM-DD');
            this.$root.$emit('bv::show::modal', 'modalAdd')
        },
        remove (item, index, button) {
            let vm = this;
            swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                showLoaderOnConfirm: true,
                preConfirm: (login) => {
                    return  axios.delete(`/api/school/year/${item.id}/destroy`).then( response => {
                        return response.data;
                    }).catch(error => console.log(error))
                },
                allowOutsideClick: () => !swal.isLoading()
                }).then((result) => {
                if (result.value) {
                    
                    if(result.value.hasOwnProperty('success') && !result.value.success){
                        swal('Warning!', result.value.message, 'warning')
                    }else{
                        swal('Deleted!', result.value.message, 'success');
                        vm.$store.dispatch('auth/updateSchool', { school: result.value })   
                    }
                }
            });
        },
        async update () {
            const { data } = await this.form.patch(`/api/school/year/${this.form.id}/update`)

            this.$store.dispatch('auth/updateSchool', { school: data })
        },
        async create () {
            const { data } = await this.form.post(`/api/school/year/${this.school.id}/create`)

            this.$store.dispatch('auth/updateSchool', { school: data })
        },
        readyForEnrollment(item){
            let vm = this;
            swal({
                title: 'Are you sure?',
                text: " All students will be evaluated accordingly. Students with passing grade from previous school year will be move to next grade",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Continue!',
                showLoaderOnConfirm: true,
                preConfirm: (login) => {
                    return axios.post(`/api/school/year/${item.id}/ready-for-enrollment`).then( response => {
                        return response.data;
                    }).catch(error => console.log(error))
                },
                allowOutsideClick: () => !swal.isLoading()
                }).then((result) => {
                if (result.value) {
                    swal('Success!', 'New school year is ready for enrollment.<br> Students are now being processed.<br> Please wait for the email we will send confirming that the process has been done', 'success');
                    vm.$store.dispatch('auth/updateSchool', { school: result.value })
                }
            });

        }
    }
}
</script>

<style scoped>
  .fade-enter-active {
    transition: all .3s ease;
  }
  .fade-leave-active {
    transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
  }
  .fade-enter, .fade-leave-to {
    transform: translateX(10px);
    opacity: 0;
  }
</style>
