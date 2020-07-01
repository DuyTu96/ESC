import Cookie from 'js-cookie';

export default function authUser({ to, next }) {
    var exceptRoute = ['user.login', 'user.register', 'user.register.active', 'user.register.notification', 'user.password.forgot', 'user.password.reset', 'user.login.socialite', 'user.register.socialite']; // here is login, register...
    var isExceptRoute = exceptRoute.includes(to.name);
    var manageQRRoute = ['user.qr.index', 'user.qr.add', 'user.qr.add.name', 'user.qr.add.department', 'user.qr.add.position', 'user.qr.group.business-card.detail'];
    var isManageQRRoute = manageQRRoute.includes(to.name);
    var manageAccountRoute = ['user.account.change_email', 'user.account.change_password', 'user.account.change_email_success', 'user.account.change_password_success'];
    var isManageAccountRoute = manageAccountRoute.includes(to.name);
    var manageBusinessCardRoute = ['user.account.business_card.edit', 'user.account.business_card.complete', 'user.account.business_card.unassign', 'user.account.check_business_card'];
    var isManageBusinessCardRoute = manageBusinessCardRoute.includes(to.name);

    if (isExceptRoute && !Cookie.get('USER_ACCESS_TOKEN')) {
        return next();
    } else if (to.name == 'user.register.active' || to.name == 'user.password.reset') {
        return next();
    } else if (isExceptRoute && Cookie.get('USER_ACCESS_TOKEN') && Cookie.get('USER_AUTHENTICATED') && JSON.parse(Cookie.get('USER_AUTHENTICATED')).is_authenticated === 1) {
        return next({ name: 'user.index' });
    } else if (to.name == 'user.account.business_card'
        && !Cookie.get('USER_ACCESS_TOKEN')) {

        return next({name: 'user.login'});
    } else if (!Cookie.get('USER_ACCESS_TOKEN') || !JSON.parse(Cookie.get('USER_AUTHENTICATED')).is_has_business_card) {
        if (isManageQRRoute) {
            return next({name: 'user.address_book'});
        } else if ((!Cookie.get('USER_ACCESS_TOKEN') && isManageAccountRoute) || isManageBusinessCardRoute) {
            return next({name: 'user.account.index'});
        } else {
            return next();
        }
    } else {
        return next();
    }
}
