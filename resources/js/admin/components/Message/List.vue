<template>
    <v-wait for="loader">
        <template slot="waiting">
            <div>
                {{ $t('Loading...', locale) }}
            </div>
        </template>
        <div class="dropdown-divider"></div>
        <b-alert
            :show="!hasMessages"
            variant="warning"
        >
            {{ $t('Messages not found', { locale: this.locale }) }}
        </b-alert>
        <sweet-modal ref="confirmDestroy">
            <h2>{{ $t('Confirm destroy message', { locale }) }}</h2>
            {{ $t('Are you sure to destroy the message? You will not be able to restore it.', { locale }) }}
            <b-button
                size="sm"
                class="mt-4"
                variant="danger"
                @click="onDestroy"
            >
                {{ $t('Confirm destroy message', { locale }) }}
            </b-button>
        </sweet-modal>
        <div v-if="hasMessages">
            <div class="mb-2">
                <div class="text-right pull-left">
                    <b-button
                        size="sm"
                        variant="danger"
                        @click="$emit('removeAllSelected')"
                        :disabled="!hasMessagesSelected"
                    >
                        <i class="fa fa-trash" /> {{ removeAllSelectedText }}
                    </b-button>
                </div>
                <div class="text-right pull-right">
                    <b-form-select
                        size="sm"
                        v-model="selectPerPage"
                        :options="optionsPerPage"
                        @input="onChangePerPage"
                    />
                </div>
                <div class="clearfix" />
            </div>
            <b-table
                id="messages"
                responsive
                small
                hover
                striped
                :fields="columns"
                :items="messages"
            >
                <template
                    slot="check"
                    slot-scope="data"
                >
                    <b-form-checkbox
                        v-model="data.item.checked"
                        @input="onToggleCheck(data.item)"
                    />
                </template>
                <template
                    slot="status"
                    slot-scope="data"
                >
                    <i
                        class="fa fa-eye"
                        :class="`text-${getDataStatusSelected(data.item.status).class}`"
                    />
                </template>
                <template
                    slot="author"
                    slot-scope="data"
                >
                    {{ data.item.author.name }}
                </template>
                <template
                    slot="tag"
                    slot-scope="data"
                >
                    <b-badge :variant="getDataTagSelected(data.item.tag).class">
                        <i
                            class="fa"
                            :class="getDataTagSelected(data.item.tag).icon"
                        />
                        {{ $t(data.item.tag, { locale }) }}
                    </b-badge>
                </template>
                <template
                    slot="created_at"
                    slot-scope="data"
                >
                    {{ data.item.created_at | momentTime }}
                </template>
                <template
                    slot="actions"
                    slot-scope="data"
                >
                    <a
                        href="#"
                        class="mr-2"
                        @click.prevent="$emit('onShow', data.item)"
                    >
                        <i class="fa fa-eye text-secondary" />
                    </a>
                    <a
                        href="#"
                        @click.prevent="onRemove(data.item)"
                    >
                        <i class="fa fa-trash text-danger" />
                    </a>
                    <a
                        v-if="data.item.deleted_at !== null"
                        href="#"
                        @click.prevent="onRestore(data.item)"
                    >
                        <i class="fa fa-undo text-success" />
                    </a>
                </template>
            </b-table>
        </div>
        <div class="text-right">
            <em>{{ messages.length }} / {{ totalMessages }}</em>
        </div>
        <b-button
            v-if="hasNextPage"
            block
            variant="primary"
            @click="loadNextPage"
        >
            {{ $t('View more', { locale: this.locale }) }}
        </b-button>
    </v-wait>
</template>

