<template>
    <div>
        <notifications
            group="notify"
            position="top right"
        />
        <b-row>
            <b-col lg="4" md="12" class="order-xl-2 mt-2">
                <b-card class="card-profile shadow">
                    <div class="row justify-content-center">
                        <div class="col-lg-5 order-lg-4 pb-5">
                            <div class="card-profile-image">
                                <img :src="`/${user.avatar}`" class="rounded-circle">
                            </div>
                        </div>
                    </div>
                    <div class="card-body text-center border-0 pt-8 pt-md-4 pb-0 pb-md-4">
                        <div class="text-center">
                            <h3>
                                {{ user.name }}
                            </h3>
                            <div class="h5 font-weight-300">
                                <i class="ni ni-email-83" /> {{ user.email }}
                            </div>
                            <b-row>
                                <b-col cols="12">
                                    <div class="card-profile-stats d-flex justify-content-center">
                                        <div v-for="(role, index) in roles" :key="index">
                                            <component :is="role.value ? 'span' : 'del'" class="header">
                                                {{ role.key }}
                                            </component>
                                        </div>
                                    </div>
                                </b-col>
                            </b-row>
                        </div>
                    </div>
                </b-card>
            </b-col>
            <b-col lg="8" md="12" class="order-xl-1 mt-2">
                <b-card>
                    <b-form>
                        <b-row>
                            <b-col
                                lg="8"
                                md="6"
                                sm="12">
                                <b-row class="mb-3">
                                    <b-col
                                        lg="6"
                                        md="6"
                                        sm="12">
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
                                        sm="12">
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
                                        sm="12">
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
                                        sm="12">
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
                                    <b-col lg="12" class="mb-4">
                                        <toggle-button v-model="toggleImage" />
                                        <label>{{ $t('Custom image', { locale}) }}</label>
                                        <b-alert
                                            :show="errorImageShow && user.avatar === null"
                                            variant="danger"
                                        >
                                            <i class="fa fa-warning text-danger" />
                                            {{ $t('Image is required', { locale}) }}
                                        </b-alert>
                                    </b-col>
                                    <b-col v-if="!toggleImage" lg="12">
                                        <div class="row">
                                            <div class="col-md-3 col-xs-6" v-for="(avatar, key) in userDataOrigin.avatars" :key="key">
                                                <div
                                                    class="avatar avatar-100 selectable mb-4"
                                                    :class="{ 'selected': avatar === user.avatar }"
                                                >
                                                    <img
                                                        :src="`/${avatar}`"
                                                        @click="onSelectAvatar(avatar)"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </b-col>
                                </b-row>
                                <b-row v-if="toggleImage">
                                    <b-col v-if="toggleImage" lg="6" sm="12">
                                        <div
                                            v-if="user.avatar !== null && avatarIsImage"
                                            class="avatar avatar-100"
                                        >
                                            <img :src="`./../${user.avatar}`" />
                                        </div>
                                        <div
                                            v-else
                                             class="avatar avatar-100"
                                        >
                                        </div>
                                    </b-col>
                                    <b-col
                                        lg="6"
                                        sm="12"
                                    >
                                        <vue2-dropzone
                                            ref="avatarDropzone"
                                            id="dropzone"
                                            :options="dropzoneOptions"
                                            v-on:vdropzone-file-added="onFileAddedDropzone"
                                            v-on:vdropzone-sending="onSendingDropzone"
                                            v-on:vdropzone-success="onSuccessDropzone"
                                            v-on:vdropzone-error="onErrorDropzone"
                                            v-on:vdropzone-removed-file="onRemovedDropzone"
                                        />
                                    </b-col>
                                </b-row>
                            </b-col>
                            <b-col
                                lg="4"
                                md="6"
                                sm="12">
                                <label>Roles:</label>
                                <div class="form-check">
                                    <div
                                        class="checkbox"
                                        v-for="(role, index) in roles"
                                        :key="index">
                                        <label
                                            :for="role.key"
                                            class="form-check-label ">
                                            <toggle-button
                                                v-model="role.value"
                                                :disabled="role.key === 'admin' && role.value === true"
                                            />
                                            {{ role.key }}
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
                </b-card>
            </b-col>
        </b-row>
    </div>
</template>

