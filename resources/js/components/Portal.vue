<template>
    <div id="portal">
        <component
            :is="layout"
            v-if="layout"
        />
    </div>
</template>

<script>
export default {
    el: '#portal',
    computed: {
    /**
     * Set the application layout.
     *
     * @param {String} layout
     */
        layout() {
            return this.$route.meta.layout || this.defaultLayout;
        }
    }
};
</script>
