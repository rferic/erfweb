import { mapState } from 'vuex'

const userMixin = {
    computed: {
        ...mapState([ 'routes' ])
    },
    methods: {
        async updateUserBaseRequest ({ name, email, avatar, lang }) {
            const { data } = await this.axios.post(this.routes.updateBaseUser, { name, email, avatar, lang })
            return data
        }
    }
}

export default userMixin
