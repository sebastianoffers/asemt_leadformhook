<?php
if (!function_exists('asemt_change_settings')) {
   function asemt_change_settings(){



if(isset( $_POST['update'])){

    if (!function_exists('asemt_recursive_sanitize_text_field_update')) {
      function asemt_recursive_sanitize_text_field_update($array) {
              foreach ( $array as $key => &$value ) {
                  if ( is_array( $value ) ) {
                      $value = asemt_recursive_sanitize_text_field_update($value);
                  }
                  else {
                      $value = sanitize_text_field( $value );
                  }
              }

              return $array;
          }
      }



       $update =  sanitize_text_field( $_POST['update'] );
       if(isset( $_POST['email'])){
         $email =  sanitize_text_field( $_POST['email'] );
        }
       if(isset( $_POST['code'])){
         $code =  sanitize_text_field( $_POST['code'] );
       }


       if($update == 1){//addnew

               if (get_option('asemt_ads_hook')) {

                        $option = asemt_recursive_sanitize_text_field_update(json_decode(get_option('asemt_ads_hook'),true));

                        $array = Array(
                                  'email' => $email,
                                  'code' => $code
                                );

                        array_push($option,$array);

                        if(update_option('asemt_ads_hook', json_encode($option))){
                        echo json_encode($option); exit();
                      }
               }else{

                 $array = Array(
                            Array(
                              'email' => $email,
                              'code' => $code
                              )
                            );

                      add_option("asemt_ads_hook", json_encode($array));
                      echo json_encode($array); exit;
               }

       }elseif($update == 0){//delete
         //echo "hallo";
              $option = asemt_recursive_sanitize_text_field_update(json_decode(get_option('asemt_ads_hook'),true));
              $array = Array();
                for($i=0;$i<count($option);$i++) {
                  if($option[$i]['email'] == $email){

                        //do nothing later maybe slice etc

                  }else{
                    array_push($array, $option[$i]);
                  }
              }

              update_option('asemt_ads_hook', json_encode($array));
              echo json_encode($array);

                exit();
       }

}

   }
   add_action( 'wp_ajax_' . 'asemt_change_settings_activate', 'asemt_change_settings' );
   add_action( 'wp_ajax_nopriv_' . 'asemt_change_settings_activate', 'asemt_change_settings' );
}
