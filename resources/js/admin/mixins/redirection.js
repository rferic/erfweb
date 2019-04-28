import { mapState } from "vuex";

const redirectionMixin = {
    data () {
        return {
            redirections: [],
            totalRedirections: 0,
            urlNextPage: null,
            orderBy: {
                'way': 'ASC',
                'attribute': 'created_at'
            },
            codes: [
                {
                    value: 301,
                    text: '301: Moved Permanently'
                },
                {
                    value: 302,
                    text: '302: Moved Temporarily'
                },
                {
                    value: 403,
                    text: '403: Forbidden'
                },
                {
                    value: 404,
                    text: '404: Not Found'
                }
            ]
        }
    },
    computed: {
        ...mapState([ 'routes' ]),
        hasRedirections () {
            return this.redirections.length > 0
        },
        hasNextPage () {
            return this.urlNextPage !== null
        }
    },
    methods: {
        // Actions
        async removeRedirectionToPage ( page ) {
            let filters = {
                slugs_origin: []
            }

            for ( const pageLocale of page.locales ) {
                filters.slugs_origin.push(pageLocale.slug)
            }

            let redirections = await this.getRedirectionsRequest({ filters })

            for ( const redirection of redirections ) {
                await this.destroyRedirectionRequest(redirection)
            }
        },
        // Getters
        async getRedirections ({ stack, page, perPage, url, filters, orderBy }) {
            const data = await this.getRedirectionsRequest({ page, perPage, url, filters, orderBy })

            if ( stack ) {
                for ( const redirection of data.data ) {
                    this.redirections.push(redirection)
                }
            } else {
                this.redirections = data.data
            }

            this.totalRedirections = data.total
            this.urlNextPage = data.next_page_url
            return data
        },
        // API Request
        async getRedirectionsRequest ({ page, perPage, url, filters, orderBy }) {
            if ( typeof page === typeof page ) {
                page = null
            }

            if ( typeof perPage === typeof undefined ) {
                perPage = null
            }

            if ( typeof url === typeof undefined ) {
                url = page !== null ? `${this.routes.getRedirections}?page=${page}` : this.routes.getRedirections
            }

            const { data } = await this.axios.post(url, {
                perPage,
                filters,
                orderBy
            })
            return data
        },
        async createRedirectionRequest ({ code, slug_origin, slug_destine }) {
            const { data } = await this.axios.post(this.routes.createRedirection, { code, slug_origin, slug_destine })
            return data
        },
        async destroyRedirectionRequest ( redirection ) {
            const { data } = await this.axios.delete(`${this.routes.indexRedirections}/${redirection.id}/destroy`, {})
            return data
        }
    }
}

export default redirectionMixin
