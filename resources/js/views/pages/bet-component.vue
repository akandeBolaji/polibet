<template>
    <v-card class="elevation-3 transparent">
        <v-list dense>
            <v-list-tile class="grey lighten-3">
            <v-list-tile-content>Event Name:</v-list-tile-content>
            <v-list-tile-content class="align-end">{{ bet.name }}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
            <v-list-tile-content> Bet Description:</v-list-tile-content>
            <v-list-tile-content class="align-end">{{ bet.summary }}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile class="grey lighten-3">
            <v-list-tile-content>Total Predicts:</v-list-tile-content>
            <v-list-tile-content class="align-end">{{ bet.count }}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
            <v-list-tile-content>Expires in:</v-list-tile-content>
            <v-list-tile-content class="align-end">
            <countdown ref="countdown" :emit-events=true :time="time(bet.close_date)" @end="endCount(keys)" :auto-start="false">
                <template slot-scope="props">{{ props.days }} days, {{ props.hours }} hours, {{ props.minutes }} minutes, {{ props.seconds }} seconds.</template>
                </countdown>
                </v-list-tile-content>
            </v-list-tile>
        </v-list>
        <v-divider></v-divider>
    </v-card>
</template>
<script>
export default {
    props:['bet', 'keys'],
     data () {
    return {
       click: false,
       submitted: true,
    }
  },
  mounted(){
            this.startCount();
        },
  methods: {
      startCount(){
        this.$refs.countdown.start();
      },
      time(date){
               var now = moment().format('x');
               //var expire = new Date(now.getFullYear() + 1, 0, 1);
               var expire = moment.utc(date).local().format('x');
               console.log(expire - now);
               var data = expire - now;
               return data;
           },
           endCount(key){
              this.$emit('ended', key);
           },
      addConnect(id, key){
        this.click = true;
        axios.post( '/api/connect/' + id + '/befriend').then(response =>  {
                    this.click = false;
                    //this.submitted = false;
                    this.$emit('removesuggest', key);
                }).catch(error => {
                     this.click = false;
                });
      },
       removeConnect(key){
        this.$emit('removesuggest', key);
      },
  }
}
</script>

