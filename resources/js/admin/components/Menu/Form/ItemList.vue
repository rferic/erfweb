<template>
    <div>
        <b-modal
            ref="modalItemForm"
            scrollable
            size="xl"
            hide-footer
            :title="$t('Item menu form', { locale })"
            @hidden="onCloseForm"
        >
            <item-form-menu
                v-if="currentItem !== null"
                :index="currentIndex"
                :language="language"
                :item-origin="currentItem"
                @onCancelForm="onCloseForm"
                @onSaveItem="onSaveItem"
            />
        </b-modal>
        <b-button
            size="sm"
            variant="success"
            class="pull-right"
            @click="onOpenForm({})"
        >
            <i class="fa fa-plus" />
            {{ $t('Create item', { locale }) }}
        </b-button>
        <p class="clearfix"></p>
        <hr>
        <b-list-group v-if="hasItems">
            <draggable
                v-model="items"
                @end="onChange"
            >
                <transition-group type="transition" name="flip-list">
                    <b-list-group-item
                        v-for="(item, index) in items"
                        :key="`item-${index}`"
                        class="itemDraggable"
                    >
                        <i class="fa" :class="{'fa-link': item.type === 'internal', 'fa-globe': item.type === 'external'}" />
                        {{ item.label }}
                        <b-button
                            variant="success"
                            class="pull-right"
                            @click="onOpenForm({ item, index })"
                        >
                            <i class="fa fa-edit" />
                        </b-button>
                        <b-button
                            variant="danger"
                            class="pull-right mr-2"
                            @click="onDelete(index)"
                        >
                            <i class="fa fa-trash" />
                        </b-button>
                        <p class="clearfix"></p>
                    </b-list-group-item>
                </transition-group>
            </draggable>
        </b-list-group>
        <b-alert :show="!hasItems" variant="warning">
            <i class="fa fa-warning" />
            {{ $t('Items not found', { locale }) }}
        </b-alert>
    </div>
</template>

<script>
    import { mapState } from 'vuex'
    import draggable from 'vuedraggable'
    import ItemFormMenu from './ItemForm'
    import cloneMixin from './../../../../includes/mixins/clone'
    import { menuItemStructure } from './../../../structures/menu'

    export default {
        name: 'ItemListFormMenu',
        props: {
            language: {
                type: Object,
                required: true
            },
            itemsOrigin: {
                type: Array,
                required: false,
                default: Array
            }
        },
        components: {ItemFormMenu, draggable },
        mixins: [ cloneMixin, ItemFormMenu ],
        data () {
            return {
                items: this.clone(this.itemsOrigin),
                currentIndex: null,
                currentItem: null
            }
        },
        computed: {
            ...mapState([ 'locale' ]),
            hasItems () {
              return this.items.length > 0
            }
        },
        watch: {
            itemsOrigin: {
                handler () {
                    this.refresh()
                },
                deep: true
            },
            currentItem () {
                if ( this.currentItem === null ) {
                    this.$refs.modalItemForm.hide()
                } else {
                    this.$refs.modalItemForm.show()
                }
            }
        },
        methods: {
            // Events
            onChange () {
                for ( let i = 0; i < this.items.length; i++ ) {
                    this.items[i].priority = i
                }
            },
            onOpenForm ({ item, index }) {
                if ( typeof item === typeof undefined ) {
                    item = this.clone(menuItemStructure)
                    this.currentIndex = null
                } else {
                    this.currentIndex = index
                }

                this.currentItem = this.clone(item)
            },
            onCloseForm () {
                this.currentItem = null
            },
            onSaveItem ({ item, index }) {
                if ( index === null ) {
                    item.priority = this.items.length
                    this.items.push(this.clone(item))
                } else {
                    this.items[index] = this.clone(item)
                }

                this.onCloseForm()
            },
            onDelete ( index ) {
                this.items.splice(index, 1)
            },
            // Actions
            refresh () {
                this.items = this.clone(this.itemsOrigin.filter(item => item.lang === this.language.iso))
                this.items.sort((a, b) => (a.priority > b.priority) ? 1 : ((b.priority > a.priority) ? -1 : 0))
            }
        },
        mounted () {
            this.refresh()
        }
    }
</script>

<style scoped>
    .itemDraggable {
        cursor: move;
    }
</style>
