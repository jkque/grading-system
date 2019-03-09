<template>
<b-col class="wrapper" v-if="user">
    <b-col class="animated fadeIn">
        <b-card no-header  v-show="isShowList">
            <template slot="header">Class Record</template>
            <b-col md="4">
                <b-btn variant="outline-primary"  @click="showAddPlan()"><i class="icon-plus"></i>&nbsp;Add</b-btn>
            </b-col> 
            <b-card-body>
                <b-table striped hover :items="list" :fields="fields" :responsive="true" :sort-by.sync="sortBy" :show-empty="true">
                    <template slot="name" slot-scope="data">
                        {{ data.item.name }}
                    </template>
                    <template slot="performance" slot-scope="data">
                        <b-button variant="link" class="px-0" @click="showPerformance(data.item.id)">{{ data.item.performances.length }} Performances</b-button>
                    </template>
                    <template slot="action" slot-scope="row">
                        <b-button size="sm" variant="primary" @click.stop="info(row.item, 'lessonPlan', $event.target)">
                            <i class="fa fa-pencil-square-o"></i>&nbsp;Update
                        </b-button>
                        <b-button size="sm" variant="danger" @click.stop="remove(row.item, 'lessonPlan', $event.target)" >
                            <i class="fa fa-trash-o"></i>&nbsp;Delete
                        </b-button>
                    </template>
                </b-table>
            </b-card-body>
        </b-card>

        <b-card no-header  v-show="isShowPerformanceList">
            <template slot="header">Class Record / Peformance</template>
            <b-col md="4">
                <b-btn variant="outline-primary" @click="showAddPerformance()"><i class="icon-plus" ></i>&nbsp;Add</b-btn>
            </b-col> 
            <b-card-body>
                <b-table striped hover :items="performanceList" :fields="performanceFields" :responsive="true" :sort-by.sync="sortBy" :show-empty="true">
                    <template slot="name" slot-scope="data">
                        {{ data.item.name }}
                    </template>
                    <template slot="count" slot-scope="row">
                        <b-button variant="link" class="px-0" @click="showAddScore(row.item)">{{ row.item.performance_score.length }} Counts</b-button>
                    </template>
                    <template slot="percent" slot-scope="data">
                       {{ data.item.percentage*100 }} %
                    </template>
                    <template slot="action" slot-scope="row">
                        <b-button size="sm" variant="primary" @click.stop="info(row.item, 'performance', $event.target)">
                            <i class="fa fa-pencil-square-o"></i>&nbsp;Update
                        </b-button>
                        <b-button size="sm" variant="danger" @click.stop="remove(row.item, 'performance', $event.target)" >
                            <i class="fa fa-trash-o"></i>&nbsp;Delete
                        </b-button>
                    </template>
                </b-table>
            </b-card-body>
            <template slot="footer">
                <b-button @click="isShowPerformanceList = false; isShowList = true">Back</b-button>
            </template>
        </b-card>

        <b-card header="Add Performance" v-show="isShowAddPerformance">
            <b-card-body class="add-section">
                <form @submit.prevent="createPerformance" @keydown="form.onKeydown($event)">
                    <alert-success :form="form" :message="'Performance has been added'"/>
                    <alert-error :form="form" message="There were some problems with your input."></alert-error>

                    <!-- Name -->
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label">Name</label>
                        <div class="col-md-7">
                        <input v-model="form.name" :class="{ 'is-invalid': form.errors.has('name') }" class="form-control" type="text" name="name">
                        <has-error :form="form" field="name"/>
                        </div>
                    </div>

                    <!-- Percentage -->
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label">Percentage</label>
                        <div class="col-md-7">
                        <input v-model="form.percentage" :class="{ 'is-invalid': form.errors.has('percentage') }" class="form-control" type="text" name="percentage">
                        <has-error :form="form" field="percentage"/>
                        </div>
                    </div>
                    <b-button variant="success" size="sm" @click="addRow()">Add Row</b-button>
                    <!-- <v-button type="success" class="btn-sm" @click="addRow()">Add Row</v-button> -->
                    <ul class="list-unstyled">
                        <li v-for="(input, index) in form.inputs" :key="index">
                            <div class="input-list">
                                <div class="label">
                                    Score
                                </div>
                                <div class="name">
                                    <!-- <input v-model="input.score" class="form-control" type="number" name="name" placeholder="Score"> -->
                                    <b-input type="number" v-model="input.score" :state="validation(index)" name="name" placeholder="Score" class="newScore"/>
                                    <b-form-invalid-feedback :state="validation(index)">
                                        Score should not be null
                                    </b-form-invalid-feedback>
                                    <b-form-valid-feedback :state="validation(index)">
                                        Looks Good.
                                    </b-form-valid-feedback>
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
                <b-button @click="isShowAddPerformance = false; isShowPerformanceList = true">Back</b-button>
            </template>
                <!-- <b-button href="#" variant="primary">Go somewhere</b-button> -->
        </b-card>

        <b-card header="Class Record / Peformance / Score" v-show="isShowAddScore">
            <b-card-body class="add-section">
                <form @submit.prevent="updateScore" @keydown="form.onKeydown($event)">
                    <alert-success :form="form" :message="'Performance has been added'"/>
                    <alert-error :form="form" message="There were some problems with your input."></alert-error>
                    <b-button variant="success" size="sm" @click="addRow()">Add Row</b-button>
                    <ul class="list-unstyled">
                        <li v-for="(input, index) in form.inputs" :key="index">
                            <div class="input-list">
                                <div class="label">
                                    Score
                                </div>
                                <div class="name">
                                    <!-- <input v-model="input.score" class="form-control" type="text" name="name" placeholder="Score"> -->
                                    <b-input type="number" v-model="input.score" :state="validation(index)" name="name" placeholder="Score" class="newScore"/>
                                    <b-form-invalid-feedback :state="validation(index)">
                                        Score should not be null
                                    </b-form-invalid-feedback>
                                    <b-form-valid-feedback :state="validation(index)">
                                        Looks Good.
                                    </b-form-valid-feedback>
                                </div>
                                <div class="remove">
                                    <a href="#" @click="deleteRow(index)"><i class="fa fa-times-circle" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <v-button type="primary" :loading="form.busy" >Update</v-button>
                </form>

            </b-card-body>
            <template slot="footer">
                <b-button @click="isShowAddScore = false; isShowPerformanceList = true">Back</b-button>
            </template>
                <!-- <b-button href="#" variant="primary">Go somewhere</b-button> -->
        </b-card>
        
        <b-card no-header  v-show="showCrafting">
            <b-progress :value="100" :max="100" animated></b-progress>
            Please wait wee are crafting some good for you...
        </b-card>
    </b-col>

    <!-- Info modal -->
    <b-modal id="modalInfo" :title="modalInfo.title" v-model="modalInfoShow">
        <form @submit.prevent="update" @keydown="form.onKeydown($event)">
            <alert-success :form="form" :message="'Class Record has been updated'"/>

            <!-- Name -->
            <div class="form-group row">
                <label class="col-md-3 col-form-label text-md-right">Name</label>
                <div class="col-md-7">
                <input v-model="form.name" :class="{ 'is-invalid': form.errors.has('name') }" class="form-control" type="text" name="name">
                <has-error :form="form" field="name"/>
                </div>
            </div>

            <!-- Name -->
            <div class="form-group row" v-show="modalInfo.percentage">
                <label class="col-md-3 col-form-label text-md-right">Percentage</label>
                <div class="col-md-7">
                <input v-model="form.percentage" :class="{ 'is-invalid': form.errors.has('percentage') }" class="form-control" type="text" name="percentage">
                <has-error :form="form" field="percentage"/>
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

    <!-- Add modal -->
    <b-modal id="modalAdd" :title="'Add new Class Record'" v-model="modalAddShow">
        <form @submit.prevent="createPlan" @keydown="form.onKeydown($event)">
            <alert-success :form="form" :message="'Class Record has been added'"/>

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
                    <v-button :loading="form.busy" type="primary">Add</v-button>
                </div>
            </div>
        </form>
        <div slot="modal-footer">
            <b-btn class="float-right" variant="secondary" @click="modalAddShow=false">Close</b-btn>
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
    components: {
        cSwitch,
    },
    middleware: 'auth',
    name: 'lessonPlan',
    computed: {
        ...mapGetters({
            user: 'auth/user',
            school: 'auth/school',
        }),
    },
    metaInfo () {
        return { title: 'Class Record' }
    },
    data: function () {
        return {
            form: new Form({
                name: '',
                id: null,
                lesson_plan_id: null,
                percentage: null,
                inputs: [],
            }),
            sortBy: 'name',
            modalInfo: { title: '', percentage: false },
            fields: [
                {
                    key: 'name',
                    sortable: true
                },
                {
                    key: 'performance',
                },
                {
                    key: 'action',
                },
            ],
            performanceFields: [
                {
                    key: 'name',
                    sortable: true
                },
                {
                    key: 'count',
                },
                {
                    key: 'percent',
                },
                {
                    key: 'action',
                },
            ],
            modalInfoShow: false,
            modalAddShow: false,
            list: [],
            performanceList: [],
            inputs: [],
            isShowAddPerformance: false,
            showCrafting: false,
            isShowList: true,
            isShowPerformanceList: false,
            isShowAddScore: null,
            activeLessonPlan: null,
            activePerformance: null,
        }
    },
    methods:{
        clearForm(){
            this.form.clear();
            this.form.name = '';
            this.form.lesson_plan_id = null;
            this.form.percentage = null,
            this.form.inputs = [];

        },
        info (item, type, button) {
            this.clearForm();
            this.modalInfo.title = item.name;
            this.form.id = item.id;
            this.form.name = item.name;
            this.modalInfo.percentage = false;
            switch (type) {
                case 'performance':
                    this.form.percentage = item.percentage*100;
                    this.modalInfo.percentage = true;
                    this.form.lesson_plan_id = item.lesson_plan_id;
                    break;
            }
            this.$root.$emit('bv::show::modal', 'modalInfo', button)
        },
        validation(index) {
            return this.form.inputs[index].score != '' ? true : false;
        },
        checkValidation(){
            let checked = this.form.inputs.slice().filter(input => input.score == '');
            return checked.length ? true : false;
        },
        addRow() {
            let vm = this;
            this.form.inputs.push({
                score: '',
            })
        },    
        deleteRow(index) {
            this.form.inputs.splice(index,1)
        },
        showAddPlan(){
            this.clearForm();
            this.$root.$emit('bv::show::modal', 'modalAdd')
        },
        showAddPerformance(){
            this.clearForm();
            for (let i = 1; i <= 5; i++) {
                this.form.inputs.push({
                    score: '',
                })
            }
            this.form.lesson_plan_id = this.activeLessonPlan;
            this.isShowAddPerformance = true
            this.isShowPerformanceList = false;
        },
        showAddScore(item){
            this.clearForm();
            this.isShowAddScore = true;
            this.isShowPerformanceList = false;
            this.activeLessonPlan = item.lesson_plan_id;
            this.form.id = item.id;
            for (let i = 0; i < item.performance_score.length; i++) {
                this.form.inputs.push({
                    score: item.performance_score[i].score,
                    id: item.performance_score[i].id,
                })
            }
        },
        remove (item, type, button) {
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
                    switch (type) {
                        case 'lessonPlan':
                            return  axios.delete(`/api/teacher/lesson-plans/${item.id}/destroy`).then( response => {
                                return response.data;
                            }).catch(error => console.log(error))
                            break;
                        case 'performance':
                            return  axios.delete(`/api/teacher/performance/${item.id}/destroy`).then( response => {
                                return response.data;
                            }).catch(error => console.log(error))
                            break;
                    }
                },
                allowOutsideClick: () => !swal.isLoading()
                }).then((result) => {
                if (result.value) {
                    swal('Deleted!', result.value.message, 'success');
                    vm.list = result.value;
                    if(type == 'performance'){
                        vm.getPerformanceList(item.lesson_plan_id)
                    }
                }
                
            });
        },
        async update () {
            if(this.modalInfo.percentage){
                const { data } = await this.form.patch(`/api/teacher/performance/${this.form.id}/update`)
                this.list = data;
                this.getPerformanceList(this.form.lesson_plan_id)
            }else{
                const { data } = await this.form.patch(`/api/teacher/lesson-plans/${this.form.id}/update`)
                this.list = data;
            }
        },
        async updateScore () {
            if(!this.checkValidation()){
                const { data } = await this.form.patch(`/api/teacher/performanceScore/update`)
                this.list = data;
                this.getPerformanceList(this.activeLessonPlan)
            }
        },
        async createPlan () {
            const { data } = await this.form.post(`/api/teacher/lesson-plans/create`)
            this.list = data;
        },
        async createPerformance () {
            if(!this.checkValidation()){
                const { data } = await this.form.post(`/api/teacher/performance/create`)
                this.list = data;
                this.getPerformanceList(this.activeLessonPlan)

            }
        },
        getList() {
            let vm = this;
            axios.get(`/api/teacher/lesson-plans/list`).then( response => {
                vm.list = response.data;
                if(!vm.list.length){
                    vm.isShowList = false;
                    vm.showCrafting = true;
                    axios.post(`/api/teacher/lesson-plans/craft`).then( response => {
                        vm.list = response.data;
                        vm.showCrafting = false;
                        vm.isShowList = true;
                    }).catch(error => console.log(error))
                }
            }).catch(error => console.log(error))
        },
        getPerformanceList(plan_id){
            this.performanceList = [];
            this.performanceList = this.list.slice().filter(obj => obj.id == plan_id)[0].performances
        },
        showPerformance(plan_id){
            this.getPerformanceList(plan_id)
            this.isShowList = false;
            this.isShowPerformanceList = true;
            this.activeLessonPlan = plan_id;
        }
    },
    mounted: function () {
        let vm = this;
        this.getList()
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
    & .label {
        flex: 0 1 10%;
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
