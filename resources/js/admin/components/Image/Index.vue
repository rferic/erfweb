<template>
    <div>
        <BlockUI v-if="isVisibleBlockui" :message="messageBlockui">
            <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
        </BlockUI>
        <notifications
            group="notify"
            position="top right"
        />
        <b-modal
            ref="modalImage"
            scrollable
            hide-footer
            size="xl"
            :title="$t('Image form', { locale })"
            @hidden="onHideModal"
        >
            <form-image
                v-if="currentImage !== null"
                ref="formImage"
                :index="currentIndexImage"
                :image-origin="currentImage"
                @onRemove="onRemove"
                @onSave="onSave"
                @onCopy="onCopy"
            />
        </b-modal>
        <b-row class="mb-4">
            <b-col cols="4" xs="6">
                <b-form-input
                    v-model="filter"
                    name="filter"
                    type="text"
                    :placeholder="$t('Filter by title', { locale })"
                />
            </b-col>
            <b-col cols="8" xs="12" class="text-right">
                <b-button
                    variant="success"
                    @click="onAdd"
                >
                    <i class="fa fa-plus" />
                    {{ $t('Add image', { locale }) }}
                </b-button>
            </b-col>
        </b-row>
        <b-row v-if="hasImages" class="no-gutters">
            <b-col
                v-for="(image, index) in imagesFiltered"
                :key="index"
                cols="3"
                xs="6"
                class="images"
            >
                <img :src="getCalculatedSrcFromAdmin(image)" :title="image.title" />
                <div class="overlay">
                    {{ image.title }}
                    <b-button
                        variant="outline-primary"
                        size="sm"
                        class="ml-2"
                        @click="onEdit(index, image)"
                    >
                        <i class="fa fa-eye" />
                    </b-button>
                </div>
            </b-col>
        </b-row>
        <b-alert
            :show="!hasImages"
            variant="warning"
        >
            <i class="fa fa-warning"/>
            {{ $t('Images not found', { locale }) }}
        </b-alert>
    </div>
</template>

<script>
    import { mapState, mapActions } from 'vuex'
    import cloneMixin from './../../mixins/clone'
    import imageMixin from './../../mixins/image'
    import FormImage from './Form'

    export default {
        name: 'IndexImage',
        props: {
            data: {
                type: String,
                required: true
            }
        },
        components: { FormImage },
        mixins: [ cloneMixin, imageMixin ],
        data () {
            return {
                filter: '',
                images: [],
                currentIndexImage: null,
                currentImage: null,
                imageStructure: {
                    id: null,
                    title: '',
                    src: ''
                }
            }
        },
        computed: {
            ...mapState([ 'locale', 'routes' ]),
            ...mapState({
                isVisibleBlockui: state => state.blockui.isVisible,
                messageBlockui: state => state.blockui.message
            }),
            hasImages () {
                return this.imagesFiltered.length > 0
            },
            imagesFiltered () {
                return this.images.filter(image => image.title.includes(this.filter))
            }
        },
        methods: {
            ...mapActions({
                toogleBlockui : 'blockui/toggleIsVisible'
            }),
            // Events
            onAdd () {
                this.currentIndexImage = null
                this.currentImage = this.clone(this.imageStructure)
                this.$refs.modalImage.show()
            },
            onEdit ( index, image ) {
                this.currentIndexImage = index
                this.currentImage = image
                this.$refs.modalImage.show()
            },
            onClose () {
                this.$refs.modalImage.hide()
            },
            onSave ({ index, image, action }) {
                let images = this.clone(this.images)

                if ( action === 'create' ) {
                    images.push(this.clone(image))
                } else {
                    images[index] = this.clone(image)
                }

                this.$refs.modalImage.hide()
                this.$notify({
                    group: 'notify',
                    title: this.$t('Save image', { locale: this.locale }),
                    text: this.$t(action === 'create' ? 'Image has been created' : 'Image has been updated', { locale: this.locale }),
                    type: 'success',
                    config: {
                        closeOnClick: true
                    }
                })

                this.images = []
                this.images = this.clone(images)
            },
            async onRemove ({ index, image }) {
                this.$refs.modalImage.hide()
                this.toogleBlockui(true)
                await this.destroyImageRequest(image)
                this.images.splice(index, 1)
                this.toogleBlockui(false)

                this.$notify({
                    group: 'notify',
                    title: this.$t('Delete image', { locale: this.locale }),
                    text: this.$t('Image has been deleted', { locale: this.locale }),
                    type: 'success',
                    config: {
                        closeOnClick: true
                    }
                })
            },
            onHideModal () {
                this.$refs.formImage.$refs.dropzone.removeAllFiles()
            },
            onCopy ( link ) {
                this.$refs.modalImage.hide()
                this.$copyText(link)
                this.$notify({
                    group: 'notify',
                    title: this.$t('Link has been copied', { locale: this.locale }),
                    type: 'success',
                    config: {
                        closeOnClick: true
                    }
                })
            },
            // API Request
            async destroyImageRequest ( image ) {
                return await axios.delete(`${this.routes.baseRouteImage}/${image.id}/delete`, image)
            }
        },
        created () {
            this.images = JSON.parse(this.data).images
        }
    }
</script>

<style scoped>
    img {
        max-width: 100%
    }
</style>
