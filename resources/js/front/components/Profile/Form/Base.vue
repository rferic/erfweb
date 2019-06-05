<template>
    <v-card class="ml-2 mr-2">
        <v-card-title primary-title>
            <h2 class="title">{{ $vuetify.t('Change your contact data') }}</h2>
        </v-card-title>
        <v-card-text class="pt-0 pb-0">
            <v-form>
                <div class="d-block mb-2 text-xs-right">
                    <div class="d-inline-block">
                        <v-chip v-for="role in user.roles" :key="role" small color="grey darken-2" text-color="white" :disabled="true">
                            <v-avatar>
                                <v-icon color="white" small>mdi-account-key</v-icon>
                            </v-avatar>
                            {{ role }}
                        </v-chip>
                    </div>
                    <div class="d-inline-block">
                        <v-btn small fab class="avatar-btn" @click="imageForm = true">
                            <v-badge overlap color="white">
                                <template v-slot:badge>
                                   <v-icon color="grey darken-4">mdi-circle-edit-outline</v-icon>
                                </template>

                                <v-avatar color="white">
                                    <img :src="`/${avatar}`" :alt="name" />
                                </v-avatar>
                            </v-badge>
                        </v-btn>
                    </div>
                </div>
                <v-text-field
                    name="name"
                    type="text"
                    v-model="name"
                    :label="$vuetify.t('Name')"
                    v-validate="'required'"
                    data-vv-name="name"
                    :error-messages="errors.collect('name')"
                    @input="$validator.reset()"
                />
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

                <v-select
                    :items="localesSupported"
                    item-value="iso"
                    item-text="name"
                    v-model="lang"
                    :label="$vuetify.t('Language')"
                />
            </v-form>
        </v-card-text>
        <v-card-actions>
            <v-spacer v-if="!loader" />
            <v-btn v-if="!loader" small color="success" @click="onSubmit">
                <v-icon class="mr-2" small>mdi-content-save</v-icon>
                {{ $vuetify.t('Save') }}
            </v-btn>
            <v-progress-linear v-if="loader" :indeterminate="true"/>
        </v-card-actions>
        <v-dialog v-model="imageForm" max-width="600px" lazy persistent>
            <profile-form-image ref="profileFormImage" :image-origin="avatar" @changeImage="onChangeImage" @cancel="imageForm = false" />
        </v-dialog>
        <v-snackbar v-model="snackbar.show" :color="snackbar.color" :multi-line="true" bottom right>
            {{ $vuetify.t(snackbar.message) }}
        </v-snackbar>
    </v-card>
</template>

<script>
    import { mapState, mapActions } from 'vuex'
    import ProfileFormImage from './Image'
    import cloneMixin from './../../../../includes/mixins/clone'
    import authMixin from './../../../mixins/auth'
    import userMixin from './../../../mixins/user'

    export default {
        name: 'ProfileFormBase',
        components: { ProfileFormImage },
        mixins: [ cloneMixin, authMixin, userMixin ],
        data () {
            return {
                name: '',
                email: '',
                avatar: '',
                lang: 'en_GB',
                loader: false,
                snackbar: {
                    show: false,
                    color: '',
                    message: ''
                },
                imageForm: false
            }
        },
        computed: {
            ...mapState([ 'locale', 'localesSupported' ]),
            ...mapState({
                user: state => state.auth.user
            })
        },
        methods: {
            ...mapActions({
                setAuth: 'auth/set'
            }),
            refreshData () {
                this.name = this.clone(this.user.name)
                this.email = this.clone(this.user.email)
                this.avatar = this.clone(this.user.avatar)
            },
            onChangeImage ({ image }) {
                this.avatar = image
                this.imageForm = false
            },
            async onSubmit () {
                if ( await this.$validator.validateAll() ) {
                    this.update()
                }
            },
            async update () {
                this.loader = true
                const { result, auth } = await this.updateUserBaseRequest({ name: this.name, email: this.email, avatar: this.avatar, lang: this.lang })

                if ( result ) {
                    this.setAuth(auth)
                    this.refreshData()

                    if ( typeof this.$refs.profileFormImage !== typeof undefined ) {
                        this.$refs.profileFormImage.reset()
                    }

                    this.snackbar.color = 'success'
                    this.snackbar.message = this.$vuetify.t('Your contact data has been updated')
                    this.snackbar.show = true
                } else {
                    this.snackbar.color = 'error'
                    this.snackbar.message = this.$vuetify.t('There has been a problem and your contact information has not been updated')
                    this.snackbar.show = true
                }

                this.loader = false
            }
        },
        mounted () {
            this.setValidators()
            this.refreshData()
            this.lang = this.clone(this.user.lang)
        }
    }
</script>

<style scoped>
    .avatar-btn:hover {
        opacity: .8;
    }
</style>
