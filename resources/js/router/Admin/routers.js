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
        }
    }
];
