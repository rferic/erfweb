import { mapState, mapGetters, mapActions } from 'vuex'
import localeMixin from './locale'

const appMixin = {
    mixins: [ localeMixin ],
    computed: {
        ...mapState([ 'routesGlobal', 'localesSupported', 'locale' ]),
        ...mapGetters({
            isLogged: 'auth/isLogged'
        })
    },
    methods: {
        ...mapActions({
            setMyApps : 'app/set',
            clearMyApps : 'app/clear'
        }),
        async getMyApps () {
            if ( this.isLogged ) {
                const apps = await this.getMyAppsRequest()
                this.setMyApps(apps)
            } else {
                this.clearMyApps()
            }
        },
        async getAppsRequest ({ format, randomOrder, maxItems, perPage, status, types }) {
            const { data } = await this.axios.post(this.routesGlobal.getApps, { format, randomOrder, maxItems, perPage, status, types })
            return data
        },
        async getMyAppsRequest () {
            const { data } = await this.axios.post(this.routesGlobal.getMyApps, {})
            return data
        }
    }
}

export default appMixin
