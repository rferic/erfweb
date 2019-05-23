<template>
    <div>
        <v-btn
            v-for="item in items"
            :key="item.id"
            class="item-menu hidden-sm-and-down"
            :class="{'item-menu-current v-btn--active': getIsCurrent(item)}"
            :inactive="getIsCurrent(item)"
            flat
            dark
            tag="a"
            :href="getItemUrl(item)"
            :target="item.type === 'internal' ? '_self' : '_blank'"
        >
            {{ item.label }}
        </v-btn>
        <language-menu />
        <account-menu :auth-json="authJson" />
    </div>
</template>

<script>
    import LanguageMenu from './Language'
    import AccountMenu from './Account'
    import menuMixin from './../../mixins/menu'

    export default {
        name: 'ToolbarMenu',
        props: {
            authJson: {
                type: String,
                required: false,
                default: null
            }
        },
        components: { LanguageMenu, AccountMenu },
        mixins: [ menuMixin ]
    }
</script>
