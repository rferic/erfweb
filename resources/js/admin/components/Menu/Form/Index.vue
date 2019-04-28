<template>
    <div>
        <BlockUI v-if="isVisibleBlockui" :message="messageBlockui">
            <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
        </BlockUI>
        <notifications
            group="notify"
            position="top right"
        />
        <b-row>
            <b-col cols="12">
                <b-form-group :label="`${$t('Name', { locale: locale })}: *`">
                    <b-form-input
                        v-model="menu.name"
                        name="name"
                        type="text"
                        v-validate
                        data-vv-rules="required"
                        :class="{ 'is-invalid' : errors.has(`name`) }"
                    />
                    <div
                        v-if="errors.has(`name`)"
                        class="invalid-feedback">
                        <i
                            v-show="errors.has('name')"
                            class="fa fa-warning text-danger"
                        />
                        {{ errors.first('name') }}
                    </div>
                </b-form-group>
            </b-col>
            <b-col cols="12">
                <b-form-group
                    class="mt-3"
                    :label="`${$t('Description', { locale })}:`"
                    label-for="text">
                    <b-form-textarea
                        name="description"
                        v-model="menu.description"
                        :rows="4"
                    />
                </b-form-group>
            </b-col>
        </b-row>
        <b-row>
            <b-col cols="12">
                <multilanguage-tab
                    ref="multilanguageTab"
                    :languages-default="languagesAvailable"
                    :show-enable-icons="false"
                    :default-active="locale"
                >
                    <template slot-scope="{ language }">
                        <item-list-form-menu
                            :ref="`itemsList${language.iso}`"
                            :language="language"
                            :items-origin="menu.items"
                        />
                    </template>
                </multilanguage-tab>
            </b-col>
        </b-row>
        <form-buttons
            :show-cancel="menu.id !== null"
            label-cancel="Remove"
            @onSave="onSave"
            @onCancel="onDestroy"
        />
    </div>
</template>

<script>
    import { mapState, mapActions } from 'vuex'
    import MultilanguageTab from './../../Multilanguage/Tab'
    import FormButtons from './../../FormResources/Buttons'
    import ItemListFormMenu from './ItemList'
    import cloneMixin from './../../../mixins/clone'

    export default {
        name: 'IndexFormMenu',
        props: {
            data: {
                type: String,
                required: true
            },
            menuOrigin: {
                type: Object,
                required: true
            }
        },
        components: { MultilanguageTab, FormButtons, ItemListFormMenu },
        mixins: [ cloneMixin ],
        data () {
            return {
                menu: this.clone(this.menuOrigin),
                languagesAvailable: JSON.parse(this.data).langsAvailable
            }
        },
        computed: {
            ...mapState([ 'locale', 'routes' ]),
            ...mapState({
                isVisibleBlockui: state => state.blockui.isVisible,
                messageBlockui: state => state.blockui.message
            }),
            hasItems () {
                return this.menu.items.length > 0
            }
        },
        watch: {
            menuOrigin () {
                this.menu = this.clone(this.menuOrigin)
            }
        },
        methods: {
            ...mapActions({
                toogleBlockui : 'blockui/toggleIsVisible'
            }),
            // Events
            async onSave () {
                this.toogleBlockui(true)

                if ( await this.$validator.validate() ) {
                    await this.processForm()
                    this.successForm()
                } else {
                    this.toogleBlockui(false)
                    this.$notify({
                        group: 'notify',
                        title: this.$t('Save menu', { locale: this.locale }),
                        text: this.$t('Menu form is not valid', { locale: this.locale }),
                        type: 'error',
                        config: {
                            closeOnClick: true
                        }
                    })
                }
            },
            async onDestroy () {
                await this.destroyMenuRequest(this.menu)
                this.$notify({
                    group: 'notify',
                    title: this.$t('Delete menu', { locale: this.locale }),
                    text: this.$t( 'Menu has been deleted' , { locale: this.locale }),
                    type: 'success',
                    config: {
                        closeOnClick: true
                    }
                })
                this.$emit('onDestroyMenuSuccess', this.menu)
            },
            // Actions
            async processForm () {
                let items = []

                for ( const language of this.languagesAvailable ) {
                    items = items.concat(this.clone(this.$refs[`itemsList${language.iso}`].items))
                }

                this.menu.items = items
                const { menu } = await this.storeMenuRequest(this.menu)
                this.menu = menu
            },
            successForm () {
                this.toogleBlockui(false)
                this.$notify({
                    group: 'notify',
                    title: this.$t('Save menu', { locale: this.locale }),
                    text: this.$t( this.menuOrigin.id === null ? 'Menu has been created' : 'Menu has been updated', { locale: this.locale }),
                    type: 'success',
                    config: {
                        closeOnClick: true
                    }
                })
                this.$emit('onSaveMenuSuccess', this.menu)
            },
            // API Request
            async storeMenuRequest ( menu ) {
                const { data } = await this.axios.post(this.routes.storeMenu, menu)
                return data
            },
            async destroyMenuRequest ( menu ) {
                await this.axios.delete(`${this.routes.getMenus}/${menu.id}/destroy`, {})
            }
        }
    }
</script>
