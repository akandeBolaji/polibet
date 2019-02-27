<template>
  <v-app id="inspire">
   <v-toolbar fixed>
         <v-toolbar-title class="green--text darken-1">Polibet</v-toolbar-title>
         <v-spacer></v-spacer>
         <v-toolbar-side-icon class="hidden-md-and-up" @click.stop="drawer = !drawer"></v-toolbar-side-icon>
         <div class="hidden-sm-and-down">
         <v-btn @click="dashboard" class="white--text" color="green darken-2" >Go to Dashboard</v-btn>
         <v-btn @click="logout" class="white--text" color="green darken-2" >Logout</v-btn>
         </div>
       </v-toolbar>
        <v-container mt-5 fluid>
        <v-layout>
          <v-flex xs12>
            <v-card class="elevation-12">
              <v-card-text>
                <v-form>
                    <v-card-text><b>Bet Details</b></v-card-text>
                     <v-text-field v-validate="'required'" data-vv-scope="profile" v-model="data.name" name="Bet Name"  label="Event Name" type="text"></v-text-field>
                  <span :value="errors.has('profile.Bet Name')" style="color:red">{{ errors.first('profile.Bet Name') }}</span>
                  <v-text-field v-validate="'required'" data-vv-scope="profile" v-model="data.summary" name="Bet Summary" hint="As short as possible" label="Bet Description" type="text"></v-text-field>
                  <span :value="errors.has('profile.Bet Summary')" style="color:red">{{ errors.first('profile.Bet Summary') }}</span>
                  <v-text-field v-validate="'required|numeric'" data-vv-scope="profile" v-model="data.minimum_stake" name="Minimum Stake" label="Minimum Stake" type="text"></v-text-field>
                  <span :value="errors.has('profile.Minimum Stake')" style="color:red">{{ errors.first('profile.Minimum Stake') }}</span>
                   <v-text-field v-validate="'numeric|min_value:2'" data-vv-scope="profile" v-model="data.maximum_part" name="Maximum Predicts" hint="Leave empty if you would like any number of predicts" label="Maximum Number of Predicts (optional)" type="text"></v-text-field>
                  <span :value="errors.has('profile.Maximum Predicts')" style="color:red">{{ errors.first('profile.Maximum Predicts') }}</span>
                  <v-tooltip bottom>
                      <div slot="activator">
                    <v-datetime-picker
                        name="Close Date"
                        data-vv-scope="profile"
                    v-validate="'required'"
                        label="Bet Close Date"
                        v-model="data.close_date">
                </v-datetime-picker>
                    <span>No stake would be allowed after this time has exceeded</span>
                      </div>
                    </v-tooltip>
                  <span :value="errors.has('profile.Close Date')" style="color:red">{{ errors.first('profile.Close Date') }}</span>
                   <v-tooltip bottom>
                      <div slot="activator">
                  <v-datetime-picker
                        name="Outcome Date"
                        data-vv-scope="profile"
                    v-validate="'required'"
                        label="Bet Outcome Date"
                        v-model="data.outcome_date">
                </v-datetime-picker>
                 <span>This is the date the event would occur and you have 24 hours to declare outcome</span>
                      </div>
                    </v-tooltip>
                  <span :value="errors.has('profile.Outcome Date')" style="color:red">{{ errors.first('profile.Outcome Date') }}</span>
              </v-form>
              </v-card-text>
            </v-card>
          </v-flex>
        </v-layout>
        </v-container>

        <v-container fluid>
        <v-layout>
          <v-flex xs12>
            <v-card class="elevation-12">
              <v-card-text>
                <v-form>
                     <v-card-text><b>Bet Options</b></v-card-text>
                   <v-text-field data-vv-scope="skill" v-validate="'required|min:2'" v-model="outcome" name="Possible Outcome" label="Enter each option here" type="text"></v-text-field>
                  <v-btn @click="addOutcome" outline class="white--text" color="green">Add</v-btn>
                   <v-card-text v-show="optionFalse"><b>Options</b></v-card-text>
                   <v-list dense>
                    <v-list-tile v-for="(option, key) in data.outcome" v-bind:key='option' class="grey lighten-3">
                    <v-list-tile-content>{{key + 1}} - {{ option }}</v-list-tile-content>
                     <v-list-tile-content class="align-end">
                         <v-icon  @click="removeOutcome( key )" color="red" right>close</v-icon>
                    </v-list-tile-content>
                    </v-list-tile>
                   </v-list>
                </v-form>
              </v-card-text>
               <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn type="submit" class="white--text" @click="submitBet" :disabled="dialog" :loading="dialog" color="green">Submit Bet</v-btn>
              </v-card-actions>
            </v-card>
          </v-flex>
        </v-layout>
        </v-container>


     <v-footer height="auto" color="green">
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
        &copy;2018 — <strong>Polibet</strong>
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
        <v-card-title class="headline">{{this.infotext}}</v-card-title>
        <div>
        <v-spacer></v-spacer>
        <v-btn color="green" @click="info = false">OK</v-btn>
        </div>
     </v-card>
    </v-dialog>
    <v-navigation-drawer
      v-model="drawer"
      fixed
      temporary
      height="400px"
    >
      <v-list class="pt-0" dense>
        <v-list-tile @click="dashboard">
          <v-list-tile-action>
            <v-icon color="green">dashboard</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>Dashboard</v-list-tile-title>
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
  </v-app>
