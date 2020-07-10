<template>
  <v-app id="inspire">
    <v-content>
        <v-toolbar fixed>
         <v-toolbar-side-icon @click.stop="drawer = !drawer"></v-toolbar-side-icon>
        <v-toolbar-title class="green--text">Polibet</v-toolbar-title>
         <v-spacer></v-spacer>
          <v-btn class="white--text" color='green' v-if="userData.user.account.balance != null">
              Bal - N{{ userData.user.account.balance }}.00
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
          <v-list-tile-avatar color="grey" v-if="userData.user.profile">
            {{ userData.user.profile.full_name.slice(0,2) }}
          </v-list-tile-avatar>

          <v-list-tile-content>
            <v-list-tile-title v-if="userData.user.profile">{{ userData.user.profile.full_name }}</v-list-tile-title>
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

         <v-list-tile @click="$router.push('/create-bet')">
          <v-list-tile-action>
            <v-icon color="green">create</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>Create Bet</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>

         <v-list-tile @click="editprofile_dialog =true">
          <v-list-tile-action>
            <v-icon color="green">person</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>Edit Profile</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>

         <v-list-tile @click="verifyAccount" v-if="userData.user.status == 'pending_activation' && showverify == true">
          <v-list-tile-action>
            <v-icon color="green">credit_card</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>Verify Account</v-list-tile-title>
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

        <v-list-tile @click="withdrawWins">
          <v-list-tile-action>
            <v-icon color="red">redeem</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>Withdraw</v-list-tile-title>
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
        <div slot="header">Placed Bets</div>
           <v-card>
            <v-list dense v-if="userData.bet" v-for="bet in userData.bet" :key="bet.id">
            <span @click="$router.push(`/bet/${bet.custom_bet.random}`)">
                <v-list-tile class="grey lighten-3">
              <v-list-tile-content> <h4>Bet id:</h4></v-list-tile-content>
              <v-list-tile-content class="align-end">{{bet.random}}</v-list-tile-content>
            </v-list-tile>
                <v-list-tile class="grey lighten-3">
              <v-list-tile-content> <h4>Amount Staked:</h4></v-list-tile-content>
              <v-list-tile-content class="align-end">N{{bet.amount}}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile class="grey lighten-3">
              <v-list-tile-content> <h4>Present Win amount:</h4></v-list-tile-content>
              <v-list-tile-content v-if="bet.win_amount" class="align-end">N{{bet.win_amount}}.00</v-list-tile-content>
              <v-list-tile-content v-else class="align-end">N{{ Math.floor(bet.amount / bet.option.total_amount * bet.custom_bet.total_amount) }}</v-list-tile-content>
            </v-list-tile>
             <v-list-tile class="grey lighten-3">
              <v-list-tile-content> <h4>Status :</h4></v-list-tile-content>
              <v-list-tile-content class="align-end">{{ bet.status }}</v-list-tile-content>
            </v-list-tile>
            <v-divider></v-divider>
            </span>
            </v-list>
            <v-card-text v-else class="grey lighten-3">No Placed bets yet</v-card-text>
        </v-card>
      </v-expansion-panel-content>

       <v-expansion-panel-content
      >
        <div slot="header">Created Bets</div>
           <v-card>
            <v-list dense v-if="userData.user.custom_bets" v-for="bet in userData.user.custom_bets" :key="bet.id">
                <span @click="$router.push(`/bet/${bet.random}`)">
                <v-list-tile class="grey lighten-3">
              <v-list-tile-content> <h4>Bet id:</h4></v-list-tile-content>
              <v-list-tile-content class="align-end">{{bet.random}}</v-list-tile-content>
            </v-list-tile>
             <v-list-tile>
              <v-list-tile-content>Total Predicts for None Winning:</v-list-tile-content>
              <v-list-tile-content class="align-end">{{ bet.count }}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile class="grey lighten-3">
              <v-list-tile-content> <h4>Status :</h4></v-list-tile-content>
              <v-list-tile-content class="align-end">{{ bet.status }}</v-list-tile-content>
            </v-list-tile>
            <v-divider></v-divider>
                </span>
            </v-list>
             <v-card-text v-else class="grey lighten-3">No Bet created yet</v-card-text>
        </v-card>
      </v-expansion-panel-content>

      <v-expansion-panel-content
      >
        <div slot="header">Disputes</div>
           <v-card>
            <v-list dense v-if="userData.user.disputes" v-for="dispute in userData.disputes" :key="dispute.id">
            <span @click="$router.push(`/dispute/${dispute.random}`)">
                <v-list-tile class="grey lighten-3">
              <v-list-tile-content> <h4>Bet id:</h4></v-list-tile-content>
              <v-list-tile-content class="align-end">{{dispute.random}}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content>Total Predicts for None Winning:</v-list-tile-content>
              <v-list-tile-content class="align-end">{{ dispute.count }}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile class="grey lighten-3">
              <v-list-tile-content> <h4>Status :</h4></v-list-tile-content>
              <v-list-tile-content class="align-end">{{ dispute.status }}</v-list-tile-content>
            </v-list-tile>
            <v-divider></v-divider>
            </span>
            </v-list>
             <v-card-text v-else class="grey lighten-3">No dispute opened yet</v-card-text>
        </v-card>
      </v-expansion-panel-content>

       <v-expansion-panel-content
      >
        <div slot="header">Account Summary</div>
           <v-card>
            <v-card-title><h4>Account Balance</h4></v-card-title>
            <v-list dense v-if="userData.user.account">
                <v-list-tile class="grey lighten-3">
              <v-list-tile-content> <h4>Amount withdrawable:</h4></v-list-tile-content>
              <v-list-tile-content class="align-end">N{{userData.user.account.balance}}.00</v-list-tile-content>
            </v-list-tile>
            <v-list-tile class="grey lighten-3">
              <v-list-tile-content> <h4>Total funded :</h4></v-list-tile-content>
              <v-list-tile-content class="align-end">N{{userData.user.account.fund}}.00</v-list-tile-content>
            </v-list-tile>
             <v-list-tile class="grey lighten-3">
              <v-list-tile-content> <h4>Total wins :</h4></v-list-tile-content>
              <v-list-tile-content class="align-end">N{{userData.user.account.win}}.00</v-list-tile-content>
            </v-list-tile>
             <v-list-tile class="grey lighten-3">
              <v-list-tile-content> <h4>Total withdawals :</h4></v-list-tile-content>
              <v-list-tile-content class="align-end">N{{userData.user.account.withdrawal}}.00</v-list-tile-content>
            </v-list-tile>
            </v-list>
        </v-card>
      </v-expansion-panel-content>
       <v-expansion-panel-content
      >
        <div slot="header">Withdrawals</div>
        <v-card>
            <div v-if="userData.user.withdrawals != 0" v-for="withdrawal in userData.user.withdrawals" :key="withdrawal.id">
            <v-list-tile >
              <v-list-tile-content>Withdrawal ID:</v-list-tile-content>
              <v-list-tile-content class="align-end">{{withdrawal.id}}</v-list-tile-content>
            </v-list-tile>
              <v-list-tile >
              <v-list-tile-content>Amount:</v-list-tile-content>
              <v-list-tile-content class="align-end">N{{ withdrawal.amount }}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile >
              <v-list-tile-content>Status:</v-list-tile-content>
              <v-list-tile-content class="align-end"> {{ withdrawal.status }}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content>Request Date:</v-list-tile-content>
              <v-list-tile-content class="align-end" > {{ withdrawal.created_at }}</v-list-tile-content>
            </v-list-tile>
            <v-divider></v-divider>
            </div>
          <v-card-text v-else class="grey lighten-3">No withdrawals yet</v-card-text>
        </v-card>
      </v-expansion-panel-content>
       <v-expansion-panel-content
      >
      <div slot="header">Funds</div>
      <v-card>
            <div  v-if="userData.user.funds != 0" v-for="fund in userData.user.funds" :key="fund.id">
            <v-list-tile >
              <v-list-tile-content>Fund ID:</v-list-tile-content>
              <v-list-tile-content class="align-end">{{fund.id}}</v-list-tile-content>
            </v-list-tile>
              <v-list-tile >
              <v-list-tile-content>Amount:</v-list-tile-content>
              <v-list-tile-content class="align-end">N{{ fund.amount }}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content>Fund Date:</v-list-tile-content>
              <v-list-tile-content class="align-end" > {{ fund.created_at }}</v-list-tile-content>
            </v-list-tile>
            <v-divider></v-divider>
            </div>
             <v-card-text v-else class="grey lighten-3">No Fundings done yet</v-card-text>
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
                <span :value="errors.has('fund.Amount')" style="color:red">{{ errors.first('fund.Amount') }}</span>
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

          <v-btn color="green" class="white--text fas fa-money-bill-alt">Make Payment</v-btn>
       </paystack>

        </v-card-actions>
      </v-card>
    </v-dialog>

          <v-dialog
        v-model="withdrawWins_dialog"
        persistent
        max-width="500px">
      <v-card>
        <v-card-title>
          <span class="headline">Withdrawal Request</span>
        </v-card-title>
        <v-card-text>
          <v-container grid-list-md>
            <v-layout wrap>
                <v-text-field label="Amount" v-model="withdraw.amount" data-vv-scope="withdraw" name="Amount" v-validate="'required|numeric|min_value:100|max_value:200000'" hint="You cannot withdraw your bonuses"></v-text-field>
                <span :value="errors.has('withdraw.Amount')" style="color:red">{{ errors.first('withdraw.Amount') }}</span>
            </v-layout>
          </v-container>
          <span v-if="userData.user != null">Available for withdraw - N{{userData.user.account.balance}}.00</span>
        </v-card-text>
        <v-card-actions>
          <v-btn flat @click.native="withdrawWins_dialog = false">Close</v-btn>
          <v-spacer></v-spacer>
          <v-btn color="green" :disabled="dialog" :loading="dialog" class="white--text" @click="withdrawConfirmed">Withdraw</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
     <v-dialog
        v-model="editprofile_dialog"
        persistent
        max-width="500px">
      <v-card>
        <v-card-title>
          <span class="headline">Edit Profile</span>
        </v-card-title>
        <v-card-text>
          <v-container grid-list-md>
            <v-layout wrap>
                 <v-text-field v-if="userData.user.user_name" prepend-icon="work"  v-model="edit.user_name" name="Username" :placeholder="userData.user.user_name" label="Unique Username" type="text"></v-text-field>
                  <v-text-field v-else prepend-icon="work" v-model="edit.user_name" data-vv-scope="edit" v-validate="'required|min:2'"  name="Username" label="Unique Username" type="text"></v-text-field>
                  <span :value="errors.has('edit.Username')" style="color:red">{{ errors.first('edit.Username') }}</span>
                  <span v-if="!userData.user.profile.account_number && !userData.user.profile.account_name && !userData.user.profile.bank_name">
                  <v-divider></v-divider>
                  <v-card-text><b>BANK ACCOUNT INFORMATION</b></v-card-text>
                  <v-text-field prepend-icon="account_balance" label="Enter Bank Name"  v-validate="'required'" data-vv-scope="edit" v-model="edit.bank_name" name="Bank Name" type="text" ></v-text-field>
                  <span :value="errors.has('edit.Bank Name')" style="color:red">{{ errors.first('edit.Bank Name') }}</span>
                  <v-text-field prepend-icon="account_box" v-validate="'required'" v-model="edit.account_name" data-vv-scope="edit" name="Account Name" label="Account Name" type="text"></v-text-field>
                  <span :value="errors.has('edit.Account Name')" style="color:red">{{ errors.first('edit.Account Name') }}</span>
                  <v-text-field prepend-icon="account_balance_wallet" v-validate="'required|numeric|min:10|max:10'" data-vv-scope="edit" v-model="edit.account_number" name="Account Number" label="Account Number" type="text" required></v-text-field>
                  <span :value="errors.has('edit.Account Number')" style="color:red">{{ errors.first('edit.Account Number') }}</span>
                  </span>
            </v-layout>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-btn flat @click.native="editprofile_dialog = false">Close</v-btn>
          <v-spacer></v-spacer>
          <v-btn color="green" :disabled="dialog || (edit.user_name == null && edit.bank_name == null && edit.account_name == null && edit.account_number == null)" :loading="dialog" class="white--text" @click="submitEdit">Submit</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  <v-footer fixed class="elevation-3" color="white darken-2" height="auto">
          <v-layout

      justify-center
      row
      wrap
    >
           <v-flex
        white
        lighten-2
        py-3
        text-xs-center
        green--text
        xs12
      >
        &copy;2020 — <strong>Polibet</strong>
      </v-flex>
      </v-layout >

      </v-footer>
    </v-content>
  </v-app>
