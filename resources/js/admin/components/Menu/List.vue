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
            {{ menu.name }}
        </b-list-group-item>
    </b-list-group>
</template>

<script>
    import { mapState } from 'vuex'

    export default {
        name: 'ListMenu',
        props: {
            menus: {
                type: Array,
                required: true
            },
            currentMenu: {
                type: Object,
                required: true
            }
        },
        computed: {
            ...mapState([ 'locale' ])
        }
    }
</script>

<style scoped>
    .menu-item {
        cursor: pointer;
    }
</style>
