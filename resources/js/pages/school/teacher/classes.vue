<template>
<div class="wrapper" v-if="user">
    <div class="animated fadeIn">
        <b-card no-header  v-if="isMain">
            <template slot="header">Classes {{getActiveGP().name}}</template>
            <b-table striped hover :items="list" :fields="fields" :responsive="true" :sort-by.sync="sortBy" :show-empty="true">
                <template slot="gradeLevel" slot-scope="data">
                    {{ data.item.subject.grade_level.name }}
                </template>
                <template slot="section" slot-scope="data">
                    {{ data.item.section.name }}
                </template>
                <template slot="subject" slot-scope="data">
                    {{ data.item.subject.name }}
                </template>
                <template slot="students" slot-scope="row">
                    <b-button variant="link" class="px-0" @click="showStudentsList(row.item)">{{ row.item.section.students.length }} Students</b-button>
                </template>
                <template slot="classRecord" slot-scope="row">
                    <b-button variant="link" class="px-0" @click="info(row.item, row.index, $event.target)">{{ row.item.lesson_plan ? row.item.lesson_plan.lesson_plan.name : 'n/a' }}</b-button>
                </template>
                <template slot="action" slot-scope="row">
                    <b-button size="sm" variant="success" @click.stop="showPerformanceList(row.item)">
                        <i class="fa fa-pencil-square-o"></i>&nbsp;Peformances
                    </b-button>
                    <b-button size="sm" variant="success" @click.stop="showGrading(row.item)">
                        <i class="fa fa-pencil-square-o"></i>&nbsp;Grading
                    </b-button>
                </template>
            </b-table>
        </b-card>

        <b-card no-header v-if="isStudentList">
            <template slot="header">{{activeClass ? activeClass.subject.grade_level.name : ''}} / {{activeClass ? activeClass.section.name : ''}} / {{activeClass ? activeClass.subject.name : ''}} / Students</template> 
                <b-row>
                    <b-col md="6">
                        <b-input-group>
                            <b-form-input v-model="filter" placeholder="Type to Search" />
                            <b-input-group-append>
                            <b-btn :disabled="!filter" @click="filter = ''">Clear</b-btn>
                            </b-input-group-append>
                        </b-input-group>
                    </b-col>
                    <b-col md="6">
                        <b-button @click="print()">Print</b-button>
                    </b-col>
                </b-row>
                <b-card-body class="add-section" id="printMe"> 
                    <b-table  striped hover :items="students" :fields="fieldsStudent" :responsive="true" :sort-by.sync="sortBy" :filter="filter" :show-empty="true">
                        <template slot="name" slot-scope="data">
                            {{ data.item.name }}
                        </template>
                        <template slot="age" slot-scope="data">
                            {{ data.item.age }}
                        </template>
                        <template slot="address" slot-scope="data">
                            {{ data.item.address }}
                        </template>
                    </b-table>
                </b-card-body>
            <template slot="footer">
                <b-button @click="isStudentList = false; isMain = true">Back</b-button>
            </template>
        </b-card>

        <b-card no-header v-if="isPerformancesList">
            <template slot="header">{{activeClass ? activeClass.subject.grade_level.name : ''}} / {{activeClass ? activeClass.section.name : ''}} / {{activeClass ? activeClass.subject.name : ''}} / Performances List</template> 
                <b-card-body class="add-section"> 
                    <b-table  striped hover :items="performances" :fields="fieldsPerforfance" :responsive="true" :show-empty="true">
                        <template slot="name" slot-scope="data">    
                            {{ data.item.name }}
                        </template>
                        <template slot="percentage" slot-scope="data">
                            {{ data.item.percentage*100 }} %
                        </template>
                        <template slot="action" slot-scope="row">
                            <b-button size="sm" v-for="(gp, index) in school.grading_periods" :key="index"  :variant="getActiveGP().id == gp.id ? 'success' : 'secondary'"  @click.stop="showUserPerformance(row.item, gp)" style="margin-left:10px">
                                <i class="fa fa-pencil-square-o"></i>&nbsp; {{gp.name}}
                            </b-button> 
                        </template>
                    </b-table>
                </b-card-body>
            <template slot="footer">
                <b-button @click="isPerformancesList = false; isMain = true">Back</b-button>
            </template>
        </b-card>

        <b-card no-header v-if="isStudentPerformance">
            <template slot="header">{{activeClass ? activeClass.subject.grade_level.name : ''}} / {{activeClass ? activeClass.section.name : ''}} / {{activeClass ? activeClass.subject.name : ''}} / Student Performance / {{activePerformance.name}} / {{selectedGP.name}}</template> 
                <b-col md="12">
                    <b-input-group>
                        <b-form-input v-model="filter" placeholder="Type to Search" />
                        <b-input-group-append>
                        <b-btn :disabled="!filter" @click="filter = ''">Clear</b-btn>
                        </b-input-group-append>
                    </b-input-group>
                </b-col>
                <b-card-body class="add-section"> 
                    <div class="header">
                        <div class="name">Name</div>
                        <div class="score" v-for="(field, index) in fieldsUserPerformance" :key="index">{{ activePerformance.name}} {{ field.key }} - {{field.total_score}} Points</div>
                    </div>
                    <ul v-if="!filteredStudent.length" class="list-unstyled">
                        <li class="empty">No records available.</li>
                    </ul>
                    <ul class="list-unstyled">
                        <li v-for="(student, index) in filteredStudent" :key="index">
                            <div class="input-list">
                                <div class="name">
                                    {{student.name}}
                                </div>
                                <div class="score" v-for="(performance, index) in student.performances" :key="index">
                                    <input v-model="performance.score" class="form-control" type="number" placeholder="Score"  min="0"> 
                                    <!-- Total: <b>{{getTotalScore(performance.performance_score_id)}}</b> -->
                                </div>
                            </div>
                        </li>
                    </ul>
                </b-card-body>
                <b-col md="12">
                    <b-button size="lg" variant="success" block @click="updateUserPerformance()">Save</b-button>
                </b-col>
            <template slot="footer">
                <b-button @click="isStudentPerformance = false; isPerformancesList = true">Back</b-button>
            </template>
        </b-card>

        <b-card no-header v-if="isGrading">
            <template slot="header">{{activeClass ? activeClass.subject.grade_level.name : ''}} / {{activeClass ? activeClass.section.name : ''}} / {{activeClass ? activeClass.subject.name : ''}} / Grading / {{ getActiveGP().name }}</template> 
                <b-col md="12">
                    <b-input-group>
                        <b-form-input v-model="filter" placeholder="Type to Search" />
                        <b-input-group-append>
                        <b-btn :disabled="!filter" @click="filter = ''">Clear</b-btn>
                        </b-input-group-append>
                    </b-input-group>
                </b-col>
                <b-card-body class="add-grading"> 
                        <b-row class="header">
                            <b-col>Name</b-col>
                            <b-col v-for="(field, index) in fieldsGrading" :key="index">{{ field.key}}</b-col>
                            <b-col>Final</b-col>
                            <b-col>Remarks</b-col>
                        </b-row>
                        <b-row  v-if="!filteredStudent.length">
                            <b-col>No records available.</b-col>
                        </b-row>
                        <b-row v-for="(student, index) in filteredStudent" :key="index">
                            <b-col>{{student.name}}</b-col>
                            <template  v-for="(grade, index) in student.grades" >
                                <b-col :key="'score-'+index">
                                    <b>{{Math.round(grade.score)}}</b>
                                </b-col>
                                <b-col :key="'comment-'+index">
                                    <b-form-textarea placeholder="Enter something" :rows="2" :max-rows="6" v-model="grade.comment"> </b-form-textarea>
                                </b-col>
                            </template>
                            <b-col :key="'final-'+index">{{computeFinalGrade(student.grades)}}</b-col>
                            <b-col :key="'remark-'+index"><b>{{computeFinalGrade(student.grades) >= school.passing_rate ? 'PASSED' : 'FAILED'}}</b></b-col>
                        </b-row>
                </b-card-body>
                <b-col md="12">
                    <b-button size="lg" variant="success" block @click="updateUserGrades()">Save</b-button>
                </b-col>
            <template slot="footer">
                <b-button @click="isGrading = false; isMain = true">Back</b-button>
            </template>
        </b-card>
        
        <b-card no-header  v-show="showCrafting">
            <b-progress :value="100" :max="100" animated></b-progress>
            Please wait we are processing the grades for you...
        </b-card>

    </div>

    <!-- Info modal -->
    <b-modal id="modalInfo" :title="modalInfo.title" v-model="modalInfoShow">
        <form @submit.prevent="updateLessonPlan" @keydown="form.onKeydown($event)">
            <alert-success :form="form" :message="'Class record has been updated'"/>

            <!-- Lesson Plan -->
            <div class="form-group row">
                <label class="col-md-3 col-form-label text-md-right">Class Record</label>
                <div class="col-md-7">
                    <b-form-select
                        :options="planSelect"
                        v-model="form.lesson_plan_id">
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
</div>
</template>

