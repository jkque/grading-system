<template>
<b-col class="wrapper">
    <b-col class="animated fadeIn">
        <b-card no-header  v-show="!isShowAdd">
            <template slot="header">
            {{`${gradeLevel.name} / Sections` }}
            </template>
            <b-col md="2">
                <b-input-group>
                    <b-form-input v-model="addCount"></b-form-input>
                    <b-input-group-append @click="showAdd()">
                        <b-btn variant="outline-primary"><i class="icon-plus" @click="showAdd()"></i>&nbsp;Add</b-btn>
                    </b-input-group-append>
                </b-input-group>
            </b-col> 
            <b-card-body>
                <b-table striped hover :items="list" :fields="fields" :responsive="true" :sort-by.sync="sortBy" :show-empty="true">
                    <template slot="name" slot-scope="data">
                        {{ data.item.name }}
                    </template>
                    <template slot="adviser" slot-scope="data">
                        {{ data.item.adviser ? data.item.adviser.name : 'n/a' }}
                    </template>
                    <template slot="students" slot-scope="data">
                        <b-button variant="link" class="px-0" :to="{ path: `${$route.fullPath}/${data.item.id}/students` }">{{ data.item.students ? data.item.students.length : 'n/a' }} Students</b-button>
                    </template>
                    <template slot="subjects" slot-scope="data">
                        <b-button variant="link" class="px-0" :to="{ path: `${$route.fullPath}/${data.item.id}/subjects` }">{{ data.item.subjects ? data.item.subjects.length : 'n/a' }} Subjects</b-button>
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
        <b-card header="Add Section" v-show="isShowAdd">
            <b-col md="2">
                <b-button variant="success" @click="addRow()">Add Row</b-button>
            </b-col> 
            <b-card-body class="add-section">
                <form @submit.prevent="create" @keydown="form.onKeydown($event)">
                    <alert-success :form="form" :message="'Sections has been added'"/>
                    <alert-error :form="form" message="There were some problems with your input."></alert-error>

                    <ul class="list-unstyled">
                        <li v-for="(input, index) in form.inputs" :key="index">
                            <div class="input-list">
                                <div class="name">
                                    <input v-model="input.name" class="form-control" type="text" name="name" placeholder="Section Name">
                                </div>
                                <div class="remove">
                                    <a href="#" @click="deleteRow(index)"><i class="fa fa-times-circle" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <v-button type="primary" :loading="form.busy" >Add</v-button>
                </form>

            </b-card-body>
            <template slot="footer">
                <b-button @click="isShowAdd = false">Back</b-button>
            </template>
                <!-- <b-button href="#" variant="primary">Go somewhere</b-button> -->
        </b-card>
    </b-col>


    <!-- Info modal -->
    <b-modal id="modalInfo" :title="modalInfo.title" v-model="modalInfoShow">
        <form @submit.prevent="update" @keydown="form.onKeydown($event)">
            <alert-success :form="form" :message="'Section has been updated'"/>

            <!-- Name -->
            <div class="form-group row">
                <label class="col-md-3 col-form-label text-md-right">Name</label>
                <div class="col-md-7">
                <input v-model="form.name" :class="{ 'is-invalid': form.errors.has('name') }" class="form-control" type="text" name="name">
                <has-error :form="form" field="name"/>
                </div>
            </div>

            <!-- Section -->
            <div class="form-group row">
                <label class="col-md-3 col-form-label text-md-right">Adviser</label>
                <div class="col-md-7">
                    <b-form-select
                        :options="teachersSelect"
                        v-model="form.user_id">
                    </b-form-select>
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
import { Switch as cSwitch } from '@coreui/vue'
import swal from 'sweetalert2'
import axios from 'axios'
import Form from 'vform'
import moment from 'moment'

export default {
    props: ['grade_level_id'],
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
                inputs: [],
                id: null,
                name: '',
                user_id: null,
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
                    key: 'adviser',
                    sortable: true
                },
                {
                    key: 'students',
                },
                {
                    key: 'subjects',
                },
                {
                    key: 'action',
                },
            ],
            modalInfoShow: false,
            list: [],
            gradeLevel: {},
            addCount: 5,
            inputs: [],
            isShowAdd: false,
            teachers: [],
            teachersSelect:[]
        }
    },
    methods:{
        info (item, index, button) {
            this.form.clear();
            this.modalInfo.title = item.name;
            this.form.id = item.id;
            this.form.name = item.name;
            this.form.user_id = item.user_id;
            this.$root.$emit('bv::show::modal', 'modalInfo', button)
        },
        addRow() {
            let vm = this;
            this.form.inputs.push({
                name: `Section ${vm.form.inputs.length+1}`,
            })
        },    
        deleteRow(index) {
            this.form.inputs.splice(index,1)
        },
        showAdd(){
            let vm = this;
            this.form.inputs = [];
            this.form.clear();
            for (let i = 1; i <= this.addCount; i++) {
                vm.form.inputs.push({
                    name: 'Section '+i,
                })
            }
            this.isShowAdd = true;
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
                    return  axios.delete(`/api/school/section/${item.id}/destroy`).then( response => {
                        return response.data;
                    }).catch(error => console.log(error))
                },
                allowOutsideClick: () => !swal.isLoading()
                }).then((result) => {
                if (result.value) {
                    swal('Deleted!', result.value.message, 'success');
                    vm.list = result.value;
                }
            });
        },
        async update () {
            const { data } = await this.form.patch(`/api/school/section/${this.form.id}/update`)
            this.list = data;
        },
        async create () {
            const { data } = await this.form.post(`/api/school/grade-level/${this.gradeLevel.id}/section/create`)
            this.list = data;
        },
        getList() {
            let vm = this;
            axios.get(`/api/school/grade-level/${this.$route.params.grade_level_id}/section/list`).then( response => {
                vm.list = response.data;
            }).catch(error => console.log(error))
        },
        getTeachers() {
            let vm = this;
            axios.get(`/api/school/${this.$route.params.school_id}/teacher/list`).then( response => {
                vm.teachers = response.data;
                if(vm.teachers.length){
                    vm.teachersSelect = vm.teachers.slice().map(obj =>{ 
                        var rObj = {};
                        rObj.text = obj.user.name;
                        rObj.value = obj.user.id;
                        return rObj;
                    });
                }
            }).catch(error => console.log(error))
        },
        getGradeLevel() {
            let vm = this;
            axios.get(`/api/school/grade-level/${this.$route.params.grade_level_id}/view`).then( response => {
                vm.gradeLevel = response.data;
            }).catch(error => {
                // Redirect home.
                vm.$router.push({ name: 'Home' })
            })
        }
    },
    mounted: function () {
        let vm = this;
        this.getList()
        this.getGradeLevel()
        this.getTeachers()
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
  .add-section{
    .input-list{
        display: flex;
        align-items: center;
        &>div{
            padding: .6rem;
            word-wrap: break-word;
        }
    }
    & .name {
        flex: 0 1 20%;
    }
    & .remove {
        flex: 0 1 20%;
        a{
            color: #f44336;
            i{
                    font-size: 1.5em;
            }
        }
    }

  }
</style>
