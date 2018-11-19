<template>
  <v-app id="inspire">
    <v-content>
        <v-toolbar fixed>
         <v-toolbar-side-icon @click.stop="drawer = !drawer"></v-toolbar-side-icon>
        <v-toolbar-title class="green--text">Polibet</v-toolbar-title>
         <v-spacer></v-spacer>
          <v-btn class="white--text" color='green' v-if="userData.account">
              Bal - N{{ userData.account.balance }}.00
         </v-btn>
       </v-toolbar>
       <v-navigation-drawer
      v-model="drawer"
      fixed
      temporary
      height="400px"
    >
      <v-list class="pa-1">
        <v-list-tile avatar >
          <v-list-tile-avatar color="grey" v-if="userData.profile">
            {{ userData.profile.full_name.slice(0,2) }}
          </v-list-tile-avatar>

          <v-list-tile-content>
            <v-list-tile-title v-if="userData.profile">{{ userData.profile.full_name }}</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>
      </v-list>

      <v-list class="pt-0" dense>
        <v-divider></v-divider>

        <v-list-tile @click="home">
          <v-list-tile-action>
            <v-icon color="green">home</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>Home</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>

        <v-list-tile @click="fundAccount">
          <v-list-tile-action>
            <v-icon color="green">credit_card</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>Fund Account</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>


         <v-list-tile @click="addBet">
          <v-list-tile-action>
            <v-icon color="green">add</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>Place Bet</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>

         <v-list-tile @click="betFriends">
          <v-list-tile-action>
            <v-icon color="green">people</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>Bet for Friends</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>


        <v-list-tile @click="withdrawWins">
          <v-list-tile-action>
            <v-icon color="red">redeem</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>Withdraw Wins</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>

        <v-list-tile @click="logout">
          <v-list-tile-action>
            <v-icon color="red">logout</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>Logout</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>
      </v-list>
      </v-navigation-drawer>

    <v-layout mt-5>
    <v-container fluid fill-height mt-5>
      <v-expansion-panel
      v-model="panel"
      expand
      >
             <v-expansion-panel-content
      >
        <div slot="header">Placed bets</div>
        <div v-if="userData.bet">
          <v-list dense v-if="userData">
            <v-list-tile class="grey lighten-3">
              <v-list-tile-content>Total Placed bets Amount:</v-list-tile-content>
              <v-list-tile-content class="align-end">N {{computedTotal}}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile class="grey lighten-3">
              <v-list-tile-content>Total Present Win Amount:</v-list-tile-content>
              <v-list-tile-content class="align-end" >N {{ computedWinAmount }}</v-list-tile-content>
            </v-list-tile>
            <v-divider></v-divider>
            <div v-for="bet in userData.bet" :key="bet.id">
             <v-list-tile >
              <v-list-tile-content>Bet ID:</v-list-tile-content>
              <v-list-tile-content class="align-end">{{adjustId(bet.id)}}</v-list-tile-content>
             </v-list-tile>
             <v-list-tile>
              <v-list-tile-content>Bet Amount:</v-list-tile-content>
              <v-list-tile-content class="align-end">N {{bet.amount}}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content>Placed By:</v-list-tile-content>
              <v-list-tile-content class="align-end">{{adjustPlacedBy(bet.placed_by, bet.placed_for)}}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content> Present Win Amount:</v-list-tile-content>
              <v-list-tile-content class="align-end">N {{adjustWinAmount(bet.amount, bet.category, bet.candidate)}}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content>Bet Candidate and Category:</v-list-tile-content>
              <v-list-tile-content class="align-end">{{adjustCategory(bet.category, bet.candidate)}}</v-list-tile-content>
            </v-list-tile>
            <v-divider></v-divider>
            </div>

          </v-list>
        </div>
        <v-card v-else>
        <v-card-text class="grey lighten-3">No bets yet</v-card-text>
        </v-card>
      </v-expansion-panel-content>

      <v-expansion-panel-content
      >
        <div slot="header">General Statistics</div>
         <v-container fluid grid-list-md>
      <v-flex
      >
        <v-card>
          <v-card-title><h4>Presidential Category</h4></v-card-title>
          <v-divider></v-divider>
          <v-list dense v-if="userData">
            <v-list-tile class="grey lighten-3">
              <v-list-tile-content>Total Eligble Votes in Category:</v-list-tile-content>
              <v-list-tile-content class="align-end">{{ userData.vote.category_one }}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content>Total Amount staked in Category:</v-list-tile-content>
              <v-list-tile-content class="align-end">N {{ userData.amount.category_one }}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile class="grey lighten-3">
              <v-list-tile-content>Total Eligble Votes for Buhari:</v-list-tile-content>
              <v-list-tile-content class="align-end">{{ userData.vote.candidate_one }}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content>Total Eligble Votes for Atiku:</v-list-tile-content>
              <v-list-tile-content class="align-end">{{ userData.vote.candidate_two }}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile class="grey lighten-3">
              <v-list-tile-content>Total Amount Staked for Buhari:</v-list-tile-content>
              <v-list-tile-content class="align-end">N {{ userData.amount.candidate_one }}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content>Total Amount Staked for Atiku:</v-list-tile-content>
              <v-list-tile-content class="align-end">N {{ userData.amount.candidate_two }}</v-list-tile-content>
            </v-list-tile>
          </v-list>
        </v-card>

        <v-card>
          <v-card-title><h4>Lagos Governorship Category</h4></v-card-title>
          <v-divider></v-divider>
            <v-list dense v-if="userData">
            <v-list-tile class="grey lighten-3">
              <v-list-tile-content>Total Eligble Votes in Category:</v-list-tile-content>
              <v-list-tile-content class="align-end">{{ userData.vote.category_two }}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content>Total Amount staked in Category:</v-list-tile-content>
              <v-list-tile-content class="align-end">N {{ userData.amount.category_two }}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile class="grey lighten-3">
              <v-list-tile-content>Total Eligble Votes for Sanwo-Olu:</v-list-tile-content>
              <v-list-tile-content class="align-end">{{ userData.vote.candidate_three }}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content>Total Eligble Votes for Agbaje:</v-list-tile-content>
              <v-list-tile-content class="align-end">{{ userData.vote.candidate_four }}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile class="grey lighten-3">
              <v-list-tile-content>Total Amount Staked for Sanwo-Olu:</v-list-tile-content>
              <v-list-tile-content class="align-end" >N {{ userData.amount.candidate_three }}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content>Total Amount Staked for Agbaje:</v-list-tile-content>
              <v-list-tile-content class="align-end">N {{ userData.amount.candidate_four }}</v-list-tile-content>
            </v-list-tile>
          </v-list>
        </v-card>
      </v-flex>
  </v-container>

      </v-expansion-panel-content>
       <v-expansion-panel-content
      >
        <div slot="header">Account Summary</div>
        <v-card>
            <v-card-title><h4>Signup Bonus</h4></v-card-title>
            <v-list dense v-if="userData.signup_bonus">
            <v-list-tile class="grey lighten-3">
              <v-list-tile-content>Signup Bonus balance:</v-list-tile-content>
              <v-list-tile-content class="align-end">N {{userData.signup_bonus.amount}}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile class="grey lighten-3">
              <v-list-tile-content>Expires on:</v-list-tile-content>
              <v-list-tile-content class="align-end"> {{ userData.signup_bonus.expires_at}}</v-list-tile-content>
            </v-list-tile>
            </v-list>
            <v-list v-else>
              <v-list-tile class="grey lighten-3">
              <v-list-tile-content>Signup Bonus balance:</v-list-tile-content>
              <v-list-tile-content class="align-end">N 0.00</v-list-tile-content>
            </v-list-tile>
            </v-list>
        </v-card>
         <v-card>
            <v-card-title><h4>Referral Bonus</h4></v-card-title>
            <v-list dense v-if="userData.referral_bonus != 0">
            <div v-for="bonus in userData.referral_bonus" :key="bonus.id">
            <v-list-tile>
              <v-list-tile-content>Referred's Name:</v-list-tile-content>
              <v-list-tile-content class="align-end">{{bonus.referred}}</v-list-tile-content>
            </v-list-tile>
              <v-list-tile >
              <v-list-tile-content>Amount:</v-list-tile-content>
              <v-list-tile-content class="align-end">N {{ bonus.amount }}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content>Expires on:</v-list-tile-content>
              <v-list-tile-content class="align-end"> {{ bonus.expires_at }}</v-list-tile-content>
            </v-list-tile>
            <v-divider></v-divider>
            </div>
            </v-list>
            <v-list v-else>
            <v-card-text class="grey lighten-3">No referrals yet</v-card-text>
            </v-list>
        </v-card>
           <v-card>
            <v-card-title><h4>Account Balance</h4></v-card-title>
            <v-list dense v-if="userData">
                <v-list-tile class="grey lighten-3">
              <v-list-tile-content> <h4>Account Balance Left:</h4></v-list-tile-content>
              <v-list-tile-content class="align-end">N {{userData.account.balance}}.00</v-list-tile-content>
            </v-list-tile>
            <div  v-if="userData.funds">
            <v-card-title>Fund Details</v-card-title>
            <div v-for="fund in userData.funds" :key="fund.id">
            <v-list-tile >
              <v-list-tile-content>Fund ID:</v-list-tile-content>
              <v-list-tile-content class="align-end">{{fund.id}}</v-list-tile-content>
            </v-list-tile>
              <v-list-tile >
              <v-list-tile-content>Amount:</v-list-tile-content>
              <v-list-tile-content class="align-end">N {{ fund.amount }}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content>Fund Date:</v-list-tile-content>
              <v-list-tile-content class="align-end" > {{ fund.created_at }}</v-list-tile-content>
            </v-list-tile>
            <v-divider></v-divider>
            </div>
            </div>
            </v-list>
        </v-card>
      </v-expansion-panel-content>
       <v-expansion-panel-content
      >
        <div slot="header">Winning Statistics</div>
        <v-card>
          <v-card-text v-if="userData.bets" class="grey lighten-3">Current win amount is N{{ computedWinAmount }}.00</v-card-text>
          <v-card-text v-else class="grey lighten-3">No bets placed yet</v-card-text>
        </v-card>
      </v-expansion-panel-content>
      <v-expansion-panel-content
      >
        <div slot="header">Check Referral link</div>
        <v-card>
             <v-card-title><h4>Share your referral link below with friends and foes and get a bonus of 100 naira for each valid referral. Each referral bonus has a validity of 5 days</h4></v-card-title>
          <v-card-text class="grey lighten-3">http://polibet.site/?ref={{userData.user.refer_id}}</v-card-text>
        </v-card>
      </v-expansion-panel-content>
       <v-expansion-panel-content
      >
        <div slot="header">Check Bet id</div>
        <v-card>
         <v-card-title><h4>Bet id : {{userData.user.bet_id}}</h4></v-card-title>
          <v-card-text class="grey lighten-3">Your Bet id allows your friends place bet for you. Please note that for Subsequent bets in a category, you are not allowed to stake for another candidate </v-card-text>
        </v-card>
      </v-expansion-panel-content>
       <v-expansion-panel-content
      >
        <div slot="header">Referrals</div>
        <v-card v-if="userData.referrals_name != 0">
          <v-card-text  class="grey lighten-3" :key="referral" v-for="referral in userData.referrals_name">{{ referral.full_name }}</v-card-text>
        </v-card>
        <v-card v-else>
            <v-card-text  class="grey lighten-3">No Valid Referrals Yet</v-card-text>
            </v-card>
      </v-expansion-panel-content>
       <v-expansion-panel-content
      >
        <div slot="header">Placed bets for friends</div>
        <div v-if="userData.bet_friends != 0">
          <v-list dense>
            <div v-for="bet in userData.bet_friends" :key="bet.id">
             <v-list-tile >
              <v-list-tile-content>Bet ID:</v-list-tile-content>
              <v-list-tile-content class="align-end">{{adjustId(bet.id)}}</v-list-tile-content>
             </v-list-tile>
             <v-list-tile>
              <v-list-tile-content>Bet Amount:</v-list-tile-content>
              <v-list-tile-content class="align-end">N {{bet.amount}}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content>Placed By:</v-list-tile-content>
              <v-list-tile-content class="align-end">{{bet.placed_by}}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content>Bet Candidate and Category:</v-list-tile-content>
              <v-list-tile-content class="align-end">{{adjustCategory(bet.category, bet.candidate)}}</v-list-tile-content>
            </v-list-tile>
            <v-divider></v-divider>
            </div>

          </v-list>
        </div>
        <v-card v-else>
        <v-card-text class="grey lighten-3">No bets for friends yet</v-card-text>
        </v-card>
      </v-expansion-panel-content>
    </v-expansion-panel>
      </v-container>
        </v-layout>

      <v-dialog
      v-model="dialog"
      hide-overlay
      persistent
      width="300"
    >
      <v-card
        color="green"
        dark
      >
        <v-card-text>
          Please wait...
          <v-progress-linear
            indeterminate
            color="white"
            class="mb-0"
          ></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog
      v-model="info"
      max-width="290"
    >
     <v-card>
        <v-card-title class="headline">{{ this.infotext }}</v-card-title>
        <div>
        <v-spacer></v-spacer>
        <v-btn color="green" @click="info = false">OK</v-btn>
        </div>
     </v-card>
    </v-dialog>

     <v-dialog
        v-model="betFriends_dialog"
        persistent
        max-width="500px">
      <v-card>
        <v-card-title>
          <span class="headline">Bet for a Friend</span>
        </v-card-title>
         <v-card-text>
          <v-container grid-list-md>
            <v-layout wrap>
                <v-text-field prepend-icon="person_add" data-vv-scope="addfriendbet" v-model="betfriend.friend_id" v-validate="'required|min:4'" name="Friends id" label="Friend's Bet id" type="text"></v-text-field>
                <v-alert :value="errors.has('addfriendbet.Friends id')" type="error">{{ errors.first('addfriendbet.Friends id') }}</v-alert>
                <v-text-field label="Stake Amount" data-vv-scope="addfriendbet" v-model="betfriend.amount"  name="amount" v-validate="'required|numeric|min_value:5000'"  hint="Minimum stake amount is N5000"></v-text-field>
                <v-alert :value="errors.has('addfriendbet.amount')" type="error">{{ errors.first('addfriendbet.amount') }}</v-alert>
                <v-select
                :items="options.category"
                name="category"
                data-vv-scope="addfriendbet"
                v-validate="'required'"
                v-model="betfriend.category"
                label="Category"
                 ></v-select>
                 <v-alert :value="errors.has('addfriendbet.category')" type="error">{{ errors.first('addfriendbet.category') }}</v-alert>
                <v-select
                v-if="betfriend.category"
                :items="filteredFriendCandidate"
                name="candidate"
                data-vv-scope="addfriendbet"
                v-validate="'required'"
                label="Candidate"
                v-model="betfriend.candidate"
                hint="Be sure that your friend is aware that subsequent/previous bets in selected category has to be for same candidate"
                ></v-select>
                <v-alert :value="errors.has('addfriendbet.candidate')" type="error">{{ errors.first('addfriendbet.candidate') }}</v-alert>
            </v-layout>
          </v-container>
          <small>*Note that placing a bet on behalf of a friend implies that such a person has specifically asked you to do so. You hereby certify that you do not have an ulterior motive (e.g vote buying/selling) </small>
          <v-checkbox v-validate="'required:true'" v-model="betfriend.check" data-vv-scope="addfriendbet" name="terms and condition" label="Do you agree with the statement above?"></v-checkbox>
          <v-alert :value="errors.has('addfriendbet.terms and condition')" type="error">{{ errors.first('addfriendbet.terms and condition') }}</v-alert>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn flat @click.native="betFriends_dialog = false">Close</v-btn>
          <v-btn color="green" class="white--text" @click="submitFriendBet">Submit</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

      <v-dialog
        v-model="fundAccount_dialog"
        persistent
        max-width="500px">
      <v-card>
        <v-card-title>
          <span class="headline">Fund Account</span>
        </v-card-title>
        <v-card-text>
          <v-container grid-list-md>
            <v-layout wrap>
                <v-text-field label="Amount" v-model="fund.real_amount" data-vv-scope="fund" name="Amount" v-validate="'required|numeric'"></v-text-field>
                <v-alert :value="errors.has('fund.Amount')" type="error">{{ errors.first('fund.Amount') }}</v-alert>
            </v-layout>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-btn flat @click.native="fundAccount_dialog = false">Close</v-btn>
          <v-spacer></v-spacer>
        <paystack
        :amount="amount"
        :email="email"
        :paystackkey="paystackkey"
        :reference="reference"
        :callback="callback"
        :close="close"
        :embed="false"
       >

          <v-btn color="green"  class="white--text fas fa-money-bill-alt">Make Payment</v-btn>
       </paystack>

        </v-card-actions>
      </v-card>
    </v-dialog>

       <v-dialog
        v-model="addBet_dialog"
        persistent
        max-width="500px">
      <v-card>
        <v-card-title>
          <span class="headline">Add Bet</span>
        </v-card-title>
        <v-card-text>
          <v-container grid-list-md>
            <v-layout wrap>
                <v-text-field label="Stake Amount" v-model="bet.amount" data-vv-scope="addbet" name="Amount" v-validate="'required|numeric|min_value:5000'"  hint="Minimum stake amount is N5000"></v-text-field>
                <v-alert :value="errors.has('addbet.Amount')" type="error">{{ errors.first('addbet.Amount') }}</v-alert>
                <v-select
                :items="options.category"
                name="Category"
                data-vv-scope="addbet"
                v-validate="'required'"
                v-model="bet.category"
                label="Category"
                 ></v-select>
                 <v-alert :value="errors.has('addbet.Category')" type="error">{{ errors.first('addbet.Category') }}</v-alert>
                <v-select
                v-if="bet.category"
                :items="filteredCandidate"
                name="Candidate"
                data-vv-scope="addbet"
                v-validate="'required'"
                label="Candidate"
                v-model="bet.candidate"
                hint="Note that subsequent/previous bets in selected category has to be for same candidate"
                ></v-select>
                <v-alert :value="errors.has('addbet.Candidate')" type="error">{{ errors.first('addbet.Candidate') }}</v-alert>
            </v-layout>
          </v-container>
          <small>*Note that placing a bet for your preferred candidate is just to encourage you to go out and vote. Going out to vote increases your chance of winning with your preferred candidate </small>
          <v-checkbox v-validate="'required:true'" v-model="bet.check" data-vv-scope="addbet" name="terms and conditions" type="checkbox" label="Do you agree not to have an ulterior motive other than the statement made above as a reason for placing a bet?"></v-checkbox>
          <v-alert :value="errors.has('addbet.terms and conditions')" type="error">{{ errors.first('addbet.terms and conditions') }}</v-alert>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn flat @click.native="addBet_dialog = false">Close</v-btn>
          <v-btn color="green" class="white--text" @click="submitBet">Submit</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
      <v-footer absolute color="green"></v-footer>
    </v-content>
  </v-app>
