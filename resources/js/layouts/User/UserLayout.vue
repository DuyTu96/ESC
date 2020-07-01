<template>
    <div class="l-2col">
        <Sidebar />
        <div class="content-user">
            <keep-alive>
                <router-view v-if="$route.meta.isKeepAlive" />
            </keep-alive>
            <router-view v-if="!$route.meta.isKeepAlive" />
            <Loading />
        </div>
    </div>
</template>

<script>
import { mapState } from 'vuex';
import Sidebar from '~/components/User/Siderbar/Sidebar';
import Loading from '~/components/Common/Loader/Loading';
export default {
    name: 'UserLayout',
    components: {
        Sidebar,
        Loading
    },
    computed: {
        ...mapState('auth', {
            USER: state => state.USER
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
.content-user {
    position: relative;
}
</style>
