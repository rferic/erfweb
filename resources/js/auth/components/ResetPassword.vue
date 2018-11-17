<template>
    <div class="unix-login">
        <div class="container-fluid">
            <b-row class="justify-content-center">
                <b-col lg="5" md="6" sm="12">
                    <b-card class="login-content">
                        <div class="login-form">
                            <h4>{{ $t('Reset password', { locale: this.locale }) }}</h4>
                            <b-form
                                id="resetPasswordForm"
                                method="POST"
                                :action="routesGlobal.resetPassword"
                                @submit.prevent="validateBeforeSubmit"
                                novalidate>

                                <input type="hidden" name="_token" :value="csrfToken">
                                <input type="hidden" name="token" :value="token">

                                <b-form-group :label="$t('Email', { locale: locale })">
                                    <b-form-input
                                        v-model="email"
                                        type="email"
                                        name="email"
                                        class="form-control"
                                        :placeholder="$t('Email', { locale: locale })"
                                        v-validate
                                        data-vv-rules="required|email"
                                        :class="{'input': true, 'is-invalid': errors.has('email') }"
                                    />
                                    <i
                                        v-show="errors.has('email')"
                                        class="fa fa-warning text-danger"></i>
                                    <div
                                        v-show="errors.has('email')"
                                        class="invalid-feedback">{{ errors.first('email') }}</div>
                                </b-form-group>
                                <b-form-group :label="$t('Password', { locale: locale })">
                                    <b-form-input
                                        v-model="password"
                                        type="password"
                                        name="password"
                                        ref="password"
                                        class="form-control"
                                        :placeholder="$t('New password', { locale: locale })"
                                        v-validate
                                        data-vv-rules="required|password"
                                        :class="{'input': true, 'is-invalid': errors.has('password') }"
                                    />
                                    <i
                                        v-show="errors.has('password')"
                                        class="fa fa-warning text-danger"></i>
                                    <div
                                        v-show="errors.has('password')"
                                        class="invalid-feedback">{{ errors.first('password') }}</div>
                                </b-form-group>
                                <b-form-group :label="$t('Password confirm', { locale: locale })">
                                    <b-form-input
                                        v-model="passwordConfirm"
                                        type="password"
                                        name="password_confirmation"
                                        class="form-control"
                                        :placeholder="$t('Password', { locale: locale })"
                                        v-validate
                                        data-vv-rules="required|confirmed:password"
                                        :class="{'input': true, 'is-invalid': errors.has('password_confirmation') }"
                                    />
                                    <i
                                        v-show="errors.has('password_confirmation')"
                                        class="fa fa-warning text-danger"></i>
                                    <div
                                        v-show="errors.has('password_confirmation')"
                                        class="invalid-feedback">{{ errors.first('password_confirmation') }}</div>
                                </b-form-group>
                                <b-button
                                    type="submit"
                                    variant="primary"
                                    class="btn-flat m-b-30 m-t-30">
                                    {{ $t('Refresh password', { locale: this.locale }) }}
                                </b-button>
                            </b-form>
                        </div>
                    </b-card>
                </b-col>
            </b-row>
        </div>
    </div>
</template>

<script>
    import { mapState } from 'vuex'
    import { Validator } from 'vee-validate'
    import passwordIsStrongRule from './../../includes/validators/passwordIsStrongRule'

    export default {
        name: 'ResetPassword',
        data () {
            return {
                email: '',
                password: '',
                passwordConfirm: '',
                token: token
            }
        },
        computed: {
            ...mapState([ 'csrfToken', 'locale', 'routesGlobal' ])
        },
        methods: {
            validateBeforeSubmit () {
                this.$validator.validateAll().then((result) => {
                    if ( result ) {
                        document.querySelector('#resetPasswordForm').submit()
                    }
                })
            }
        },
        mounted () {
            Validator.extend('password', passwordIsStrongRule)
        }
    }
</script>
