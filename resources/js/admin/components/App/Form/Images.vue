<template>
    <b-row>
        <b-col xs="12">
            <vue2-dropzone
                ref="dropzone"
                id="dropzone"
                :options="dropzoneOptions"
                v-on:vdropzone-sending="onSendingDropzone"
                v-on:vdropzone-success="onSuccessDropzone"
                v-on:vdropzone-error="onErrorDropzone"
            />
        </b-col>
        <b-col xs="12">
            <b-row v-if="hasImages">
                <draggable
                    v-model="images"
                    @end="onChangePriority"
                    v-bind="dragOptions"
                >
                    <transition-group type="transition" name="flip-list">
                        <b-col
                            v-for="(image, index) in images"
                            :key="`image-${index}`"
                            cols="12"
                            class="images mb-2"
                        >
                            <b-row>
                                <b-col cols="6" xs="12">
                                    <img :src="getCalculatedSrcFromAdmin(image)" />
                                </b-col>
                                <b-col cols="6" xs="12">
                                    <b-form-group :label="`${$t('Title', { locale: locale })}: *`">
                                        <b-form-input
                                            v-model="image.title"
                                            :name="`image-title-${index}`"
                                            type="text"
                                            v-validate
                                            data-vv-rules="required"
                                            :class="{ 'is-invalid' : errors.has(`image-title-${index}`) }"
                                        />
                                        <div
                                            v-if="errors.has(`image-title-${index}`)"
                                            class="invalid-feedback">
                                            <i
                                                v-show="errors.has(`image-title-${index}`)"
                                                class="fa fa-warning text-danger"
                                            />
                                            {{ errors.first(`image-title-${index}`) }}
                                        </div>
                                    </b-form-group>
                                    <div v-if="typeof image.languages !== typeof undefined" class="pull-left">
                                        <b-form-checkbox
                                            v-for="language in image.languages"
                                            :key="`image-${index}-${language.iso}`"
                                            v-model="language.checked"
                                            switch
                                            @input="setLangsInImage(image)"
                                        >
                                            {{ language.name }}
                                        </b-form-checkbox>
                                    </div>
                                    <b-button
                                        variant="outline-danger"
                                        size="xs"
                                        class="pull-right"
                                        @click="remove(index)"
                                    >
                                        <i class="fa fa-trash" />
                                    </b-button>
                                    <p class="clearfix"></p>
                                </b-col>
                            </b-row>
                        </b-col>
                    </transition-group>
                </draggable>
            </b-row>
            <b-alert
                :show="!hasImages"
                variant="warning"
            >
                <i class="fa fa-warning text-warning" />
                {{ $t('Image not found', { locale }) }}
            </b-alert>
        </b-col>
    </b-row>
</template>

<script>
    import { mapState } from 'vuex'
    import draggable from 'vuedraggable'
    import vue2Dropzone from 'vue2-dropzone'
    import cloneMixin from './../../../mixins/clone'
    import imageMixin from './../../../mixins/image'
    import { appImageStructure } from './../../../structures/app'

    export default {
        name: 'FormAppImages',
        props: {
            imagesOrigin: {
                type: Array,
                required: false,
                default: []
            },
            languagesAvailable: {
                type: Array,
                required: true
            }
        },
        components: { draggable, vue2Dropzone },
        mixins: [ cloneMixin, imageMixin ],
        data () {
            return {
                images: this.clone(this.imagesOrigin),
                dropzoneOptions: {
                    method: 'post',
                    paramName: 'image',
                    acceptedFiles: 'image/*',
                    url: null,
                    thumbnailWidth: 150,
                    maxFilesize: 4,
                    addRemoveLinks: false,
                    dictDefaultMessage: '<i class="fa fa-cloud-upload"></i> ' + this.$t('Upload me', { locale: this.locale }),
                    dictRemoveFile: this.$t('Remove image', { locale: this.locale })
                },
                dragOptions: {
                    animation: 200,
                    group: "description",
                    disabled: false,
                    ghostClass: "ghost"
                }
            }
        },
        computed: {
            ...mapState([ 'locale', 'routes', 'csrfToken' ]),
            hasImages () {
                return this.images.length > 0
            }
        },
        methods: {
            // Events
            onSendingDropzone ( file, xhr, formData ) {
                formData.append('_token', this.csrfToken);
            },
            onSuccessDropzone ( file, response ) {
                if ( response.result ) {
                    let image = this.clone(appImageStructure)
                    image.src = response.data.image
                    image.priority = this.images.length
                    this.images.push(image)
                    this.setLanguagesChecksInImage(this.images[this.images.length - 1])
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
            onChangePriority () {
                this.refreshPriorities()
            },
            // Actions
            refreshPriorities () {
                for ( let i = 0; i < this.images.length; i++ ) {
                    this.images[i].priority = i
                }
            },
            setLanguagesChecksInImages () {
                for ( let image of this.images ) {
                    if ( typeof image.languages === typeof undefined ) {
                        this.setLanguagesChecksInImage(image)
                    }
                }
            },
            setLanguagesChecksInImage ( image ) {
                let langs = JSON.parse(image.langs)
                image.languages = []

                for ( const languageAvailable of this.languagesAvailable ) {
                    image.languages.push({
                        name: languageAvailable.name,
                        iso: languageAvailable.iso,
                        checked: langs.includes(languageAvailable.iso)
                    })
                }
            },
            setLangsInImage ( image ) {
                let langs = []

                for ( const language of image.languages ) {
                    if ( language.checked ) {
                        langs.push(language.iso)
                    }
                }

                image.langs = JSON.stringify(langs)
            },
            remove ( index ) {
                this.images.splice(index, 1)
            },
            // Getters
            async getIsValid () {
                return await this.$validator.validate()
            }
        },
        created () {
            this.dropzoneOptions.url = this.routes.uploadImage
        },
        mounted () {
            this.refreshPriorities()
            this.images.sort((a, b) => (a.priority > b.priority) ? 1 : ((b.priority > a.priority) ? -1 : 0))
            this.setLanguagesChecksInImages()
        }
    }
</script>

<style scoped>
    .images {
        cursor: move;
    }

    .images img {
        max-width: 100%;
    }
</style>
