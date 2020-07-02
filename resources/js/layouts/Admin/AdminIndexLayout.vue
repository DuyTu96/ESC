<template>
    <div>
        <Header />
        <div class="l-1col content-admin">
            <router-view />
            <Loading :full-view="true" />
        </div>
    </div>
</template>

<script>
import Header from '~/components/Admin/Header/Header';
import Loading from '~/components/Common/Loader/Loading';

export default {
    name: 'AdminIndexLayout',
    components: {
        Header,
        Loading
    }
};
</script>

<style>
.content-admin {
    position: relative;
}
.c-form__txt {
    height: 54px;
}
</style>
