<template>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle text-muted text-muted" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa" :class="icon"></i>
            <div class="notify">
                <span
                    v-if="count !== null"
                    class="count">{{ count }}</span>
                <span
                    v-if="alert"
                    class="heartbit"></span>
                <span
                    v-if="alert"
                    class="point"></span>
            </div>
        </a>
        <div class="dropdown-menu dropdown-menu-right mailbox animated zoomIn">
            <ul>
                <li>
                    <div class="drop-title">{{ title }} <span v-if="count !== null">({{ count }})</span></div>
                </li>
                <li>
                    <div class="message-center">
                        <!-- Message -->
                        <a
                            v-for="(item, index) in items"
                            :key="index"
                            :href="item.url">
                            <div
                                class="btn btn-circle m-r-10"
                                :class="item.prepend.class">
                                <i
                                    class="fa"
                                    :class="item.prepend.icon"></i>
                            </div>
                            <div class="mail-contnet">
                                <h5>{{ item.title }}</h5>
                                <span class="mail-desc">{{ item.content }}</span> <span class="time">{{ item.time | moment("LLL") }}</span>
                            </div>
                        </a>
                    </div>
                </li>
                <li>
                    <a
                        class="nav-link text-center"
                        :href="url">
                        <strong>{{ urlText }}</strong> <i class="fa fa-angle-right"></i>
                    </a>
                </li>
            </ul>
        </div>
    </li>
</template>

<script>
    import { mapState } from 'vuex'

    export default {
        name: 'NavMessageNotify',
        props: {
            title: {
                type: String,
                required: true
            },
            alert: {
                type: Boolean,
                required: true
            },
            icon: {
                type: String,
                required: true
            },
            items: {
                type: Array,
                required: true
            },
            count: {
                type: Number,
                required: false,
                default: null
            },
            url: {
                type: String,
                required: true
            },
            urlText: {
                type: String,
                required: true
            }
        },
        computed: {
            ...mapState([ 'locale' ])
        },
        mounted () {
            Vue.moment().locale(this.locale)
        }
    }
</script>

<style scoped>
    .notify .count {
        font-size: 80%;
        position: absolute;
        color: #ff4249;
        font-weight: bold;
        margin-top: -5px;
        margin-left: 5px;
    }
</style>
