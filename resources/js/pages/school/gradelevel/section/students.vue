<template>
<b-col class="wrapper" v-if="authenticated">
    <b-col class="animated fadeIn">
        <b-card no-header  v-show="!isShowAddUser && !isShowImportUser">
            <template slot="header">
                {{`${section.name} / Students` }}
            </template>
            <b-col md="4">
                <b-button variant="outline-primary" @click="showAddUser()"><i class="icon-plus"></i>&nbsp;Add</b-button>
                <b-button variant="outline-primary" @click="showImportUser()"><i class="icon-arrow-up-circle"></i>&nbsp;Import</b-button>
                <b-button variant="outline-primary" @click="print()">Print</b-button>
            </b-col> 
            <b-col md="12">
                <b-input-group>
                    <b-form-input v-model="filter" placeholder="Type to Search" />
                    <b-input-group-append>
                    <b-btn :disabled="!filter" @click="filter = ''">Clear</b-btn>
                    </b-input-group-append>
                </b-input-group>
            </b-col>
            <b-card-body id="printMe">
                <b-table striped hover :items="filteredStudent" :fields="fields" :responsive="true" :sort-by.sync="sortBy" :show-empty="true" :filter="filter">
                    <template slot="name" slot-scope="data">
                        {{ data.item.name }}
                    </template>
                    <template slot="age" slot-scope="data">
                        {{ data.item.age }}
                    </template>
                    <template slot="address" slot-scope="data">
                        {{ data.item.address }}
                    </template>
                    <template slot="action" slot-scope="row">
                        <b-button size="sm" variant="primary" @click.stop="info(row.item, row.index, $event.target)">
                            <i class="fa fa-pencil-square-o"></i>&nbsp;Update
                        </b-button>
                        <b-button size="sm" variant="danger" @click.stop="remove(row.item, row.index, $event.target)" >
                            <i class="fa fa-trash-o"></i>&nbsp;Delete
                        </b-button>
                    </template>
                </b-table>
            </b-card-body>
        </b-card>
        
        <b-card no-header v-show="isShowAddUser">
            <template slot="header">Add Student</template>
            <b-card-body>
                <form @submit.prevent="create" @keydown="form.onKeydown($event)">
                    <alert-success :form="form" :message="'Student has been added'"/>
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

                    <!-- Address -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right">Address</label>
                        <div class="col-md-7">
                        <input v-model="form.address" :class="{ 'is-invalid': form.errors.has('address') }" class="form-control" type="text" name="address">
                        <has-error :form="form" field="address"/>
                        </div>
                    </div>

                    <!-- Birthdate -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right">Birrthdate</label>
                        <div class="col-md-7">
                        <datePicker name="birthdate" v-model="form.birthdate" :class="{ 'is-invalid': form.errors.has('birthdate') }" :config="datePickerOptions"></datePicker>
                        <has-error :form="form" field="birthdate"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-3 offset-md-3 d-flex">
                        <!-- Submit Button -->
                        <v-button :loading="form.busy">Add</v-button>
                        </div>
                    </div>
                </form>
            </b-card-body>
            <template slot="footer">
                <b-button @click="isShowAddUser = false">Back</b-button>
            </template>
        </b-card>
        
        <b-card header="Add User" no-header v-show="isShowImportUser">
            <template slot="header">Import Student</template>
            <b-card-body> 
                <upload-excel-component :on-success="handleSuccess" :before-upload="beforeUpload"/>
                <!-- <el-table :data="tableData" border highlight-current-row style="width: 100%;margin-top:20px;">
                    <el-table-column v-for="item of tableHeader" :prop="item" :label="item" :key="item"/>
                </el-table> -->
                
                <b-table striped hover :items="tableData" :fields="tableHeader" :responsive="true" :sort-by.sync="sortBy" :show-empty="true" style="margin-top:20px;">
                    <template slot="first_name" slot-scope="data">
                        {{ data.item.first_name }}
                    </template>
                    <template slot="last_name" slot-scope="data">
                        {{ data.item.first_name }}
                    </template>
                    <template slot="address" slot-scope="data">
                        {{ data.item.address }}
                    </template>
                    <template slot="birthdate" slot-scope="data">
                        {{ data.item.birthdate}}
                    </template>
                </b-table>

            </b-card-body>
            <template slot="footer">
                <b-button @click="isShowImportUser = false">Back</b-button>
                <b-button variant="primary" @click="importExcel()">Upload</b-button>
            </template>
        </b-card>
    </b-col>


    <!-- Info modal -->
    <b-modal id="modalInfo" :title="modalInfo.title" v-model="modalInfoShow">
        <form @submit.prevent="update" @keydown="form.onKeydown($event)">
            <alert-success :form="form" :message="'Student has been updated'"/>

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

            <!-- Address -->
            <div class="form-group row">
                <label class="col-md-3 col-form-label text-md-right">Address</label>
                <div class="col-md-7">
                <input v-model="form.address" :class="{ 'is-invalid': form.errors.has('address') }" class="form-control" type="text" name="address">
                <has-error :form="form" field="address"/>
                </div>
            </div>

            <!-- Birthdate -->
            <div class="form-group row">
                <label class="col-md-3 col-form-label text-md-right">Birrthdate</label>
                <div class="col-md-7">
                <datePicker name="birthdate" v-model="form.birthdate" :class="{ 'is-invalid': form.errors.has('birthdate') }" :config="datePickerOptions"></datePicker>
                <has-error :form="form" field="birthdate"/>
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
</b-col>
</template>

