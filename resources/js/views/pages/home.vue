<template>
  <v-app id="inspire">
    <v-content>
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
         <v-parallax
        dark
       src="http://polibet.site/images/background/polibet/background5.jpg"
       >
    <v-layout
      align-center
      column
      justify-center
    >
      <h1 class="display-2 font-weight-thin mb-3">Polibet</h1>
      <h4 class="subheading">Bet, Vote, Win twice!</h4>
    </v-layout>
  </v-parallax>




       <v-navigation-drawer
      v-model="drawer"
      absolute
      temporary
      height="400px"
    >
      <v-list class="pt-0" dense>
        <v-divider></v-divider>

        <v-list-tile v-if="!loggedOut" @click="dashboard">
          <v-list-tile-action>
            <v-icon>dashboard</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>Dashboard</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>

        <v-list-tile v-if="!loggedOut" @click="logout">
          <v-list-tile-action>
            <v-icon>logout</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>Logout</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>

        <v-list-tile v-if="loggedOut" @click="login">
          <v-list-tile-action>
            <v-icon>lock_open</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>Login</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>

        <v-list-tile v-if="loggedOut" @click="register">
          <v-list-tile-action>
            <v-icon>create</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>Register</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>
      </v-list>
    </v-navigation-drawer>
      <v-footer fixed color="green darken-2"></v-footer>
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
    </v-content>
  </v-app>
</template>

<script>

  export default {
    data: () => ({
      dialog: false,
      info: false,
      infotext: '',
      loggedOut: null,
      disable: false,
      drawer: null,
    }),
    props: {},

    created() {
       this.check();
      this.fetchData();
    },

    methods: {
       fetchData() {
         this.$store.dispatch( 'getStats');
       },

       check() {
          this.$store.dispatch('check')
       },

        login() {
          this.dialog = true;
          this.$router.push('/login');

        },
        register() {
          this.dialog = true;
          this.$router.push({ path: '/register', query: { ref: this.$route.query.ref }})
          //this.$router.push('/register');
        },
        dashboard() {
          const info = false;
          this.dialog = true;
          this.$router.push('/dashboard');
          //this.$router.push({ name: 'home', params: { info }})
          //this.$router.push({ path: `'/home/${info}'`})
          //this.$router.push({ path: '/home', query: { info: 'true' }})
        },
        logout() {
          this.$store.dispatch( 'logoutUser');
        },
    },


   computed: {
          logoutLoadStatus(){
       return this.$store.getters.getLogoutLoadStatus;
     },
     statsData(){
       return this.$store.getters.getStatsData;
     },
       checkStatus(){
       return this.$store.getters.getCheckStatus;
     },
     checkStats(){
       return this.$store.getters.getaStatsStatus;
     },
   },

   watch: {
     'checkStatus': function(){
         if(this.checkStatus == 2){
           this.dialog = false;
           if (this.$store.getters.getCheckMessage == true) {
           this.loggedOut = false;
           }
           else {
             this.loggedOut = true;
           }
         }
         else if (this.checkStatus == 3){
            this.dialog = false;
            // this.infotext = "Network is unavailable";
            // this.info = true;
         }
         else if (this.checkStatus == 1){
           this.dialog = true;
         }
       },

          'checkStats': function(){
         if(this.checkStats == 2){
           this.dialog = false;
         }
         else if (this.checkStats == 3){
            this.dialog = false;
             this.infotext = "Network is unavailable";
             this.info = true;
         }
         else if (this.checkStats == 1){
           this.dialog = true;
         }
       },

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
