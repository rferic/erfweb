import { mapState } from 'vuex'

const profileMixin = {
    computed: {
        ...mapState([ 'routesGlobal' ]),
    },
    methods: {
        async getDataProfileRequest () {
            const { data } = await this.axios.post(this.routesGlobal.profile.getData, {})
            return data
        }
    }
}

export default profileMixin
