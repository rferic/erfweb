import { mapState } from 'vuex'
import localeMixin from './locale'

const appMixin = {
    mixins: [ localeMixin ],
    computed: {
        ...mapState([ 'routesGlobal', 'localesSupported', 'locale' ]),
    },
    methods: {
        getAppLocale ( app ) {
            const localeItem = this.getLocaleItem()

            for ( const item of app.locales ) {
                if ( item.lang === localeItem.iso ) {
                    return item
                }
            }

            return null
        },
        getFirstAppImage ( app ) {
            for ( const image of app.images ) {
                if ( image.langs.includes(this.locale) ) {
                    return image
                }
            }

            return null
        },
        async getAppsRequest ({ format, randomOrder, maxItems, perPage, status, types }) {
            const { data } = await this.axios.post(this.routesGlobal.getApps, { format, randomOrder, maxItems, perPage, status, types })
            return data
        }
    }
}

export default appMixin