</template>

<script>
import paystack from 'vue-paystack';
  export default {
      metaInfo: {
         // if no subcomponents specify a metaInfo.title, this title will be used
      title: 'Dashboard',
      // all titles will be injected into this template
      titleTemplate: '%s | Polibet',
  meta: [
    // OpenGraph data (Most widely used)
    {property: 'og:title', content: 'Dashboard'},
    {property: 'og:site_name', content: 'Polibet'},
    // The list of types is available here: http://ogp.me/#types
    {property: 'og:type', content: 'website'},
    // Should the the same as your canonical link, see below.
    {property: 'og:url', content: 'http://beta.polibet.ng/create-bet'},
    {property: 'og:image', content: 'http://beta.polibet.ng/images/favi/pb.png'},
    // Often the same as your meta description, but not always.
    {property: 'og:description', content: 'Polibet allows its users to create their own bets, set their options and conditions and share.'}
  ]
},
    components: {
        paystack
    },
    data() {
        return {
            showverify: true,
            paystackkey: "pk_test_5d79171e2fca58569a41602a1fdd4887daa4e8f0", //paystack public key
            fund: {
            real_amount: 0,
            },
            panel:[],
            withdraw: {
                amount: '',
            },
      edit: {
                user_name: null,
                account_name: null,
                bank_name: null,
                account_number: null
            },
      withdrawWins_dialog: false,
      fundAccount_dialog: false,
      editprofile_dialog: false,
      info: false,
      infotext: '',
      dialog: false,
      disable: false,
      drawer: null,
     }
    },
    props: {},

    mounted() {
     this.fetchData();
    },

    watch: {
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

         'withdrawFundStatus': function(){
         if(this.withdrawFundStatus == 2){
             this.withdrawWins_dialog = false;
             this.dialog = false;
             this.infotext = this.$store.getters.getWithdrawFundMessage;
             this.info = true;
         }
         else if (this.withdrawFundStatus == 3){
           this.dialog = false;
           if (this.$store.getters.getWithdrawFundMessage) {
           this.infotext = this.$store.getters.getWithdrawFundMessage
           this.info = true;
           }
           else {
             this.infotext = 'A network error has occured . Please Contact Support';
             this.info = true;
           };
         }
         else if (this.withdrawFundStatus == 1){
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
           this.$router.go();
         }
         else if (this.userLoadStatus == 1){
           this.dialog = true;
         }
       }
   },

    computed: {
        amount(){
            let amount = this.fund.real_amount * 100;
            let percentage = this.fund.real_amount * 0.015 * 100;
            let kobo = 100 * 100;
          if (this.fund.real_amount < 2500) {
          return amount + percentage;
          }
          else {
            return amount + percentage + kobo;
          }
        },
        email(){
          let userData = this.$store.getters.getUserData;
          return userData.user.email;
          //return 'sgbhfj'
        },
          reference(){
        let text = "";
        let possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for( let i=0; i < 10; i++ )
          text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
      },
      logoutLoadStatus(){
       return this.$store.getters.getLogoutLoadStatus;
     },
       addFundStatus(){
       return this.$store.getters.getAddFundStatus;
     },
     withdrawFundStatus(){
       return this.$store.getters.getWithdrawFundStatus;
     },
      userData(){
       return this.$store.getters.getUserData;
     },
       userLoadStatus(){
       return this.$store.getters.getUserStatus;
     },
   },

    methods: {
        submitEdit() {
            this.$validator.validateAll('edit').then(result => {
        if (result) {
             this.dialog = true;
              axios.post('/api/edit-user', this.edit).then(response =>  {
                     console.log(response.data);
                     this.dialog = false;
                     this.editprofile_dialog = false;
                      this.infotext = response.data.message;
                    this.info = true;
                }).catch(error => {
                    this.dialog = false;
                    this.infotext = error.response.data.message;
                    this.info = true;
                });
        }
            })
        },
        verifyAccount() {
             this.dialog = true;
              axios.post('/api/verify-account').then(response =>  {
                     console.log(response.data);
                     this.dialog = false;
                     this.drawer = false;
                      this.infotext = response.data.message;
                    this.info = true;
                    this.showverify = false;
                }).catch(error => {
                    this.dialog = false;
                    this.infotext = error.response.data.message;
                    this.info = true;
                });
        },
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
        withdrawConfirmed(){
          this.$validator.validateAll('withdraw').then(result => {
        if (result) {
            let userData = this.$store.getters.getUserData;
             if (userData.user.account.balance != null) {
            if (userData.user.account.balance >= this.withdraw.amount) {
            let withdrawData = this.withdraw;
             this.$store.dispatch( 'withdrawFund',
               {
               withdrawData
               }
               );
            } else {
             this.infotext = 'Insufficient cleared Funds to withdraw';
             this.info = true;
        }
         }
        }
         })
        },


        withdrawWins() {
          this.withdrawWins_dialog = true;
        },
        fundAccount() {
            this.fundAccount_dialog = true;
        },
       fetchData() {
         this.$store.dispatch( 'getUser');
       },
        home() {
             this.dialog = true;
          this.$router.push('/');
        },
        logout() {
             this.dialog = true;
          this.$store.dispatch( 'logoutUser');
        },
    }
  }
</script>

