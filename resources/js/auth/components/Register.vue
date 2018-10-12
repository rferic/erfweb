<template>
    <div class="unix-login">
        <div class="container-fluid">
            <transition name="bounceRight" :style="transitionStyleTime">
                <b-row
                    v-show="enableRegisterForm"
                    class="justify-content-center">
                    <b-col lg="5" md="6" sm="12">
                        <b-card class="login-content">
                            <div class="login-form">
                                <h4>{{ $t('Register', { locale: this.locale }) }}</h4>
                                <b-form
                                    id="registerForm"
                                    method="POST"
                                    :action="routesGlobal.register"
                                    @submit.prevent="validateBeforeSubmit"
                                    novalidate>
                                    <input type="hidden" name="_token" :value="csrfToken">
                                    <b-form-group :label="$t('Name', { locale: locale })">
                                        <b-form-input
                                            v-model="name"
                                            type="email"
                                            id="name"
                                            name="name"
                                            ref="name"
                                            class="form-control"
                                            :placeholder="$t('Email', { locale: locale })"
                                            v-validate
                                            data-vv-rules="required|min:3"
                                            :class="{'input': true, 'is-invalid': errors.has('name') }"
                                        />
                                        <i
                                            v-show="errors.has('name')"
                                            class="fa fa-warning text-danger"></i>
                                        <span
                                            v-show="errors.has('name')"
                                            class="text-danger">{{ errors.first('name') }}</span>
                                    </b-form-group>
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
                                            ref="password"
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
                                        <span
                                            v-show="errors.has('password_confirmation')"
                                            class="text-danger">{{ errors.first('password_confirmation') }}</span>
                                    </b-form-group>
                                    <div>
                                        <a href="" @click.prevent="showTermsAndPolicy">{{ $t('Read the terms and policy', { locale: this.locale }) }}</a>
                                    </div>
                                    <b-form-checkbox
                                        v-model="terms"
                                        id="terms"
                                        name="terms"
                                        value="1"
                                        unchecked-value=""
                                        v-validate
                                        data-vv-rules="required">
                                        {{ $t('Agree the terms and policy', { locale: this.locale }) }}
                                        <div
                                            v-show="errors.has('terms')"
                                            class="help text-danger">{{ errors.first('terms') }}</div>
                                    </b-form-checkbox>
                                    <b-button
                                        type="submit"
                                        variant="primary"
                                        class="btn-flat m-b-30 m-t-30">
                                        {{ $t('Register', { locale: this.locale }) }}
                                    </b-button>
                                </b-form>
                            </div>
                        </b-card>
                    </b-col>
                </b-row>
            </transition>
            <transition name="bounceRight">
                <b-row
                    v-show="enableTermsAndPolicy"
                    class="justify-content-center">
                    <b-col lg="5" md="6" sm="12">
                        <b-card class="login-content">
                            <h4>{{ $t('Terms and Policy', { locale: this.locale }) }}</h4>
                            <div>
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui dicta minus molestiae vel beatae natus eveniet ratione temporibus aperiam harum alias officiis assumenda officia quibusdam deleniti eos cupiditate dolore doloribus!</p>
                                <p>Ad dolore dignissimos asperiores dicta facere optio quod commodi nam tempore recusandae. Rerum sed nulla eum vero expedita ex delectus voluptates rem at neque quos facere sequi unde optio aliquam!</p>
                                <p>Tenetur quod quidem in voluptatem corporis dolorum dicta sit pariatur porro quaerat autem ipsam odit quam beatae tempora quibusdam illum! Modi velit odio nam nulla unde amet odit pariatur at!</p>
                                <p>Consequatur rerum amet fuga expedita sunt et tempora saepe? Iusto nihil explicabo perferendis quos provident delectus ducimus necessitatibus reiciendis optio tempora unde earum doloremque commodi laudantium ad nulla vel odio?</p>
                            </div>
                            <b-button
                                variant="primary"
                                class="pull-right"
                                @click="showRegisterForm">{{ $t('Close', { locale: this.locale }) }}
                            </b-button>
                        </b-card>
                    </b-col>
                </b-row>
            </transition>
        </div>
    </div>
</template>

<script>
    import { mapState } from 'vuex'
    import { Validator } from 'vee-validate'
    import passwordIsStrongRule from './../../includes/validators/passwordIsStrongRule'

    export default {
        name: 'Register',
        data () {
            return {
                name: '',
                email: '',
                password: '',
                passwordConfirm: '',
                terms: '',
                enableRegisterForm: true,
                enableTermsAndPolicy: false,
                transitionTime: 300,
            }
        },
        computed: {
            ...mapState([ 'csrfToken', 'locale', 'routesGlobal' ]),
            transitionStyleTime () {
                return `animation-duration: ${this.transitionTime}ms`
            }
        },
        methods: {
            validateBeforeSubmit () {
                this.$validator.validateAll().then((result) => {
                    if ( result ) {
                        document.querySelector('#registerForm').submit()
                    }
                })
            },

            showTermsAndPolicy () {
                this.enableRegisterForm = false
                setTimeout(() => {
                    this.enableTermsAndPolicy = true
                }, this.transitionTime * 3)
            },

            showRegisterForm () {
                this.enableTermsAndPolicy = false
                setTimeout(() => {
                    this.enableRegisterForm = true
                }, this.transitionTime * 3)
            }
        },
        mounted () {
            Validator.extend('password', passwordIsStrongRule)
        }
    }
</script>
