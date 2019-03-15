<template>
    <div>
        <b-row>
            <b-col cols="12">
                <b-form-checkbox
                    switch
                    v-model="enable"
                >
                    {{ $t(enable ? 'Enable' : 'Disable', { locale }) }}
                </b-form-checkbox>
            </b-col>
        </b-row>
        <hr>
        <div class="overflow-hidden">
            <transition
                enter-active-class="slideInDown"
                leave-active-class="slideOutUp"
            >
                <b-row v-if="enable">
                    <b-col cols="12">
                        asd
                    </b-col>
                </b-row>
            </transition>
        </div>
    </div>
</template>

<script>
    import { mapState } from 'vuex'
    import cloneMixin from './../../../mixins/clone'

    export default {
        name: 'LocaleForm',
        props: {
            language: {
                type: Object,
                required: true
            },
            pageLocaleOrigin: {
                type: Object,
                required: true
            },
            layouts: {
                type: Array,
                required: true
            }
        },
        mixins: [ cloneMixin ],
        data () {
            return {
                pageLocale: this.clone(this.pageLocaleOrigin),
                enable: this.pageLocaleOrigin.id !== null
            }
        },
        computed: {
            ...mapState([ 'locale' ]),
            transitionToggle () {
                return this.enable ? 'slideDown' : 'slideDown'
            }
        }
    }
</script>
