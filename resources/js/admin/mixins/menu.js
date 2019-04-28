const menuMixin = {
    methods: {
        async getMenuRequest ({ url, params }) {
            params = typeof params !== typeof undefined ? params : {}
            const { data } = await this.axios.post(this.routes.getMenus, params)
            return data
        }
    }
}

export default menuMixin
