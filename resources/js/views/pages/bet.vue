<template>
<v-app>
    <v-toolbar fixed>
        <v-toolbar-side-icon @click.stop="drawer = !drawer"></v-toolbar-side-icon>
         <v-toolbar-title class="green--text darken-1">Polibet</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn class="white--text" color='green' v-if="auth">
              Bal - N{{ user.account.balance }}.00
         </v-btn>
       </v-toolbar>

<v-container mt-5>
       <v-card>
          <v-card-title>Event Name - {{data.name}} <br/> Bet Description - {{ data.summary }}</v-card-title>
          <v-divider></v-divider>
          <v-list dense>
             <v-list-tile>
              <v-list-tile-content>Bet Status :</v-list-tile-content>
              <v-list-tile-content class="align-end">
                   <span v-if="expired == true || time <= 0 || ( data.maximum_part != null && data.maximum_part <= data.count )|| data.status == 'closed'" class="blue lighten-3"><i>Closed</i></span>
                  <span v-else-if="data.status == 'pending'" class="yellow lighten-3"><i>Pending Approval</i></span>
                  <span v-else-if="data.status == 'approved' && time > 0 && expired == false" class="green lighten-3"><i>Active</i></span>
                  <span v-else-if="data.status == 'rejected' && expired != true && time > 0" class="red lighten-3"><i>Rejected</i></span>
                  <span v-else-if="data.status == 'concluded' && expired != true && time > 0" class="purple lighten-3"><i>Concluded</i></span>

              </v-list-tile-content>
            </v-list-tile>
            <v-list-tile class="grey lighten-3">
              <v-list-tile-content>Bet Master:</v-list-tile-content>
              <v-list-tile-content class="align-end">{{ data.bet_master }}</v-list-tile-content>
            </v-list-tile>
             <v-list-tile>
              <v-list-tile-content> Bet Master Reputation:</v-list-tile-content>
              <v-list-tile-content class="align-end">{{ data.user_rep }}%</v-list-tile-content>
            </v-list-tile>
            <v-list-tile class="grey lighten-3">
              <v-list-tile-content>Expires in:</v-list-tile-content>
              <v-list-tile-content class="align-end">
               <countdown ref="countdown" :emit-events=true v-if="expired == false && time > 0" :time="time" @end="endCount" :auto-start="false">
                <template slot-scope="props">{{ props.days }} days, {{ props.hours }} hours, {{ props.minutes }} minutes, {{ props.seconds }} seconds.</template>
                </countdown>
                <span v-if="expired == true || time < 0"><i>bet expired</i></span>
                </v-list-tile-content>
            </v-list-tile>
            <v-list-tile >
              <v-list-tile-content>Outcome Date:</v-list-tile-content>
              <v-list-tile-content class="align-end">{{ computedOutcome }}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile class="grey lighten-3">
              <v-list-tile-content>Total Predicts:</v-list-tile-content>
              <v-list-tile-content class="align-end">
                  <span v-if="data.count">{{ data.count }}</span>
                  <span v-else>Nil</span>
               </v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content>Total Amount staked:</v-list-tile-content>
              <v-list-tile-content class="align-end">
                  <span v-if="data.total_amount">{{ data.total_amount }}</span>
                  <span v-else>Nil</span>
               </v-list-tile-content>
            </v-list-tile>
            <v-list-tile class="grey lighten-3">
              <v-list-tile-content>Number of options:</v-list-tile-content>
              <v-list-tile-content class="align-end"><span v-if="data.options">{{ data.options.length }}</span></v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content>Minimum stake amount:</v-list-tile-content>
              <v-list-tile-content class="align-end"> N{{data.minimum_stake }}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile class="grey lighten-3">
              <v-list-tile-content>Maximum Predicts:</v-list-tile-content>
              <v-list-tile-content class="align-end">
                  <span v-if="data.maximum_part">{{ data.maximum_part }}</span>
                  <span v-else>Nil</span>
               </v-list-tile-content>
            </v-list-tile>
             <v-list-tile>
              <v-list-tile-content> Decided Outcome:</v-list-tile-content>
               <v-list-tile-content class="align-end">
                  <span v-if="data.outcome">{{ data.outcome }}</span>
                  <span v-else>Nil</span>
               </v-list-tile-content>
            </v-list-tile>
             <v-list-tile class="grey lighten-3">
              <v-list-tile-content>Decided Date:</v-list-tile-content>
               <v-list-tile-content class="align-end">
                  <span v-if="data.decided_date">{{ computedDecided }}</span>
                  <span v-else>Nil</span>
               </v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content>Outcome Decided by:</v-list-tile-content>
              <v-list-tile-content class="align-end">
                  <span v-if="data.decided_by">{{ data.decided_by }}</span>
                  <span v-else>Nil</span>
               </v-list-tile-content>
            </v-list-tile>
            <v-list-tile class="grey lighten-3">
              <v-list-tile-content> Decided Outcome Status:</v-list-tile-content>
             <v-list-tile-content class="align-end">
                  <span v-if="data.outcome_status">{{ data.outcome_status }}</span>
                  <span v-else>Nil</span>
               </v-list-tile-content>
            </v-list-tile>
          </v-list>
        </v-card>
         <v-layout row v-if="(data.status == 'approved' || data.status == 'pending' ) && time > 0 && expired == false && (data.maximum_part == null || data.maximum_part > data.count)">
            <v-flex xs5 sm4>
                <v-btn flat @click="calculateWin" outline color="green">Calculate Win</v-btn>
            </v-flex>
             <v-flex xs3 sm4>
             <v-btn @click="addBet" class="white--text" color="green">Bet</v-btn>
            </v-flex>
             <Shares :bet="data"></Shares>
         </v-layout><br/>
         <span v-if="(userbet != undefined || data.user_id == user.id) && auth">
         <span v-if="(time < 0 || expired == true) && data.status != 'concluded' && outcome > 0">
            <v-container>
                <v-card >
                    <v-card-text class="layout justify-center">
                        Awaiting Outcome Date ...
                    </v-card-text>
                </v-card>
            </v-container>
         </span>
         <span v-else-if="(time < 0 || expired == true) && data.status != 'concluded' && data.outcome == null">
            <span v-if="data.user_id == user.id && outcome >= -86400000">
                <v-card>
                 <v-layout row>
                <v-flex offset-xs3 xs6 offset-sm5 sm2>
                    <v-btn @click="submit_dialog = true" flat outline color="green">Submit Outcome</v-btn>
                </v-flex>
            </v-layout>
                </v-card>
                <v-card>
                    <v-card-text class="layout justify-center">
                        You have 24 hours to submit an outcome or risk a reduction in your reputation.
                    </v-card-text>
                </v-card>
            </span>
            <span v-else>
                <span v-if="outcome >= -86400000">
                <v-container>
                <v-card>
                    <v-card-text>
                        Awaiting outcome from bet master. You or any of the other participants would only be allowed to submit an outcome
                        if the bet master fails to submit an outcome in 24 hours.
                    </v-card-text>
                </v-card>
                </v-container>
                </span>
                <span v-else-if="data.user_id != user.id && outcome < -86400000">
                    <v-card>
                    <v-layout row>
                <v-flex offset-xs3 xs6 offset-sm5 sm2>
                    <v-btn @click="submit_dialog = true" flat outline color="green">Submit Outcome</v-btn>
                </v-flex>
            </v-layout>
                    </v-card>
                <v-card>
                    <v-card-text class="layout justify-center">
                              The bet Master failed to submit an outcome, You or any other participant can go ahead to submit an outcome.
                    </v-card-text>
                </v-card>
                    </span>
            </span>
     </span>
     <span v-else-if="(accept == undefined || dispute == undefined) && data.status != 'concluded' && data.outcome && data.decidedby_id != user.id && decideDate > -86400000">
             <v-card>
              <v-layout row wrap>
            <v-flex xs6 offset-sm3 sm2>
                <v-btn small @click="accept_dialog = true" flat outline color="green">Accept Outcome</v-btn>
            </v-flex>
            <v-flex xs6 offset-sm3 sm2>
                <v-btn small @click="dispute_dialog = true" flat outline color="green">Dispute Outcome</v-btn>
            </v-flex>
    </v-layout>
             </v-card>
             <v-card>
                 <v-card-text class="layout justify-center">
                     You can either choose to accept or dispute this outcome. You or other participants have 24 hours to dispute this outcome or it would be marked as accepted
                 </v-card-text>
             </v-card>
     </span>
     <span v-else-if="dispute != undefined">
             <v-card @click="this.$router.push(`/dispute/${dispute.random}`)">
                 <v-card-text class="layout justify-center">
                     Dispute status - {{dispute.status}}
                 </v-card-text>
             </v-card>
     </span>
         </span>