<script>
    import { mapState } from 'vuex'
    import paginatorMixin from './../../mixins/paginator'
    import messagesMixin from './../../mixins/messages'

    export default {
        name: 'ListMessage',
        props: {
            data: {
                type: String,
                required: true
            },
            filters: {
                type: Object,
                required: false,
                default: {}
            }
        },
        mixins: [ paginatorMixin, messagesMixin ],
        data () {
            return {
                stackMessages: true,
                statusList: JSON.parse(this.data).statusList,
                tagsList: JSON.parse(this.data).tagsList,
                columns: [
                    {
                        key: 'check',
                        label: ''
                    },
                    {
                        key: 'status',
                        label: this.$t('Status', this.locale)
                    },
                    {
                        key: 'author',
                        label: this.$t('Author', this.locale)
                    },
                    {
                        key: 'subject',
                        label: this.$t('Subject', this.locale)
                    },
                    {
                        key: 'created_at',
                        label: this.$t('Create', this.locale)
                    },
                    {
                        key: 'tag',
                        label: this.$t('Tag', this.locale)
                    },
                    {
                        key: 'actions',
                        label: ''
                    }
                ],
                selectPerPage: null,
                messageToDestroy: null
            }
        },
        computed: {
            ...mapState([ 'locale', 'routes' ]),
            hasMessagesSelected () {
                return this.$parent.messagesIdsSelected.length > 0
            },
            removeAllSelectedText () {
                return JSON.parse(this.data).onlyTrashed
                    ? this.$t('Confirm delete all selected messages', { locale: this.locale })
                    : this.$t('Delete all selected messages', { locale: this.locale })
            }
        },
        watch: {
            filters: {
                deep: true,
                handler () {
                    this.page = 1
                    this.refresh()
                }
            }
        },
        methods: {
            // Events
            onToggleCheck ( message ) {
                this.$emit('onToggleCheck', message)
            },
            onChangePerPage () {
                this.setPerPage(this.selectPerPage)
                this.refresh()
            },
            async onRemove ( message ) {
                if ( message.deleted_at === null ) {
                    await this.removeMessageRequest(message)
                    Vue.notify({
                        group: 'notify',
                        title: this.$t('Delete message'),
                        text: this.$t('Message has been deleted and moved to trash'),
                        type: 'success',
                        config: {
                            closeOnClick: true
                        }
                    })
                    this.removeFromList(message)
                } else {
                    this.$refs.confirmDestroy.open()
                }
            },
            async onRestore ( message ) {
                if ( message.deleted_at !== null ) {
                    await this.restoreMessageRequest(message)
                    this.removeFromList(message)
                    Vue.notify({
                        group: 'notify',
                        title: this.$t('Restore message'),
                        text: this.$t('Message has been restored and moved to list'),
                        type: 'success',
                        config: {
                            closeOnClick: true
                        }
                    })
                }
            },
            async onDestroy () {
                if ( this.messageToDestroy !== null ) {
                    await this.destroyMessageRequest(this.messageToDestroy)
                    this.removeFromList(this.messageToDestroy)
                    this.messageToDestroy = null
                    Vue.notify({
                        group: 'notify',
                        title: this.$t('Destroy message'),
                        text: this.$t('Message has been destroyed'),
                        type: 'success',
                        config: {
                            closeOnClick: true
                        }
                    })
                }
            },
            // Actions
            async loadNextPage () {
                this.loadPage({
                    page: this.currentPage,
                    perPage: this.perPage,
                    url: this.urlNextPage
                })
            },
            async loadPage ({ page, perPage, url }) {
                const data = await this.getMessages({ stack: this.stackMessages, page, perPage, url })
                this.setMessagesCheckAttr()
                this.currentPage = data.current_page
                this.totalPages = data.to
                this.setPerPage(perPage)
            },
            refresh () {
                this.$wait.start('loader')
                this.messages = []
                this.loadPage({
                    page: this.page,
                    perPage: this.perPage
                })
                this.$wait.end('loader')
            },
            // Getters
            getDataStatusSelected ( statusKey ) {
                let statusSelected = ''

                this.statusList.some((status) => {
                    const conditionScape = statusKey === status.key

                    if ( conditionScape ) {
                        statusSelected = status
                    }

                    return conditionScape
                })

                return statusSelected
            },
            getDataTagSelected ( tagKey ) {
                let tagSelected = ''

                this.tagsList.some((tag) => {
                    const conditionScape = tagKey === tag.key

                    if ( conditionScape ) {
                        tagSelected = tag
                    }

                    return conditionScape
                })

                return tagSelected
            },
            // Setters
            setMessagesCheckAttr () {
                this.messages.forEach((message) => {
                    message.checked = false
                })
            }
        },
        mounted () {
            this.selectPerPage = this.perPage
            this.refresh()
        }
    }
</script>
