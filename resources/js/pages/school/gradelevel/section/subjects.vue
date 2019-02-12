<template>
<b-col class="wrapper">
    <b-col class="animated fadeIn">
        <b-row>
            <b-col cols="12" xl="6">
                <b-card no-header  v-show="!isShowAdd">
                    <template slot="header">
                    {{`${section.name} / Subjects` }}
                    </template>
                    <b-card-body>
                        <b-table striped hover :items="list" :fields="fields" :responsive="true" :sort-by.sync="sortBy" :show-empty="true">
                            <template slot="name" slot-scope="data">
                                {{ data.item.subject.name }}
                            </template>
                            <template slot="teacher" slot-scope="data">
                                {{ data.item.teacher ? data.item.teacher.name : 'n/a'}}
                            </template>
                            <template slot="action" slot-scope="row">
                                <b-button size="sm" variant="primary" @click.stop="info(row.item, row.index, $event.target)">
                                    <i class="fa fa-pencil-square-o"></i>&nbsp;Update
                                </b-button>
                            </template>
                        </b-table>
                    </b-card-body>
                </b-card>
            </b-col>
        </b-row>
    </b-col>


    <!-- Info modal -->
    <b-modal id="modalInfo" :title="modalInfo.title" v-model="modalInfoShow">
        <form @submit.prevent="update" @keydown="form.onKeydown($event)">
            <alert-success :form="form" :message="'Subject has been updated'"/>

            <!-- Teacher -->
            <div class="form-group row">
                <label class="col-md-3 col-form-label text-md-right">Teacher</label>
                <div class="col-md-7">
                    <b-form-select
                        :options="teachersSelect"
                        v-model="form.teacher_id">
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
    props: ['section_id','grade_level_id'],
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
                teacher_id: null,
            }),
            sortBy: 'name',
            modalInfo: { title: '', content: '' },
            fields: [
                {
                    key: 'name',
                    sortable: true
                },
                {
                    key: 'teacher',
                    sortable: true
                },
                {
                    key: 'action',
                },
            ],
            modalInfoShow: false,
            list: [],
            section: {},
            addCount: 5,
            inputs: [],
            isShowAdd: false,
            teachersSelect:[]
        }
    },
    methods:{
        info (item, index, button) {
            this.modalInfo.title = item.subject.name;
            this.form.id = item.id;
            this.$root.$emit('bv::show::modal', 'modalInfo', button)
        },
        async update () {
            const { data } = await this.form.patch(`/api/section-subject/${this.form.id}/update`)
            this.list = data;
        },
        getList() {
            let vm = this;
            axios.get(`/api/school/grade-level/section/${this.section_id}/subject/list`).then( response => {
                vm.list = response.data;
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
    },
    mounted: function () {
        let vm = this;
        this.getList()
        this.getSection()
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
        flex: 0 1 40%;
    }
    & .remove {
        flex: 0 1 40%;
        a{
            color: #f44336;
            i{
                    font-size: 1.5em;
            }
        }
    }

  }
</style>
