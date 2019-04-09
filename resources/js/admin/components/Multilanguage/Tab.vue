<template>
    <b-card no-body>
        <b-tabs
            pills
            card
            vertical
            class="tabs-pills"
            v-model="tab"
        >
            <b-tab
                v-for="language in languages"
                :key="language.iso"
                :active="(defaultActive !== null && language.iso === defaultActive) || (defaultActive === null && language.code === locale)"
            >
                <template slot="title">
                    <b-badge
                        v-if="showEnableIcons && language.enable"
                        variant="success"
                        class="semaphore"
                    >&nbsp;</b-badge>
                    <b-badge
                        v-if="showEnableIcons && !language.enable"
                        variant="danger"
                        class="semaphore"
                    >&nbsp;</b-badge>
                    {{ $t(language.name, { locale }) | capitalizeFilter }}
                    <span
                        v-if="language.default"
                        class="ml-1"
                    >
                        ({{ $t('default', { locale }) }})
                    </span>
                </template>
                <slot
                    :language="language"
                />
            </b-tab>
        </b-tabs>
    </b-card>
</template>

<script>
    import { mapState } from 'vuex'
    import capitalizeFilter from '../../../includes/filters/capitalizeFilter'
    import cloneMixin from './../../mixins/clone'

    export default {
        name: 'MultilanguageTab',
        props: {
            languagesDefault: {
                type: Array,
                required: true
            },
            showEnableIcons: {
                type: Boolean,
                required: false,
                default: false
            },
            defaultActive: {
                type: [ String, Number ],
                required: false,
                default: null
            }
        },
        mixins: [ cloneMixin ],
        data () {
            return {
                languages: this.clone(this.languagesDefault),
                tab: null
            }
        },
        computed: {
            ...mapState([ 'locale' ])
        },
        filters: { capitalizeFilter },
        methods: {
            // Actions
            toggleLanguage ({ enable, language }) {
                for ( let languageData of this.languages ) {
                    if ( languageData.iso === language.iso ) {
                        languageData.enable = enable
                    }
                }
            },
            changeLanguageTab ( language ) {
                for ( const [index, lang] of this.languages.entries() ) {
                    if ( language.iso === lang.iso ) {
                        this.tab = index
                    }
                }
            },
            // Getters
            getCurrentLanguage () {
                return this.languages[this.tab]
            },
            getAnyEnable () {
                return this.languages.some(language => language.enable)
            }
        }
    }
</script>
