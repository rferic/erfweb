<template>
    <transition name="bounceRight">
        <v-wait for="loader">
            <notifications
                group="notify"
                position="top right"
            />
            <template slot="waiting">
                <div>
                    {{ $t('Loading...', locale) }}
                </div>
            </template>
            <div class="dropdown-divider" />
            <b-alert
                :show="!hasMessages"
                variant="warning"
            >
                {{ $t('Messages not found', { locale: this.locale }) }}
            </b-alert>
            <sweet-modal ref="confirmDestroy">
                <h2>{{ $t('Confirm destroy message', { locale }) }}</h2>
                <p>{{ $t('Are you sure to destroy the message? You will not be able to restore it.', { locale }) }}</p>
                <b-button
                    size="sm"
                    class="mt-4"
                    variant="danger"
                    @click="onDestroy"
                >
                    {{ $t('Confirm destroy message', { locale }) }}
                </b-button>
            </sweet-modal>
            <sweet-modal ref="confirmDestroySelected">
                <h2>{{ $t('Confirm destroy selected messages', { locale }) }}</h2>
                <p>{{ $t('Are you sure to destroy the selected messages? You will not be able to restore it.', { locale }) }}</p>
                <b-button
                    size="sm"
                    class="mt-4"
                    variant="danger"
                    @click="onDestroySelected"
                >
                    {{ $t('Confirm destroy selected messages', { locale }) }}
                </b-button>
            </sweet-modal>
            <div v-if="hasMessages">
                <div class="mb-2">
                    <div class="pull-left">
                        <b-form-checkbox
                            v-model="checkAll"
                            @input="onToggleCheckAll"
                        />
                        <b-button
                            size="sm"
                            variant="danger"
                            @click="onRemoveSelected"
                            :disabled="!hasMessagesSelected"
                        >
                            <i class="fa fa-trash" /> {{ removeSelectedText }}
                        </b-button>
                        <b-button
                            v-if="isTrashView"
                            size="sm"
                            variant="success"
                            @click="onRestoreSelected"
                            :disabled="!hasMessagesSelected"
                        >
                            <i class="fa fa-undo" /> {{ $t('Restore selected messages', { locale }) }}
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
                    ref="table"
                    responsive
                    small
                    hover
                    striped
                    :fields="columns"
                    :items="messagesWithCheckedAttr"
                    :busy="isBusy"
                >
                    <div
                        slot="table-busy"
                        class="text-center text-primary my-2"
                    >
                        <b-spinner class="align-middle"></b-spinner>
                        <strong>{{ $t('Loading', { locale }) }}...</strong>
                    </div>
                    <template
                        slot="check"
                        slot-scope="data"
                    >
                        <b-form-checkbox v-model="data.item.checked" />
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
                            v-if="data.item.deleted_at === null"
                            href="#"
                            class="mr-2"
                            @click.prevent="$emit('onGoToMessage', data.item)"
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
            <div
                v-if="hasMessages"
                class="text-right"
            >
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
    </transition>
</template>