</v-container>
       <v-footer class="elevation-3" color="green darken-2" height="auto">
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
        &copy;2019 — <strong>Polibet</strong>
      </v-flex>
      </v-layout>
          <v-layout row wrap align-center>
          <v-flex xs12>
            <div class="white--text ml-3 text-xs-center">
              Made with
              <v-icon class="white--text">favorite</v-icon>
              by <a class="white--text" href="http://codebator.me" target="_blank">CodeBator</a>
            </div>
          </v-flex>
        </v-layout>
      </v-footer>
       <v-navigation-drawer
      v-model="drawer"
      fixed
      temporary
      height="400px"
    >
      <v-list class="pt-0" dense>
          <v-list-tile @click="home">
          <v-list-tile-action>
            <v-icon color="green">home</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>Home</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>

        <v-list-tile v-if="auth" @click="dashboard">
          <v-list-tile-action>
            <v-icon color="green">dashboard</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>Dashboard</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>

        <v-list-tile v-if="auth" @click="fundAccount_dialog = true">
          <v-list-tile-action>
            <v-icon color="green">credit_card</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>Fund Account</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>

        <v-list-tile v-if="auth" @click="logout">
          <v-list-tile-action>
            <v-icon color="red">logout</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>Logout</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>

        <v-list-tile v-if="!auth" @click="login">
          <v-list-tile-action>
            <v-icon color="green">lock_open</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>Login</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>

        <v-list-tile v-if="!auth" @click="register">
          <v-list-tile-action>
            <v-icon color="green">create</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>Register</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>
      </v-list>
    </v-navigation-drawer>

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
          Please wait
          <v-progress-linear
            indeterminate
            color="white"
            class="mb-0"
          ></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>

     <v-dialog
      v-model="infofund"
      max-width="350"
    >
     <v-card>
        <v-card-title class="headline">Insufficient Funds. Fund Account now?</v-card-title>
        <div>
        <v-card-actions>
        <v-btn flat @click.native="infofund = false">No</v-btn>
        <v-spacer></v-spacer>
          <v-btn color="green" class="white--text" @click="fundAccount_dialog = true; infofund = false;">Yes</v-btn>
        </v-card-actions>
        </div>
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
      v-model="accept_dialog"
      max-width="290"
    >
     <v-card>
        <v-card-title class="headline">Do you agree with the submitted outcome ?</v-card-title>
        <v-spacer></v-spacer>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn flat @click.native="accept_dialog = false">No</v-btn>
          <v-btn color="green" :disabled="dialog" :loading="dialog" class="white--text" @click="acceptOutcome">Yes</v-btn>
        </v-card-actions>
     </v-card>
    </v-dialog>

    <v-dialog
        v-model="addBet_dialog"
        persistent
        max-width="500px">
      <v-card>
        <v-card-title>
          <span class="headline">Place Bet</span>
        </v-card-title>
        <v-card-text>
          <v-container grid-list-md>
            <v-layout wrap>
                <v-text-field label="Stake Amount" v-model="bet.amount" data-vv-scope="addbet" name="Amount" v-validate="`required|numeric|min_value:${data.minimum_stake}`"></v-text-field>
                <span :value="errors.has('addbet.Amount')" style="color:red">{{ errors.first('addbet.Amount') }}</span>
                <v-select
                :items="filteredOptions"
                name="Option"
                data-vv-scope="addbet"
                v-validate="'required'"
                label="Option"
                v-model="bet.option"
                ></v-select>
                <span :value="errors.has('addbet.Option')" style="color:red">{{ errors.first('addbet.Option') }}</span>
            </v-layout>
          </v-container>
          <small>*Before placing bets, Do make sure all possible options are covered and the outcome is verifiable incases of disputes. Where its impossible for outcome to be verified, check your bet master's reputation or be sure enough your bet master cannot be compromised.</small>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn flat @click.native="addBet_dialog = false">Close</v-btn>
          <v-btn color="green" :disabled="dialog" :loading="dialog" class="white--text" @click="submitBet">Submit</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog
      v-model="calculateWin_dialog"
    >
    <v-card>
        <v-card-title>
          <span class="headline">Calculate present possible win</span>
        </v-card-title>
        <v-card-text>
          <v-container grid-list-md>
            <v-layout wrap>
                <v-text-field label="Stake Amount" v-model="calculate.amount" data-vv-scope="calculate" name="Amount" v-validate="`required|numeric|min_value:${data.minimum_stake}`"></v-text-field>
                <span :value="errors.has('calculate.Amount')" style="color:red">{{ errors.first('calculate.Amount') }}</span>
                <v-select
               :items="filteredOptions"
                name="Option"
                data-vv-scope="calculate"
                v-validate="'required'"
                label="Option"
                v-model="calculate.option"
                ></v-select>
                <span :value="errors.has('calculate.Option')" style="color:red">{{ errors.first('calculate.Option') }}</span>
                <span class="headline">Win amount: N{{ this.calculate.win_amount }}</span>
            </v-layout>
             <small>*Note that your possible win amount changes and its not determined or influenced by polibet. It is calculated as a percentage of your stake amount against the total amount. Share bet with opposing friends to increase wins !! </small>
          </v-container>
        </v-card-text>
        <v-card-actions>
                <v-btn flat @click="calculateWin_dialog = false">Close</v-btn>
                <v-spacer></v-spacer>
                <v-btn color="green" class="white--text" @click="calculated">Calculate</v-btn>
        </v-card-actions>
    </v-card>

     </v-dialog>

     <v-dialog
      v-model="dispute_dialog"
    >
    <v-card>
        <v-card-title>
          <span class="headline">Create a dispute</span>
        </v-card-title>
        <v-card-text>
          <v-container grid-list-md>
            <v-layout wrap>
                <v-text-field label="Reason for dispute" v-model="dispute_data.reason" data-vv-scope="dispute" name="Reason" v-validate="'required'"></v-text-field>
                <span :value="errors.has('dispute.Reason')" style="color:red">{{ errors.first('dispute.Reason') }}</span>
            </v-layout>
             <small>*Note that you might be asked to back up your claims with verifiable proofs.</small>
          </v-container>
        </v-card-text>
        <v-card-actions>
                <v-btn flat @click="dispute_dialog = false">Close</v-btn>
                <v-spacer></v-spacer>
                <v-btn color="green" class="white--text" @click="disputeOutcome">Submit</v-btn>
        </v-card-actions>
    </v-card>

     </v-dialog>

     <v-dialog
      v-model="submit_dialog"
    >
    <v-card>
        <v-card-title>
          <span class="headline">Submit Bet outcome</span>
        </v-card-title>
        <v-card-text>
          <v-container grid-list-md>
            <v-layout wrap>
                <v-select
               :items="filteredOptions"
                name="Option"
                data-vv-scope="outcome"
                v-validate="'required'"
                label="Option"
                v-model="outcome_data.option"
                ></v-select>
                <span :value="errors.has('outcome.Option')" style="color:red">{{ errors.first('outcome.Option') }}</span>
            </v-layout>
             <small>*Please confirm that you are submitting the right outcome. Other participants would be allowed to create a dispute. Your outcome would become accepted if not disputed within 24 hours or immediately all participants have choosen to accept.</small>
          </v-container>
        </v-card-text>
        <v-card-actions>
                <v-btn flat @click="submit_dialog = false">Close</v-btn>
                <v-spacer></v-spacer>
                <v-btn color="green" class="white--text" @click="submitOutcome">Submit</v-btn>
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

          <v-btn @click="paystack_dialog = true" :disabled="paystack_dialog" :loading="paystack_dialog" color="green" class="white--text fas fa-money-bill-alt">Make Payment</v-btn>
       </paystack>

        </v-card-actions>
      </v-card>
    </v-dialog>
