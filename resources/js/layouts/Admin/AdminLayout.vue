<template>
    <div class="l-2col">
        <Sidebar />
        <div class="content-admin">
            <router-view />
            <Loading />
        </div>
    </div>
</template>

<script>
import Sidebar from '~/components/Admin/Siderbar/Sidebar';
import Loading from '~/components/Common/Loader/Loading';

import {mapState} from 'vuex';

export default {
    name: 'AdminLayout',

    components: {
        Sidebar,
        Loading
    },
    computed: {
        ...mapState('auth', {
            ADMIN_USER: state => state.ADMIN_USER
        }),
    },
    created() {
        this.getUserInfo();
    },
    methods: {
        async getUserInfo() {
            await this.$store.dispatch('auth/fetchUser');
        }
    }
};
</script>

<style>
.content-admin {
    position: relative;
}
</style>
