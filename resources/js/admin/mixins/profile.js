import { mapState } from 'vuex'

const profileMixin = {
    computed: {
        ...mapState([ 'routesGlobal' ]),
    },
    methods: {
        async getDataProfileRequest () {
            const response = await axios.post(this.routesGlobal.profile.getData, {})
            return response.data
        }
    }
}

export default profileMixin
