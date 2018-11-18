import Vue from 'vue'
import VueRouter from 'vue-router'
import helper from './services/helper'

Vue.use(VueRouter);

let routes = [
    {
        path: '/',
        query: {ref: ''},
        component : require('./views/pages/home'),
    },

    {
        path: '/dashboard',
        query: {info: ''},
        component: require('./views/pages/dashboard'),
        meta: { requiresAuth: true },
    },

    {
        path: '/user',
        component: require('./views/user/index'),
        meta: { requiresAuth: true },
    },

    {
        path: '/profile',
        component: require('./views/user/profile'),
        meta: { requiresAuth: true },
    },

    {
        path: '/login/',
        component: require('./views/auth/login'),
        meta: { requiresGuest: true },
    },

    {
        path: '/password',
        component: require('./views/auth/password'),
        meta: { requiresGuest: true },
    },

    {
        path: '/register',
        component: require('./views/auth/register'),
        meta: { requiresGuest: true },
    },


    {
        path: '/sample',
        component: require('./views/pages/sample'),
    },


    {
        path: '/auth/:token/activate',
        component: require('./views/auth/activate'),
        meta: { requiresGuest: true },
    },

    {
        path: '/password/reset/:token',
        component: require('./views/auth/reset'),
        meta: { requiresGuest: true },
    },

    {
        path: '*',
        component : require('./views/errors/page-not-found'),
    }
];

const router = new VueRouter({
	routes,
    linkActiveClass: 'active',
    mode: 'history'
});

router.beforeEach((to, from, next) => {

    if (to.matched.some(m => m.meta.requiresAuth)){
        return helper.check().then(response => {
            if(!response){
                return next({ path : '/login'})
            }

            return next()
        }).catch( response => {
            return next();
        })
    }

    if (to.matched.some(m => m.meta.requiresGuest)){
        return helper.check().then(response => {
            if(response){
                return next({ path : '/dashboard'})
            }

            return next()
        }).catch( response => {
            return next();
        })
    }

    return next()
});

export default router;
