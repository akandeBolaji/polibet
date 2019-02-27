<template>
<span>
      <v-flex xs4 sm4  @click="sharesheet = true">
             <v-btn flat outline color="green"><v-icon>share</v-icon> Share</v-btn>
            </v-flex>
  <v-bottom-sheet inset v-model="sharesheet">
      <v-list>
         <social-sharing :url= posturl(bet)
            :title = title(bet)
            :description= description(bet)
            quote=" "
            hashtags=" "
            twitter-user=" "
            inline-template>
        <div>
            <network network="facebook">
                <i class="fa fa-facebook"></i>
                <v-list-tile>
                <v-list-tile-avatar>
                    <v-avatar size="32px" tile>
                    <v-icon>share</v-icon>
                    </v-avatar>
                </v-list-tile-avatar>
                <v-list-tile-title><b>Share on Facebook</b></v-list-tile-title>
                </v-list-tile>
            </network>
        </div>
        </social-sharing>

         <social-sharing :url= posturl(bet)
            :title = title(bet)
            :description= description(bet)
            quote= ''
            hashtags= " "
            twitter-user=" "
            inline-template>
        <div>
            <network network="twitter">
        <i class="fa fa-twitter"></i>
               <v-list-tile>
          <v-list-tile-avatar>
            <v-avatar size="32px" tile>
              <v-icon>share</v-icon>
            </v-avatar>
          </v-list-tile-avatar>
          <v-list-tile-title><b>Share on Twitter</b></v-list-tile-title>
        </v-list-tile>
            </network>
        </div>
        </social-sharing>

        <social-sharing :url= posturl(bet)
            :title = title(bet)
            :description= description(bet)
            quote=" "
            hashtags=" "
            twitter-user=" "
            inline-template>
        <div>
           <network network="whatsapp">
        <i class="fa fa-whatsapp"></i>
               <v-list-tile>
          <v-list-tile-avatar>
            <v-avatar size="32px" tile>
              <v-icon>share</v-icon>
            </v-avatar>
          </v-list-tile-avatar>
          <v-list-tile-title><b>Share on Whatsapp</b></v-list-tile-title>
        </v-list-tile>
            </network>
        </div>
        </social-sharing>
      </v-list>
    </v-bottom-sheet>
</span>
</template>
<script>
export default {
     props:['bet'],
   data () {
    return {
    sharing: false,
   sharesheet: false,
   shared : true
    }
  },

   methods: {
        posturl(bet){
        return `http://beta.polibet.ng/bet/${bet.random}`;
       },
       title(bet){
         return `I am inviting you to play a bet on ${bet.name}`;
       },
       description(bet){
         return `${bet.summary.slice(0,200)} ...`;
       },
  }
}
</script>
