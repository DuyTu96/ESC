import Cookie from 'js-cookie';

export default function authAdmin({ to, next }) {
    var exceptRoute = ['admin.login', 'admin.password.forgot', 'admin.password.reset', 'admin.register', 'admin.register.active', 'admin.register.notification', 'admin.account.change_email_success']; // here is login, register...
    var isExceptRoute = exceptRoute.includes(to.name);

    if (to.name == 'admin.register.active') {
        Cookie.remove('ADMIN_ACCESS_TOKEN');
        Cookie.remove('ADMIN_USER');

        return next();
    }
    if (isExceptRoute
        && !Cookie.get('ADMIN_ACCESS_TOKEN')) {

        return next();
    } else if (to.name == 'admin.password.reset') {
        return next();
    } else if (isExceptRoute
        && Cookie.get('ADMIN_ACCESS_TOKEN')
        && Cookie.get('ADMIN_USER')
        && JSON.parse(Cookie.get('ADMIN_USER')).is_authenticated === 1
        && JSON.parse(Cookie.get('ADMIN_USER')).company_id !== null
        && to.name != 'admin.account.change_email_success') {

        return next({ name: 'admin.index' });
    } else if (!isExceptRoute
        && !Cookie.get('ADMIN_ACCESS_TOKEN')) {

        return next({name: 'admin.login'});
    } else if (!isExceptRoute
        && Cookie.get('ADMIN_ACCESS_TOKEN')
        && Cookie.get('ADMIN_USER')
        && JSON.parse(Cookie.get('ADMIN_USER')).company_id == null
        && to.name != 'admin.companies.create') {

        return next({ name: 'admin.companies.create' });
    } else {

        return next();
    }
}
