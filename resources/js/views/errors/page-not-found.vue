<template>
<v-app id="inspire">
 <div>
    <v-container fill-height>
      <v-layout align-center>
        <v-flex>
          <h3 class="display-3">404</h3>
          <h3 class="display-3">Oops! There is something wrong!</h3>

          <span class="subheading">The page you have requested is either moved away or doesn't exist.</span>

          <v-divider class="my-3"></v-divider>


        <router-link to="/">
          <v-btn
            class="mx-0"
            color="primary"
            large
          >
            Back to Home
          </v-btn>
          </router-link>
        </v-flex>
      </v-layout>
    </v-container>
 </div>
</v-app>
</template>

<script>
    export default {
        mounted() {
        }
    }
</script>
