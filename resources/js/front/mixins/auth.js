import { mapState } from 'vuex'
import { Validator } from 'vee-validate'
import passwordIsStrongRule from './../../includes/validators/passwordIsStrongRule'

const authMixin = {
    computed: {
        ...mapState([ 'routesGlobal' ])
    },
    methods: {
        async setValidators () {
            Validator.extend('password', passwordIsStrongRule)
            this.setEmailIsFreeValidator()
        },
        async setEmailIsFreeValidator () {
            const context = this
            const emailIsFree = async ( value ) => {
                const { result } = await context.getEmailIsFreeRequest({ email: value })
                return {
                    valid: result
                }
            }

            Validator.extend('emailIsFree', {
                validate: emailIsFree,
                getMessage: field => this.$vuetify.t(`The ${field} already exists`, context.locale)
            });
        },
        async getEmailIsFreeRequest ({ email }) {
            const { data } = await this.axios.post(this.routesGlobal.emailIsFree, { email })
            return data
        },
        async loginRequest ({ email, password }) {
            const { data } = await this.axios.post(this.routesGlobal.loginAjax, { email, password })
            return data
        },
        async registerRequest ({ name, email, password, password_confirmation, lang, terms }) {
            const { data } = await this.axios.post(this.routesGlobal.registerAjax, { name, email, password, password_confirmation, lang, terms })
            return data
        }
    }
}

export default authMixin
