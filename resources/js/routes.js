import Vue from 'vue'
import VueRouter from 'vue-router'
import helper from './services/helper'

Vue.use(VueRouter);

let routes = [
    {
        path: '/bet/:id',
        component : require('./views/pages/bet'),
    },

    {
        path: '/dispute/:id',
        component : require('./views/pages/dispute'),
    },

    {
        path: '/',
        query: {ref: ''},
        component : require('./views/pages/home'),
    },

    {
        path: '/create-bet',
        query: {ref: ''},
        component : require('./views/pages/create'),
        meta: { requiresAuth: true },
    },

    {
        path: '/dashboard',
        component: require('./views/pages/dashboard'),
        meta: { requiresAuth: true },
    },

    {
        name: 'login',
        query: {redirect: ''},
        path: '/login',
        component: require('./views/auth/login'),
        meta: { requiresGuest: true },
    },

    {
        path: '/password',
        component: require('./views/auth/password'),
        meta: { requiresGuest: true },
    },

    {
        name: 'register',
        query: {redirect: ''},
        path: '/register',
        component: require('./views/auth/register'),
        meta: { requiresGuest: true },
    },

    {
        path: '/about-us',
        component: require('./views/pages/about'),
    },


    {
        path: '/auth/:token/activate',
        component: require('./views/auth/activate'),
       // meta: { requiresGuest: true },
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
                const loginpath = window.location.pathname;
                console.log(window.location);
                return next({ path : '/login', query: {redirect: loginpath}})
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
