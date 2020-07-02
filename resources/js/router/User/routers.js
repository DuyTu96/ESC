function page(path) {
    return () => import(`~/pages/${path}`).then(m => m.default || m);
}

export default [
    {
        path: '/',
        component: {
            render(c) {
                return c('router-view');
            }
        },

        children: [
            {
                path: '',
                name: 'user.index',
                meta: {
                    layout: 'UserLayout'
                },
                redirect: { name: 'user.address_book' }
            },
            {
                path: 'login',
                component: {
                    render(c) {
                        return c('router-view');
                    }
                },
                children: [
                    {
                        path: '',
                        name: 'user.login',
                        meta: {
                            layout: 'UserIndexLayout'
                        },
                        component: page('User/Auth/Login.vue')
                    },
                    {
                        path: 'social',
                        name: 'user.login.social',
                        meta: {
                            layout: 'UserIndexLayout'
                        },
                        component: page('User/Auth/SocialLogin.vue')
                    }
                ]
            }
        ]
    }
];
