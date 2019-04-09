<template>
    <b-form-group :label="`${$t(inputLabel, { locale: locale })}: *`">
        <b-input-group>
            <b-input-group-prepend>
                <b-button
                    variant="primary"
                    @click.prevent="onToggleComputed"
                >
                    <i
                        class="fa"
                        :class="{ 'fa-lock': slug.isComputed, 'fa-unlock': !slug.isComputed }"
                    />
                </b-button>
            </b-input-group-prepend>
            <b-form-input
                v-model="slug.text"
                name="slug"
                type="text"
                v-validate
                data-vv-rules="required|slug_is_free"
                :class="{ 'is-invalid' : errors.has(`slug`) }"
                :disabled="slug.isComputed"
                v-debounce:300ms="onChange"
            />
            <b-input-group-append>
                <b-button
                    :variant="slug.isFree ? 'success' : 'danger'"
                    :disabled="true"
                >
                    <i
                        class="fa"
                        :class="{ 'fa-check': slug.isFree, 'fa-exclamation': !slug.isFree }"
                    />
                </b-button>
            </b-input-group-append>
        </b-input-group>
        <div
            v-if="errors.has(`slug`)"
            class="invalid-feedback">
            <i
                v-show="errors.has('slug')"
                class="fa fa-warning text-danger"
            />
            {{ errors.first('slug') }}
        </div>
    </b-form-group>
</template>

<script>
    import { mapState } from 'vuex'
    import cloneMixin from './../../mixins/clone'
    import slugMixin from './../../mixins/slug'

    export default {
        name: 'SlugInput',
        props: {
            type: {
                type: String,
                required: true
            },
            parentId: {
                type: [ String, Number ],
                required: false,
                default: null
            },
            slugDefault: {
                type: String,
                required: false,
                default: ''
            },
            textSluggable: {
                type: String,
                required: false,
                default: ''
            },
            label: {
                type: String,
                required: false,
                default: ''
            },
            language: {
                type: Object,
                required: true
            }
        },
        mixins: [ cloneMixin, slugMixin ],
        computed: {
            ...mapState([ 'locale' ]),
            inputLabel () {
                return this.label !== '' ? this.label : 'Slug'
            }
        },
        watch: {
            textSluggable () {
                this.refreshSlug()
            }
        },
        methods: {
            // Events
            onToggleComputed () {
                this.slug.isComputed = !this.slug.isComputed
                this.refreshSlug()
            },
            async onChange () {
                this.getSlugIsValid({ type: this.type, currentParentId: this.parentId })
                this.$emit('onChangeSlug', this.slug.text)
            },
            // Actions
            setSlugDefaultParams () {
                this.slug.text = this.clone(this.slugDefault)
                this.slug.lang = this.language.iso
                this.slug.isComputed = this.getIsComputed(this.textSluggable)

                this.refreshSlug()
            },
            refreshSlug () {
                if ( this.slug.isComputed ) {
                    this.slug.text = this.getSlugComputed(this.textSluggable)
                    this.onChange()
                }
            }
        },
        mounted () {
            this.setSlugIsFreeValidator()
            this.setSlugDefaultParams()
        }
    }
</script>
