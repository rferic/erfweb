<template>
    <div>
        <notifications
            group="notify"
            position="top right"
        />
        <sweet-modal ref="confirmDestroy">
            <h2>{{ $t('Confirm destroy user', { locale }) }}</h2>
            <p>{{ $t('Are you sure to destroy the user? You will not be able to restore it.', { locale }) }}</p>
            <b-button
                size="sm"
                class="mt-4"
                variant="danger"
                @click="onDestroy"
            >
                {{ $t('Confirm destroy user', { locale }) }}
            </b-button>
        </sweet-modal>
        <b-card>
            <b-row class="mb-2">
                <b-col lg="3" sm="12">
                    <input-text-filter
                        v-model="filters.text"
                        :placeholder="$t('Search user...', { locale })"
                        v-debounce:300ms="onChangeTextFilter"
                    />
                </b-col>
                <b-col v-if="filters.role === 'public'" lg="4" sm="12">
                    <label class="custom-toggle">
                        <input
                            type="checkbox"
                            v-model="filters.banned"
                            @change="refresh"
                        />
                        <span class="custom-toggle-slider rounded-circle"></span>
                    </label>
                    <span class="align-top ml-1">{{ $t('Includes banned', { locale}) }}</span>
                </b-col>
            </b-row>
            <b-row>
                <b-col cols="12">
                    <b-table
                        v-if="hasUsers"
                        id="users"
                        ref="table"
                        responsive
                        :fields="columns"
                        :items="users"
                        :busy="isBusy"
                        table-class="table align-items-center table-flush light"
                        thead-class="thead-light"
                        tbody-classes="list"
                    >
                        <div
                            slot="table-busy"
                            class="text-center text-primary my-2"
                        >
                            <b-spinner class="align-middle"></b-spinner>
                            <strong>{{ $t('Loading', { locale }) }}...</strong>
                        </div>
                        <template slot="avatar" slot-scope="data">
                            <a
                                href="#"
                                class="avatar avatar-sm rounded-circle"
                                data-toggle="tooltip"
                                :data-original-title="data.item.name"
                            >
                                <img alt="Image placeholder" :src="`/${data.item.avatar}`">
                            </a>
                        </template>
                        <template slot="status" slot-scope="data">
                            <badge v-if="data.item.deleted_at === null" class="badge-dot mr-4" type="success">
                                <i class="bg-success"></i>
                                <span class="status">{{ $t('enable', { locale }) }}</span>
                            </badge>
                            <badge v-else class="badge-dot mr-4" type="danger">
                                <i class="bg-danger"></i>
                                <span class="status">{{ $t('banned', { locale }) }}</span>
                            </badge>
                        </template>
                        <template slot="actions" slot-scope="data">
                            <a :href="getUrlToEditUser(data.item)" target="_blank" class="mr-2">
                                <i class="fa fa-pencil text-primary" />
                            </a>
                            <a v-if="data.item.deleted_at === null && filters.role === 'public'" href="#" @click="onDisable(data.item)" class="mr-2">
                                <i class="fa fa-ban text-danger" />
                            </a>
                            <a v-else-if="filters.role === 'public'" href="#" @click="onEnable(data.item)" class="mr-2">
                                <i class="fa fa-undo text-success" />
                            </a>
                            <a href="#" @click.prevent="onOpenConfirmDestroy(data.item)">
                                <i class="fa fa-trash text-danger" />
                            </a>
                        </template>
                    </b-table>

                    <div
                        v-if="hasUsers"
                        class="text-right"
                    >
                        <em>{{ users.length }} / {{ totalUsers }}</em>
                    </div>
                    <div id="viewMore" v-if="hasUsers">
                        <b-button
                            v-if="hasNextPage"
                            block
                            variant="primary"
                            @click="loadNextPage"
                        >
                            {{ $t('View more', { locale: this.locale }) }}
                        </b-button>
                    </div>
                    <b-alert :show="!hasUsers" variant="warning">
                        <i class="fa fa-warning" />
                        {{ $t('Users not found', { locale }) }}
                    </b-alert>
                </b-col>
            </b-row>
        </b-card>
    </div>
</template>

