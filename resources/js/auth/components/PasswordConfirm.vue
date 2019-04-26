<template>
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="card bg-secondary shadow border-0">
                <div class="card-header bg-transparent pb-2">
                    <h1 class="text-center">{{ $t('Reset password', { locale }) }}</h1>
                </div>
                <div class="card-body px-lg-5 py-lg-5">
                    <base-alert
                        show
                        type="success"
                    >
                        <i class="ni ni-check-bold" />
                        {{ $t('Your password has been reseted', { locale: locale }) }}
                    </base-alert>
                    <base-button tag="a" type="primary" block size="lg" href="/" class="active" role="button" aria-pressed="true">
                        {{ $t('Return to home', { locale: locale }) }}
                    </base-button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapState } from 'vuex'

    export default {
        name: 'PasswordConfirm',
        computed: {
            ...mapState([ 'locale' ])
        }
    }
</script>
