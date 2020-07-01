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
                name: 'Dashboard',
                meta: {
                    layout: 'PortalLayout'
                },
                component: page('Portal/DashBoard.vue')
            },
            {
                path: 'login',
                name: 'portal.login',
                meta: {
                    layout: 'DefaultLayout'
                },
                component: page('Portal/Auth/Login.vue')
            },
            {
                path: 'password',
                component: {
                    render(c) {
                        return c('router-view');
                    }
                },
                children: [
                    {
                        path: '',
                        redirect: {name: 'portal.login'}
                    },
                    {
                        path: 'forgot',
                        name: 'portal.password.forgot',
                        meta: {
                            layout: 'DefaultLayout'
                        },
                        component: page('Portal/Auth/ForgotPassword/Request.vue'),
                    },
                    {
                        path: 'reset',
                        component: {
                            render(c) {
                                return c('router-view');
                            }
                        },
                        children: [
                            {
                                path: '',
                                redirect: {name: 'portal.login'}
                            },
                            {
                                path: ':token',
                                name: 'portal.password.reset',
                                meta: {
                                    layout: 'DefaultLayout'
                                },
                                component: page('Portal/Auth/ForgotPassword/Reset.vue'),
                            }
                        ]
                    },
                ]
            },
            {
                path: '/employees',
                component: {
                    render(c) {
                        return c('router-view');
                    }
                },
                children: [
                    {
                        path: '/',
                        name: 'Employees',
                        meta: {
                            layout: 'PortalLayout',
                        },
                        component: page('Portal/Employees/ListEmployee.vue'),
                    },
                    {
                        path: ':id',
                        name: 'Employees information',
                        meta: {
                            layout: 'PortalLayout',
                        },
                        component: page('Portal/Employees/EmployeeInformation.vue'),
                    }
                ]
            }
        ]
    }
];
