<template>
    <section>
        <v-parallax dark :src="image">
            <v-layout
                column
                align-center
                justify-center
                class="white--text"
            >
                <slot name="parallax-header"  />
                <slot name="parallax-container" />
            </v-layout>
        </v-parallax>
    </section>
</template>

<script>
    export default {
        name: 'Parallax',
        props: {
            image: {
                type: String,
                required: true
            }
        }
    }
</script>
