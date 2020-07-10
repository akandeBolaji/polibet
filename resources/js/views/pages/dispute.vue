<template>
<v-app>
    <v-toolbar fixed>
         <v-toolbar-title class="green--text darken-1">Polibet</v-toolbar-title>
         <v-spacer></v-spacer>
         <v-toolbar-side-icon class="hidden-md-and-up" @click.stop="drawer = !drawer"></v-toolbar-side-icon>
         <div class="hidden-sm-and-down">
         <v-btn @click="home" class="white--text" color="green darken-2">Home</v-btn>
         <v-btn @click="dashboard" class="white--text" color="green darken-2" >Go to Dashboard</v-btn>
         <v-btn @click="logout" class="white--text" color="green darken-2" >Logout</v-btn>
         </div>
       </v-toolbar>

<v-container mt-5>
       <v-card>
          <v-card-title>Bet Description - {{ data.option.bet.summary }}</v-card-title>
          <v-divider></v-divider>
          <v-list dense>
              <v-list-tile>
              <v-list-tile-content>Selected outcome:</v-list-tile-content>
              <v-list-tile-content class="align-end"> {{data.option.value }}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content>Outcome by:</v-list-tile-content>
              <v-list-tile-content class="align-end"> {{data.option.bet.decided_by }}</v-list-tile-content>
            </v-list-tile>
             <v-list-tile>
              <v-list-tile-content> Dispute Status :</v-list-tile-content>
              <v-list-tile-content class="align-end">
                   <span><i>{{data.status}}</i></span>
              </v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content>Opened by:</v-list-tile-content>
              <v-list-tile-content class="align-end">  You </v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content>Reason:</v-list-tile-content>
              <v-list-tile-content class="align-end"> {{data.reason }}</v-list-tile-content>
            </v-list-tile>
          </v-list>
        </v-card>
        <v-card v-if="data.comments">
      <h3> Comments</h3>
      <span>
      <v-layout v-for="comment in data.comments" v-bind:key='comment'>
            <v-flex align-center justify-center layout xs2>
            <v-icon large style="font-size: 50px;">account_circle</v-icon>
          </v-flex>
          <v-flex xs10>
             <v-card-text><span style="font-size: 11px;"><b>{{comment.user.full_name}}</b><br/></span><span style="font-size: 16px;">{{comment.text}}</span></v-card-text>
          </v-flex>
    </v-layout>
         <v-divider></v-divider>
      </span>
      </v-card>
                <v-card v-if="data.status != 'closed' || data.status != 'concluded'">
                 <v-layout row>
                <v-flex offset-xs3 xs6 offset-sm5 sm2>
                    <v-btn @click="comment_dialog = true" flat outline color="green">Add Comment</v-btn>
                </v-flex>
            </v-layout>
                </v-card>
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

    <v-dialog
        v-model="comment_dialog"
        persistent
        max-width="500px">
      <v-card>
        <v-card-title>
          <span class="headline">Add Comment</span>
        </v-card-title>
        <v-card-text>
          <v-container grid-list-md>
            <v-layout wrap>
                <v-text-field label="Enter Text here" v-model="comment_data.text" data-vv-scope="comment" name="Text" v-validate="'required'"></v-text-field>
                <span :value="errors.has('comment.Text')" style="color:red">{{ errors.first('comment.Text') }}</span>
            </v-layout>
          </v-container>
          <small>*Avoid using vulgar words in your comments</small>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn flat @click.native="comment_dialog = false">Close</v-btn>
          <v-btn color="green" :disabled="dialog" :loading="dialog" class="white--text" @click="submitComment">Comment</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
</v-app>
</template>
<script>
export default {
    metaInfo: {
         // if no subcomponents specify a metaInfo.title, this title will be used
      title: 'Dispute',
      // all titles will be injected into this template
      titleTemplate: '%s | Polibet',
  meta: [
    // OpenGraph data (Most widely used)
    {property: 'og:title', content: 'Dispute Outcome'},
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
                comment_data: {
                text: '',
                dispute: ''
                },
                data: '',
                user: '',
                comment_dialog: false,
                dialog: false,
                 id:this.$route.params.id,
               info: false,
               infotext: '',
               disable: false,
              drawer: null,
            }
        },
        mounted(){
            this.getPost();
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



        computed: {
             logoutLoadStatus(){
            return this.$store.getters.getLogoutLoadStatus;
            },
        },

        methods: {
            submitComment(){
                this.$validator.validateAll('comment').then(result => {
        if (result) {
            this.dialog = true;
            this.comment_data.dispute = this.data.id
              axios.post('/api/submit-comment', this.comment_data ).then(response =>  {
                     console.log(response.data);
                     this.dialog = false;
                     this.comment = {
                      user : {
                       full_name: this.user.full_name,
                         },
                       text: this.comment_data.text,
                    };
                    this.data.comments.push(this.comment);
                }).catch(error => {
                    this.dialog = false;
                    //this.infotext = error.response.data.message;
                    //this.info = true;
                });
        }
        })
            },
            getPost() {
                let id = this.$route.params.id;
                 axios.get('/api/get-dispute/' + id).then(response =>  {
                     console.log(response.data);
                     this.data = response.data.message;
                     this.user = response.data.user;
                }).catch(error => {
                    this.$router.push(-1);
                });
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
