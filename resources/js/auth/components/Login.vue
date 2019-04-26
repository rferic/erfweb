<template>
    <!--
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
                id="email"
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
                id="password"
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
                id="remember"
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
    <div v-if="hasSessionErrors">
        <b-alert
            v-for="(sessionError, index) in sessionErrors"
            :key="index"
            show
            variant="danger">
            {{ $t(sessionError, { locale: this.locale }) }}
        </b-alert>
    </div>
    -->
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="card bg-secondary shadow border-0">
                <div class="card-header bg-transparent pb-2">
                    <h1 class="text-center">{{ $t('Login', { locale }) }}</h1>
                </div>
                <div class="card-body px-lg-5 py-lg-5">
                    <b-form
                        id="loginForm"
                        method="POST"
                        :action="routesGlobal.login"
                        @submit.prevent="validateBeforeSubmit"
                        novalidate
                    >
                        <input type="hidden" name="_token" :value="csrfToken">

                        <div class="form-group input-group-alternative mb-3 input-group" :class="{ 'has-danger': errors.has('email') }">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="ni ni-email-83" />
                                </span>
                            </div>
                            <input
                                v-model="email"
                                type="email"
                                name="email"
                                aria-describedby="addon-right addon-left"
                                class="form-control"
                                :class="{ 'is-invalid': errors.has('email') }"
                                :placeholder="$t('Email', { locale })"
                                 v-validate
                                data-vv-rules="required|email"
                            />
                        </div>
                        <base-alert
                            v-show="errors.has('email')"
                            type="danger"
                        >
                            <i class="fa fa-warning" />
                            {{ errors.first('email') }}
                        </base-alert>

                        <div class="form-group input-group-alternative mb-3 input-group" :class="{ 'has-danger': errors.has('password') }">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="ni ni-lock-circle-open" />
                                </span>
                            </div>
                            <input
                                v-model="password"
                                type="password"
                                name="password"
                                aria-describedby="addon-right addon-left"
                                class="form-control"
                                :class="{ 'is-invalid': errors.has('password') }"
                                :placeholder="$t('Password', { locale })"
                                v-validate
                                data-vv-rules="required|password"
                            />
                        </div>
                        <base-alert
                            v-show="errors.has('password')"
                            type="danger"
                        >
                            <i class="fa fa-warning" />
                            {{ errors.first('password') }}
                        </base-alert>

                        <div class="text-center">
                            <base-button
                                type="primary"
                                class="my-4"
                                nativeType="submit"
                            >
                                {{ $t('Sign in', { locale }) }}
                            </base-button>
                        </div>
                    </b-form>
                    <div v-if="hasSessionErrors">
                        <base-alert
                            v-for="(sessionError, index) in sessionErrors"
                            :key="index"
                            show
                            type="danger"
                        >
                            <i class="fa fa-warning" />
                            {{ $t(sessionError, { locale: locale }) }}
                        </base-alert>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-6">
                    <a :href="routesGlobal.forgottenPassword" class="text-light">
                        <small>{{ $t('Forgot password?', { locale }) }}</small>
                    </a>
                </div>
                <div class="col-6 text-right">
                    <a :href="routesGlobal.register" class="text-light">
                        <small>{{ $t('Create new account', { locale }) }}</small>
                    </a>
                </div>
            </div>
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
            data: {
                type: String,
                required: true
            }
        },
        data () {
            return {
                email: '',
                password: '',
                remember: JSON.parse(this.data).remember === 1,
                sessionErrors: JSON.parse(JSON.parse(this.data).sessionErrors)
            }
        },
        computed: {
            ...mapState([ 'csrfToken', 'locale', 'routesGlobal' ]),
            hasSessionErrors () {
                return this.sessionErrors.length > 0
            }
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
