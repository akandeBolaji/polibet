<template>
  <v-app id="inspire">
    <v-content>
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
        <v-toolbar fixed dense>
         <v-toolbar-title class="green--text">Polibet</v-toolbar-title>
         <v-spacer></v-spacer>
         <v-toolbar-side-icon class="hidden-md-and-up" @click.stop="drawer = !drawer"></v-toolbar-side-icon>
         <div class="hidden-sm-and-down">
         <v-btn @click="login" color="green">Login</v-btn>
         <v-btn @click="home" color="green">Home</v-btn>
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
                  <v-card-text><b>PERSONAL INFORMATION</b></v-card-text>
                  <v-text-field prepend-icon="person_add" v-validate="'required|min:4'" v-model="data.full_name" name="Full Name" label="Name in full" type="text" ref="name"></v-text-field>
                  <span :value="errors.has('Full Name')" style="color:red">{{ errors.first('Full Name') }}</span>
                  <v-text-field prepend-icon="contact_mail" v-validate="'required|email'" v-model="data.email" name="email" label="Email" type="text"></v-text-field>
                  <span :value="errors.has('Email')" style="color:red">{{ errors.first('Email') }}</span>
                  <v-text-field prepend-icon="person_outline" v-validate="'required|numeric|min_value:18'" v-model="data.age" name="Age" label="Age" type="text"></v-text-field>
                  <span :value="errors.has('Age')" style="color:red">{{ errors.first('Age') }}</span>
                  <v-select  prepend-icon="add_location" :items="states" label="Choose Location"  v-validate="'required'" v-model="data.location" name="Location" type="text" ></v-select>
                  <span :value="errors.has('Location')" style="color:red">{{ errors.first('Location') }}</span>
                  <v-select  prepend-icon="people" :items="sex" label="Choose Gender"  v-validate="'required'" v-model="data.gender" name="Gender" type="text" ></v-select>
                  <span :value="errors.has('Gender')" style="color:red">{{ errors.first('Gender') }}</span>
                  <v-text-field prepend-icon="phone" name="Phone" v-validate="'required|numeric'" v-model="data.phone" label="Phone number" type="text"></v-text-field>
                  <span :value="errors.has('Phone')" style="color:red">{{ errors.first('Phone') }}</span>
                  <v-text-field id="password"  v-validate="'required|min:6'" prepend-icon="lock" v-model="data.password" name="password" label="Password" type="password" ref="password"></v-text-field>
                  <span :value="errors.has('password')" style="color:red">{{ errors.first('password') }}</span>
                  <v-text-field id="password_confirmation" v-validate="'required|confirmed:password'" prepend-icon="lock" v-model="data.password_confirmation" name="password_confirmation" data-vv-as="password" label="Password Again" type="password"></v-text-field>
                  <span :value="errors.has('password_confirmation')" style="color:red">{{ errors.first('password_confirmation') }}</span>
                  <v-divider></v-divider>
                  <v-card-text><b>BANK ACCOUNT INFORMATION</b></v-card-text>
                  <v-select  prepend-icon="account_balance" :items="banks" label="Choose Bank"  v-validate="'required'" v-model="data.bank_name" name="Bank Name" type="text" ></v-select>
                  <span :value="errors.has('Bank Name')" style="color:red">{{ errors.first('Bank Name') }}</span>
                  <v-text-field prepend-icon="account_box" v-validate="'required|confirmed:name'" v-model="data.account_name" name="Account Name" label="Account Name (Same as Full Name)" type="text" data-vv-as="name"></v-text-field>
                  <span :value="errors.has('Account Name')" style="color:red">{{ errors.first('Account Name') }}</span>
                  <v-text-field prepend-icon="account_balance_wallet" v-validate="'required|numeric|min:10|max:10'" v-model="data.account_number" name="Account Number" label="Account Number" type="text" required></v-text-field>
                  <span :value="errors.has('Account Number')" style="color:red">{{ errors.first('Account Number') }}</span>
                  <v-divider></v-divider>
                  <v-card-text><b>OTHER INFORMATION</b></v-card-text>
                   <v-text-field prepend-icon="people_outline" v-if="ifreferred" disabled v-model="referrer_name" name="Referrer's Name" label="Referrer's Name" type="text" ref="name"></v-text-field>
                   <v-checkbox v-model="data.accredited" label="Are you an Accredited Voter?(Optional)"></v-checkbox>
                   <v-checkbox v-validate="'required:true'" name="terms and condition" v-model="data.checkbox" label="Do you agree with our terms and condition?"></v-checkbox>
                   <span :value="errors.has('terms and condition')" style="color:red">{{ errors.first('terms and condition') }}</span>
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
              <v-icon class="red--text">favorite</v-icon>
              by <a class="white--text" href="https://codebators.com" target="_blank">CodeBators</a>
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
    </v-content>
  </v-app>
</template>

<script>
  export default {
    data() {
        return {
           states: [
                "Abia",
                "Adamawa",
                "Anambra",
                "Akwa Ibom",
                "Bauchi",
                "Bayelsa",
                "Benue",
                "Borno",
                "Cross River",
                "Delta",
                "Ebonyi",
                "Enugu",
                "Edo",
                "Ekiti",
                "FCT - Abuja",
                "Gombe",
                "Imo",
                "Jigawa",
                "Kaduna",
                "Kano",
                "Katsina",
                "Kebbi",
                "Kogi",
                "Kwara",
                "Lagos",
                "Nasarawa",
                "Niger",
                "Ogun",
                "Ondo",
                "Osun",
                "Oyo",
                "Plateau",
                "Rivers",
                "Sokoto",
                "Taraba",
                "Yobe",
                "Zamfara"
           ],
           banks: [
               'Access Bank',
               'Citi Bank',
               'Diamond Bank',
               'Ecobank Nigeria',
               'Fidelity Bank',
               'First City Monument Bank',
               'First Bank of Nigeria',
               'GTB',
               'Heritage Banking Company',
               'Keystone Bank',
               'Polaris Bank',
               'Stanbic IBTC Bank',
               'Standard Chartered',
               'Sterling Bank',
               'Union Bank',
               'UBA',
               'Unity Bank',
               'Wema Bank',
               'Zenith Bank'
           ],
           sex: [
               'male',
               'female'
           ],
           ifreferred: false,
           dialog: false,
           info: false,
           infotext: '',
           disable: false,
           drawer: null,
           referrer_name: '',
            data: {
                    email: '',
                    age: '',
                    checkbox: '',
                    accredited: '',
                    location: '',
                    gender: '',
                    phone: '',
                    referrer_id: this.$route.query.ref,
                    bank_name: '',
                    account_name: '',
                    account_number: '',
                    password: '',
                    password_confirmation: '',
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
             this.dialog = false;
            this.$router.push('/login');
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
          //this.dialog= true;
          this.$router.push('/login');
        },
         home() {
          //this.dialog= true;
          this.$router.push('/');
        }
    }
  }
</script>
