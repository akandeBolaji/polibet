<template>
  <v-app id="inspire">
    <v-content>
          <v-toolbar fixed>
         <v-toolbar-title class="green--text">Polibet</v-toolbar-title>
         <v-spacer></v-spacer>
         <v-toolbar-side-icon class="hidden-md-and-up" @click.stop="drawer = !drawer"></v-toolbar-side-icon>
         <div class="hidden-sm-and-down">
         <v-btn @click="register" color="green">Register</v-btn>
         <v-btn @click="home" color="green">Home</v-btn>
        </div>
       </v-toolbar>
       <v-navigation-drawer
      v-model="drawer"
      absolute
      temporary
      height="400px"
    >

      <v-list class="pt-0" dense>
        <v-list-tile @click="register">
          <v-list-tile-action>
            <v-icon>create</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>Register</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>

        <v-list-tile @click="home">
          <v-list-tile-action>
            <v-icon>home</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>Home</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>
      </v-list>
       </v-navigation-drawer>
      <v-container fluid fill-height>
        <v-layout align-center justify-center>
          <v-flex xs12 sm8 md4>
            <v-card class="elevation-12">
              <v-toolbar dark color="green">
                <v-toolbar-title>Login form</v-toolbar-title>
              </v-toolbar>
              <v-card-text>
                <v-form>
                  <v-text-field prepend-icon="person" v-validate="'required|email'" v-model="loginForm.email" name="Email" label="Email" type="text"></v-text-field>
                  <v-alert :value="errors.has('Email')" type="error">{{ errors.first('Email') }}</v-alert>
                  <v-text-field id="password"  v-validate="'required|min:6'" prepend-icon="lock" v-model="loginForm.password" name="password" label="Password" type="password"></v-text-field>
                  <v-alert :value="errors.has('password')" type="error">{{ errors.first('password') }}</v-alert>
                </v-form>
              </v-card-text>
              <v-card-actions>
              <v-btn color="green" @click="register" :disabled="dialog" flat>Register?</v-btn>
              <v-spacer></v-spacer>
              <v-btn color="green" flat @click="forgot" :disabled="dialog" >Forgot Password?</v-btn>
              </v-card-actions>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn @click="signIn" class="white--text" :disabled="dialog" :loading="dialog" color="green">Login</v-btn>
              </v-card-actions>
            </v-card>
          </v-flex>
        </v-layout>
      </v-container>
      <v-footer fixed color="green"></v-footer>
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
          Please wait ...
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
    </v-content>
  </v-app>
</template>

<script>
  export default {
    data() {
        return {
      infotext: '',
      info: false,
      dialog: false,
      drawer: null,
      loginForm: {
                    email: '',
                    password: ''
                },
      }
    },

    props: {},

       watch: {
       'loginLoadStatus': function(){
         if(this.loginLoadStatus == 2){
            console.log(this.$store.getters.getLoginMessage);
            localStorage.setItem('auth_token', this.$store.getters.getToken);
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('auth_token');
            this.$router.push({ path: '/dashboard', query: { info: 0 }});
         }
         else if (this.loginLoadStatus == 3){
           this.dialog = false;
            if (this.$store.getters.getLoginMessage) {
              this.infotext = this.$store.getters.getLoginMessage;
              this.info = true;
             }
             else {
                 this.infotext = 'An error Occured. Please check your network';
                 this.info = true;
             }
         }
         else if (this.loginLoadStatus == 1){
           this.dialog = true;
           console.log('loading...')
         }
       },
   },

   created() {
     this.checkInfo();

    },

   computed: {
     loginLoadStatus(){
       return this.$store.getters.getLoginLoadStatus;
     },
   },

    methods: {
        checkInfo(){
            if (this.$store.getters.getConfirmResetPassStatus == 3){
                this.dialog = false;
                if (this.$store.getters.getConfirmResetMessage) {
                 this.infotext = this.$store.getters.getConfirmResetMessage;
                 this.info = true;
                }
                else {
                 this.infotext = 'An error Occured. Please check your network';
                 this.info = true;
               }
            };
            if (this.$store.getters.getChangePassStatus == 2){
                this.dialog = false;
                this.infotext = this.$store.getters.getChangePassMessage;
                this.info = true;
            };
              if (this.$store.getters.getLogoutLoadStatus == 2){
                this.info = true;
                this.dialog = false;
                this.infotext = this.$store.getters.getLogoutMessage;
            };
              if (this.$store.getters.getActivateLoadStatus == 2){
                this.info = true;
                this.dialog = false;
                this.infotext = this.$store.getters.getActivateMessage;
            };
            if (this.$store.getters.getRegisterLoadStatus == 2){
                this.info = true;
                this.dialog = false;
                this.infotext = this.$store.getters.getRegisterMessage;
            };
        },

        signIn(e){
             this.$validator.validate().then(result => {
              if (result) {
               let loginData;
               loginData = this.loginForm;
               this.$store.dispatch( 'loginUser',
               {
               loginData
               }
               );
              }
             })
            },

        register() {
          this.dialog= true;
          this.$router.push('/register');
        },
         home() {
          this.dialog= true;
          this.$router.push('/');
        },
        forgot() {
          this.dialog= true;
          this.$router.push('/password');
        }
    }
  }
</script>
