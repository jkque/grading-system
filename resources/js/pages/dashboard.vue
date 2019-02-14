<template>
  <div class="animated fadeIn" v-if="user">
    <b-row v-if="roles.includes('teacher')">
      <b-col sm="6" md="2">
        <b-card>
          <div class="h1 text-muted text-right mb-4">
            <i class="icon-people"></i>
          </div>
          <div class="h4 mb-0">{{sections_count}}</div>
          <small class="text-muted text-uppercase font-weight-bold">Sections</small>
        </b-card>
      </b-col>
      <b-col sm="6" md="2">
        <b-card>
          <div class="h1 text-muted text-right mb-4">
            <i class="icon-people"></i>
          </div>
          <div class="h4 mb-0">{{students_count}}</div>
          <small class="text-muted text-uppercase font-weight-bold">Students</small>
        </b-card>
      </b-col>
      <b-col sm="6" md="2">
        <b-card>
          <div class="h1 text-muted text-right mb-4">
            <i class="icon-basket-loaded"></i>
          </div>
          <div class="h4 mb-0">1238</div>
          <small class="text-muted text-uppercase font-weight-bold">Products sold</small>
          <b-progress height={} class="progress-xs mt-3 mb-0" variant="warning" :value="25"/>
        </b-card>
      </b-col>
      <b-col sm="6" md="2">
        <b-card>
          <div class="h1 text-muted text-right mb-4">
            <i class="icon-pie-chart"></i>
          </div>
          <div class="h4 mb-0">28%</div>
          <small class="text-muted text-uppercase font-weight-bold">Returning Visitors</small>
          <b-progress height={} class="progress-xs mt-3 mb-0" :value="25"/>
        </b-card>
      </b-col>
      <b-col sm="6" md="2">
        <b-card>
          <div class="h1 text-muted text-right mb-4">
            <i class="icon-speedometer"></i>
          </div>
          <div class="h4 mb-0">5:34:11</div>
          <small class="text-muted text-uppercase font-weight-bold">Avg. Time</small>
          <b-progress height={} class="progress-xs mt-3 mb-0" variant="danger" :value="25"/>
        </b-card>
      </b-col>
      <b-col sm="6" md="2">
        <b-card>
          <div class="h1 text-muted text-right mb-4">
            <i class="icon-speech"></i>
          </div>
          <div class="h4 mb-0">972</div>
          <small class="text-muted text-uppercase font-weight-bold">Comments</small>
          <b-progress height={} class="progress-xs mt-3 mb-0" variant="info" :value="25"/>
        </b-card>
      </b-col>
    </b-row><!--/.row-->
    <b-row v-if="roles.includes('school_admin') && user">
      <b-col sm="6" md="2">
        <b-card>
          <div class="h1 text-muted text-right mb-4">
            <i class="icon-people"></i>
          </div>
          <div class="h4 mb-0">{{members_count}}</div>
          <small class="text-muted text-uppercase font-weight-bold">Members</small>
        </b-card>
      </b-col>
      <b-col sm="6" md="2">
        <b-card>
          <div class="h1 text-muted text-right mb-4">
            <i class="icon-people"></i>
          </div>
          <div class="h4 mb-0">{{teachers_count}}</div>
          <small class="text-muted text-uppercase font-weight-bold">Teachers</small>
        </b-card>
      </b-col>
      <b-col sm="6" md="2">
        <b-card>
          <div class="h1 text-muted text-right mb-4">
            <i class="icon-people"></i>
          </div>
          <div class="h4 mb-0">{{students_count}}</div>
          <small class="text-muted text-uppercase font-weight-bold">Students</small>
        </b-card>
      </b-col>
      <b-col sm="6" md="2">
        <b-card>
          <div class="h1 text-muted text-right mb-4">
            <i class="icon-pie-chart"></i>
          </div>
          <div class="h4 mb-0">{{grade_level_count}}</div>
          <small class="text-muted text-uppercase font-weight-bold">Grade Level</small>
        </b-card>
      </b-col>
      <!-- <b-col sm="6" md="2">
        <b-card>
          <div class="h1 text-muted text-right mb-4">
            <i class="icon-speedometer"></i>
          </div>
          <div class="h4 mb-0">5:34:11</div>
          <small class="text-muted text-uppercase font-weight-bold">Avg. Time</small>
          <b-progress height={} class="progress-xs mt-3 mb-0" variant="danger" :value="25"/>
        </b-card>
      </b-col>
      <b-col sm="6" md="2">
        <b-card>
          <div class="h1 text-muted text-right mb-4">
            <i class="icon-speech"></i>
          </div>
          <div class="h4 mb-0">972</div>
          <small class="text-muted text-uppercase font-weight-bold">Comments</small>
          <b-progress height={} class="progress-xs mt-3 mb-0" variant="info" :value="25"/>
        </b-card>
      </b-col> -->
    </b-row><!--/.row-->
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import axios from 'axios'
import SocialBoxChartExample from '~/components/SocialBoxChartExample'
export default {
  middleware: 'auth',
  name: 'dashboard',
  data: function () {
    return {
      sections_count: 0,
      students_count: 0,
      grade_level_count: 0,
      teachers_count: 0,
      members_count: 0,
    }
  },
  computed: {
      ...mapGetters({
          user: 'auth/user',
          roles: 'auth/roles',
      }),
  },
  components: {
    SocialBoxChartExample
  },
  metaInfo () {
    return { title: 'Dashoard' }
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
