<template>
    <v-list dense class="pa-0 hidden-md-and-up">
        <h2 class="text-xs-center white--text grey darken-4 pt-2 pb-2">< <strong>ERF</strong>Web /></h2>
        <v-list-tile
            v-for="item in items"
            :key="item.id"
            class="item-menu"
            :class="{'item-menu-current': getIsCurrent(item)}"
            :inactive="getIsCurrent(item)"
            tag="a"
            :href="getItemUrl(item)"
            :target="item.type === 'internal' ? '_self' : '_blank'"
        >
            <v-list-tile-action>
                <v-icon color="grey darken-4">{{ `mdi-${item.icon}` }}</v-icon>
            </v-list-tile-action>
            <v-list-tile-content>
                <v-list-tile-title class="item-menu-text grey-darken-4--text">{{ item.label }}</v-list-tile-title>
            </v-list-tile-content>
        </v-list-tile>
    </v-list>
</template>

<script>
    import menuMixin from './../../mixins/menu'

    export default {
        name: 'MobileMenu',
        mixins: [ menuMixin ]
    }
</script>
