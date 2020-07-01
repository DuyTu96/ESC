<template>
    <div>
        <Header />
        <div class="l-1col">
            <router-view />
            <Loading :full-view="true" />
        </div>
    </div>
</template>

<script>
import Header from '~/components/User/Header/Header';
import Loading from '~/components/Common/Loader/Loading';

export default {
    name: 'UserIndexLayout',
    components: {
        Header,
        Loading
    }
};
</script>
