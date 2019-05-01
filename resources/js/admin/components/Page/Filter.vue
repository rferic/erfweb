<template>
    <transition name="bounceRight">
        <div>
            <b-nav vertical>
                <div class="dropdown-divider" />
                <input-text-filter @onChangeFilter="onChangeTextFilter" class="mb-2" />
                <div class="dropdown-divider" />
                <checkboxes-filter
                    v-if="hasLangFilter"
                    :title="$t('Languages', { locale })"
                    :options="filters.langs"
                    :translate-label="true"
                    @onChangeFilter="onChangeLangsFilter"
                    class="mb-2"
                />
                <div class="dropdown-divider" />
                <checkboxes-filter
                    v-if="hasMenuFilter"
                    :title="$t('Menus', { locale })"
                    :options="filters.menus"
                    :translate-label="true"
                    @onChangeFilter="onChangeMenusFilter"
                    class="mb-2"
                />
                <div class="dropdown-divider" />
                <checkboxes-filter
                    :title="$t('Status', { locale })"
                    :options="filters.status"
                    :translate-label="true"
                    @onChangeFilter="onChangeStatusFilter"
                    class="mb-2"
                />
            </b-nav>
        </div>
    </transition>
</template>

<script>
    import { mapState } from 'vuex'
    import filterPageStructure from './../../structures/filterPage'
    import InputTextFilter from './../Filters/InputText'
    import CheckboxesFilter from './../Filters/Checkboxes'
    import cloneMixin from './../../mixins/clone'
    import menuMixin from './../../mixins/menu'

    export default {
        name: 'FilterPage',
        props: {
            data: {
                type: String,
                required: true
            }
        },
        components: { InputTextFilter, CheckboxesFilter },
        mixins: [ cloneMixin, menuMixin ],
        data () {
            return {
                filters: this.clone(filterPageStructure),
                menus: []
            }
        },
        computed: {
            ...mapState([ 'locale', 'routes' ]),
            hasMenuFilter () {
                return this.filters.menus.length > 0
            },
            hasLangFilter () {
                return this.filters.langs.length > 0
            }
        },
        methods: {
            onChangeTextFilter (filter) {
                this.filters.text = filter
                this.$emit('onChangeFilters', this.filters)
            },
            onChangeMenusFilter (filter) {
                this.filters.menus.some(menuFilter => {
                    const scapeCondition = menuFilter.key === filter.key

                    if ( scapeCondition ) {
                        menuFilter.checked = filter.checked
                    }

                    return scapeCondition
                })

                this.$emit('onChangeFilters', this.filters)
            },
            onChangeLangsFilter (filter) {
                this.filters.langs.some(langFilter => {
                    const scapeCondition = langFilter.key === filter.key

                    if ( scapeCondition ) {
                        langFilter.checked = filter.checked
                    }

                    return scapeCondition
                })

                this.$emit('onChangeFilters', this.filters)
            },
            onChangeStatusFilter (filter) {
                this.filters.status.some(item => {
                    const scapeCondition = item.key === filter.key

                    if ( scapeCondition ) {
                        item.checked = filter.checked
                    }

                    return scapeCondition
                })

                this.$emit('onChangeFilters', this.filters)
            },
            async setMenusFilter () {
                this.menus = await this.getMenuRequest({
                    url: this.routes.getMenus
                })

                for ( const menu of this.menus ) {
                    this.filters.menus.push({
                        key: menu.id,
                        label: menu.name,
                        class: 'default',
                        checked: false
                    })
                }
            },
            setLangsFilter () {
                const langsAvailable = JSON.parse(this.data).langsAvailable

                for ( const lang of langsAvailable ) {
                    this.filters.langs.push({
                        key: lang.code,
                        label: lang.name,
                        class: 'default',
                        checked: false
                    })
                }
            }
        },
        created () {
            this.setMenusFilter()
            this.setLangsFilter()
        }
    }
</script>
