/*
    Imports Vue and Vuex
*/
import Vue from 'vue'
import Vuex from 'Vuex'

/*
    Initializes Vuex on Vue.
*/
Vue.use( Vuex )

import { feeds } from './modules/feeds.js'
import { auth } from './modules/auth.js'
import { user } from './modules/user.js'

/*
  Exports our data store.
*/
export default new Vuex.Store({
  modules: {
   feeds,
   auth,
   user
  }
});
