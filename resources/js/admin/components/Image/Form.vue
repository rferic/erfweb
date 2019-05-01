<template>
    <div>
        <b-row>
            <b-col lg="6" sm="12">
                <img v-if="image.src !== ''" :src="getCalculatedSrcFromAdmin(image)" :title="image.title" />
                <b-alert
                    :show="image.src === ''"
                    variant="warning"
                >
                    <i class="fa fa-warning" />
                    {{ $t('Image is required', { locale }) }}
                </b-alert>
            </b-col>
            <b-col lg="6" sm="12">
                <b-form-group :label="`${$t('Title', { locale: locale })}: *`">
                    <b-form-input
                        v-model="image.title"
                        name="title"
                        type="text"
                        v-validate
                        data-vv-rules="required"
                        :class="{ 'is-invalid' : errors.has(`title`) }"
                    />
                    <div
                        v-if="errors.has(`title`)"
                        class="invalid-feedback">
                        <i
                            v-show="errors.has('title')"
                            class="fa fa-warning text-danger"
                        />
                        {{ errors.first('title') }}
                    </div>
                </b-form-group>
                <vue2-dropzone
                    ref="dropzone"
                    id="dropzone"
                    :options="dropzoneOptions"
                    v-on:vdropzone-sending="onSendingDropzone"
                    v-on:vdropzone-success="onSuccessDropzone"
                    v-on:vdropzone-error="onErrorDropzone"
                />
            </b-col>
            <b-col lg="6" sm="12">
                <b-input-group class="mt-3">
                    <b-form-input
                        :value="link"
                        name="title"
                        type="text"
                        disabled
                    />
                    <b-input-group-append>
                        <b-button
                            variant="info"
                            @click="$emit('onCopy', link)"
                        >
                            <i class="fa fa-clipboard" />
                        </b-button>
                    </b-input-group-append>
                </b-input-group>
            </b-col>
            <b-col v-if="showError && !hasImage" lg="12">
                <b-alert
                    :show="!hasImage"
                    variant="danger"
                >
                    <i class="fa fa-warning" />
                    {{ $t('Image is required', { locale }) }}
                </b-alert>
            </b-col>
        </b-row>
        <form-buttons
            label-cancel="Remove"
            icon-cancel="fa-trash"
            @onCancel="onRemove"
            @onSave="onSave"
        />
    </div>
</template>

<script>
    import { mapState } from 'vuex'
    import vue2Dropzone from 'vue2-dropzone'
    import FormButtons from './../FormResources/Buttons'
    import cloneMixin from './../../mixins/clone'
    import imageMixin from './../../mixins/image'

    export default {
        name: 'FormImage',
        props: {
            index: {
                type: Number,
                required: false,
                default: null
            },
            imageOrigin: {
                type: Object,
                required: true
            }
        },
        components: { vue2Dropzone, FormButtons },
        mixins: [ cloneMixin, imageMixin ],
        data () {
            return {
                image: this.clone(this.imageOrigin),
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
                showError: false
            }
        },
        computed: {
            ...mapState([ 'locale', 'routes', 'csrfToken' ]),
            isNew () {
                return this.image.id === null
            },
            hasImage () {
                return this.image.src !== ''
            },
            link () {
                return this.getCalculatedSrcFromAdmin(this.image)
            }
        },
        watch: {
            imageOrigin: {
                handler () {
                    this.image = this.clone(this.imageOrigin)
                },
                deep: true
            }
        },
        methods: {
            // Events
            async onSave () {
                if ( await this.$validator.validate() && this.hasImage ) {
                    const action = await this.processForm()
                    this.successForm(action)
                } else {
                    this.showError = this.hasImage
                }
            },
            onRemove () {
                this.$emit('onRemove', { index: this.index, image: this.image })
            },
            onSendingDropzone ( file, xhr, formData ) {
                formData.append('_token', this.csrfToken);
            },
            onSuccessDropzone ( file, response ) {
                if ( response.result ) {
                    this.image.src = response.data.image
                    this.showError = false
                }
            },
            onErrorDropzone ( file, error, xhr ) {
                this.$refs.dropzone.removeFile(file)

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
            // Actions
            async processForm () {
                let action = this.isNew ? 'create' : 'update'
                const { result, image } = this.isNew
                    ? await this.createImageRequest(this.image)
                    : await this.updateImageRequest(this.image)

                this.image = image

                return action
            },
            successForm ( action ) {
                this.$emit('onSave', { index: this.index, image: this.clone(this.image), action });
            },
            // API Request
            async createImageRequest ( image ) {
                const { data } = await this.axios.post(`${this.routes.baseRouteImage}/create`, image)
                return data
            },
            async updateImageRequest ( image ) {
                const { data } = await this.axios.post(`${this.routes.baseRouteImage}/${image.id}/update`, image)
                return data
            }
        },
        created () {
            this.dropzoneOptions.url = this.routes.uploadImage
        }
    }
</script>

<style scoped>
    img {
        max-width: 100%;
    }
</style>
