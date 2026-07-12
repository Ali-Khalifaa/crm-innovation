import Cookies from "js-cookie";
import adminApi from "../../api/adminAxios";
import store from "../../store/admin";

export default function auth({ next, store }){
    console.log('[auth middleware] cookie:', Cookies.get("token") ? 'EXISTS' : 'MISSING');
    if (!Cookies.get("token")) {
        console.log('[auth middleware] no token → redirect to login');
        return next({name: 'login'});
    } else {
        console.log('[auth middleware] has token → calling checkToken...');
        adminApi.post(`dashboard/auth/checkToken`)
            .then((res) => {
                console.log('[auth middleware] checkToken success:', res.data);
                let l = res.data.data;
                let name = [];
                (l?.permission || []).forEach(el => { name.push(el.name); });
                store.commit('authAdmin/editPermission', name);
                store.commit('authAdmin/editUser', l?.user);
                store.commit('authAdmin/editType', l?.type || 'admin');
                return next();
            })
            .catch((err) => {
                console.error('[auth middleware] checkToken FAILED:', err);
                store.commit('authAdmin/logoutToken');
                return next({name: 'login'});
            });
    }
}
