<template>
    <v-card class="ml-2 mr-2">
        <v-card-title primary-title>
            <h2 class="title">{{ $vuetify.t('Change your password') }}</h2>
        </v-card-title>
        <v-card-text class="pt-0 pb-0">
            <v-form class="pl-2 pr-2">
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
            </v-form>
        </v-card-text>
        <v-card-actions>
            <v-spacer />
            <v-btn small color="success" @click="onSubmit">
                <v-icon class="mr-2" small>mdi-content-save</v-icon>
                {{ $vuetify.t('Save') }}
            </v-btn>
        </v-card-actions>
        <v-snackbar v-model="snackbar.show" :color="snackbar.color" :multi-line="true" bottom right>
            {{ $vuetify.t(snackbar.message) }}
        </v-snackbar>
    </v-card>
</template>

<script>
    import { mapState } from 'vuex'
    import authMixin from './../../../mixins/auth'

    export default {
        name: 'ProfileFormPassword',
        mixins: [ authMixin ],
        data () {
            return {
                password: '',
                password_confirmation: '',
                snackbar: {
                    show: false,
                    color: '',
                    message: ''
                }
            }
        },
        computed: {
            ...mapState(['routes'])
        },
        methods: {
            async onSubmit () {
                if ( await this.$validator.validateAll() ) {
                    await this.updatePasswordRequest({
                        password: this.password,
                        password_confirmation: this.password_confirmation
                    })

                    this.snackbar.color = 'success'
                    this.snackbar.message = this.$vuetify.t('Password has been updated')
                    this.snackbar.show = true
                }
            },
            async updatePasswordRequest ({ password, password_confirmation }) {
                const { data } = await this.axios.post(this.routes.updatePasswordUser, { password, password_confirmation})
                return data
            }
        },
        mounted () {
            this.setValidators()
        }
    }
</script>
