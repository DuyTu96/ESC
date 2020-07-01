<template>
    <div class="c-app">
        <Sidebar />
        <CWrapper>
            <Header />
            <div class="c-body">
                <main class="c-main">
                    <CContainer fluid>
                        <transition name="fade">
                            <router-view />
                        </transition>
                    </CContainer>
                </main>
                <Footer />
                <PortalLoading v-if="loading" />
            </div>
        </CWrapper>
    </div>
</template>

<script>
import Sidebar from '~/components/Portal/Sidebar';
import Header from '~/components/Portal/Header';
import Footer from '~/components/Portal/Footer';
import PortalLoading from '~/components/Portal/Loading';
import {mapState} from 'vuex';

export default {
    name: 'PortalLayout',
    components: {
        Sidebar,
        Header,
        Footer,
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
    @import "~/assets/scss/Portal/portal.scss";
</style>
