import Category from "../../views/admin/category/index.vue";
import store from "../../store/admin";

export default [
    {
        path: 'category',
        component:  {
            template:'<router-view />',
        },
        children:[
            {
                path: '',
                name: 'category',
                component: Category,
                beforeEnter: (to, from,next) => {
                    let permission = store.state.authAdmin.permission;

                    if(permission.includes('category read')){
                        return next();
                    }else{
                        return next({name:'Page404'});
                    }
                }
            },
        ]
    },
];
