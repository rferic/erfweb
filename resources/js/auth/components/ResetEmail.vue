<template>
    <div class="unix-login">
        <div class="container-fluid">
            <b-row class="justify-content-center">
                <b-col lg="5" md="6" sm="12">
                    <b-card class="login-content">
                        <div class="login-form">
                            <h4>{{ $t('Request reset password', { locale: this.locale }) }}</h4>
                            <b-alert
                                :show="showAlertStatus"
                                variante="success">{{ $t(statusResetEmail , { locale: locale }) }}</b-alert>
                            <b-form
                                id="resetEmailForm"
                                method="POST"
                                :action="routesGlobal.resetEmail"
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

    export default {
        name: 'ResetEmail',
        data () {
            return {
                email: '',
                statusResetEmail: statusResetEmail
            }
        },
        computed: {
            ...mapState([ 'csrfToken', 'locale', 'routesGlobal' ]),
            showAlertStatus () {
                return this.statusResetEmail !== ''
            }
        },
        methods: {
            validateBeforeSubmit () {
                this.$validator.validateAll().then((result) => {
                    if ( result ) {
                        document.querySelector('#resetEmailForm').submit()
                    }
                })
            }
        }
    }
</script>
