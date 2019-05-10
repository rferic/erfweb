<template>
    <div>
        <BlockUI v-if="isVisibleBlockui" :message="messageBlockui">
            <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
        </BlockUI>
        <div v-if="hasStatisticsLoaded">
            <b-row class="pt-3">
                <b-col lg="3" sm="6">
                    <stats-card
                        :title="$t('Enable users', { locale })"
                        type="gradient-info"
                        :sub-title="`${statistics.users.user.enable}/${statistics.users.user.total}`"
                        npm
                        icon="fa fa-users"
                        class="mb-4 mb-xl-0"
                    />
                </b-col>
                <b-col lg="3" sm="6">
                    <stats-card
                        :title="$t('Total pages', { locale })"
                        type="gradient-green"
                        :sub-title="`${statistics.pages.total}`"
                        npm
                        icon="fa fa-file"
                        class="mb-4 mb-xl-0"
                    />
                </b-col>
                <b-col lg="3" sm="6">
                    <stats-card
                        :title="$t('Run apps', { locale })"
                        type="gradient-purple"
                        :sub-title="`${statistics.apps.status.success.count}/${statistics.apps.total}`"
                        npm
                        icon="fa fa-cube"
                        class="mb-4 mb-xl-0"
                    />
                </b-col>
                <b-col lg="3" sm="6">
                    <stats-card
                        :title="$t('Pending messages', { locale })"
                        type="gradient-red"
                        :sub-title="`${statistics.messages.status.pending.count}/${statistics.messages.total}`"
                        npm
                        icon="fa fa-envelope"
                        class="mb-4 mb-xl-0"
                    />
                </b-col>
            </b-row>
            <b-row class="mt-2">
                <b-col v-if="messagesTimeLineChart !== null" lg="6" sm="12">
                    <b-card>
                        <div slot="header" class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">{{ $t('Messages received last three months', { locale }) }}</h3>
                            </div>
                        </div>
                        <b-card-text>
                            <line-chart
                                :height="350"
                                ref="bigChart"
                                :chart-data="messagesTimeLineChart"
                            />
                        </b-card-text>
                    </b-card>
                </b-col>
                <b-col v-if="usersTimeLineChart !== null" lg="6" sm="12">
                    <b-card>
                        <div slot="header" class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">{{ $t('Users created last three months', { locale }) }}</h3>
                            </div>
                        </div>
                        <b-card-text>
                            <line-chart
                                :height="350"
                                ref="bigChart"
                                :chart-data="usersTimeLineChart"
                            />
                        </b-card-text>
                    </b-card>
                </b-col>
            </b-row>
            <b-row class="mt-2">
                <b-col lg="6" sm="12">
                    <b-card>
                        <div slot="header" class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">{{ $t('Messages status', { locale }) }}</h3>
                            </div>
                        </div>
                        <b-card-text>
                            <div class="table-responsive">
                                <base-table thead-classes="thead-light" :data="statusMessages">
                                    <template slot="columns">
                                        <th>{{ $t('Status', { locale }) }}</th>
                                        <th>{{ $t('Count', { locale }) }}</th>
                                        <th>{{ $t('Percentage', { locale }) }}</th>
                                    </template>

                                    <template slot-scope="{ row }">
                                        <th scope="row">
                                            {{ $t(row.name, { locale }) }}
                                        </th>
                                        <td>
                                            {{ row.count }}
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="mr-2">{{ row.percentage }}%</span>
                                                <base-progress :type="row.progressType" class="pt-0" :show-percentage="false" :value="row.percentage" />
                                            </div>
                                        </td>
                                    </template>
                                </base-table>
                            </div>
                        </b-card-text>
                    </b-card>
                </b-col>
                <b-col lg="6" sm="12">
                    <b-card>
                        <div slot="header" class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">{{ $t('Pages languages', { locale }) }}</h3>
                            </div>
                        </div>
                        <b-card-text>
                            <div class="table-responsive">
                                <base-table thead-classes="thead-light" :data="langsPages">
                                    <template slot="columns">
                                        <th>{{ $t('Language', { locale }) }}</th>
                                        <th>{{ $t('Code ISO', { locale }) }}</th>
                                        <th>{{ $t('Count', { locale }) }}</th>
                                        <th>{{ $t('Percentage', { locale }) }}</th>
                                    </template>

                                    <template slot-scope="{ row }">
                                        <th scope="row">
                                            {{ $t(row.name, { locale }) }}
                                        </th>
                                        <td>
                                            {{ row.iso }}
                                        </td>
                                        <td>
                                            {{ row.count }}
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="mr-2">{{ row.percentage }}%</span>
                                                <base-progress :type="row.progressType" class="pt-0" :show-percentage="false" :value="row.percentage" />
                                            </div>
                                        </td>
                                    </template>
                                </base-table>
                            </div>
                        </b-card-text>
                    </b-card>
                </b-col>
            </b-row>
            <b-row class="mt-2">
                <b-col lg="6" sm="12">
                    <b-card>
                        <div slot="header" class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">{{ $t('Apps status', { locale }) }}</h3>
                            </div>
                        </div>
                        <b-card-text>
                            <div class="table-responsive">
                                <base-table thead-classes="thead-light" :data="statusApps">
                                    <template slot="columns">
                                        <th>{{ $t('Status', { locale }) }}</th>
                                        <th>{{ $t('Count', { locale }) }}</th>
                                        <th>{{ $t('Percentage', { locale }) }}</th>
                                    </template>

                                    <template slot-scope="{ row }">
                                        <th scope="row">
                                            {{ $t(row.name, { locale }) }}
                                        </th>
                                        <td>
                                            {{ row.count }}
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="mr-2">{{ row.percentage }}%</span>
                                                <base-progress :type="row.progressType" class="pt-0" :show-percentage="false" :value="row.percentage" />
                                            </div>
                                        </td>
                                    </template>
                                </base-table>
                            </div>
                        </b-card-text>
                    </b-card>
                </b-col>
                <b-col lg="6" sm="12">
                    <b-card>
                        <div slot="header" class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">{{ $t('Apps types', { locale }) }}</h3>
                            </div>
                        </div>
                        <b-card-text>
                            <div class="table-responsive">
                                <base-table thead-classes="thead-light" :data="typesApps">
                                    <template slot="columns">
                                        <th>{{ $t('Type', { locale }) }}</th>
                                        <th>{{ $t('Count', { locale }) }}</th>
                                        <th>{{ $t('Percentage', { locale }) }}</th>
                                    </template>

                                    <template slot-scope="{ row }">
                                        <th scope="row">
                                            {{ $t(row.name, { locale }) }}
                                        </th>
                                        <td>
                                            {{ row.count }}
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="mr-2">{{ row.percentage }}%</span>
                                                <base-progress :type="row.progressType" class="pt-0" :show-percentage="false" :value="row.percentage" />
                                            </div>
                                        </td>
                                    </template>
                                </base-table>
                            </div>
                        </b-card-text>
                    </b-card>
                </b-col>
            </b-row>
        </div>
    </div>