<script>
    import { mapState, mapActions } from 'vuex'
    import cloneMixin from '../../../includes/mixins/clone'
    import userMixin from '../../mixins/user'
    import { Validator } from 'vee-validate'
    import passwordIsStrongRule from '../../../includes/validators/passwordIsStrongRule'
    import vue2Dropzone from 'vue2-dropzone'

    export default {
        name: 'IndexProfile',
        props: {
            data: {
                type: String,
                required: true
            }
        },
        mixins: [ cloneMixin, userMixin ],
        components: { vue2Dropzone },
        data () {
            return {
                user: null,
                roles: [],
                emailIsFree: true,
                password: '',
                passwordConfirm: '',
                avatarIsImage: true,
                toggleImage: true,
                dropzoneOptions: {
                    method: 'post',
                    paramName: 'image',
                    acceptedFiles: 'image/*',
                    url: null,
                    thumbnailWidth: 150,
                    maxFilesize: 4,
                    maxFiles: 1,
                    addRemoveLinks: true,
                    dictDefaultMessage: '<i class="fa fa-cloud-upload"></i> ' + this.$t('Upload me', { locale: this.locale }),
                    dictRemoveFile: this.$t('Remove image', { locale: this.locale })
                },
                temporalImageDropzone: null,
                imageIsTemporal: false,
                errorImageShow: false
            }
        },
        computed: {
            ...mapState([ 'locale', 'routes', 'csrfToken' ]),
            ...mapState({
                auth: state => state.auth.user
            }),
            userDataOrigin () {
                return JSON.parse(this.data)
            },
            userIsCurrentAuth () {
                return this.auth.id === this.user.id
            }
        },
        methods: {
            ...mapActions({
                setAuth : 'auth/set'
            }),
            async onSelectAvatar (avatar) {
                await this.removeTemporalImage()
                this.user.avatar = avatar
                this.avatarIsImage = this.getAvatarIsImage()
            },
            async onFileAddedDropzone ( file ) {
                if ( this.temporalImageDropzone !== null ) {
                    this.$refs.avatarDropzone.removeFile(this.temporalImageDropzone)
                }
            },
            onSendingDropzone ( file, xhr, formData ) {
                formData.append('_token', this.csrfToken);
            },
            onSuccessDropzone ( file, response ) {
                if ( response.result ) {
                    this.user.avatar = response.data.image
                    this.temporalImageDropzone = file
                    this.avatarIsImage = this.getAvatarIsImage()
                    this.imageIsTemporal = true
                }
            },
            onErrorDropzone ( file, error, xhr ) {
                this.$refs.avatarDropzone.removeFile(file)

                this.$notify({
                    group: 'notify',
                    title: this.$t('Upload image'),
                    text: this.$t(error),
                    type: 'error',
                    config: {
                        closeOnClick: true
                    }
                })
            },
            async onRemovedDropzone ( file ) {
                await this.removeTemporalImage()
                this.user.avatar = null
                this.temporalImageDropzone = null
            },
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

                const response = await this.axios.post(this.routes.emailIsFree, {
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
                    if ( result && this.user.avatar !== null ) {
                        const { result } = await this.update()

                        if ( result ) {
                            this.$notify({
                                group: 'notify',
                                title: this.$t('Profile'),
                                text: this.$t('Profile has been save'),
                                type: 'success',
                                config: {
                                    closeOnClick: true
                                }
                            })

                            if ( this.password !== '' ) {
                                this.$notify({
                                    group: 'notify',
                                    title: this.$t('Profile'),
                                    text: this.$t('Profile has changed password'),
                                    type: 'success',
                                    config: {
                                        closeOnClick: true
                                    }
                                })
                            }

                            if ( this.userIsCurrentAuth ) {
                                this.setAuth(await this.getUserDataRequest())
                            }

                            this.clearPasswords()
                        } else {
                            this.$notify({
                                group: 'notify',
                                title: this.$t('Profile'),
                                text: this.$t('An error has been detected. Check the form.'),
                                type: 'error',
                                config: {
                                    closeOnClick: true
                                }
                            })
                        }
                    } else if ( result && this.user.avatar === null ) {
                        this.errorImageShow = true
                    }
                })
            },
            async update () {
                let paramsRequest = {
                    email: this.user.email,
                    name: this.user.name,
                    avatar: this.user.avatar,
                    roles: this.roles
                }

                if ( this.password !== '' ) {
                    paramsRequest.password = this.password
                    paramsRequest.password_confirmation = this.passwordConfirm
                }

                try {
                    const response = await this.axios.post(this.routes.userUpdate, paramsRequest)
                    return response.data
                } catch (err) {
                    return { result: false }
                }
            },
            async removeTemporalImage () {
                if ( this.user.avatar !== null && this.getAvatarIsImage() && this.imageIsTemporal ) {
                    const { result, data } = await this.axios.delete(this.routes.removeImage, {
                        data: {
                            image: this.user.avatar
                        }
                    })

                    if ( result ) {
                        this.user = data.user
                    }
                }
            },
            getAvatarIsImage () {
                return !(this.user.avatar !== null && this.userDataOrigin.avatars.includes(this.user.avatar))
            },
            clearPasswords () {
                setTimeout(() => { this.password = '' }, 1000)
            }
        },
        created () {
            const data = JSON.parse(this.data)

            this.user = data.user
            this.avatarIsImage = this.getAvatarIsImage()
            this.toggleImage = this.avatarIsImage
            this.dropzoneOptions.url = this.routes.uploadImage

            data.roles.forEach(role => {
                this.roles.push({
                    key: role,
                    value: data.userRoles.indexOf(role) >= 0
                })
            })

            this.setEmailIsFreeValidator()
            Validator.extend('password', passwordIsStrongRule)
        },
        mounted () {
            this.clearPasswords()
        }
    }
</script>

<style scoped>
    .avatar.selected:hover {
        opacity: .8;
        cursor: pointer;
    }

    .avatar.selected:hover,
    .avatar.selectable.selected {
        box-shadow: 0px 0px 8px #474b67;
    }
</style>
