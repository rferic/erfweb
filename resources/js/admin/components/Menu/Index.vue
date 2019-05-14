<template>
    <b-row>
        <b-col v-if="hasMenus" lg="3" sm="12">
            <b-card>
                <h3>{{ $t('Menus list', { locale }) }}</h3>
                <list-menu
                    ref="list"
                    :menus-origin="menus"
                    :current-menu="currentMenu"
                    @onCreateMenu="onCreateMenu"
                    @onEditMenu="onEditMenu"
                />
            </b-card>
        </b-col>
        <b-col v-if="hasMenus" lg="9" sm="12">
            <b-card>
                <h3>{{ $t('Menu form', { locale }) }}</h3>
                <index-form-menu
                    :data="data"
                    :menu-origin="currentMenu"
                    @onSaveMenuSuccess="onSaveMenuSuccess"
                    @onDestroyMenuSuccess="onDestroyMenuSuccess"
                />
            </b-card>
        </b-col>
        <b-col v-else lg="12">
            <b-card>
                <b-alert :show="!hasMenus" variant="warning">
                    {{ $t('Menus not found', { locale }) }}
                </b-alert>
            </b-card>
        </b-col>
    </b-row>
</template>

<script>
    import { mapState } from 'vuex'
    import ListMenu from './List'
    import IndexFormMenu from './Form/Index'
    import cloneMixin from './../../../includes/mixins/clone'
    import { menuStructure } from './../../structures/menu'
    import BCard from "bootstrap-vue/src/components/card/card";

    export default {
        name: 'IndexMenu',
        props: {
            data: {
                type: String,
                required: true
            }
        },
        components: {BCard, ListMenu, IndexFormMenu },
        mixins: [ cloneMixin ],
        data () {
            return {
                languagesAvailable: JSON.parse(this.data).langsAvailable,
                menus: JSON.parse(this.data).menus,
                currentMenu: this.clone(menuStructure)
            }
        },
        computed: {
            ...mapState([ 'locale' ]),
            hasMenus () {
                return this.menus.length > 0
            }
        },
        methods: {
            // Events
            onCreateMenu () {
                this.currentMenu = this.clone(menuStructure)
            },
            onEditMenu ( menu ) {
                this.currentMenu = this.clone(menu)
            },
            onSaveMenuSuccess ( menuStored ) {
                let find = false

                for ( let [index, menu] of Object.entries(this.menus) ) {
                    if ( menuStored.id === menu.id ) {
                        find = true
                        this.menus[index] = this.clone(menuStored)
                    } else if ( menuStored.is_default ) {
                        this.menus[index].is_default = false
                    }
                }

                if ( !find ) {
                    this.menus.push(this.clone(menuStored))
                    this.currentMenu = this.clone(menuStored)
                }

                this.$refs.list.setMenuOrigin()
            },
            onDestroyMenuSuccess ( menuDeleted ) {
                for ( const [index, menu] of this.menus.entries() ) {
                    if ( menu.id === menuDeleted.id ) {
                        this.menus.splice(index, 1)
                    }
                }
            }
        },
        mounted () {
            if ( this.hasMenus ) {
                this.currentMenu = this.menus[0]
            }
        }
    }
</script>
