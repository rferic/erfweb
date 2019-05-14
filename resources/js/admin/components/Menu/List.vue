<template>
    <b-list-group>
        <b-list-group-item
            variant="success"
            :active="currentMenu.id === null"
            @click="$emit('onCreateMenu')"
            class="menu-item"
        >
            <i class="fa fa-plus" />
            {{ $t('Create menu', { locale }) }}
        </b-list-group-item>
        <b-list-group-item
            v-for="(menu, index) in menus"
            :key="index"
            :active="menu.id === currentMenu.id"
            @click="$emit('onEditMenu', menu)"
            class="menu-item"
        >
            <i v-if="menu.is_default" class="fa fa-star" />
            {{ menu.name }}
        </b-list-group-item>
    </b-list-group>
</template>

<script>
    import { mapState } from 'vuex'
    import cloneMixin from './../../../includes/mixins/clone'

    export default {
        name: 'ListMenu',
        props: {
            menusOrigin: {
                type: Array,
                required: true
            },
            currentMenu: {
                type: Object,
                required: true
            }
        },
        mixins: [ cloneMixin ],
        computed: {
            ...mapState([ 'locale' ])
        },
        data () {
            return {
                menus: this.clone(this.menusOrigin)
            }
        },
        methods: {
            setMenuOrigin () {
                this.menus = this.clone(this.menusOrigin)
            }
        }
    }
</script>

<style scoped>
    .menu-item {
        cursor: pointer;
    }
</style>
