<template>
    <CHeader
        fixed
        with-subheader
        light
    >
        <CToggler
            in-header
            class="ml-3 d-lg-none"
            @click="toggleSidebarMobile"
        />
        <CToggler
            in-header
            class="ml-3 d-md-down-none"
            @click="toggleSidebarDesktop"
        />
        <CHeaderBrand
            class="mx-auto d-lg-none"
            to="/"
        >
            <span class="logo-text">Fabbi ECS</span>
        </CHeaderBrand>
        <CHeaderNav class="d-md-down-none mr-auto">
            <CHeaderNavItem class="px-3">
                <CHeaderNavLink to="/dashboard">
                    Dashboard
                </CHeaderNavLink>
            </CHeaderNavItem>
        </CHeaderNav>
        <CHeaderNav class="mr-4">
            <CHeaderNavItem class="d-md-down-none mx-2">
                <CSwitch
                    class="mx-1 mt-1"
                    color="info"
                    checked
                    variant="opposite"
                    shape="square"
                    v-bind="labelTxt"
                />
            </CHeaderNavItem>
            <CHeaderNavItem class="d-md-down-none mx-2">
                <CHeaderNavLink>
                    <CIcon name="cil-cloud" />&nbsp;&nbsp;Feedback
                </CHeaderNavLink>
            </CHeaderNavItem>
            <TheHeaderDropdownAccnt />
        </CHeaderNav>
        <CSubheader class="px-3">
            <CBreadcrumbRouter class="border-0 mb-0" />
        </CSubheader>
    </CHeader>
</template>

<script>
import {mapActions} from 'vuex';
import TheHeaderDropdownAccnt from './TheHeaderDropdownAccnt';

export default {
    name: 'TheHeader',
    components: {
        TheHeaderDropdownAccnt
    },
    data() {
        return {
            labelTxt: {
                labelOn: 'EN',
                labelOff: 'JP'
            }
        };
    },
    methods: {
        ...mapActions('layout', [
            'toggleSidebarMobile',
            'toggleSidebarDesktop'
        ]),

        ...mapActions('auth', [
            'logout'
        ]),

        async logoutHandler() {
            await this.logout();
            this.$router.push({ name: 'portal.login' });
        }
    }
};
</script>

<style lang="scss">
    .c-header-brand {
        text-decoration: none !important;
    }

    .logo-text {
        font-size: 20px;
    }
</style>