</v-app>
</template>
<script>
import paystack from 'vue-paystack';
import Shares from './shares.vue';
//var moment = require('moment-timezone');
 moment.updateLocale('en', {
            calendar : {
            lastDay : '[Yesterday at] LT',
                sameDay : '[Today at] LT',
                nextDay : '[Tomorrow at] LT',
                lastWeek : 'll [at] LT',
                nextWeek : 'll [at] LT',
                sameElse : 'll [at] LT'
            }
        });
export default {
     components: {
        paystack,
        Shares
    },
     data() {
            return {
                 paystackkey: "pk_test_5d79171e2fca58569a41602a1fdd4887daa4e8f0", //paystack public key
            fund: {
            real_amount: '',
            },
                accept_data: {
             custom_bet : '',
            },
             outcome_data: {
             option: '',
             custom_bet : '',
            },
            dispute_data: {
             reason: '',
             custom_bet : '',
            },
                calculate: {
         amount: '',
         option: '',
         win_amount: 0
        },
                bet: {
             option: '',
             amount: '',
             custom_bet : '',
            },
            infofund: false,
            paystack_dialog: false,
            fundAccount_dialog: false,
                addBet_dialog: false,
                submit_dialog: false,
                dispute_dialog: false,
                accept_dialog: false,
                calculateWin_dialog: false,
                expired: false,
                id:this.$route.params.id,
                data: '',
                user: '',
                accept: undefined,
                dispute: undefined,
                userbet: undefined,
                auth: '',
                dialog: false,
               info: false,
               infotext: '',
               disable: false,
              drawer: null,
            }
        },
        metaInfo () {
      return {
        //links: [
        //{rel: 'canonical', href: 'https://www.polibet.ng'}
        //],
        title: `Bet-${this.data.random}`,
        titleTemplate: '%s | Polibet',
        meta: [
        //{ vmid: 'description', name: 'description', content: `Bet${this.description}` },
           // OpenGraph data (Most widely used)
        {property: 'og:title', content: `A Bet on ${this.data.name}`},
        {property: 'og:site_name', content: 'Polibet'},
        // The list of types is available here: http://ogp.me/#types
        {property: 'og:type', content: 'website'},
        // Should the the same as your canonical link, see below.
        {property: 'og:url', content: `http://beta.polibet.ng/bet/${this.data.random}`},
        {property: 'og:image', content: 'http://beta.polibet.ng/images/favi/pb.png'},
        // Often the same as your meta description, but not always.
        {property: 'og:description', content: this.data.summary}
            ]
        }
    },
        mounted(){
            this.getPost();
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
   },



        computed: {
             amount(){
          let kobo = 100;
          return this.fund.real_amount * kobo ;
        },
        email(){

          return this.user.email;

        },
          reference(){
        let text = "";
        let possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for( let i=0; i < 10; i++ )
          text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
      },
             filteredOptions(){
                 function getValue(n) {
                return { text: n.value,
              value: n.id };
                }
          return _.map(this.data.options, getValue);
        },
             logoutLoadStatus(){
       return this.$store.getters.getLogoutLoadStatus;
     },
             computedDecided() {
            const then = moment.utc(this.data.decided_date).local()
            return then.calendar();
        },
              computedOutcome() {
            const then = moment.utc(this.data.outcome_date).local()
            return then.calendar();
        },
        decideDate(){
            if (this.data.decided_date) {
               var now = moment().format('x');
               //var expire = new Date(now.getFullYear() + 1, 0, 1);
               var expire = moment.utc(this.data.decided_date).local().format('x');
               console.log(expire - now);
               var data = expire - now;
               return data;
            }
           },
            outcome(){
               var now = moment().format('x');
               //var expire = new Date(now.getFullYear() + 1, 0, 1);
               var expire = moment.utc(this.data.outcome_date).local().format('x');
               console.log(expire - now);
               var data = expire - now;
               return data;
           },
           time(){
               var now = moment().format('x');
               //var expire = new Date(now.getFullYear() + 1, 0, 1);
               var expire = moment.utc(this.data.close_date).local().format('x');
               console.log(expire - now);
               var data = expire - now;
               return data;
           }
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
            submitOutcome(){
                this.$validator.validateAll('outcome').then(result => {
        if (result) {
            this.dialog = true;
            this.outcome_data.custom_bet = this.data.id
              axios.post('/api/submit-outcome', this.outcome_data ).then(response =>  {
                     console.log(response.data);
                     this.dialog = false;
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
            acceptOutcome(){
            this.dialog = true;
            this.accept_data.custom_bet = this.data.id
              axios.post('/api/accept-outcome', this.accept_data ).then(response =>  {
                     console.log(response.data);
                     this.dialog = false;
                      this.infotext = response.data.message;
                    this.info = true;
                }).catch(error => {
                    this.dialog = false;
                    this.infotext = error.response.data.message;
                    this.info = true;
                });
            },
            disputeOutcome(){
                   this.$validator.validateAll('dispute').then(result => {
        if (result) {
            this.dialog = true;
            this.dispute_data.custom_bet = this.data.id
              axios.post('/api/create-dispute', this.dispute_data ).then(response =>  {
                     console.log(response.data);
                     this.dialog = false;
                      this.infotext = response.data.message;
                    this.info = true;
                     this.$router.push(`/dispute/${response.data.random}`);
                }).catch(error => {
                    this.dialog = false;
                    this.infotext = error.response.data.message;
                    this.info = true;
                });
        }
        })
            },
             calculated(){
            this.$validator.validateAll('calculate').then(result => {
        if (result) {


          let option = _.find(this.data.options, { 'id': this.calculate.option });
          if (option) {
               let total_amount = 0;
              let option_amount = 0;
              let udid = this.data.total_amount;
              let amount = this.calculate.amount;
              if (this.data.total_amount > 0) {
                  total_amount = parseInt(this.data.total_amount) + parseInt(amount);
                  console.log(total_amount);
              }
              else  {
                total_amount = amount;
              }
              if (option.total_amount != null) {
                  option_amount = parseInt(option.total_amount) + parseInt(amount);
                  console.log(option_amount);
              }
              else {
                  option_amount = amount;
              }
                this.calculate.win_amount = Math.floor(amount / option_amount * total_amount);
          }
          }
        })
        },
        calculateWin(){
            this.calculateWin_dialog = true;
        },
            submitBet() {
        this.$validator.validateAll('addbet').then(result => {
        if (result) {
            this.dialog = true;
            this.bet.custom_bet = this.data.id
            if (this.user.account.balance != null) {
            if (this.user.account.balance >= this.bet.amount) {
              axios.post('/api/add-bet', this.bet ).then(response =>  {
                     console.log(response.data);
                     this.data = response.data.bet;
                     this.user.account.balance -= this.bet.amount;
                     this.bet = {
                         option: '',
                        amount: '',
                        custom_bet : '',
                     };

                     this.addBet_dialog = false;
                     this.dialog = false;
                      this.infotext = response.data.message;
                    this.info = true;
                }).catch(error => {
                    this.dialog = false;
                    this.infotext = error.response.data.message;
                    this.info = true;
                });
        }  else {
             this.dialog = false;
             this.infofund = true;
        }
        }
        }
        })
        },
            addBet() {
            if (this.auth) {
            this.addBet_dialog = true;
            } else {
                this.$router.push({ path: '/login', query: { redirect: `/bet/${this.data.random}` }});
            }
        },
            endCount(data) {
                 this.expired = true;
            console.log('yes');
          },
            getPost() {
                let id = this.$route.params.id;
                 axios.get('/api/get-bet/' + id).then(response =>  {
                     console.log(response.data);
                     this.data = response.data.message;
                     this.auth = response.data.authenticated;
                     if (response.data.user){
                     this.user = response.data.user;
                     }
                     this.dispute = _.find(this.data.options, function(o) { return o.userdispute != 0; });
                     this.accept = _.find(this.data.options, function(o) { return o.useraccept != 0; });
                     this.userbet = _.find(this.data.options, function(o) { return o.userbets != 0; });
                     this.$refs.countdown.start();
                }).catch(error => {
                    //this.$router.push(`/`);
                });
            },
              login() {
             this.dialog = true;
                this.$router.push({ path: '/login', query: { redirect: `/bet/${this.data.random}` }});
                },
                register() {
                    this.dialog = true;
                this.$router.push('/register');
                },
                home() {
                    this.dialog = true;
                this.$router.push('/');
                },
                logout() {
                    this.dialog = true;
                this.$store.dispatch( 'logoutUser');
                },
                dashboard() {
          this.dialog = true;
          this.$router.push('/dashboard');
        },
        }

}
</script>



