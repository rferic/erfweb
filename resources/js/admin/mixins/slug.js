import { mapState } from 'vuex'
import { Validator } from 'vee-validate'
const slugify = require('slugify')

const slugMixin = {
    data () {
        return {
            slug: {
                text: '',
                lang: '',
                isFree: true,
                isUsed: false,
                hasRedirection: false,
                isMine: false,
                isComputed: true
            }
        }
    },
    computed: {
        ...mapState([ 'routes' ])
    },
    methods: {
        // Getters
        async getSlugIsValid ({ type, currentParentId }) {
            const { result, isUsed, hasRedirection, isMine, isFree } = await this.getSlugIsFreeRequest({ type, currentParentId })

            if ( result ) {
                this.slug.isUsed = isUsed
                this.slug.hasRedirection = hasRedirection
                this.slug.isMine = isMine
                this.slug.isFree = isFree
            }
        },
        getIsComputed ( text ) {
            return this.slug.text === this.getSlugComputed(text)
        },
        getSlugComputed ( text ) {
            return slugify(text)
        },
        // Setters
        setSlugIsFreeValidator () {
            Validator.extend('slug_is_free', {
                getMessage: () => this.$t(`El slug ya existe`, { locale: this.locale }),
                validate: () => {
                    return this.slug.isFree
                }
            })
        },
        // API Request
        async getSlugIsFreeRequest ({ type, currentParentId }) {
            if ( this.slug.text === '' ) {
                return {
                    result: true,
                    isUsed: false,
                    hasRedirection: false,
                    isMine: false,
                    isFree: true
                }
            }

            if ( typeof currentPageLocale === undefined ) {
                currentPageLocale = null
            }

            const { data } = await axios.post(this.routes.getSlugIsFree, {
                type,
                slug: this.slug.text,
                lang: this.slug.lang,
                currentParentId
            })

            return data
        },
        async getAllSlugsRequest () {
            const { data } = await axios.post(this.routes.getAllSlugs, {})
            return data
        }
    }
}

export default slugMixin
