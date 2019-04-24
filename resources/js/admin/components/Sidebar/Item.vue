<template>
    <li>
        <a
            :class="{'has-arrow' : hasChildren, 'active': isCurrent }"
            :aria-expanded="ariaExpanded"
            :href="url"
            @click.prevent="onClick">
            <i
                v-if="hasIcon"
                class="fa"
                :class="item.icon"
            />
            <span :class="{'hide-menu': !hasURL }">{{ item.label }}</span>
        </a>
        <ul
            v-if="hasChildren"
            aria-expanded="false"
            class="collapse"
            :class="{ 'show': isOpen }"
        >
            <sidebar-item
                v-for="(children, key) in item.childrens"
                :key="key"
                :item="children"
            />
        </ul>
    </li>
</template>

<script>
    import { mapState } from 'vuex'
    export default {
        name: 'SidebarItem',
        props: {
            item: {
                type: Object,
                required: true
            }
        },
        data () {
            return {
                isOpen: false
            }
        },
        computed: {
            ...mapState([ 'routesGlobal' ]),
            hasIcon () {
                return this.item.icon !== null
            },
            hasURL () {
                return this.item.url !== null
            },
            hasChildren () {
                return typeof this.item.childrens !== typeof undefined && this.item.childrens.length > 0
            },
            isCurrent () {
                return this.getIsCurrent(this.item)
            },
            url () {
                return this.item.url !== null ? this.item.url : '#'
            },
            ariaExpanded () {
                return this.isOpen ? "true" : "false"
            }
        },
        methods: {
            onClick () {
                this.isOpen = !this.isOpen

                if ( this.item.url !== null ) {
                    window.location.replace(this.item.url)
                }
            },
            getIsCurrent ( item ) {
                let findChildrenCurrent = item.url === routesGlobal.current || routesGlobal.current.includes(item.url)

                if ( !findChildrenCurrent && typeof item.childrens !== typeof undefined && item.childrens.length > 0 ) {
                    for ( let children of item.childrens ) {
                        if ( this.getIsCurrent(children) ) {
                            findChildrenCurrent = true
                        }
                    }
                }

                return findChildrenCurrent
            }
        },
        mounted () {
            this.isOpen = this.isCurrent
        }
    }
</script>
