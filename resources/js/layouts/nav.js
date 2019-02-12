import store from '~/store'
export default { 
  data: function () {
    return {
      navs: [
        {
          name: 'Dashboard',
          url: '/dashboard',
          icon: 'icon-speedometer',
          middleware: ['admin','school_admin','teacher']
        },
        {
          title: true,
          name: 'School',
          middleware: ['admin','school_admin']
        },
        {
          name: 'School Info',
          url: `/school/${store.getters['auth/school'].id}/school-info`,  
          icon: 'icon-home',
          middleware: ['admin','school_admin']
        },
        {
          name: 'School Year',
          url: `/school/${store.getters['auth/school'].id}/school-year`,
          icon: 'icon-calendar',
          middleware: ['admin','school_admin']
        },
        {
          name: 'Grading Period',
          url: `/school/${store.getters['auth/school'].id}/grading-period`,
          icon: 'icon-layers',
          middleware: ['admin','school_admin']
        },
        {
          name: 'Grade Level',
          url: `/school/${store.getters['auth/school'].id}/grade-level`,
          icon: 'icon-badge',
          middleware: ['admin','school_admin'],
        },
        {
          name: 'Students',
          url: `/school/${store.getters['auth/school'].id}/students`,
          icon: 'icon-user',
          middleware: ['admin','school_admin']
        },
        {
          name: 'Teachers',
          url: `/school/${store.getters['auth/school'].id}/teachers`,
          icon: 'icon-user',
          middleware: ['admin','school_admin']
        },
        {
          name: 'Classes',
          url: `/classes`,
          icon: 'icon-list',
          middleware: ['teacher']
        },
        {
          name: 'Lesson Plans',
          url: `/lesson-plans`,
          icon: 'icon-list',
          middleware: ['teacher']
        },
      ],
    }
  }
}
  
  