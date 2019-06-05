<template>
    <form>
        <v-layout row wrap class="pt-2">
            <v-flex xs12 sm6 md6 lg6 class="pl-2 pr-2">
                <v-text-field
                    name="name"
                    type="name"
                    v-model="name"
                    :label="$vuetify.t('Name')"
                    v-validate="'required'"
                    data-vv-name="name"
                    :error-messages="errors.collect('name')"
                    @input="$validator.reset()"
                />
            </v-flex>
            <v-flex xs12 sm6 md6 lg6 class="pl-2 pr-2">
                <v-text-field
                    name="email"
                    type="email"
                    v-model="email"
                    :label="$vuetify.t('Email')"
                    v-validate="'required|email|emailIsFree'"
                    :append-icon="email !== '' && errors.collect('email').length === 0 ? 'mdi-check-bold' : 'mdi-alert-circle'"
                    data-vv-name="email"
                    :error-messages="errors.collect('email')"
                />
            </v-flex>
            <v-flex xs12 sm6 md6 lg6 class="pl-2 pr-2">
                <v-text-field
                    ref="password"
                    name="password"
                    type="password"
                    v-model="password"
                    :label="$vuetify.t('Password')"
                    v-validate="'required|password'"
                    data-vv-name="password"
                    :error-messages="errors.collect('password')"
                    :hint="$vuetify.t('Require minimum a letter, require minimum a number, require minimum a single character present in the list below')"
                    persistent-hint
                    @input="$validator.reset()"
                    browser-autocomplete="new-password"
                />
            </v-flex>
            <v-flex xs12 sm6 md6 lg6 class="pl-2 pr-2">
                <v-text-field
                    name="password_confirmation"
                    type="password"
                    v-model="password_confirmation"
                    :label="$vuetify.t('Password confirmation')"
                    v-validate="'required|confirmed:password'"
                    data-vv-name="password_confirmation"
                    :error-messages="errors.collect('password_confirmation')"
                    @input="$validator.reset()"
                />
            </v-flex>
            <v-flex xs12 sm6 md6 lg6 class="pl-2 pr-2">
                <v-select
                    :items="localesSupported"
                    item-value="iso"
                    item-text="name"
                    v-model="lang"
                    :label="$vuetify.t('Language')"
                />
            </v-flex>
            <v-flex xs12 sm6 md6 lg6 class="pl-2 pr-2">
                <v-checkbox
                    name="terms"
                    v-model="terms"
                    :label="$vuetify.t('I accept the use of my data without any commercial use')"
                    v-validate="'required:true'"
                    data-vv-name="terms"
                    :error-messages="errors.collect('terms')"
                    @change="$validator.reset()"
                />
            </v-flex>
            <v-flex xs12 sm12 md12 lg12 class="pl-2 pr-2">
                <div class="text-xs-right">
                    <v-btn color="grey darken-4" class="white--text" @click="onSubmit">
                        {{ $vuetify.t('Register') }}
                    </v-btn>
                </div>
            </v-flex>
        </v-layout>
    </form>
</template>

<script>
    import { mapState, mapActions } from 'vuex'
    import authMixin from './../../mixins/auth'

    export default {
        name: 'AuthRegister',
        mixins: [ authMixin ],
        data () {
            return {
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
                lang: 'en_GB',
                terms: false
            }
        },
        computed: {
            ...mapState([ 'locale', 'localesSupported' ])
        },
        methods: {
            ...mapActions({
                setAuth : 'auth/set',
                closeAuthModal: 'auth/close',
                refreshCsrfToken: 'refreshCsrfToken'
            }),
            async onSubmit () {
                if ( await this.$validator.validateAll() ) {
                    const { result, user, csrfToken } = await this.registerRequest({
                        name: this.name,
                        email: this.email,
                        password: this.password,
                        password_confirmation: this.password_confirmation,
                        lang: this.lang,
                        terms: this.terms
                    })

                    if ( result ) {
                        this.setAuth(user)
                        this.refreshCsrfToken(csrfToken)
                        this.closeAuthModal()
                        this.$emit('onRegistered')
                    } else {
                        this.$emit('onErrorRegistered')
                    }
                }
            }
        },
        mounted () {
            this.setValidators()
            this.lang = this.localesSupported.filter(locale => locale.code === this.locale)[0].iso
        }
    }
</script>
