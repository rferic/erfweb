<template>
    <div>
        <li class="nav-item" :class="{'nav-item-current': isCurrent || childrenIsCurrent}">
            <a
                :href="link.path"
                :target="link.target"
                :to="link.path"
                @click="linkClick"
                class="nav-link">
                <template>
                    <i v-if="link.icon !== null" :class="link.icon"></i>
                    <span class="nav-link-text">{{ link.name }}</span>
                    <i v-if="hasChildrens" class="ml-2 ni" :class="{'ni-bold-down': collapsed, 'ni-bold-up': !collapsed}" />
                </template>
            </a>
        </li>
        <collapse-transition>
            <div v-show="!collapsed" class="nav-childrens">
                <sidebar-item
                    v-for="children in link.childrens"
                    :show="!collapsed"
                    :key="children.key"
                    :link="children"
                    :sidebar-background="sidebarBackground"
                />
            </div>
        </collapse-transition>
    </div>
</template>

<script>
    import { mapState } from 'vuex'
    import { CollapseTransition } from 'vue2-transitions'

    export default {
        name: 'sidebar-item',
        props: {
            link: {
                type: Object,
                default: () => {
                    return {
                        name: '',
                        path: '',
                        childrens: []
                    };
                },
                description: 'Sidebar link. Can contain name, path, icon and other attributes. See examples for more info'
            },
            sidebarBackground: {
                type: String,
                required: false,
                default: 'vue'
            },
            defaultCollapsed: {
                type: Boolean,
                required: false,
                default: true
            }
        },
        components: { CollapseTransition },
        data () {
            return {
                childrens: this.link.childrens,
                collapsed: this.defaultCollapsed
            };
        },
        computed: {
            ...mapState( ['routesGlobal'] ),
            hasChildrens () {
                return typeof this.link.childrens !== typeof undefined && this.link.childrens.length > 0
            },
            isCurrent () {
                return this.link.path !== null && this.link.path === `${window.location.origin}${window.location.pathname}`
            },
            childrenIsCurrent () {
                return this.hasChildrens && this.link.childrens.some(
                        children => children.path !== null && children.path === `${window.location.origin}${window.location.pathname}`
                )
            }
        },
        methods: {
            linkClick () {
                this.collapsed = !this.collapsed
            }
        },
        mounted () {
            this.collapsed = !(!this.defaultCollapsed || this.isCurrent || this.childrenIsCurrent)
        }
    };
</script>
