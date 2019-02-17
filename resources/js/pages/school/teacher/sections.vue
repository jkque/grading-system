<template>
<div class="wrapper" v-if="user">
    <div class="animated fadeIn">
        <b-card no-header  v-if="isMain">
            <template slot="header">Sections {{getActiveGP().name}}</template>
            <b-table striped hover :items="list" :fields="fields" :responsive="true" :sort-by.sync="sortBy" :show-empty="true">
                <template slot="gradeLevel" slot-scope="data">
                    {{ data.item.grade_level.name }}
                </template>
                <template slot="section" slot-scope="data">
                    {{ data.item.name }}
                </template>
                <template slot="students" slot-scope="row">
                    <b-button variant="link" class="px-0" @click="showStudentsList(row.item)">{{ row.item.students.length }} Students</b-button>
                </template>
                <!-- <template slot="action" slot-scope="row">
                    <b-button size="sm" variant="success" @click.stop="showGrading(row.item)">
                        <i class="fa fa-pencil-square-o"></i>&nbsp; View Grades
                    </b-button>
                </template> -->
            </b-table>
        </b-card>

        <b-card no-header v-if="isStudentList">
            <template slot="header">{{activeClass ? activeClass.grade_level.name : ''}} / {{activeClass ? activeClass.name : ''}} / Students</template> 
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
                        <template slot="action" slot-scope="row">
                            <b-button size="sm" variant="success" @click.stop="showGrading(row.item)">
                                <i class="fa fa-pencil-square-o"></i>&nbsp; View Grades
                            </b-button>
                        </template>
                    </b-table>
                </b-card-body>
            <template slot="footer">
                <b-button @click="isStudentList = false; isMain = true">Back</b-button>
            </template>
        </b-card>

        <b-card no-header v-if="isGrading" id="gradingPrint">
            <b-button @click="printGrading()">Print</b-button>
            <template slot="header">{{activeClass ? activeClass.grade_level.name : ''}} / {{activeClass ? activeClass.name : ''}} / Students / {{activeStudent.name}}</template> 
                <b-card-body class="add-grading" > 
                        <b-row class="header">
                            <b-col>Subject</b-col>
                            <b-col v-for="(field, index) in school.grading_periods" :key="index">{{ field.name}}</b-col>
                            <b-col>Final</b-col>
                            <b-col>Remarks</b-col>
                        </b-row>
                        <b-row v-for="(subject, index) in activeClass.grade_level.subjects" :key="index">
                            <b-col>{{subject.name}}</b-col>
                            <template  v-for="(period, index) in school.grading_periods" >
                                <b-col :key="'score-'+index">
                                    <b>{{getSubjectGrade(subject.id,period.id)}}</b>
                                </b-col>
                            </template>
                            <b-col :key="'final-'+index"><b>{{getSubjectFinalGrade(subject.id)}}</b></b-col>
                            <b-col :key="'remark-'+index"><b>{{getSubjectFinalGrade(subject.id) != 'n/a' ? getSubjectFinalGrade(subject.id) >= school.passing_rate ? 'PASSED' : 'FAILED' : 'n/a'}}</b></b-col>
                        </b-row>
                        <b-row>
                            <b-col v-for="(n, index) in school.grading_periods.length" :key="index"></b-col>
                            <b-col><b>TOTAL</b></b-col>
                            <b-col><b>{{computeTotalFinalGrade()}}</b></b-col>
                            <b-col><b>{{computeTotalFinalGrade() >= school.passing_rate ? 'PASSED' : 'FAILED'}}</b></b-col>
                        </b-row>
                </b-card-body>
            <template slot="footer">
                <b-button @click="isGrading = false; isStudentList = true">Back</b-button>
            </template>
        </b-card>
        
        <b-card no-header  v-show="showCrafting">
            <b-progress :value="100" :max="100" animated></b-progress>
            Please wait we are processing the grades for you...
        </b-card>

    </div>
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
            sortBy: 'name',
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
                    key: 'students',
                },
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
                {
                    key: 'action',
                },
            ],
            fieldsGrading: [],
            list: [],
            students: [],
            isMain: true,
            isStudentList: false,
            isGrading: false,
            showCrafting: false,
            activeClass: null,
            activeStudent: null,
            filter: '',
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
        getActiveGP() {
            return this.school.grading_periods.slice().filter(gp => gp.status === true)[0];
        },
        getList() {
            let vm = this;
            axios.get(`/api/teacher/sections/list`).then( response => {
                vm.list = response.data;
            }).catch(error => console.log(error))
        },
        getStudent(students) {
            let vm = this;
            this.students = [];
            students.forEach(student => vm.students.push(...[student.user]));
        },
        showStudentsList(item){
            this.getStudent(item.students);
            this.activeClass = item
            this.isMain = false;
            this.isStudentList = true;
        },
        print() {
            // Pass the element id here
            this.$htmlToPaper('printMe');
        },
        printGrading() {
            // Pass the element id here
            this.$htmlToPaper('gradingPrint');
        },
        showGrading(item){
            let vm = this;
            this.activeStudent = item
            this.isStudentList = false;
            this.isGrading = true;
        },
        getSubjectGrade(subject_id,grading_period_id){
            let filtered = this.activeStudent.grades.find( grade => grade.subject_id == subject_id && grade.grading_period_id == grading_period_id)
            if(filtered){
                return Math.round(filtered.score);
            }
            return 'n/a';
        },
        getSubjectFinalGrade(subject_id){
            let filtered = this.activeStudent.final_grades.find( grade => grade.subject_id == subject_id)
            if(filtered){
                return Math.round(filtered.score);
            }
            return 'n/a';
        },
        computeTotalFinalGrade(){
            let total = this.activeStudent.final_grades.reduce((sum, current) => { return sum + parseFloat(current.score);}, 0)
            let final_grade = total / this.activeClass.grade_level.subjects.length
            return Math.round(final_grade);
        },
    },
    mounted: function () {
        this.getList()
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
