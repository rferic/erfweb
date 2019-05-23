import { mapState } from "vuex";

const authMixin = {
    computed: {
        ...mapState([ 'routesGlobal' ])
    },
    methods: {
        async getEmailIsFreeRequest ({ email }) {
            const { data } = await this.axios.post(this.routesGlobal.emailIsFree, { email })
            return data
        },
        async loginRequest ({ email, password }) {
            const { data } = await this.axios.post(this.routesGlobal.loginAjax, { email, password })
            return data
        },
        async registerRequest ({ name, email, password, password_confirmation, terms }) {
            const { data } = await this.axios.post(this.routesGlobal.registerAjax, { name, email, password, password_confirmation, terms })
            return data
        }
    }
}

export default authMixin
