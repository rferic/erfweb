<template>
    <div>
        <b-row
            v-for="(urlRedirection, index) in urlsRedirection"
            :key="index"
            :show="index === currentIndex"
        >
            <div v-if="index === currentIndex" class="w-100">
                <b-col cols="12">
                    <b-form-checkbox
                        switch
                        v-model="urlRedirection.enable"
                    >
                        {{ $t(urlRedirection.enable ? 'Enable' : 'Disable', { locale }) }} {{ $t('redirection to', { locale }) }} <b><i>"{{ urlRedirection.slug_origin }}"</i></b>
                    </b-form-checkbox>
                </b-col>
                <div v-if="urlRedirection.enable" class="w-100">
                    <b-col cols="12">
                        <hr>
                    </b-col>
                    <b-col
                        cols="12"
                        class="mb-2"
                    >
                        <b-form-select
                            name="slug"
                            v-model="urlRedirection.slug_destine"
                            v-validate
                            data-vv-rules="required"
                            :class="{ 'is-invalid' : errors.has(`slug`) }"
                        >
                            <template slot="first">
                                <option
                                    value=""
                                    disabled
                                >
                                    {{ $t('Select a slug to redirection', { locale }) }}
                                </option>
                            </template>
                            <option
                                v-for="(slugCreated, index) in slugsCreated"
                                v-if="slugCreated !== urlRedirection.slug_origin"
                                :key="index"
                                :value="slugCreated"
                            >
                                {{ slugCreated }}
                            </option>
                        </b-form-select>
                        <div
                            v-if="errors.has(`slug`)"
                            class="invalid-feedback">
                            <i
                                v-show="errors.has('slug')"
                                class="fa fa-warning text-danger"
                            />
                            {{ errors.first('slug') }}
                        </div>
                    </b-col>
                    <b-col cols="12">
                        <b-form-select
                            name="code"
                            v-model="urlRedirection.code"
                            :options="codes"
                            v-validate
                            data-vv-rules="required"
                            :class="{ 'is-invalid' : errors.has(`code`) }"
                        />
                        <div
                            v-if="errors.has(`code`)"
                            class="invalid-feedback">
                            <i
                                v-show="errors.has('code')"
                                class="fa fa-warning text-danger"
                            />
                            {{ errors.first('code') }}
                        </div>
                    </b-col>
                </div>
                <b-col cols="12">
                    <hr>
                </b-col>
                <b-col cols="12">
                    <b-button
                        variant="secondary"
                        :disabled="index === 0"
                        @click="goPreviousUrlRedirection"
                    >
                        <i class="fa fa-chevron-left" />
                    </b-button>
                    <b-button
                        v-if="index < urlsRedirection.length - 1"
                        variant="secondary"
                        @click="goNextUrlRedirection"
                    >
                        <i class="fa fa-chevron-right" />
                    </b-button>
                    <b-button
                        v-else
                        variant="success"
                        @click="goNextUrlRedirection"
                    >
                        {{ $t('Confirm redirections', { locale }) }}
                    </b-button>
                </b-col>
            </div>
        </b-row>
    </div>
</template>

<script>
    import { mapState } from 'vuex'
    import pageMixin from './../../mixins/page'
    import slugMixin from './../../mixins/slug'
    import redirectionMixin from './../../mixins/redirection'

    export default {
        name: 'RedirectionsPage',
        props: {
            pageLocales: {
                type: Array,
                required: true
            },
            enableRedirection: {
                type: Boolean,
                required: false,
                default: false
            }
        },
        mixins: [ pageMixin, slugMixin, redirectionMixin ],
        data () {
            return {
                slugsCreated: [],
                urlsRedirection: [],
                currentIndex: 0
            }
        },
        computed: {
            ...mapState([ 'routes', 'locale' ]),
            hasUrlsRedirection () {
                return this.urlsRedirection.length > 0
            },
            isLast () {
                return this.currentIndex === this.urlsRedirection.length
            }
        },
        watch: {
            pageLocales: {
                deep: true,
                async handler () {
                    this.currentIndex = 0
                    await this.refreshRedirections()
                }
            }
        },
        methods: {
            // Actions
            nextCurrentIndex () {
                this.currentIndex++
            },
            previousCurrentIndex () {
                this.currentIndex--
            },
            async refreshRedirections () {
                let urlRedirection

                this.urlsRedirection = []

                await this.getRedirectionsWithoutPaginate()

                for ( const pageLocale of this.pageLocales ) {
                    urlRedirection = ''

                    this.redirections.some(redirection => {
                        const scapeCondition = redirection.slug_origin === pageLocale.slug

                        if ( scapeCondition ) {
                            urlRedirection = redirection.slug_destine
                        }

                        return scapeCondition
                    })

                    this.urlsRedirection.push({
                        slug_origin: pageLocale.slug,
                        slug_destine: urlRedirection,
                        code: 301,
                        enable: urlRedirection !== '' ? true : this.enableRedirection
                    })
                }
            },
            goPreviousUrlRedirection () {
                this.currentIndex--
            },
            goNextUrlRedirection () {
                if ( this.validateUrlRedirection() ) {
                    if ( this.currentIndex >= this.urlsRedirection.length - 1 ) {
                        this.confirmRedirections()
                    } else {
                        this.currentIndex++
                    }
                }
            },
            async confirmRedirections () {
                let hasAnyUrlRedirection = false

                for ( const urlRedirection of this.urlsRedirection ) {
                    if ( urlRedirection.enable ) {
                        hasAnyUrlRedirection = true
                        await this.createRedirectionRequest(urlRedirection)
                    }
                }

                this.$emit('onConfirmRedirections')
            },
            validateUrlRedirection () {
                return !this.urlsRedirection[this.currentIndex].enable || this.$validator.validate()
            },
            // Getters
            async getRedirectionsWithoutPaginate (  ) {
                let filters = {
                    slugs_origin: []
                }

                for ( const pageLocale of this.pageLocales ) {
                    filters.slugs_origin.push(pageLocale.slug)
                }

                this.redirections = await this.getRedirectionsRequest({
                    page: 1,
                    perPage: null,
                    url: this.routes.getRedirections,
                    filters,
                    orderBy: {
                        way: 'DESC',
                        attribute: 'created_at'
                    }
                })
            }
        },
        async created () {
            this.slugsCreated = await this.getAllSlugsRequest()
        }
    }
</script>
