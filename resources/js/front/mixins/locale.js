import { mapState } from 'vuex'

const localeMixin = {
    computed: {
        ...mapState([ 'localesSupported', 'locale' ])
    },
    methods: {
        getLocaleItem () {
            if ( typeof this.localesSupported !== typeof undefined ) {
                for (const localeSupported of this.localesSupported) {
                    if (this.locale === localeSupported.code) {
                        return localeSupported
                    }
                }
            }

            return null
        }
    }
}

export default localeMixin
