<template>
    <base-dropdown class="nav-link pr-0">
        <div class="media align-items-center" slot="title">
            <i :class="icon" />
            <sup v-if="count !== null" class="ml-1">{{ count }}</sup>
        </div>
        <template>
            <div class=" dropdown-header noti-title">
                <h6 class="text-overflow m-0">{{ title }} <span v-if="count !== null">({{ count }})</span></h6>
                <div class="dropdown-divider"></div>
                <a v-for="(item, index) in items"
                    :key="index"
                    :href="item.url"
                    class="dropdown-item"
                >
                    <div :class="`text-${item.prepend.class}`">
                        <i class="fa" :class="item.prepend.icon" />
                        <b>{{ item.title }}</b>
                    </div>
                    <div>
                        <span class="time">{{ item.time | moment("LLL") }}</span>
                    </div>
                </a>
            </div>
        </template>
    </base-dropdown>
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
    .icon i {
        font-size: .8rem;
    }
</style>
