import { mapState } from 'vuex'

const appMixin = {
    data () {
        return {
            apps: [],
            totalApps: 0,
            urlNextPage: null,
            orderBy: {
                'way': 'ASC',
                'attribute': 'created_at'
            }
        }
    },
    computed: {
        ...mapState([ 'routes' ]),
        hasApps () {
            return this.apps.length > 0
        },
        hasNextPage () {
            return this.urlNextPage !== null
        }
    },
    methods: {
        // Actions
        async loadPage ({ page, perPage, url }) {
            this.filters.receivers = [ 'admin' ]

            const data = await this.getApps({
                stack: this.stackPages,
                page,
                perPage,
                url,
                filters: this.getFilterParse(),
                orderBy: this.orderBy
            })
            this.setAppsCheckAttrAndDefaultData(false)
            this.currentPage = data.current_page
            this.totalApps = data.total
            this.setPerPage(perPage)
        },
        // Getters
        async getApps ({ stack, page, perPage, url, filters, orderBy }) {
            const data = await this.getAppsRequest({ page, perPage, url, filters, orderBy })

            if ( stack ) {
                for ( const app of data.data ) {
                    this.apps.push(app)
                }
            } else {
                this.apps = data.data
            }

            this.totalApps = data.total
            this.urlNextPage = data.next_page_url
            return data
        },
        getFilterParse () {
            let filters = {
                text: this.filters.text,
                types: this.filters.types.filter(type => type.checked),
                status: this.filters.status.filter(status => status.checked)
            }

            return filters
        },
        // API Request
        async getAppsRequest ({ page, perPage, url, filters, orderBy }) {
            if ( typeof page === typeof page ) {
                page = null
            }

            if ( typeof perPage === typeof undefined ) {
                perPage = null
            }

            if ( typeof url === typeof undefined ) {
                url = page !== null ? `${this.routes.getApps}?page=${page}` : this.routes.getApps
            }

            const { data } = await this.axios.post(url, {
                perPage,
                filters,
                orderBy
            })
            return data
        },
        async storeAppRequest ( app ) {
            const { data } = await this.axios.post(`${this.routes.storeApp}`, app)
            return data
        },
        async destroyAppRequest ( app ) {
            const { data } = await this.axios.delete(`${this.routes.getApps}/${app.id}/destroy`, {})
            return data
        }
    }
}

export default appMixin
