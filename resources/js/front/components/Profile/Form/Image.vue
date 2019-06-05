<template>
    <div>
        <v-tabs v-model="currentTab" light centered slider-color="grey darken-4">
            <v-tab href="#avatar">{{ $vuetify.t('Avatar') }}</v-tab>
            <v-tab href="#image">{{ $vuetify.t('Custom image') }}</v-tab>

            <v-tab-item value="avatar">
                <v-card color="white">
                    <v-card-text>
                        <v-layout row wrap>
                            <v-flex v-for="(avatar, index) of avatars" :key="index" xs4 sm4 md2 lg2 class="pa-1 text-xs-right">
                                <v-btn small fab class="avatar-btn" @click="selectAvatar(avatar)">
                                    <v-badge overlap color="transparent">
                                        <template v-slot:badge>
                                            <v-icon v-if="avatar === image" color="success">mdi-check-bold</v-icon>
                                        </template>

                                        <v-avatar color="white">
                                            <img :src="`/${avatar}`" :alt="avatar" />
                                        </v-avatar>
                                    </v-badge>
                                </v-btn>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn small color="error" @click="$emit('cancel')">
                            <v-icon class="mr-2" small>mdi-close</v-icon>
                            {{ $vuetify.t('Cancel') }}
                        </v-btn>
                        <v-spacer />
                        <v-btn small color="success" @click="$emit('changeImage', { image })">
                            <v-icon class="mr-2" small>mdi-check-bold</v-icon>
                            {{ $vuetify.t('Confirm') }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-tab-item>

            <v-tab-item value="image">
                <v-card color="white">
                    <v-card-text>
                        <v-layout row wrap>
                            <v-flex xs12 sm12 md12 lg12>
                                <vue2-dropzone
                                    v-if="dropzoneOptions.url !== null"
                                    ref="avatarDropzone"
                                    id="dropzone"
                                    :options="dropzoneOptions"
                                    @vdropzone-file-added="onFileAddedDropzone"
                                    @vdropzone-sending="onSendingDropzone"
                                    @vdropzone-success="onSuccessDropzone"
                                    @vdropzone-error="onErrorDropzone"
                                    @vdropzone-removed-file="onRemovedDropzone"
                                />
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn small color="error" @click="$emit('cancel')">
                            <v-icon class="mr-2" small>mdi-close</v-icon>
                            {{ $vuetify.t('Cancel') }}
                        </v-btn>
                        <v-spacer />
                        <v-btn small color="success" @click="$emit('changeImage', { image })">
                            <v-icon class="mr-2" small>mdi-check-bold</v-icon>
                            {{ $vuetify.t('Confirm') }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-tab-item>
        </v-tabs>

        <v-snackbar v-model="snackbar.show" :color="snackbar.color" :multi-line="true" bottom right>
            {{ $vuetify.t(snackbar.message) }}
        </v-snackbar>
    </div>
</template>

<script>
    import { mapState } from 'vuex'
    import vue2Dropzone from 'vue2-dropzone'
    import cloneMixin from './../../../../includes/mixins/clone'

    export default {
        name: 'ProfileFormImage',
        props: {
            imageOrigin: {
                type: String,
                required: false,
                default: ''
            }
        },
        components: { vue2Dropzone },
        mixins: [ cloneMixin ],
        data () {
            return {
                avatars: this.clone(this.$root.pageData.avatars),
                image: this.clone(this.imageOrigin),
                currentTab: 'avatar',
                temporalImageDropzone: null,
                imageIsTemporal: false,
                dropzoneOptions: {
                    method: 'post',
                    paramName: 'image',
                    acceptedFiles: 'image/*',
                    url: null,
                    thumbnailWidth: 150,
                    maxFilesize: 4,
                    maxFiles: 1,
                    addRemoveLinks: true,
                    dictDefaultMessage: '<i class="v-icon mdi mdi-cloud-upload-outline theme--light grey--text text--darken-4"></i><br><span class="grey--text text--darken-4">' + this.$vuetify.t('Upload me') + '</span>',
                    dictRemoveFile: this.$vuetify.t('Remove image')
                },
                snackbar: {
                    show: false,
                    color: '',
                    message: ''
                }
            }
        },
        computed: {
            ...mapState([ 'csrfToken', 'routes' ])
        },
        methods: {
            reset () {
                this.avatars = this.clone(this.$root.pageData.avatars),
                this.image = this.clone(this.imageOrigin)
                this.currentTab = this.getIsAvatar() ? 'avatar' : 'image'

                if ( this.imageIsTemporal ) {
                    this.$refs.avatarDropzone.removeFile(this.temporalImageDropzone)
                    this.temporalImageDropzone = null
                    this.imageIsTemporal = false
                }
            },
            getIsAvatar () {
                return this.image === '' || this.avatars.includes(this.image)
            },
            selectAvatar ( avatar ) {
                if ( this.imageIsTemporal ) {
                    this.$refs.avatarDropzone.removeFile(this.temporalImageDropzone)
                    this.imageIsTemporal = false
                }

                this.image = avatar
            },
            // Dropzone events
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
                    this.image = response.data.image
                    this.temporalImageDropzone = file
                    this.imageIsTemporal = true
                }
            },
            onErrorDropzone ( file, error, xhr ) {
                this.$refs.avatarDropzone.removeFile(file)

                this.snackbar.color = 'error'
                this.snackbar.message = this.$vuetify.t('It was not possible to upload the image, check that the image complies with the parameters.')
                this.snackbar.show = true
            },
            async onRemovedDropzone ( file ) {
                await this.deleteImageTemporalRequest(this.image)
                this.temporalImageDropzone = null
                this.imageIsTemporal = false

                if ( !this.getIsAvatar() ) {
                    this.image = ''
                }
            },
            async deleteImageTemporalRequest ( image ) {
                const { data } = this.axios.delete(this.routes.deleteImageTemporal, { data: { image } })
                return data
            }
        },
        mounted () {
            this.dropzoneOptions.url = this.routes.uploadImageTemporal
            this.reset()
        }
    }
</script>

<style scoped>
    #dropzone {
        height: 300px;
    }
</style>