<script>
import { mapGetters } from 'vuex'
import swal from 'sweetalert2'
import axios from 'axios'
import Form from 'vform'
import moment from 'moment'
import datePicker from 'vue-bootstrap-datetimepicker'
import 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css';
import UploadExcelComponent from '~/components/UploadExcel'

export default {
    props: ['section_id','grade_level_id'],
    components: {
        datePicker,
        UploadExcelComponent 
    },
    middleware: 'auth',
    data: function () {
        return {
            form: new Form({
                first_name: '',
                last_name: '',
                address: '',
                birthdate: null,
                id: null,
                role: 'student',
                section_id: this.section_id,
            }),
            datePickerOptions: {
                format: 'YYYY-MM-DD',
                useCurrent: false,
                showClear: true,
                showClose: true,
            },
            sortBy: 'name',
            modalInfo: { title: '', content: '' },
            fields: [
                {
                    key: 'name',
                    sortable: true
                },
                {
                    key: 'age',
                    sortable: true
                },
                {
                    key: 'address',
                    sortable: true
                },
                {
                    key: 'action',
                },
            ],
            modalInfoShow: false,
            list: [],
            students: [],
            section: {},
            addCount: 5,
            inputs: [],
            isShowAddUser: false,
            isShowImportUser: false,
            tableData: [],
            tableHeader: [],
            filter: '',
        }
    },
    computed: {
        ...mapGetters({
            authenticated: 'auth/check'
        }),
        filteredStudent() {
            return this.students.slice().filter(student => 
                student.name.toLowerCase().includes(this.filter.toLowerCase())
            );
        }
    },
    methods:{
        info (item, index, button) {
            let vm = this;
            this.form.clear();
            this.form.reset();
            this.modalInfo.title = item.name;
            this.form.id = item.id;
            this.form.first_name = item.first_name;
            this.form.last_name = item.last_name;
            this.form.address = item.address;
            this.form.birthdate = moment(item.birthdate).format('YYYY-MM-DD');
            this.$root.$emit('bv::show::modal', 'modalInfo', button)
        },
        showAddUser(){
            let vm = this;
            this.form.clear();
            this.form.reset();
            this.isShowAddUser = true;
        },
        showImportUser(){
            let vm = this;
            this.tableData = [];
            this.tableHeader = [];
            this.isShowImportUser = true;
        },
        remove (item, index, button) {
            let vm = this;  
            swal({
                title: 'Are you sure?',
                html:   `Delete ${item.name}. <br> You won't be able to revert this!`,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                showLoaderOnConfirm: true,
                preConfirm: (login) => {
                    return  axios.delete(`/api/school/grade-level/${this.grade_level_id}/student/${item.id}/destroy`).then( response => {
                        return response.data;
                    }).catch(error => console.log(error))
                },
                allowOutsideClick: () => !swal.isLoading()
                }).then((result) => {
                if (result.value) {
                    swal('Deleted!', result.value.message, 'success');
                    vm.list = result.value;
                    vm.getStudents(vm.list);
                }
            });
        },
        async update () {
            const { data } = await this.form.patch(`/api/school/grade-level/section/${this.section_id}/student/${this.form.id}/update`)
            this.list = data;
            this.getStudents(this.list);
        },
        async create () {
            const { data } = await this.form.post(`/api/school/grade-level/section/${this.section_id}/student/create`)
            this.list = data;
            this.getStudents(this.list);
        },
        getList() {
            let vm = this;
            axios.get(`/api/school/grade-level/section/${this.section_id}/student/list`).then( response => {
                vm.list = response.data;
                vm.getStudents(vm.list);
            }).catch(error => console.log(error))
        },
        getSection() {
            let vm = this;
            axios.get(`/api/school/grade-level/section/${this.section_id}/view`).then( response => {
                vm.section = response.data;
            }).catch(error => {
                // Redirect home.
                vm.$router.push({ name: 'Home' })
            })
        },
        getStudents(students) {
            let vm = this;
            this.students = [];
            students.forEach(student => vm.students.push(...[student.user]));
        },
        beforeUpload(file) {
            const isLt1M = file.size / 1024 / 1024 < 1
            if (isLt1M) {
                return true
            }
            this.$message({
                message: 'Please do not upload files larger than 1m in size.',
                type: 'warning'
            })
            return false
        },
        handleSuccess({ results, header }) {
            this.tableData = results
            this.tableHeader = header
        },
        importExcel(){
            let vm = this;
            let payload = {
                users: this.tableData,
                school_id: this.$route.params.school_id,
                grade_level_id: this.grade_level_id,
                section_id: this.section_id
            }
            if(this.tableData.length){
                axios.post(`/api/school/student/import`, payload).then( response => {
                    swal('Success!', response.data, 'success');
                    vm.tableData = [];
                    vm.tableHeader = [];
                }).catch(error => console.log(error))
            }
        },
        print() {
            // Pass the element id here
            this.$htmlToPaper('printMe');
        },
    },
    mounted: function () {
        let vm = this;
        this.getList()
        this.getSection()
        this.$nextTick(function() {
            
        })
    }
}
</script>

<style scoped lang="scss">
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
