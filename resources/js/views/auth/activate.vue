<template>
   <v-app id="inspire">
    <v-content>
        <v-toolbar fixed dense>
         <v-toolbar-title class="green--text">Polibet</v-toolbar-title>
         <v-spacer></v-spacer>
         <v-toolbar-side-icon class="hidden-md-and-up"></v-toolbar-side-icon>
       </v-toolbar>
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
                Processing...
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
                <v-btn color="green" @click="this.$router.go(-1);">OK</v-btn>
                </div>
            </v-card>
            </v-dialog>
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
    </v-content>
</v-app>
</template>

<script>
    export default {
        metaInfo: {
         // if no subcomponents specify a metaInfo.title, this title will be used
      title: 'Activate',
      // all titles will be injected into this template
      titleTemplate: '%s | Polibet',
  meta: [
    // OpenGraph data (Most widely used)
    {property: 'og:title', content: 'Polibet Account Activation'},
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
                dialog: false,
                info: false,
                infotext: '',
                token:this.$route.params.token,
            }
        },
        mounted(){
            this.activate();
        },

        methods: {
            activate() {
               let activateData;
               activateData = this.token
               this.$store.dispatch( 'activateUser',
               {
               activateData
               }
               );
            },
        },

        watch: {
       'activateLoadStatus': function(){
         if(this.activateLoadStatus == 2){
             this.dialog = false;
             this.infotext = this.$store.getters.getActivateMessage;
              this.info = true;
         }
         else if (this.activateLoadStatus == 3){
             this.dialog = false;
             if (this.$store.getters.getActivateMessage) {
              this.infotext = this.$store.getters.getActivateMessage;
              this.info = true;
             }
             else {
                 this.infotext = 'An error Occured. Please check your network';
                 this.info = true;
             }
         }
         else if (this.activateLoadStatus == 1){
             this.dialog = true;
         }
       }
   },


   computed: {
     activateLoadStatus(){
       return this.$store.getters.getActivateLoadStatus;
     }

   },

    }
</script>
<style>
</style>
