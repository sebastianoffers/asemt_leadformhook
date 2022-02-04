<?php


?>
<noscript>The Plugin only works with activated JavaScript</noscript>
<div id="app">
    <v-app>
      <v-main>
        <v-container>
          <template>
                <v-card
                  class="mx-auto"
                  max-width="688"
                >
                  <v-toolbar
                    color="deep-purple accent-4"
                    dark
                    prominent
                  >

                    <v-app-bar-nav-icon></v-app-bar-nav-icon>

                    <v-toolbar-title>Google Ads Hook</v-toolbar-title>

                    <v-btn
                      absolute
                      bottom
                      color="white"
                      fab
                      left
                      light
                      @click="swapComponent('view03')"
                    >
                      <v-icon>mdi-plus</v-icon>
                    </v-btn>

                    <v-spacer></v-spacer>

                    <v-btn icon
                    @click="swapComponent('view02')"
                    >
                        <v-icon>mdi-home</v-icon>
                    </v-btn>
                  </v-toolbar>

                  <v-container style="height: 400px;">
                    <v-row
                      class="fill-height"
                      align-content="center"
                      justify="center"
                    >
                    Endpoint / Hook URL: {{url}}
                    <div :is="currentComponent" :swap-component="swapComponent"></div>
                    </v-row>
                  </v-container>
                </v-card>
              </template>
        </v-container>
      </v-main>
    </v-app>
  </div>
<?php
wp_enqueue_script('asemt_mainjs', plugins_url('static/main.js',__FILE__ ));
 ?>
