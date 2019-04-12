import { mapState } from 'vuex'

const pageMixin = {
    data () {
        return {
            pages: [],
            totalPages: 0,
            urlNextPage: null,
            orderBy: {
                'way': 'ASC',
                'attribute': 'created_at'
            }
        }
    },
    computed: {
        ...mapState([ 'routes' ]),
        hasPages () {
            return this.pages.length > 0
        },
        hasNextPage () {
            return this.urlNextPage !== null
        }
    },
    methods: {
        // Actions
        async loadPage ({ page, perPage, url }) {
            this.filters.receivers = [ 'admin' ]

            const data = await this.getPages({
                stack: this.stackPages,
                page,
                perPage,
                url,
                filters: this.getFilterParse(),
                orderBy: this.orderBy
            })
            this.setPagesCheckAttrAndDefaultData(false)
            this.currentPage = data.current_page
            this.totalPages = data.total
            this.setPerPage(perPage)
        },
        // Getters
        async getPages ({ stack, page, perPage, url, filters, orderBy }) {
            const data = await this.getPagesRequest({ page, perPage, url, filters, orderBy })

            if ( stack ) {
                for ( const page of data.data ) {
                    this.pages.push(page)
                }
            } else {
                this.pages = data.data
            }

            this.totalPages = data.total
            this.urlNextPage = data.next_page_url
            return data
        },
        getFilterParse () {
            let filters = {
                text: this.filters.text,
                enables: true,
                disables: true
            }

            if ( this.filters.langs.some(lang => lang.checked) ) {
                filters.langs = []

                for ( const lang of this.filters.langs ) {
                    if ( lang.checked ) {
                        filters.langs.push(lang.key)
                    }
                }
            }

            if ( this.filters.menus.some(menu => menu.checked) ) {
                filters.menus = []

                for ( const menu of this.filters.menus ) {
                    if ( menu.checked ) {
                        filters.menus.push(menu.key)
                    }
                }
            }

            if ( this.filters.status.some(status => status.checked) ) {
                for ( const status of this.filters.status ) {
                    if ( status.key === 'enable' ) {
                        filters.enables = status.checked
                    } else {
                        filters.disables = status.checked
                    }
                }
            }

            return filters
        },
        // API Request
        async getPagesRequest ({ page, perPage, url, filters, orderBy }) {
            if ( typeof page === typeof page ) {
                page = null
            }

            if ( typeof perPage === typeof undefined ) {
                perPage = null
            }

            if ( typeof url === typeof undefined ) {
                url = page !== null ? `${this.routes.getPages}?page=${page}` : this.routes.getPages
            }

            const { data } = await axios.post(url, {
                perPage,
                filters,
                orderBy
            })
            return data
        },
        async storePageRequest ( page ) {
            const { data } = await axios.post(`${this.routes.storePage}`, page)
            return data
        },
        async removePageRequest ( page ) {
            const { data } = await axios.delete(`${this.routes.getPages}/${page.id}/remove`, {})
            return data
        },
        async restorePageRequest ( page ) {
            const { data } = await axios.post(`${this.routes.getPages}/${page.id}/restore`, {})
            return data
        },
        async destroyPageRequest ( page ) {
            const { data } = await axios.delete(`${this.routes.getPages}/${page.id}/destroy`, {})
            return data
        }
    }
}

export default pageMixin
