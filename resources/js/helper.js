export default {
    methods: {
        checkUserRoles: function(role) {
            return this.user.roles.filter(roles =>
                roles.name === role
            );
        }
    }
  }