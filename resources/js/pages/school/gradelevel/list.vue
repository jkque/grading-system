<template>
<div class="wrapper">
    <div class="animated fadeIn">
        <b-card header="Grade Level">
            <b-table striped hover :items="list" :fields="fields" :responsive="true" :sort-by.sync="sortBy">
                <template slot="level" slot-scope="data">
                    {{ data.item.level }}
                </template>
                <template slot="name" slot-scope="data">
                    {{ data.item.name }}
                </template>
                <template slot="sections" slot-scope="data">
                    <b-button variant="link" class="px-0" :to="{ path: `${$route.fullPath}/${data.item.id}/sections`}">{{ data.item.sections.length }} Sections</b-button>
                </template>
                <template slot="students" slot-scope="data">
                    <b-button variant="link" class="px-0" :to="{ path: `${$route.fullPath}/${data.item.id}/students` }">{{ data.item.students.length }} Students</b-button>
                </template>
                <template slot="teachers" slot-scope="data">
                    <b-button variant="link" class="px-0" :to="{ path: `${$route.fullPath}/${data.item.id}/teachers` }">{{ data.item.teachers.length }} Teachers</b-button>
                </template>
                <template slot="subjects" slot-scope="data">
                    <b-button variant="link" class="px-0" :to="{ path: `${$route.fullPath}/${data.item.id}/subjects` }">{{ data.item.subjects.length }} Subjects</b-button>
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
            <!-- Name -->
            <div class="form-group row">
                <label class="col-md-3 col-form-label text-md-right">Name</label>
                <div class="col-md-7">
                <input v-model="form.name" :class="{ 'is-invalid': form.errors.has('name') }" class="form-control" type="text" name="name">
                <has-error :form="form" field="name"/>
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
import axios from 'axios'
import Form from 'vform'

export default {
    props:['school_id'],
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
                    key: 'level',
                    sortable: true
                },
                {
                    key: 'name',
                    sortable: true
                },
                {
                    key: 'sections',
                },
                {
                    key: 'students',
                },
                {
                    key: 'teachers',
                },
                {
                    key: 'subjects',
                },
                // {
                //     key: 'action',
                // }
            ],
            modalInfoShow: false,
            list: [],
        }
    },
    methods:{
        info (item, index, button) {
            this.form.clear();
            this.modalInfo.title = item.name;
            this.form.id = item.id;
            this.form.name = item.name;
            this.form.status = item.status;
            this.$root.$emit('bv::show::modal', 'modalInfo', button)
        },
        async update () {
            const { data } = await this.form.patch(`/api/school/grade-level/${this.form.id}/update`)
            this.getList();
        },
        getList() {
            let vm = this;
            axios.get(`/api/school/${this.$route.params.school_id}/grade-level/list`).then( response => {
                vm.list = response.data;
            }).catch(error => console.log(error))
        }
    },
    mounted: function () {
        this.getList()
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
