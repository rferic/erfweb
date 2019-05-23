const menuMixin = {
    props: {
        items: {
            type: Array,
            required: true
        }
    },
    methods: {
        getItemUrl ( item ) {
            return item.type === 'internal' ? item.page_locale.url : item.url_external
        },
        getIsCurrent ( item ) {
            if ( item.type === 'internal' ) {
                return window.location.href.includes(item.page_locale.url)
            }

            return false
        }
    }
}

export default menuMixin
