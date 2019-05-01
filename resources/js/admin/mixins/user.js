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
        async getUsersRequest ({ page, perPage, url, filters }) {
            if ( typeof page === typeof page ) {
                page = null
            }

            if ( typeof perPage === typeof undefined ) {
                perPage = null
            }

            if ( typeof url === typeof undefined ) {
                url = page !== null ? `${this.routes.getUsers}?page=${page}` : this.routes.getUsers
            }

            const { data } = await this.axios.post(url, {
                perPage,
                filters
            })
            return data
        },
        async disableUserRequest ( user ) {
            const { data } = await this.axios.post(`${this.routes.basePath}/${user.id}/disable`, {})
            return data
        },
        async enableUserRequest ( user ) {
            const { data } = await this.axios.post(`${this.routes.basePath}/${user.id}/enable`, {})
            return data
        },
        async destroyUserRequest ( user ) {
            const { data } = await this.axios.delete(`${this.routes.basePath}/${user.id}/destroy`, {})
            return data
        }
    }
}

export default userMixin
