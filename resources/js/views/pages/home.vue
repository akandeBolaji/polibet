<template>
  <v-app id="inspire">
        <v-toolbar fixed>
         <v-toolbar-title class="green--text darken-1">Polibet</v-toolbar-title>
         <v-spacer></v-spacer>
         <v-toolbar-side-icon class="hidden-md-and-up" @click.stop="drawer = !drawer"></v-toolbar-side-icon>
         <div class="hidden-sm-and-down">
         <v-btn @click="login" v-if="loggedOut" class="white--text" color="green darken-2">Login</v-btn>
         <v-btn @click="register" v-if="loggedOut" class="white--text" color="green darken-2">Register</v-btn>
         <v-btn @click="dashboard" v-if="!loggedOut"  class="white--text" color="green darken-2" >Go to Dashboard</v-btn>
         <v-btn @click="logout" v-if="!loggedOut"  class="white--text" color="green darken-2" >Logout</v-btn>
         </div>
       </v-toolbar>
       <v-layout mt-5>
           <v-flex xs12>
       <section>
         <v-parallax
       src="/images/background/polibet/new.jpg"
       height="300"
       >
    <v-layout
      align-center
      column
      justify-center
    >
      <h1 class="display-2 font-weight-thin mb-2 text-xs-center">Polibet</h1>
      <h4 class="subheading mb-5 text-xs-center">Own your bets</h4>
      <v-divider></v-divider>
      <v-btn
              class="white green--text darken-2 mt-5"
              dark
              large
              @click="$router.push('/create-bet')"
            >
              Create Bet
            </v-btn>
    </v-layout>
  </v-parallax>
       </section>

       <h3 class="text-xs-center">
              <b> Trending Bets</b>
        </h3>
        <section>
        <v-layout
          column
          wrap
        >
          <v-flex xs12>
            <v-container grid-list-xl>
              <v-layout row wrap align-center>
                <v-flex xs12 md6  v-for="(bet, index) in data" :key="index">
                    <span @click="$router.push(`/bet/${bet.random}`)">
                    <Bet v-on:ended="ended" :bet="bet" :keys="index"></Bet>
                    </span>
                </v-flex>
              </v-layout>
            </v-container>
          </v-flex>
        </v-layout>
      </section>

        <section>
        <v-container grid-list-xl>
          <v-layout row wrap justify-center>
            <v-flex xs12 sm4>
              <v-card class="elevation-0 transparent">
                <v-card-title primary-title class="layout justify-center">
                  <div class="headline">Know More ?</div>
                </v-card-title>
                <v-card-text>
                 Take your time to read our <router-link to="/about-us" class="green--text"> Privacy Policy</router-link>
                  and <router-link to="/about-us" class="green--text"> Terms and Conditions</router-link> to know more about how we operate.
                </v-card-text>
              </v-card>
            </v-flex>
            <v-flex xs12 sm4 offset-sm1>
              <v-card class="elevation-0 transparent">
                <v-card-title primary-title class="layout justify-center">
                  <div class="headline">Contact us</div>
                </v-card-title>
                  <v-card-text>
                 In cases of complaints and feedbacks, you can reach us through the following :
                </v-card-text>
                <v-list class="transparent">
                  <v-list-tile>
                    <v-list-tile-action>
                      <v-icon class="green--text text--lighten-2">phone</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-content>
                      <v-list-tile-title>2348052764314</v-list-tile-title>
                    </v-list-tile-content>
                  </v-list-tile>
                  <v-list-tile>
                    <v-list-tile-action>
                      <v-icon class="green--text text--lighten-2">email</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-content>
                      <v-list-tile-title>support@polibet.ng</v-list-tile-title>
                    </v-list-tile-content>
                  </v-list-tile>
                </v-list>
              </v-card>
            </v-flex>
          </v-layout>
        </v-container>
      </section>
           </v-flex>
       </v-layout>
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
        <v-list-tile v-if="!loggedOut" @click="dashboard">
          <v-list-tile-action>
            <v-icon color="green">dashboard</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>Dashboard</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>

        <v-list-tile v-if="!loggedOut" @click="logout">
          <v-list-tile-action>
            <v-icon color="red">logout</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>Logout</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>

        <v-list-tile v-if="loggedOut" @click="login">
          <v-list-tile-action>
            <v-icon color="green">lock_open</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>Login</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>

        <v-list-tile v-if="loggedOut" @click="register">
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
  </v-app>
</template>

<script>
import Bet from './bet-component.vue';

  export default {
      metaInfo: {
         // if no subcomponents specify a metaInfo.title, this title will be used
      title: 'Home',
      // all titles will be injected into this template
      titleTemplate: '%s | Polibet',
  meta: [
    // OpenGraph data (Most widely used)
    {property: 'og:title', content: 'First Peer to Peer Betting Platform'},
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
     Bet
    },
    data: () => ({
      data:'',
      dialog: false,
      info: false,
      infotext: '',
      loggedOut: true,
      disable: false,
      drawer: null,
    }),
    props: {},

    mounted() {
       //this.check();
      this.fetchData();
    },

    methods: {
        ended(value) {
          this.data.splice( value, 1 );
          //console.log(value);
      },
        fetchData() {
        axios.get('/api/trending-bets').then(response =>  {
        this.data = response.data.message;
        this.loggedOut = response.data.loggedOut;
        }).catch(error => {

        });
        },
        login() {
          this.dialog = true;
          this.$router.push('/login');

        },
        register() {
          this.dialog = true;
          this.$router.push({ path: '/register', query: { ref: this.$route.query.ref }})
        },
        dashboard() {
          this.dialog = true;
          this.$router.push('/dashboard');
        },
        logout() {
          this.$store.dispatch( 'logoutUser');
        },
    },


   computed: {

          logoutLoadStatus(){
       return this.$store.getters.getLogoutLoadStatus;
     },

   },

   watch: {
         'logoutLoadStatus': function(){
         if(this.logoutLoadStatus == 2){
           this.dialog = false;
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
       }
   }
  }
</script>