</template>

<script>
import paystack from 'vue-paystack';
  export default {
    components: {
        paystack
    },
    data() {
        return {
            paystackkey: "pk_test_8e49810c12ca188a313ff2340e5d4d7113fd1acf", //paystack public key
            fund: {
            real_amount: 0,
            //email: userData.user.email, // Customer email
            //amount: real_amount * 100,  // in kobo
            },
            panel:[],
            bet: {
             check: '',
             candidate: '',
             category: '',
             amount: '',
            },
            betfriend: {
             friend_id: '',
             check: '',
             candidate: '',
             category: '',
             amount: '',
            },
      options: {
          category: [{
              text:'Presidential Election',
              value: 1 }, {
              text:'Lagos State Governorship Election',
              value: 2
           }],
           candidate: [{
               text: 'Muhammadu Buhari (APC)',
               value: 1,
               dependency: 1 }, {
               text: 'Atiku Abubakar (PDP)',
               value: 2,
               dependency: 1 }, {
               text: 'Jide Sanwo-olu (APC)',
               value: 3,
               dependency: 2 }, {
               text: 'Jimi Agbaje (PDP)',
               value: 4,
               dependency: 2
           }]
      },
      addBet_dialog: false,
      betFriends_dialog: false,
      fundAccount_dialog: false,
      info: false,
      infotext: '',
      dialog: false,
      disable: false,
      drawer: null,
      infostatus: this.$route.query.info
     }
    },
    props: {},

    created() {
     this.fetchData();
     this.checkInfo();
    },

    watch: {
        'addBetFriendStatus': function(){
         if(this.addBetFriendStatus == 2){
             this.addBet_dialog = false;
             this.dialog = false;
             this.infotext = this.$store.getters.getAddBetFriendMessage;
             this.info = true;
         }
         else if (this.addBetFriendStatus == 3){
           this.dialog = false;
           if (this.$store.getters.getAddBetFriendMessage) {
           this.infotext = this.$store.getters.getAddBetFriendMessage
           this.info = true;
           }
           else {
             this.infotext = 'An error Occured. Please check your network';
             this.info = true;
           };
         }
         else if (this.addBetFriendStatus == 1){
           this.dialog = true;
         }
       },
        'addBetStatus': function(){
         if(this.addBetStatus == 2){
             this.addBet_dialog = false;
             this.dialog = false;
             this.infotext = this.$store.getters.getAddBetMessage;
             this.info = true;
         }
         else if (this.addBetStatus == 3){
           this.dialog = false;
           if (this.$store.getters.getAddBetMessage) {
           this.infotext = this.$store.getters.getAddBetMessage
           this.info = true;
           }
           else {
             this.infotext = 'An error Occured. Please check your network';
             this.info = true;
           };
         }
         else if (this.addBetStatus == 1){
           this.dialog = true;
         }
       },
        'addFundStatus': function(){
         if(this.addFundStatus == 2){
             this.fundAccount_dialog = false;
             this.dialog = false;
             this.infotext = this.$store.getters.getAddFundMessage;
             this.info = true;
             this.$router.go();
         }
         else if (this.addFundStatus == 3){
           this.dialog = false;
           if (this.$store.getters.getAddFundMessage) {
           this.infotext = this.$store.getters.getAddFundMessage
           this.info = true;
           }
           else {
             this.infotext = 'A network error has occured . Please Contact Support';
             this.info = true;
           };
         }
         else if (this.addFundStatus == 1){
           this.dialog = true;
         }
       },
       'logoutLoadStatus': function(){
         if(this.logoutLoadStatus == 2){
            this.$router.push("/login");
         }
         else if (this.logoutLoadStatus == 3){
           this.dialog = false;
           if (this.$store.getters.getLogoutMessage) {
           this.infotext = this.$store.getters.getLogoutMessage
           this.info = true;
           }
           else {
             this.infotext = 'An error Occured. Please check your network';
             this.info = true;
           };
         }
         else if (this.logoutLoadStatus == 1){
           this.dialog = true;
         }
       },
         'userLoadStatus': function(){
         if(this.userLoadStatus == 2){
             this.dialog = false;
         }
         else if (this.userLoadStatus == 3){
           this.dialog = false;
           if (!this.$store.getters.getUserMessage) {
            this.infotext = 'An Error Occured. Please check your Network and Reload page';
             this.info = true;
           }
         }
         else if (this.userLoadStatus == 1){
           this.dialog = true;
         }
       }
   },

    computed: {
        amount(){
          let kobo = 100;
          return this.fund.real_amount * kobo ;
        },
        email(){
          let userData = this.$store.getters.getUserData;
          return userData.user.email;
        },
          reference(){
        let text = "";
        let possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for( let i=0; i < 10; i++ )
          text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
      },
        computedTotal(){
           let bet = this.$store.getters.getUserData.bet;
           let total = 0;
           bet.forEach(element => {
               total = element.amount + total;
           });
           return total;
        },
        computedWinAmount() {
            let bet = this.$store.getters.getUserData.bet;
            let userData = this.$store.getters.getUserData;
            let total = 0;
            bet.forEach(element => {
                if (element.category == 1){
              if (element.candidate == 1){
                let category_amount = userData.amount.category_one;
                let candidate_amount = userData.amount.candidate_one;
                let winamount =  Math.floor(((element.amount)/candidate_amount) * category_amount);
                total = winamount + total;
              } else if (element.candidate == 2){
                 let category_amount = userData.amount.category_one;
                let candidate_amount = userData.amount.candidate_two;
                let winamount = Math.floor(((element.amount)/candidate_amount) * category_amount);
                total = winamount + total;
              }
          } else if (element.category == 2){
            if (element.candidate == 3){
                let category_amount = userData.amount.category_two;
                let candidate_amount = userData.amount.candidate_three;
                let winamount =  Math.floor(((element.amount)/candidate_amount) * category_amount);
                total = winamount + total;
              } else if (candidate == 2){
                 let category_amount = userData.amount.category_two;
                let candidate_amount = userData.amount.candidate_four;
                let winamount =  Math.floor(((element.amount)/candidate_amount) * category_amount);
                total = winamount + total;
              }
          }
           });
           return total;
        },
        filteredFriendCandidate(){
          let candidate = this.options.candidate;
          return candidate.filter(o => o.dependency == this.betfriend.category);
        },
        filteredCandidate(){
          let candidate = this.options.candidate;
          return candidate.filter(o => o.dependency == this.bet.category);
        },
      logoutLoadStatus(){
       return this.$store.getters.getLogoutLoadStatus;
     },
      addBetFriendStatus(){
       return this.$store.getters.getAddBetFriendStatus;
     },
       addBetStatus(){
       return this.$store.getters.getAddBetStatus;
     },
       addFundStatus(){
       return this.$store.getters.getAddFundStatus;
     },
      userData(){
       return this.$store.getters.getUserData;
     },
       userLoadStatus(){
       return this.$store.getters.getUserStatus;
     },
   },

    methods: {
            callback: function(response){
                if (response.status == 'success' && response.message == 'Approved' && response.reference == response.trxref ) {
                let fundData;
                fundData = {
                   amount: this.fund.real_amount,
                   reference: response.reference,
                   transaction_id: response.transaction
                };
                this.$store.dispatch( 'addFund',
               {
               fundData
               });
                //this.$router.go();
                }
                else {
                     this.infotext = "Transaction not successful, please try again";
                     this.info = true;
                }
      },
      close: function(){
           this.infotext = "You cancelled Payment, please try again";
             this.info = true;
      },
        adjustCategory(category, candidate) {
           if (category == 1) {
               if (candidate == 1) {
                   return 'Presidential - Buhari';
               } else if (candidate == 2){
                   return 'Presidential - Atiku';
               }
           } else if (category == 2) {
               if (candidate == 3) {
                   return 'Lagos - Sanwo-Olu';
               } else if (candidate == 4) {
                   return 'Lagos - Agbaje';
               }
           }
        },
        adjustWinAmount(amount, category, candidate) {
            let userData = this.$store.getters.getUserData;
          if (category == 1){
              if (candidate == 1){
                let category_amount = userData.amount.category_one;
                let candidate_amount = userData.amount.candidate_one;
                let winamount =  Math.floor(((amount)/candidate_amount) * category_amount);
                return winamount;
              } else if (candidate == 2){
                 let category_amount = userData.amount.category_one;
                let candidate_amount = userData.amount.candidate_two;
                let winamount = Math.floor(((amount)/candidate_amount) * category_amount);
                return winamount;
              }
          } else if (category == 2){
            if (candidate == 3){
                let category_amount = userData.amount.category_two;
                let candidate_amount = userData.amount.candidate_three;
                let winamount =  Math.floor(((amount)/candidate_amount) * category_amount);
                return winamount;
              } else if (candidate == 2){
                 let category_amount = userData.amount.category_two;
                let candidate_amount = userData.amount.candidate_four;
                let winamount =  Math.floor(((amount)/candidate_amount) * category_amount);
                return winamount;
              }
          }
        },
        adjustPlacedBy(placed_by, placed_for){
          if (placed_by == placed_for) {
              return 'YOU';
          }
          else {
              return placed_by;
          }
        },
        adjustId(id){
           var text = '';
           var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
           for (var i = 0; i < 6; i++)
           text += possible.charAt(Math.floor(Math.random() * possible.length));
           return text + id;
        },
        addBet() {
            this.addBet_dialog = true;
          console.log('add bet');
        },

        submitBet() {
        this.$validator.validateAll('addbet').then(result => {
        if (result) {
              let betData;
              betData = this.bet;
               this.$store.dispatch( 'addBet',
               {
               betData
               }
               );
          console.log('submit bet');
        }
        })
        },

         submitFriendBet() {
         this.$validator.validateAll('addfriendbet').then(result => {
         if (result) {
              let betData;
              betData = this.betfriend;
               this.$store.dispatch( 'addBetFriend',
               {
               betData
               }
               );
          console.log('submit friend bet');
        }
        })
        },


        withdrawWins() {
          console.log(' withdraw wins');
        },

        betFriends() {
            this.betFriends_dialog = true;
          console.log('bet for friends');
        },

        fundAccount() {
            this.fundAccount_dialog = true;
          console.log('fund account');
        },

        checkInfo(){
            if (this.$store.getters.getLoginLoadStatus == 2 && this.infostatus == 0 ){
                this.infotext = this.$store.getters.getLoginMessage;
                this.info = true;
            };
         },
       fetchData() {
         this.$store.dispatch( 'getUser');
       },
        login() {
          this.$router.push('/login');
        },
        register() {
          this.$router.push('/register');
        },
        home() {
          this.$router.push('/');
        },
        logout() {
          this.$store.dispatch( 'logoutUser');
        },
    }
  }
</script>

