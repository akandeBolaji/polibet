<template>
  <v-app id="inspire">
    <v-navigation-drawer
      v-model="drawer"
      fixed
      temporary
      height="400px"
    >

      <v-list class="pt-0" dense>
        <v-list-tile @click="login">
          <v-list-tile-action>
            <v-icon>lock_open</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>Login</v-list-tile-title>
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
        <v-toolbar>
         <v-toolbar-title class="green--text">Polibet</v-toolbar-title>
         <v-spacer></v-spacer>
         <v-toolbar-side-icon class="hidden-md-and-up" @click.stop="drawer = !drawer"></v-toolbar-side-icon>
         <div class="hidden-sm-and-down">
         <v-btn @click="login" class="white--text" color="green">Login</v-btn>
         <v-btn @click="home" class="white--text" color="green">Home</v-btn>
        </div>
       </v-toolbar>
      <v-container fluid fill-height>
        <v-layout align-center justify-center>
          <v-flex xs12 sm8 md4>
            <v-card class="elevation-12">
              <v-toolbar dark color="green">
                <v-toolbar-title >Register form</v-toolbar-title>
              </v-toolbar>
              <v-card-text>
                <v-form>
                  <v-text-field prepend-icon="person_add" v-validate="'required|min:4'" v-model="data.full_name" name="Full Name" label="Full Name" type="text"></v-text-field>
                  <span :value="errors.has('Full Name')" style="color:red">{{ errors.first('Full Name') }}</span>
                   <v-text-field prepend-icon="person_add" v-validate="'required|min:2'" v-model="data.user_name" name="UserName" label="Codename" type="text"></v-text-field>
                  <span :value="errors.has('UserName')" style="color:red">{{ errors.first('UserName') }}</span>
                  <v-text-field prepend-icon="contact_mail" v-validate="'required|email'" v-model="data.email" name="email" label="Email" type="text"></v-text-field>
                  <span :value="errors.has('email')" style="color:red">{{ errors.first('email') }}</span>
                  <v-select prepend-icon="person_outline" :items="age" v-validate="'required'" v-model="data.age" name="Age" label="Age" type="text"></v-select>
                  <span :value="errors.has('Age')" style="color:red">{{ errors.first('Age') }}</span>
                  <v-select  prepend-icon="people" :items="sex" label="Choose Gender"  v-validate="'required'" v-model="data.gender" name="Gender" type="text" ></v-select>
                  <span :value="errors.has('Gender')" style="color:red">{{ errors.first('Gender') }}</span>
                  <v-text-field prepend-icon="phone" name="Phone" v-validate="'required|numeric'" v-model="data.phone" label="Phone number" type="text"></v-text-field>
                  <span :value="errors.has('Phone')" style="color:red">{{ errors.first('Phone') }}</span>
                  <v-text-field id="password"  :append-icon="show1 ? 'visibility_off' : 'visibility'" :type="show1 ? 'text' : 'password'"  v-validate="'required|min:6'" prepend-icon="lock" @click:append="show1 = !show1" v-model="data.password" name="password" label="Set Password"></v-text-field>
                  <span :value="errors.has('password')" style="color:red">{{ errors.first('password') }}</span>
                </v-form>
              </v-card-text>
              <v-card-actions>
                <v-btn flat :disabled="dialog" @click="login" color="green" >or Login?</v-btn>
                <v-spacer></v-spacer>
                <v-btn type="submit" class="white--text" @click="signUp" :disabled="dialog" :loading="dialog" color="green">Get Registered</v-btn>
              </v-card-actions>
            </v-card>
          </v-flex>
        </v-layout>
      </v-container>
       <v-footer class="elevation-3" color="white darken-2" height="auto">
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
  </v-app>
</template>

<script>
  export default {
      metaInfo: {
         // if no subcomponents specify a metaInfo.title, this title will be used
      title: 'Register',
      // all titles will be injected into this template
      titleTemplate: '%s | Polibet',
  meta: [
    // OpenGraph data (Most widely used)
    {property: 'og:title', content: 'Register on Polibet'},
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
            show1: false,
           sex: [
               'Male',
               'Female',
               'Rather not say'
           ],
           age: [
               '18 - 24',
               '25 - 34',
               '35 + above'
           ],
           ifreferred: false,
           dialog: false,
           info: false,
           infotext: '',
           disable: false,
           drawer: null,
           referrer_name: '',
            data: {
                 show1: false,
                    email: '',
                    age: '',
                    checkbox: '',
                    accredited: '',
                    gender: '',
                    phone: '',
                    referrer_id: this.$route.query.ref,
                    user_name: '',
                    password: '',
                    full_name: '',
                }
        }
    },

    props: {},

    mounted(){
        this.getReferrer();
    },

    watch: {
       'registerLoadStatus': function(){
         if(this.registerLoadStatus == 2){
            localStorage.setItem('auth_token', this.$store.getters.getToken);
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('auth_token');
            this.$router.push(this.$route.query.redirect || '/dashboard');
         }
         else if (this.registerLoadStatus == 3){
             this.dialog = false;
              if (this.$store.getters.getRegisterMessage) {
                 this.infotext = this.$store.getters.getRegisterMessage;
                 this.info = true;
                }
                else {
                 this.infotext = 'An error Occured. Please check your network';
                 this.info = true;
               }
         }
         else if (this.registerLoadStatus == 1){
             this.dialog = true;
         }
       },
        'referrerLoadStatus': function(){
         if(this.referrerLoadStatus == 2){
             this.dialog = false;
             this.ifreferred = true;
             this.referrer_name = this.$store.getters.getReferrerMessage;
         }
         else if (this.referrerLoadStatus == 3){
             this.dialog = false;
         }
       },
   },


   computed: {
     registerLoadStatus(){
       return this.$store.getters.getRegisterLoadStatus;
     },
     referrerLoadStatus(){
       return this.$store.getters.getReferrerLoadStatus;
     }

   },

    methods: {
        getReferrer(){
            if (this.data.referrer_id) {
               let referrer_id;
               referrer_id = this.data.referrer_id;
               this.$store.dispatch( 'getReferrer',
               {
               referrer_id
               }
               );
            }
        },
        signUp(e){
            this.$validator.validate().then(result => {
            if (result) {
               this.disable = true;
               let registerData;
               registerData = this.data
               this.$store.dispatch( 'registerUser',
               {
               registerData
               }
               );
              }
             })
            },

        login() {
          this.dialog= true;
          this.$router.push('/login');
        },
         home() {
          this.dialog= true;
          this.$router.push('/');
        }
    }
  }
</script>
