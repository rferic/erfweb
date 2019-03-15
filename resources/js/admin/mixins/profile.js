import { mapState } from 'vuex'

const profileMixin = {
    computed: {
        ...mapState([ 'routesGlobal' ]),
    },
    methods: {
        async getDataProfileRequest () {
            const { data } = await axios.post(this.routesGlobal.profile.getData, {})
            return data
        }
    }
}

export default profileMixin
