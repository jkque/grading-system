<template>
<div class="wrapper">
    <div class="animated fadeIn">
        <b-card header="Grading Period">
            <b-table striped hover :items="school.grading_periods" :fields="fields" :fixed="true" :sort-by.sync="sortBy">
                <template slot="name" slot-scope="data">
                    {{ data.item.name }}
                </template>
                <template slot="status" slot-scope="row">
                    <c-switch class="mx-1" color="success" variant="pill" :checked="row.item.status" v-model="row.item.status" @change="handleOnChange($event,row.item.id,row.index)"/>
                    <!-- <b-badge :variant="getBadge(data.item.status)">{{data.item.status ? 'Active' : 'Inactive'}}</b-badge> -->
                </template>
                <!-- <template slot="action" slot-scope="row">
                    <b-button size="sm" variant="primary" @click.stop="info(row.item, row.index, $event.target)">
                        <i class="fa fa-pencil-square-o"></i>&nbsp;Update
                    </b-button>
                </template> -->
            </b-table>
        </b-card>
    </div>

    <!-- Info modal -->
    <b-modal id="modalInfo" :title="modalInfo.title" v-model="modalInfoShow">
        <form @submit.prevent="update" @keydown="form.onKeydown($event)">
            <alert-success :form="form" :message="'Grading period has been updated'"/>
            <alert-error :form="form" message="There were some problems with your input."></alert-error>

            <!-- Name -->
            <div class="form-group row">
                <label class="col-md-3 col-form-label text-md-right">Name</label>
                <div class="col-md-7">
                <input v-model="form.name" :class="{ 'is-invalid': form.errors.has('name') }" class="form-control" type="text" name="name">
                <has-error :form="form" field="name"/>
                </div>
            </div>

            <!-- Status -->
            <div class="form-group row">
                <label class="col-md-3 col-form-label text-md-right">Status</label>
                <div class="col-md-7">
                    <c-switch class="mx-1" color="success" variant="pill" :checked="form.status" v-model="form.status"/>
                    <!-- <c-switch class="mx-1" color="success" variant="pill" :checked="form.status" v-model="form.status"/> -->
                </div>
            </div>

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
</div>
</template>

<script>
import { mapGetters } from 'vuex'
import { Switch as cSwitch } from '@coreui/vue'
import swal from 'sweetalert2'
import axios from 'axios'
import Form from 'vform'
import moment from 'moment'

export default {
    components: {
        cSwitch,
    },
    middleware: 'auth',
    name: 'schoolYear',
    computed: {
        ...mapGetters({
            school: 'auth/school',
        }),
    },
    data: function () {
        return {
            form: new Form({
                name: '',
                id: null,
                status: false,
            }),
            datePickerOptions: {
                format: 'YYYY-MM-DD',
                useCurrent: false,
                showClear: true,
                showClose: true,
            },
            sortBy: 'level',
            modalInfo: { title: '', content: '' },
            fields: [
                {
                    key: 'name',
                    sortable: true
                },
                {
                    key: 'status',
                },
                // {
                //     key: 'action',
                // }
            ],
            modalInfoShow: false,
        }
    },
    methods:{
        handleOnChange(event,grading_period_id,index){
            let vm = this;
            let payload = {
                status: event,
            }
            axios.patch(`/api/school/grading-period/${grading_period_id}/setStatus`,payload).then( response => {
                this.$store.dispatch('auth/updateSchool', { school: response.data })
            }).catch((error) => {
                swal('Warning!', error.response.data, 'warning')
                vm.school.grading_periods[index].status = true
            })
            
        },
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
            this.modalInfo.title = item.name;
            this.form.id = item.id;
            this.form.name = item.name;
            this.form.status = item.status;
            this.$root.$emit('bv::show::modal', 'modalInfo', button)
        },
        setStatus (index) {
            let vm = this;
            let item = this.school.grading_periods[index];

            let payload = {
                status: item.status,
            }
            
            axios.patch(`/api/school/grading-period/${item.id}/setStatus`,payload).then( response => {
                vm.$store.dispatch('auth/updateSchool', { school: response.data })
            }).catch(error => swal('Warning!', error.response.data, 'warning'))

        },
        showAdd () {
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
            const { data } = await this.form.patch(`/api/school/grading-period/${this.form.id}/update`)

            this.$store.dispatch('auth/updateSchool', { school: data })
        },
        async create () {
            const { data } = await this.form.post(`/api/school/year/${this.school.id}/create`)

            this.$store.dispatch('auth/updateSchool', { school: data })
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
