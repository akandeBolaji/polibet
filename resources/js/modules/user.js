/*
|-------------------------------------------------------------------------------
| VUEX modules/auth.js
|-------------------------------------------------------------------------------
| The Vuex data store for user
*/

import userAPI from '../services/api/user.js';

export const user = {

   /*
    Defines the state being monitored for the module.
  */
state: {
  userStatus: 0,
  userData : {
      user: {
          email: '',
          account: {

          },
          custom_bets: [],
          profile: {
              full_name: ''
          },
          funds: [],
          withdrawals: []
      },
      bet: []
  },
  statsStatus: 0,
  statsData : {},
  editProfileStatus: 0,
  editProfileMessage: '',
  addBetStatus: 0,
  addBetMessage: '',
  addFundStatus: 0,
  addFundMessage: '',
  withdrawFundStatus: 0,
  withdrawFundMessage: '',
  addBetFriendStatus: 0,
  addBetFriendMessage: '',
},

/*
Defines the actions used to retrieve the data.
*/
actions: {

/*
  Adds a post
*/
withdrawFund( { commit, state, dispatch }, data ){
    commit( 'setWithdrawFundStatus', 1 );

    userAPI.withdrawFund( data.withdrawData )
        .then( function( response ){
          commit( 'setWithdrawFundStatus', 2 );
          commit( 'setAddFundMessage', response.data.message);
          dispatch('getUser');
        })
        .catch( function( error ){
          commit( 'setWithdrawFundStatus', 3 );
          commit( 'setwithdrawFundMessage', error.response.data.message );
        });
  },

  addFund( { commit, state, dispatch }, data ){
    commit( 'setAddFundStatus', 1 );

    userAPI.addFund( data.fundData )
        .then( function( response ){
          commit( 'setAddFundStatus', 2 );
          commit( 'setAddFundMessage', response.data.message);
          dispatch('getUser');
        })
        .catch( function( error ){
          commit( 'setAddFundStatus', 3 );
          commit( 'setAddFundMessage', error.response.data.message );
        });
  },

addBet( { commit, state, dispatch }, data ){
    commit( 'setAddBetStatus', 1 );

    userAPI.addBet( data.betData )
        .then( function( response ){
          commit( 'setAddBetStatus', 2 );
          commit( 'setAddBetMessage', response.data.message);
          dispatch('getUser');
        })
        .catch( function( error ){
          commit( 'setAddBetStatus', 3 );
          commit( 'setAddBetMessage', error.response.data.message );
        });
  },

  addBetFriend( { commit, state, dispatch }, data ){
    commit( 'setAddBetFriendStatus', 1 );

    userAPI.addBetFriend( data.betData )
        .then( function( response ){
          commit( 'setAddBetFriendStatus', 2 );
          commit( 'setAddBetFriendMessage', response.data.message);
          //dispatch('getUser')
        })
        .catch( function( error ){
          commit( 'setAddBetFriendStatus', 3 );
          commit( 'setAddBetFriendMessage', error.response.data.message );
        });
  },

getUser( { commit, state, dispatch } ){
  commit( 'setUserStatus', 1 );

  userAPI.getProfile()
      .then( function( response ){
        commit( 'setUserStatus', 2 );
        commit( 'setUserData', response.data);
      })
      .catch( function( error ){
        commit( 'setUserStatus', 3 );
        commit( 'setUserData', error.response.data.message );
      });
},

getStats( { commit, state, dispatch } ){
    commit( 'setStatsStatus', 1 );

    userAPI.getStats()
        .then( function( response ){
          commit( 'setStatsStatus', 2 );
          commit( 'setStatsData', response.data);
        })
        .catch( function( error ){
          commit( 'setStatsStatus', 3 );
          commit( 'setStatsData', error.response.data.message );
        });
  },


},

/*
Defines the mutations used
*/
mutations: {
/*
  Set the cafe add status
*/
setUserStatus( state, status ){
  state.userStatus = status;
},

setUserData( state, data ){
  state.userData = data;
},

setStatsStatus( state, status ){
    state.statsStatus = status;
  },

setStatsData( state, data ){
    state.statsData = data;
  },

setAddBetStatus( state, status ){
    state.addBetStatus = status;
  },

setAddBetMessage( state, message ){
    state.addBetMessage = message;
  },

setAddFundStatus( state, status ){
    state.addFundStatus = status;
  },

setAddFundMessage( state, message ){
    state.addFundMessage = message;
  },

setWithdrawFundStatus( state, status ){
    state.withdrawFundStatus = status;
  },

setwithdrawFundMessage( state, message ){
    state.withdrawFundMessage = message;
  },

setAddBetFriendStatus( state, status ){
    state.addBetFriendStatus = status;
  },

setAddBetFriendMessage( state, message ){
    state.addBetFriendMessage = message;
  },
},

/*
  Defines the getters used by the module
*/
  getters: {
  /*
    Returns the posts load status.
  */
  getUserStatus( state ){
    return state.userStatus;
  },

  getUserData( state ){
    return state.userData;
  },

  getStatsStatus( state ){
    return state.statsStatus;
  },

  getStatsData( state ){
    return state.statsData;
  },


  getAddBetStatus( state ){
    return state.addBetStatus;
  },

  getAddBetMessage( state ){
    return state.addBetMessage;
  },

  getAddFundStatus( state ){
    return state.addFundStatus;
  },

  getAddFundMessage( state ){
    return state.addFundMessage;
  },

  getWithdrawFundStatus( state ){
    return state.withdrawFundStatus;
  },

  getWithdrawFundMessage( state ){
    return state.withdrawFundMessage;
  },

  getAddBetFriendStatus( state ){
    return state.addBetFriendStatus;
  },

  getAddBetFriendMessage( state ){
    return state.addBetFriendMessage;
  },
  }
}
