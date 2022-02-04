const loading =  { template:`    <v-col cols="6" class="text-subtitle-1 text-center">
                                        Saving ...
                                    <v-progress-linear
                                      color="deep-purple accent-4"
                                      :active="loading"
                                      :indeterminate="loading"
                                      rounded
                                      height="6"
                                    ></v-progress-linear>
                                  </v-col>`,
                                  data: () => ({
                                    loading: true,
                                  }),
                  }

const main = {
              template: ` <v-simple-table>
                              <template v-slot:default>
                                <thead v-if="options.length > 0">
                                  <tr>
                                    <th class="text-left">
                                      Email
                                    </th>
                                    <th class="text-left">
                                      Code
                                    </th>
                                    <th class="text-left">
                                      Delete
                                    </th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr
                                    v-for="item in options"
                                    :key="item.name"
                                  >
                                    <td>{{ item.email }}</td>
                                    <td>{{ item.code }}</td>
                                    <td

                                    class="row-pointer"
                                    >
                                    <v-icon @click="deleteit(item.email)">mdi-delete</v-icon>
                                    </td>
                                  </tr>
                                </tbody>
                                <v-alert
                                    v-if="options.length == 0"
                                    border="bottom"
                                    colored-border
                                    type="warning"
                                    elevation="2"
                                  >
                                   No Leadform forwarding configured
                                   Klick on plus to create one
                                  </v-alert>
                              </template>
                            </v-simple-table>`,
                          data: function (){
                            return{
                              options: asemt_option

                            }
                          },
                          methods:{
                            deleteit(email){

                              vm.swapComponent('view01');
                              var xhttp = new XMLHttpRequest();
                               xhttp.onreadystatechange = function() {
                                 if (this.readyState == 4 && this.status == 200) {

                                  var response = xhttp.response;

                                       console.log(response)
                                       if(response != 0){
                                         window.asemt_option = JSON.parse(response);
                                         setTimeout(function(){
                                          vm.swapComponent('view02');
                                         },1500);
                                     }else{
                                           setTimeout(function(){vm.swapComponent('view02');},1000);
                                     }
                                 }
                               };
                               xhttp.open("POST", asemt_ajax_url, true);
                               xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                               xhttp.send('action=asemt_change_settings_activate&update=0&email='+email);
                            }
                          }
                        }

const add = {
              template: `   <div>
                              <v-text-field
                                label="Insert Your Email"
                                :rules="rules"
                                v-model="email"
                              ></v-text-field>
                              <v-text-field
                              class="mt-5"
                              label="Your Secret Code"
                              :rules="rules2"
                              v-model="code"
                              width="100%;"
                              >

                              </v-text-field>
                              <v-btn
                                  class="mt-5"
                                  color="primary"
                                  elevation="2"
                                  @click="update()"
                                >Create</v-btn>
                                <v-spacer></v-spacer>
                                <v-alert
                                    class="mt-5"
                                    border="top"
                                    colored-border
                                    type="info"
                                    elevation="2"
                                  >
                                    The Code should be unique and difficult. Example:
                                    AyS+23349dWE
                              </v-alert>
                            </div>

                        `,
                        data: () => {
                          return{
                               rules: [
                                 value => !!value || 'Required.',
                                 value => value.match(/^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/) != null || 'No valid Email'
                               ],
                               rules2: [
                                 value => !!value || 'Required.'

                               ],
                               email:'',
                               code:''

                         }
                         },
                         methods:{
                           update(){

                              if(this.email.match(/^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/) && this.code !== ''){
                                vm.swapComponent('view01');
                                var xhttp = new XMLHttpRequest();
                                 xhttp.onreadystatechange = function() {
                                   if (this.readyState == 4 && this.status == 200) {

                                    var response = xhttp.response;

                                         console.log(response)
                                         if(response != 0){
                                           window.asemt_option = JSON.parse(response);
                                           setTimeout(function(){
                                             vm.swapComponent('view02');
                                           },1500);
                                       }else{
                                         vm.swapComponent('view03');
                                       }
                                   }
                                 };
                                 xhttp.open("POST", asemt_ajax_url, true);
                                 xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                                 xhttp.send('action=asemt_change_settings_activate&update=1&email='+this.email+'&code='+this.code);


                              }
                           }
                         }
            }

vm = new Vue({
    el: '#app',
    vuetify: new Vuetify(),
    data: () => ({
      currentComponent: 'view02',
      main_hadline: 'Asemt Video',
      url: asemt_root+"wp-json/asemt/adlead"

    }),
    components: {

        'view01': loading,
        'view02': main,
        'view03': add
      },
      methods: {
        swapComponent: function(component) {
            this.currentComponent = component;
        }
    }
  })
