<template>
    <div>
        <div class="text-right mb-4">
            <b-button variant="primary" @click.prevent="onOpenModal({})">
                <i class="fa fa-plus" /> {{ $t('Add new content', { locale }) }}
            </b-button>
        </div>
        <div class="clearfix" />
        <b-list-group>
            <draggable
                v-model="contents"
                @end="onChange"
                v-bind="dragOptions"
            >
                <transition-group type="transition" name="flip-list">
                    <b-list-group-item
                        v-for="(content, index) in contents"
                        :key="content.key"
                        class="itemDraggable"
                    >
                        <div class="row"
                             :disabled="content.deleted_at !== null">
                            <b-col lg="9" sm="6">
                                <b-badge
                                    v-if="content.deleted_at === null"
                                    variant="success"
                                    class="semaphore"
                                >&nbsp;</b-badge>
                                <b-badge
                                    v-if="content.deleted_at !== null"
                                    variant="danger"
                                    class="semaphore"
                                >&nbsp;</b-badge>
                                <component :is="content.deleted_at !== null ? 'del' : 'span'">
                                    <b>{{ content.key }}</b>
                                    <i v-if="content.id_html !== ''" class="ml-2">id="{{ content.id_html }}"</i>
                                    <i v-if="content.class_html !== ''" class="ml-2">class="{{ content.class_html }}"</i>
                                </component>
                            </b-col>
                            <b-col lg="3" sm="6" class="text-right">
                                <b-button
                                    v-if="content.deleted_at === null"
                                    variant="primary"
                                    @click.prevent="onOpenModal({ content, index })"
                                >
                                    <i class="fa fa-pencil" />
                                </b-button>
                                <b-button
                                    v-if="content.deleted_at === null"
                                    variant="danger"
                                    @click.prevent="remove(content)"
                                >
                                    <i class="fa fa-ban" />
                                </b-button>
                                <b-button
                                    v-else
                                    variant="success"
                                    @click.prevent="restore(content)"
                                >
                                    <i class="fa fa-undo" />
                                </b-button>
                                <b-button
                                    v-if="content.deleted_at !== null"
                                    variant="danger"
                                    @click.prevent="destroy(content, index)"
                                >
                                    <i class="fa fa-trash" />
                                </b-button>
                            </b-col>
                        </div>
                        <div class="clearfix" />
                    </b-list-group-item>
                </transition-group>
            </draggable>
        </b-list-group>
        <b-modal
            ref="modalContentForm"
            scrollable
            size="xl"
            :title="$t('Content form', { locale })"
        >
            <content-form
                ref="contentForm"
                :content-origin="currentContent.data"
                :index="currentContent.index"
                :keys-in-use="keysInUse"
                @onSaveContent="onSaveContent"
                @onCloseModal="onCloseModal"
            />
            <div slot="modal-footer" class="w-100">
                <b-row>
                    <b-col lg="6">
                        <b-button
                            variant="danger"
                            sm
                            @click="onCloseModal"
                        >
                            <i class="fa fa-close" />
                            {{ $t('Cancel', { locale }) }}
                        </b-button>
                    </b-col>
                    <b-col lg="6" class="text-right">
                        <b-button
                            variant="success"
                            sm
                            @click="$refs.contentForm.onSave()"
                        >
                            <i class="fa fa-save" />
                            {{ $t('Save', { locale }) }}
                        </b-button>
                    </b-col>
                </b-row>
            </div>
        </b-modal>
    </div>
</template>

<script>
    import { mapState } from 'vuex'
    import draggable from 'vuedraggable'
    import cloneMixin from './../../../../includes/mixins/clone'
    import contentStructure from './../../../structures/content'
    import ContentForm from './Form'

    export default {
        name: 'ContentList',
        props: {
            contentsOrigin: {
                type: Array,
                required: true
            },
            dragAndDrop: {
                type: Boolean,
                required: false,
                default: true
            }
        },
        components: { draggable, ContentForm },
        mixins: [ cloneMixin ],
        data () {
            return {
                contents: [],
                currentContent: {
                    data: this.clone(contentStructure),
                    index: null
                },
                keysInUse: []
            }
        },
        computed: {
            ...mapState([ 'locale' ]),
            dragOptions() {
                return {
                    animation: 200,
                    group: "description",
                    disabled: false,
                    ghostClass: "ghost"
                };
            }
        },
        methods: {
            // Events
            onChange () {
                for ( let i = 0; i < this.contents.length; i++ ) {
                    this.contents[i].priority = i
                }

                this.$emit('onChangeInContents', this.contents)
            },
            onOpenModal ({ content, index }) {
                this.currentContent.index = typeof index === undefined ? null : index
                this.currentContent.data = this.clone(typeof content === undefined ? contentStructure : content)
                this.$refs.modalContentForm.show()
            },
            onCloseModal () {
                this.$refs.modalContentForm.hide()
            },
            onSaveContent ({ content, index }) {
                if ( index !== null ) {
                    this.contents[index] = this.clone(content)
                } else {
                    content.priority = this.contents.length
                    this.contents.push(this.clone(content))
                }

                this.refreshKeysInUse()
                this.onCloseModal()
                this.$emit('onChangeInContents', this.contents)
            },
            // Actions
            refreshKeysInUse () {
                this.keysInUse = []

                for ( const [index, content]  of this.contents.entries() ) {
                    this.keysInUse.push({ key: content.key, index: index })
                }
            },
            remove ( content ) {
                content.deleted_at = Vue.moment().locale(this.locale).format('YYYY-MM-DD HH:mm:ss')
                this.$emit('onChangeInContents', this.contents)
            },
            restore ( content ) {
                content.deleted_at = null
                this.$emit('onChangeInContents', this.contents)
            },
            destroy ( content, index ) {
                this.contents.splice(index, 1)
                this.$emit('onChangeInContents', this.contents)
            }
        },
        mounted () {
            this.contents = this.clone(this.contentsOrigin)
            this.contents.sort((a, b) => (a.priority > b.priority) ? 1 : ((b.priority > a.priority) ? -1 : 0))
            this.refreshKeysInUse()
        }
    }
</script>

<style scoped>
    .itemDraggable {
        cursor: move;
    }
</style>
