<template>
  <b-col class="wrapper">
      <full-calendar :events="events"></full-calendar>
  </b-col>
</template>


<script>
import { mapGetters } from 'vuex'
import axios from 'axios'
import SocialBoxChartExample from '~/components/SocialBoxChartExample'
import 'fullcalendar/dist/fullcalendar.css'

export default {
  middleware: 'auth',
  name: 'calendar',
  components: {
    SocialBoxChartExample,
  },
  data: function () {
    return {
      sections_count: 0,
      students_count: 0,
      grade_level_count: 0,
      teachers_count: 0,
      members_count: 0,
      events: [
        {
            title  : 'event1',
            start  : '2010-01-01',
        },
        {
            title  : 'event2',
            start  : '2010-01-05',
            end    : '2010-01-07',
        },
        {
            title  : 'event3',
            start  : '2010-01-09T12:30:00',
            allDay : false,
        },
      ]
    }
  },
  computed: {
      ...mapGetters({
          user: 'auth/user',
          roles: 'auth/roles',
      }),
  },
  metaInfo () {
    return { title: 'Calendar' }
  },
  methods:{
    getDashboardReport(){
      if(this.roles.includes('teacher')){
        axios.get(`/api/teacher/dashboardReport`).then( response => {
          this.sections_count = response.data.section_count;
          this.students_count = response.data.student_count;
        }).catch(error => console.log(error))
      }
      if(this.roles.includes('school_admin')){
        axios.get(`/api/school/dashboardReport`).then( response => {
          this.students_count = response.data.students_count;
          this.grade_level_count = response.data.grade_level_count;
          this.teachers_count = response.data.teachers_count;
          this.members_count = response.data.members_count;
        }).catch(error => console.log(error))
      }

    }
  },
  mounted: function () {
    this.getDashboardReport()
  }
}
</script>