<script>
    import { mapState } from 'vuex'
    import cloneMixin from './../../mixins/clone'
    import paginatorMixin from './../../mixins/paginator'
    import messageMixin from '../../mixins/message'

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
        mixins: [ cloneMixin, paginatorMixin, messageMixin ],
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
                isBusy: true,
                selectPerPage: null,
                messageToDestroy: null,
                messagesWithCheckedAttr: [],
                checkAll: false
            }
        },
        computed: {
            ...mapState([ 'locale' ]),
            isTrashView () {
                return JSON.parse(this.data).onlyTrashed
            },
            hasMessagesSelected () {
                return this.messagesWithCheckedAttr.some(message => message.checked)
            },
            removeSelectedText () {
                return this.isTrashView
                    ? this.$t('Destroy selected messages', { locale: this.locale })
                    : this.$t('Delete selected messages', { locale: this.locale })
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
            onToggleCheckAll () {
                this.setMessagesCheckAttr(true)
            },
            onChangePerPage () {
                this.setPerPage(this.selectPerPage)
                this.refresh()
            },
            async onRemoveSelected () {
                if ( this.isTrashView ) {
                    this.$refs.confirmDestroySelected.open()
                } else {
                    for ( let message of this.messagesWithCheckedAttr ) {
                        if ( message.checked ) {
                            await this.remove(message)
                        }
                    }

                    await this.refreshList()

                    this.$notify({
                        group: 'notify',
                        title: this.$t('Delete message', { locale: this.locale }),
                        text: this.$t('Messages selected has been deleted and moved to trash', { locale: this.locale }),
                        type: 'success',
                        config: {
                            closeOnClick: true
                        }
                    })
                    this.refreshNavMessages()
                }
            },
            async onRemove ( message ) {
                if ( message.deleted_at === null ) {
                    await this.remove(message)
                    await this.refreshList()

                    this.$notify({
                        group: 'notify',
                        title: this.$t('Delete message', { locale: this.locale }),
                        text: this.$t('Message has been deleted and moved to trash', { locale: this.locale }),
                        type: 'success',
                        config: {
                            closeOnClick: true
                        }
                    })
                    this.refreshNavMessages()
                } else {
                    this.messageToDestroy = message
                    this.$refs.confirmDestroy.open()
                }
            },
            async onRestoreSelected () {
                for ( let message of this.messagesWithCheckedAttr ) {
                    if ( message.checked ) {
                        await this.restore(message)
                    }
                }

                await this.refreshList()

                this.$notify({
                    group: 'notify',
                    title: this.$t('Restore selected messages', { locale: this.locale }),
                    text: this.$t('Selected messages has been restored and moved to list', { locale: this.locale }),
                    type: 'success',
                    config: {
                        closeOnClick: true
                    }
                })
                this.refreshNavMessages()
            },
            async onRestore ( message ) {
                await this.restore(message)
                await this.refreshList()

                this.$notify({
                    group: 'notify',
                    title: this.$t('Restore message', { locale: this.locale }),
                    text: this.$t('Message has been restored and moved to list', { locale: this.locale }),
                    type: 'success',
                    config: {
                        closeOnClick: true
                    }
                })
                this.refreshNavMessages()
            },
            async onDestroySelected () {
                for ( let message of this.messagesWithCheckedAttr ) {
                    if ( message.checked ) {
                        await this.destroy(message)
                        this.$refs.confirmDestroySelected.close()
                    }
                }

                await this.refreshList()

                this.$notify({
                    group: 'notify',
                    title: this.$t('Destroy selected messages', { locale: this.locale }),
                    text: this.$t('Selected messages has been destroyed', { locale: this.locale }),
                    type: 'success',
                    config: {
                        closeOnClick: true
                    }
                })
                this.refreshNavMessages()
            },
            async onDestroy () {
                if ( this.messageToDestroy !== null ) {
                    await this.destroy(this.messageToDestroy)
                    await this.refreshList()

                    this.messageToDestroy = null
                    this.$refs.confirmDestroy.close()
                    this.$notify({
                        group: 'notify',
                        title: this.$t('Destroy message', { locale: this.locale }),
                        text: this.$t('Message has been destroyed', { locale: this.locale }),
                        type: 'success',
                        config: {
                            closeOnClick: true
                        }
                    })
                    this.refreshNavMessages()
                }
            },
            // Actions
            async refreshList () {
                this.isBusy = true
                this.messages = []

                for ( let page = 1; page <= this.currentPage; page++ ) {
                    await this.loadPage({
                        page: page,
                        perPage: this.perPage
                    })
                }

                this.isBusy = false
            },
            async loadNextPage () {
                this.isBusy = true

                await this.loadPage({
                    page: this.currentPage,
                    perPage: this.perPage,
                    url: this.urlNextPage
                })

                this.isBusy = false
            },
            async loadPage ({ page, perPage, url }) {
                this.filters.receivers = [ 'admin' ]

                const data = await this.getMessages({
                    stack: this.stackMessages,
                    page,
                    perPage,
                    url,
                    filters: this.filters,
                    orderBy: this.orderBy
                })
                this.setMessagesCheckAttr(false)
                this.currentPage = data.current_page
                this.totalPages = data.total
                this.setPerPage(perPage)
            },
            async refresh () {
                this.$wait.start('loader')
                this.isBusy = true
                this.messages = []

                await this.loadPage({
                    page: this.page,
                    perPage: this.perPage
                })

                this.isBusy = false
                this.$wait.end('loader')
            },
            async remove ( message ) {
                await this.removeMessageRequest(message)
                this.removeFromList(message)
            },
            async restore ( message ) {
                await this.restoreMessageRequest(message)
                this.removeFromList(message)
            },
            async destroy ( message ) {
                await this.destroyMessageRequest(message)
                this.removeFromList(message)
            },
            refreshNavMessages () {
                this.$root.$refs.nav.$refs.navRight.refreshData()
            },
            // Setters
            setMessagesCheckAttr ( force ) {
                for ( let message of this.messages ) {
                    if ( force || typeof message.checked === typeof undefined ) {
                        message.checked = this.checkAll
                    }
                }

                this.messagesWithCheckedAttr = this.clone(this.messages)
            }
        },
        mounted () {
            this.selectPerPage = this.perPage
            this.refresh()
        }
    }
</script>

<style scoped>
    .custom-checkbox {
        display: inline-block;
    }
</style>
