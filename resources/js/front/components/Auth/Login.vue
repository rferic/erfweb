<template>
    <form>
        <v-layout row wrap>
            <v-flex xs12 sm6 md6 lg6 class="pa-2">
                <v-text-field
                    name="email"
                    type="email"
                    v-model="email"
                    :label="$vuetify.t('Email')"
                    v-validate="'required|email'"
                    data-vv-name="email"
                    :error-messages="errors.collect('email')"
                    @input="$validator.reset()"
                />
            </v-flex>
            <v-flex xs12 sm6 md6 lg6 class="pa-2">
                <v-text-field
                    name="password"
                    type="password"
                    v-model="password"
                    :label="$vuetify.t('Password')"
                    v-validate="'required|password'"
                    data-vv-name="password"
                    :hint="$vuetify.t('Require minimum a letter, require minimum a number, require minimum a single character present in the list below')"
                    persistent-hint
                    :error-messages="errors.collect('password')"
                    @input="$validator.reset()"
                    browser-autocomplete="new-password"
                />
            </v-flex>
            <v-flex xs12 sm12 md12 lg12 class="pa-2">
                <div class="text-xs-right">
                    <v-btn color="grey darken-4" class="white--text" @click="onSubmit">
                        {{ $vuetify.t('Login') }}
                    </v-btn>
                </div>
            </v-flex>
        </v-layout>
    </form>
</template>

<script>
    import { mapActions } from 'vuex'
    import authMixin from './../../mixins/auth'
    import { Validator } from 'vee-validate'
    import passwordIsStrongRule from './../../../includes/validators/passwordIsStrongRule'

    export default {
        name: 'AuthLogin',
        mixins: [ authMixin ],
        data () {
            return {
                email: '',
                password: ''
            }
        },
        methods: {
            ...mapActions({
                setAuth : 'auth/set',
                closeAuthModal: 'auth/close',
                refreshCsrfToken: 'refreshCsrfToken'
            }),
            async onSubmit () {
                if ( await this.$validator.validateAll() ) {
                    const { result, user, csrfToken } = await this.loginRequest({
                        email: this.email,
                        password: this.password
                    })

                    if ( result ) {
                        this.setAuth(user)
                        this.refreshCsrfToken(csrfToken)
                        this.closeAuthModal()
                        this.$emit('onLogged')
                    } else {
                        this.$emit('onErrorLogged')
                    }
                }
            }
        },
        mounted () {
            Validator.extend('password', passwordIsStrongRule)
        }
    }
</script>
