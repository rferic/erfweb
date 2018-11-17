<template>
    <b-form>
        <notifications group="notify" position="bottom right" />
        <b-row>
            <b-col
                lg="8"
                md="6"
                xs="12">
                <b-row class="mb-3">
                    <b-col
                        lg="6"
                        md="6"
                        xs="12">
                        <b-form-group :label="`${$t('Password', { locale: locale })}: *`">
                            <b-form-input
                                v-model="user.email"
                                name="email"
                                type="email"
                                v-validate
                                data-vv-rules="required|email|emailIsFree"
                                :class="{ 'is-invalid' : errors.has(`email`) }"
                                @change="validateEmail" />
                            <div
                                v-if="errors.has(`email`)"
                                class="invalid-feedback">
                                <i
                                    v-show="errors.has('email')"
                                    class="fa fa-warning text-danger"/>
                                {{ errors.first('email') }}
                            </div>
                        </b-form-group>
                    </b-col>
                    <b-col
                        lg="6"
                        md="6"
                        xs="12">
                        <b-form-group :label="`${$t('Name', { locale: locale })}: *`">
                            <b-form-input
                            v-model="user.name"
                            name="name"
                            type="text"
                            v-validate
                            data-vv-rules="required|min:3|max:255"
                            :class="{ 'is-invalid' : errors.has(`name`) }" />
                            <div
                                v-if="errors.has(`name`)"
                                class="invalid-feedback">
                                <i
                                    v-show="errors.has('name')"
                                    class="fa fa-warning text-danger" />
                                {{ errors.first('name') }}
                            </div>
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col
                        lg="6"
                        md="6"
                        xs="12">
                        <b-form-group :label="$t('Password', { locale: locale })">
                            <b-form-input
                                v-model="password"
                                type="password"
                                name="password"
                                ref="password"
                                class="form-control"
                                :placeholder="$t('New password', { locale: locale })"
                                v-validate
                                data-vv-rules="password"
                                :class="{'input': true, 'is-invalid': errors.has('password') }"
                            />
                            <div
                                v-show="errors.has('password')"
                                class="invalid-feedback">
                                <i
                                    v-show="errors.has('password')"
                                    class="fa fa-warning text-danger"/>
                                {{ errors.first('password') }}
                            </div>
                        </b-form-group>
                    </b-col>
                    <b-col
                        lg="6"
                        md="6"
                        xs="12">
                        <b-form-group :label="$t('Password confirm', { locale: locale })">
                            <b-form-input
                                v-model="passwordConfirm"
                                type="password"
                                name="password_confirmation"
                                class="form-control"
                                :placeholder="$t('Password', { locale: locale })"
                                v-validate
                                data-vv-rules="confirmed:password"
                                :class="{'input': true, 'is-invalid': errors.has('password_confirmation') }"
                            />
                            <div
                                v-show="errors.has('password_confirmation')"
                                class="invalid-feedback">
                                <i
                                    v-show="errors.has('password_confirmation')"
                                    class="fa fa-warning text-danger" />
                                {{ errors.first('password_confirmation') }}
                            </div>
                        </b-form-group>
                    </b-col>
                </b-row>
            </b-col>
            <b-col
                lg="4"
                md="6"
                xs="12">
                <label>Roles:</label>
                <div class="form-check">
                    <div
                        class="checkbox"
                        v-for="(role, index) in roles"
                        :key="index">
                        <label
                            :for="role.key"
                            class="form-check-label ">
                            <input
                                type="checkbox"
                                :name="role.key"
                                v-model="role.value"
                                :disabled="role.key === 'admin' && role.value === true"
                                class="form-check-input">{{ role.key }}
                        </label>
                    </div>
                </div>
            </b-col>
        </b-row>
        <b-row>
            <b-col cols="12">
                <b-button
                    class="pull-right"
                    type="submit"
                    variant="primary"
                    @click.prevent="submit">
                    <i class="fa fa-save" />
                    {{ $t('Save', locale) }}
                </b-button>
            </b-col>
        </b-row>
    </b-form>
</template>

<script>
    import { mapState } from 'vuex'
    import cloneMixin from '../../../includes/mixins/cloneMixin'
    import { Validator } from 'vee-validate'
    import passwordIsStrongRule from '../../../includes/validators/passwordIsStrongRule'

    export default {
        name: 'IndexProfile',
        props: {
            data: {
                type: String,
                required: true
            }
        },
        mixins: [ cloneMixin ],
        data () {
            return {
                user: null,
                roles: [],
                emailIsFree: true,
                password: '',
                passwordConfirm: ''
            }
        },
        computed: {
            ...mapState([ 'locale', 'routes' ]),
            userDataOrigin () {
                return JSON.parse(this.data)
            }
        },
        methods: {
            async validateEmail () {
                const { result } = await this.checkIfEmailIsFreeRequest()

                this.emailIsFree = result
                this.setEmailIsFreeValidator()
                this.$validator.validate('email')
            },
            async checkIfEmailIsFreeRequest () {
                // Check if email is empty
                if ( this.user.email === '' ) {
                    return { result: false }
                }

                const response = await axios.post(this.routes.emailIsFree, {
                    email: this.user.email
                })

                return response.data
            },
            setEmailIsFreeValidator () {
                const context = this

                Validator.extend('emailIsFree', {
                    getMessage: field => context.$t(`The ${field} already exists`, context.locale),
                    validate: () => {
                        return context.emailIsFree
                    }
                })
            },
            async submit () {
                // Call dynamic validate email
                await this.validateEmail()
                this.$validator.validate().then(async result => {
                    if ( result ) {
                        const { result } = await this.update()

                        if ( result ) {
                            Vue.notify({
                                group: 'notify',
                                title: this.$t('Profile'),
                                text: this.$t('Profile has been save'),
                                type: 'success',
                                config: {
                                    closeOnClick: true
                                }
                            })

                            if ( this.password !== '' ) {
                                this.password = ''
                                this.passwordConfirm = ''

                                Vue.notify({
                                    group: 'notify',
                                    title: this.$t('Profile'),
                                    text: this.$t('Profile has changed password'),
                                    type: 'success',
                                    config: {
                                        closeOnClick: true
                                    }
                                })
                            }
                        } else {
                            Vue.notify({
                                group: 'notify',
                                title: this.$t('Profile'),
                                text: this.$t('An error has been detected. Check the form.'),
                                type: 'error',
                                config: {
                                    closeOnClick: true
                                }
                            })
                        }
                    }
                })
            },
            async update () {
                let paramsRequest = {
                    email: this.user.email,
                    name: this.user.name,
                    roles: this.roles
                }

                if ( this.password !== '' ) {
                    paramsRequest.password = this.password
                    paramsRequest.password_confirmation = this.passwordConfirm
                }

                const response = await axios.post(this.routes.profileUpdate, paramsRequest)
                return response.data
            }
        },
        created () {
            const data = JSON.parse(this.data)

            this.user = data.user

            data.roles.forEach(role => {
                this.roles.push({
                    key: role,
                    value: data.userRoles.indexOf(role) >= 0
                })
            })

            this.setEmailIsFreeValidator()
            Validator.extend('password', passwordIsStrongRule)
        }
    }
</script>
