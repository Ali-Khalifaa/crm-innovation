import color from "../../views/admin/color/index.vue";
import store from "../../store/admin";

export default [
    {
        path: 'color',
        component:  {
            template:'<router-view />',
        },
        children:[
            {
                path: '',
                name: 'color',
                component: color,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('color read')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
        ]
    },
];
