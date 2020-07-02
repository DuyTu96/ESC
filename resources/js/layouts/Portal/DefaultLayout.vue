<template>
    <div>
        <router-view />
        <PortalLoading v-if="loading" />
    </div>
</template>

<script>
import PortalLoading from '~/components/Portal/Loading';
import {mapState} from 'vuex';
export default {
    name: 'DefaultLayout',
    components: {
        PortalLoading
    },
    computed: {
        ...mapState('layout', {
            loading: state => state.loading
        })
    }
};
</script>

<style lang="scss">
    @import "~/assets/scss/Portal/auth.scss";
</style>