</template>

<script>
//<small>*Note that your bet would be approved based on your reputation and description of bet. Your bet close date should be lesser than your outcome date. You have 24 hours to declare the outcome of bet after outcome date to risk seizure of your funds. Wrong declaration of outcome would lead to seizure of your funds.</small>
export default {
    metaInfo: {
         // if no subcomponents specify a metaInfo.title, this title will be used
      title: 'Create',
      // all titles will be injected into this template
      titleTemplate: '%s | Polibet',
  meta: [
    // OpenGraph data (Most widely used)
    {property: 'og:title', content: 'Create Bet'},
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
  data() {
   return {
       optionFalse : false,
       menu: false,
    dialog: false,
    info: false,
    infotext: '',
    disable: false,
    outcome: '',
    drawer: null,
    data: {
            name: '',
            summary: '',
            minimum_stake: '',
            outcome: [],
            outcome1: '',
            outcome2: '',
            maximum_part: null,
            close_date: '',
            outcome_date: '',
          },
   }
  },

  computed: {
      logoutLoadStatus(){
       return this.$store.getters.getLogoutLoadStatus;
     }
   },

   watch: {
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

  methods: {
      removeOutcome(key) {
      this.data.outcome.splice( key, 1 );
      if (!this.data.outcome[0]) {
        this.outcomeFalse = false;
      }
    },
      addOutcome() {
      this.$validator.validateAll('skill').then(result => {
        if (result) {
        this.data.outcome.push(this.outcome);
        this.outcome = '';
        this.outcomeFalse = true;
        console.log(this.data.outcome);
       }
       else {
          this.infotext = "Please fill field with a minimum of 2 characters";
          this.info = true;
      }
      })
    },
        submitBet(e){
            this.$validator.validateAll('profile').then(result => {
            if (result) {
                if (this.data.outcome.length > 1) {
                this.dialog = true;
                axios.post('api/create-bet', this.data).then(response => {
                   this.dialog = false;
                    this.infotext = response.data.message;
                    this.info = true;
                    this.$router.push(`/bet/${response.data.random}`);
                })
                .catch(error => {
                    this.dialog = false;
                    this.infotext = error.response.data.message;
                    this.info = true;
                });
              }
               else {
                this.infotext = "Your bet must have atleast two options";
                this.info = true;
            }
            }
             })
            },

      dashboard() {
          this.dialog = true;
          this.$router.push('/dashboard');
        },
        logout() {
          this.$store.dispatch( 'logoutUser');
        },

  }
}
</script>