<script>
import { mapGetters } from 'vuex'
import swal from 'sweetalert2'
import axios from 'axios'
import Form from 'vform'

export default {
    middleware: 'auth',
    name: 'Classes',
    computed: {
        ...mapGetters({
            user: 'auth/user',
            school: 'auth/school',
        }),
        filteredStudent() {
            return this.students.slice().filter(student => 
                student.name.toLowerCase().includes(this.filter.toLowerCase())
            );
        }
    },
    metaInfo () {
        return { title: 'Classes' }
    },
    data: function () {
        return {
            form: new Form({
                name: '',
                id: null,
                status: false,
                lesson_plan_id: null,
                grading_period_id: null,
                section_id: null,
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
                    key: 'gradeLevel',
                    sortable: true
                },
                {
                    key: 'section',
                    sortable: true
                },
                {
                    key: 'subject',
                    sortable: true
                },
                {
                    key: 'students',
                },
                {
                    key: 'classRecord',
                },
                {
                    key: 'action',
                }
            ],
            fieldsStudent: [
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
            ],
            fieldsPerforfance: [
                {
                    key: 'name',
                    sortable: true
                },
                {
                    key: 'percentage',
                    sortable: true
                },
                {
                    key: 'action',
                },
            ],
            fieldsUserPerformance: [],
            fieldsGrading: [],
            modalInfoShow: false,
            list: [],
            lessonPlans: [],
            students: [],
            performances: [],
            planSelect: [],
            userPerformances: [],
            isMain: true,
            isStudentList: false,
            isPerformancesList: false,
            isGrading: false,
            showCrafting: false,
            activeClass: null,
            activePerformance: null,
            filter: '',
            isStudentPerformance: false,
            selectedGP: null,
        }
    },
    methods:{
        info (item, index, button) {
            this.form.clear();
            this.modalInfo.title = 'Add Class Record';
            this.form.id = item.id; 
            this.form.grading_period_id = this.getActiveGP().id;
            this.form.section_id = item.section_id;
            this.$root.$emit('bv::show::modal', 'modalInfo', button)
        },
        async updateLessonPlan () {
            const { data } = await this.form.post(`/api/teacher/section-subject/lesson-plan/update`)
            this.list = data
        },
        updateUserPerformance () {
            let vm = this;
            swal({
                title: 'Are you sure?',
                html:   `Students performance will be change`,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Countinue!',
                showLoaderOnConfirm: true,
                preConfirm: (login) => {
                    axios.post('/api/student/performance/update',{users: vm.userPerformances}).then( response => {
                        return response.data;
                    }).catch(error => console.log(error))
                },
                allowOutsideClick: () => !swal.isLoading()
                }).then((result) => {
                if (result.value) {
                    swal('Successfull!', result.value.message, 'success');
                }
                
            });
        },
        updateUserGrades () {
            let vm = this;
            swal({
                title: 'Are you sure?',
                html:   `Students grades will be change`,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Countinue!',
                showLoaderOnConfirm: true,
                preConfirm: (login) => {
                    axios.post('/api/student/grades/updateComment',{users: vm.students}).then( response => {
                        return response.data;
                    }).catch(error => console.log(error))
                },
                allowOutsideClick: () => !swal.isLoading()
                }).then((result) => {
                    if (result.value) {
                        console.log(result.value);
                        
                        swal('Successfull!', result.value, 'success');
                    }
            });
        },
        getTotalScore(score_id){
            return this.activePerformance.performance_score.find(score => score.id === score_id).score;
        },
        getActiveGP() {
            return this.school.grading_periods.slice().filter(gp => gp.status === true)[0];
        },
        getList() {
            let vm = this;
            axios.get(`/api/teacher/class/list`,{
                params:{
                    grading_period_id: this.getActiveGP().id
                }
            }).then( response => {
                vm.list = response.data;
            }).catch(error => console.log(error))
        },
        getStudent(students) {
            let vm = this;
            this.students = [];
            students.forEach(student => vm.students.push(...[student.user]));
        },
        getPerformances(plan_id) {
            let vm = this;
            this.performances = [];
            if(plan_id){
                let filtered = this.lessonPlans.slice().find(plan => plan.id == plan_id)
                this.performances  = filtered.performances;
            }
        },
        showStudentsList(item){
            this.getStudent(item.section.students);
            this.activeClass = item
            this.isMain = false;
            this.isStudentList = true;
        },
        print() {
            // Pass the element id here
            this.$htmlToPaper('printMe');
        },
        getLessonPlan() {
            let vm = this;
            axios.get(`/api/teacher/lesson-plans/list`).then( response => {
                vm.lessonPlans = response.data;
                if(vm.lessonPlans.length){
                    vm.planSelect = vm.lessonPlans.slice().map(obj =>{ 
                        var rObj = {};
                        rObj.text = obj.name;
                        rObj.value = obj.id;
                        return rObj;
                    });
                }
            }).catch(error => console.log(error))
        },
        showPerformanceList(item){
            this.getPerformances(item.lesson_plan ? item.lesson_plan.lesson_plan.id : null);
            this.getStudent(item.section.students);
            this.activeClass = item
            this.isPerformancesList = true;
            this.isMain = false;
        },
        showUserPerformance(item,gp){
            let vm = this
            this.activePerformance = item;
            this.userPerformances = [];
            var idx = 1;
            this.fieldsUserPerformance = [];
            item.performance_score.forEach(perf => {
                this.fieldsUserPerformance.push({
                    key: idx.toString(),
                    id: perf.id,
                    total_score: perf.score
                })
                idx++;
            });
            this.selectedGP = gp;
            axios.get(`/api/student/performance/list`,{
                params: {
                    subject_lesson_plan_id: this.activeClass.lesson_plan.id,
                    section_subject_id: this.activeClass.id,
                    grading_period_id: gp.id,
                    performance_id: item.id
                }
            }).then( response => {
                this.userPerformances = response.data;
                setTimeout(() => {
                    vm.getStudent(response.data);  
                }, 500);
            }).catch(error => console.log(error))
            this.isStudentPerformance = true;
            this.isPerformancesList = false;
        },
        showGrading(item){
            let vm = this;
            this.showCrafting = true;
            this.isMain = false;
            this.getStudent(item.section.students);
            this.activeClass = item
            this.fieldsGrading = [];
            var student_list = this.students.slice().map(student => student.id);
            axios.post(`/api/student/grades/update`,{
                subject_lesson_plan_id: vm.activeClass.lesson_plan ?  vm.activeClass.lesson_plan.id : null,
                grading_period_id: vm.getActiveGP().id,
                student_list: student_list,
                section_subject_id: item.id,
            }).then( response => {
                vm.students = response.data;
                if (vm.students.length) {
                    var grade_list = response.data[0].grades.forEach((grades, index )=> {
                        this.fieldsGrading.push({
                            key: grades.grading_period.name,
                            id: grades.grading_period_id,
                            idx: index
                        })
                        this.fieldsGrading.push({
                            key: 'Comment',
                            id: grades.grading_period_id,
                            idx: index
                        })
                    });
                }
                setTimeout(() => {
                    this.showCrafting = false;
                    this.isGrading = true;
                }, 500);
            }).catch(error => console.log(error))
        },
        computeFinalGrade(grades){
            let total = grades.reduce((sum, current) => { return sum + parseFloat(current.score);}, 0)
            let final_grade = total / this.school.grading_periods.length
            return Math.round(final_grade);
        },
    },
    mounted: function () {
        this.getList()
        this.getLessonPlan()
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
            padding: .4rem;
            word-wrap: break-word;
        }
    }
        
    &>ul {
        width: 100%;
        margin: 0;
        padding: 0;
        list-style: none;
        &>li{
            border: 1px solid #e8e8e8;
            border-top: initial;
            &.empty{
                padding: .6rem .6rem;
            }
        }
    }
    & .name {
        flex: 0 1 30%;
    }
    & .label {
        flex: 0 1 10%;
    }
    & .score {
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
  .header{
        display: flex;
        background: #607d8b;
        color: hsla(0,0%,100%,.9);
        padding: 5px;
        &>div{
            padding: .4rem;
        }
  }
  .add-grading{
        & .row{
                border: 1px solid #e8e8e8;
                border-top: initial;
                padding: 0.6rem;
        }
      
        & .col{
            padding-left: initial!important;
        }
        // &.name{
        // }

        // &.empty{
        //     padding: .6rem .6rem;
        // }
  }
</style>
