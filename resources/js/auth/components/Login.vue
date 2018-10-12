<template>
    <div class="unix-login">
        <div class="container-fluid">
            <b-row class="justify-content-center">
                <b-col lg="5" md="6" sm="12">
                    <b-card class="login-content">
                        <div class="login-form">
                            <h4>{{ $t('Login', { locale: this.locale }) }}</h4>
                            <b-form
                                id="loginForm"
                                method="POST"
                                :action="routesGlobal.login"
                                @submit.prevent="validateBeforeSubmit"
                                novalidate>
                                <input type="hidden" name="_token" :value="csrfToken">
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
                                    <span
                                        v-show="errors.has('email')"
                                        class="text-danger">{{ errors.first('email') }}</span>
                                </b-form-group>
                                <b-form-group :label="$t('Password', { locale: locale })">
                                    <b-form-input
                                        v-model="password"
                                        type="password"
                                        name="password"
                                        class="form-control"
                                        :placeholder="$t('Password', { locale: locale })"
                                        v-validate
                                        data-vv-rules="required|password"
                                        :class="{'input': true, 'is-invalid': errors.has('password') }"
                                    />
                                    <i
                                        v-show="errors.has('password')"
                                        class="fa fa-warning text-danger"></i>
                                    <span
                                        v-show="errors.has('password')"
                                        class="text-danger">{{ errors.first('password') }}</span>
                                </b-form-group>
                                <div class="checkbox">
                                    <b-form-checkbox
                                        v-model="remember"
                                        value="1"
                                        unchecked-value="2"
                                        name="remember">
                                        {{ $t('Remember Me', { locale: this.locale }) }}
                                    </b-form-checkbox>
                                    <label class="pull-right">
                                        <a :href="routesGlobal.forgottenPassword">{{ $t('Forgotten Password?', { locale: this.locale }) }}</a>
                                    </label>

                                </div>
                                <b-button
                                    type="submit"
                                    variant="primary"
                                    class="btn-flat m-b-30 m-t-30">
                                    {{ $t('Sign in', { locale: this.locale }) }}
                                </b-button>
                                <div class="register-link m-t-15 text-center">
                                    <p>
                                        {{ $t('Don\'t have account ?', { locale: this.locale }) }} <a :href="routesGlobal.register"> {{ $t('Sign Up Here', { locale: this.locale }) }}</a>
                                    </p>
                                </div>
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
        name: 'Login',
        props: {
            rememberValue: {
                required: true,
                type: String
            }
        },
        data () {
            return {
                email: '',
                password: '',
                remember: this.rememberValue
            }
        },
        computed: {
            ...mapState([ 'csrfToken', 'locale', 'routesGlobal' ])
        },
        methods: {
            validateBeforeSubmit () {
                this.$validator.validateAll().then((result) => {
                    if ( result ) {
                        document.querySelector('#loginForm').submit()
                    }
                })
            }
        },
        mounted () {
            Validator.extend('password', passwordIsStrongRule)
        }
    }
</script>
