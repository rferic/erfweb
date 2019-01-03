import { mapState, mapActions } from 'vuex'

const paginatorMixin = {
    data () {
        return {
            currentPage: 1,
            totalPages: 1,
            optionsPerPage: [ 10, 20, 50, 100, 200 ]
        }
    },
    computed: {
        ...mapState({
            perPage: state => state.paginator.perPage
        }),
    },
    methods: {
        ...mapActions({
            setPerPage : 'paginator/setPerPage'
        })
    }
}

export default paginatorMixin