<script>
    import { mapState } from 'vuex'
    import InputTextFilter from './../Filters/InputText'
    import SelectFilter from './../Filters/Select'
    import cloneMixin from './../../mixins/clone'
    import paginatorMixin from './../../mixins/paginator'
    import userMixin from './../../mixins/user'

    export default {
        name: 'IndexUser',
        props: {
            data: {
                type: String,
                required: true
            }
        },
        components: { InputTextFilter, SelectFilter },
        mixins: [ cloneMixin, paginatorMixin, userMixin ],
        data () {
            return {
                isLoaded: false,
                isBusy: false,
                stack: true,
                roles: JSON.parse(this.data).roles,
                role: JSON.parse(this.data).role,
                filters: {
                    banned: false,
                    text: '',
                    role: JSON.parse(this.data).role
                },
                users: [],
                totalUsers: 0,
                columns: [
                    {
                        key: 'avatar',
                        label: ''
                    },
                    {
                        key: 'id',
                        label: 'ID'
                    },
                    {
                        key: 'name',
                        label: this.$t('Name', this.locale)
                    },
                    {
                        key: 'email',
                        label: this.$t('Email', this.locale)
                    },
                    {
                        key: 'status',
                        label: this.$t('Is banned', this.locale)
                    },
                    {
                        key: 'actions',
                        label: ''
                    }
                ],
                userToDestroy: null
            }
        },
        computed: {
            ...mapState([ 'locale', 'routes' ]),
            hasUsers () {
                return this.users.length > 0
            },
            hasNextPage () {
                return this.urlNextPage !== null
            }
        },
        methods: {
            // Events
            onChangeTextFilter ( text ) {
                this.filters.text = text
                this.refresh()
            },
            async onDisable ( user ) {
                await this.disableUserRequest(user)

                for ( let item of this.users ) {
                    if ( item.id === user.id ) {
                        item.deleted_at = Vue.moment().locale(this.locale).format('YYYY-MM-DD HH:mm:ss')
                    }
                }

                this.$notify({
                    group: 'notify',
                    title: this.$t('Ban user'),
                    text: this.$t('User has been banned', { locale: this.locale }),
                    type: 'success',
                    config: {
                        closeOnClick: true
                    }
                })
            },
            async onEnable ( user ) {
                await this.enableUserRequest(user)

                for ( let item of this.users ) {
                    if ( item.id === user.id ) {
                        item.deleted_at = null
                    }
                }

                this.$notify({
                    group: 'notify',
                    title: this.$t('Unban user'),
                    text: this.$t('User has been unbanned', { locale: this.locale }),
                    type: 'success',
                    config: {
                        closeOnClick: true
                    }
                })
            },
            onOpenConfirmDestroy ( user ) {
                this.userToDestroy = user
                this.$refs.confirmDestroy.open()
            },
            async onDestroy () {
                await this.destroyUserRequest(this.userToDestroy)
                this.userToDestroy = null
                this.$refs.confirmDestroy.close()
                this.refreshList()

                this.$notify({
                    group: 'notify',
                    title: this.$t('Destroy user'),
                    text: this.$t('User has been destroyed', { locale: this.locale }),
                    type: 'success',
                    config: {
                        closeOnClick: true
                    }
                })
            },
            // Actions
            async refreshList () {
                const currentPage = this.currentPage
                this.users = []
                this.isBusy = true

                for ( let page = 1; page <= currentPage; page++ ) {
                    await this.loadPage({
                        page: page,
                        perPage: this.perPage,

                    })
                }

                this.isBusy = false
            },
            async refresh () {
                this.users = []
                this.isBusy = true

                await this.loadPage({
                    page: this.page,
                    perPage: this.perPage
                })

                this.isBusy = false
            },
            async loadPage ({ page, perPage, url }) {
                const { current_page, total, next_page_url } = await this.getUsers({
                    page,
                    perPage,
                    url,
                    filters: this.filters
                })

                this.currentPage = current_page
                this.totalUsers = total
                this.urlNextPage = next_page_url
                this.setPerPage(perPage)
            },
            async loadNextPage () {
                this.isBusy = true

                await this.loadPage({
                    page: this.currentPage,
                    perPage: this.perPage,
                    url: this.urlNextPage
                })

                this.isBusy = false
                this.$scrollTo(`#viewMore`, 1000, {
                    easing: 'ease-in',
                    offset: 1000
                })
            },
            // Getters
            async getUsers ({ page, perPage, url, filters }) {
                const data = await this.getUsersRequest({ page, perPage, url, filters })

                if ( this.stack ) {
                    for ( const user of data.data ) {
                        this.users.push(user)
                    }
                } else {
                    this.users = data.data
                }

                this.totalUsers = data.total
                this.urlNextPage = data.next_page_url
                return data
            },
            getUrlToEditUser ( user ) {
                return `${this.routes.basePath}/${user.id}`
            }
        },
        mounted () {
            this.refresh()
        }
    }
</script>
