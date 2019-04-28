import { mapState } from 'vuex'

const userMixin = {
    computed: {
        ...mapState([ 'routes' ]),
    },
    methods: {
        async getUserDataRequest () {
            const { data } = await this.axios.post(this.routes.getUserData, {})
            return data
        },
        async disableUserRequest () {
            const { data } = await this.axios.post(this.routes.disableUser, {})
            return data
        },
        async enableUserRequest () {
            const { data } = await this.axios.post(this.routes.enableUser, {})
            return data
        },
        async destroyUserRequest () {
            const { data } = await this.axios.delete(this.routes.destroyUser, {})
            return data
        }
    }
}

export default userMixin