</template>

<script>
    import { mapState, mapActions } from 'vuex'
    import LineChart from './../Charts/LineChart'

    export default {
        name: 'IndexDashboard',
        props: {
            data: {
                type: String,
                required: true,
            }
        },
        components: { LineChart },
        data () {
            return {
                statistics: null,
                languagesAvailable: JSON.parse(this.data).langsAvailable,
                messagesTimeLineChart: null,
                usersTimeLineChart: null
            }
        },
        computed: {
            ...mapState([ 'locale', 'routes' ]),
            ...mapState({
                isVisibleBlockui: state => state.blockui.isVisible,
                messageBlockui: state => state.blockui.message
            }),
            hasStatisticsLoaded () {
                return this.statistics !== null
            },
            totalMessages () {
                let count = 0

                if ( this.hasStatisticsLoaded ) {
                    try {
                        for (let [key, status] of Object.entries(this.statistics.messages.status)) {
                            count += status.count
                        }
                    } catch (e) {
                    }
                }

                return count
            },
            statusMessages () {
                let statusMessages = []

                if ( this.hasStatisticsLoaded ) {
                    try {
                        for (let [key, status] of Object.entries(this.statistics.messages.status)) {
                            statusMessages.push({
                                name: key,
                                count: status.count,
                                percentage: Math.round((100 / this.statistics.messages.total) * status.count),
                                progressType: `gradient-${ status.class }`
                            })
                        }
                    } catch (e) {
                    }
                }

                return statusMessages
            },
            statusApps () {
                let statusApps = []

                if ( this.hasStatisticsLoaded ) {
                    try {
                        for (let [key, status] of Object.entries(this.statistics.apps.status)) {
                            statusApps.push({
                                name: key,
                                count: status.count,
                                percentage: Math.round((100 / this.statistics.apps.total) * status.count),
                                progressType: `gradient-${ status.class }`
                            })
                        }
                    } catch (e) {
                    }
                }

                return statusApps
            },
            typesApps () {
                let typesApps = []

                if ( this.hasStatisticsLoaded ) {
                    try {
                        for (let [key, type] of Object.entries(this.statistics.apps.types)) {
                            typesApps.push({
                                name: key,
                                count: type.count,
                                percentage: Math.round((100 / this.statistics.apps.total) * type.count),
                                progressType: `gradient-${ type.class }`
                            })
                        }
                    } catch (e) {
                    }
                }

                return typesApps
            },
            langsPages () {
                let langsPages = []

                if ( this.hasStatisticsLoaded ) {
                    try {
                        for (const language of this.languagesAvailable) {
                            for (const item of this.statistics.pages.langs) {
                                if (language.iso === item.lang) {
                                    const percentage = Math.round((100 / this.statistics.pages.total) * item.count)
                                    let progressType = 'gradient-success'

                                    if ( percentage < 75 ) {
                                        progressType = 'gradient-warning'
                                    } else if ( percentage < 50 ) {
                                        progressType = 'gradient-danger'
                                    }

                                    langsPages.push({
                                        count: item.count,
                                        percentage,
                                        progressType,
                                        code: language.code,
                                        iso: language.iso,
                                        name: language.name
                                    })
                                }
                            }
                        }
                    } catch (e) {

                    }
                }

                return langsPages
            }
        },
        methods: {
            ...mapActions({
                toogleBlockui : 'blockui/toggleIsVisible'
            }),
            // Actions
            async initalLoadedStatistics () {
                this.toogleBlockui(true)
                await this.getStatistics()
                this.toogleBlockui(false)
            },
            // Getters
            async getStatistics () {
                this.statistics = await this.getStatisticsRequest()
                this.getMessagesTimeLineChart()
                this.getUsersTimeLineChart()
                setTimeout(() => this.getStatistics(), 120000)
            },

            getMessagesTimeLineChart () {
                let chartData = {
                    datasets: [{
                        label: this.$t('Messages', { locale: this.locale }),
                        data: [ 0, 0, 0 ]
                    }],
                    labels: [
                        Vue.moment().subtract(2, 'month').format('MMMM'),
                        Vue.moment().subtract(1, 'month').format('MMMM'),
                        Vue.moment().format('MMMM')
                    ]
                }

                if ( this.hasStatisticsLoaded ) {
                    try {
                        for ( const message of this.statistics.messages.lastThreeMonths ) {
                            const month = Vue.moment(message.created_at).month()

                            if ( month === Vue.moment().subtract(2, 'month').month() ) {
                                chartData.datasets[0].data[0]++
                            } else if ( month === Vue.moment().subtract(1, 'month').month() ) {
                                chartData.datasets[0].data[1]++
                            } else if ( month === Vue.moment().month() ) {
                                chartData.datasets[0].data[2]++
                            }
                        }
                    } catch (e) {}
                }

                this.messagesTimeLineChart = chartData
            },
            getUsersTimeLineChart () {
                let chartData = {
                    datasets: [{
                        label: this.$t('Users', { locale: this.locale }),
                        data: [ 0, 0, 0 ]
                    }],
                    labels: [
                        Vue.moment().subtract(2, 'month').format('MMMM'),
                        Vue.moment().subtract(1, 'month').format('MMMM'),
                        Vue.moment().format('MMMM')
                    ]
                }

                if ( this.hasStatisticsLoaded ) {
                    try {
                        for ( const user of this.statistics.users.user.lastThreeMonths ) {
                            const month = Vue.moment(user.created_at).month()

                            if ( month === Vue.moment().subtract(2, 'month').month() ) {
                                chartData.datasets[0].data[0]++
                            } else if ( month === Vue.moment().subtract(1, 'month').month() ) {
                                chartData.datasets[0].data[1]++
                            } else if ( month === Vue.moment().month() ) {
                                chartData.datasets[0].data[2]++
                            }
                        }
                    } catch (e) {}
                }

                this.usersTimeLineChart = chartData
            },
            // API Request
            async getStatisticsRequest () {
                const { data } = await this.axios.post(this.routes.getStatistics, {})
                return data
            }
        },
        mounted () {
            this.initalLoadedStatistics()
        }
    }
</script>
