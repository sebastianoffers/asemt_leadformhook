<?php
/**
  * Plugin Name: ASEMT Leadform Hook
  * Author: Sebastian Offers
  * Author URI: https://www.analytics-sem-tutorials.de/
  * Description: Send the Leadform from Google Ads to your Email
  * Tags: Google Ads Leadform, Google Leadform Hook, Leadform Hook
  * Version: 1.0
  * License: GPLv2 or later
  * License URI: http://www.gnu.org/licenses/gpl-2.0.html

 **/


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define("ASEMT_ROOT", plugin_dir_url( __FILE__ ));
define("ASEMT_ROOT_INCLUDE", plugin_dir_path(__FILE__));
define("ASEMT_AJAX", admin_url('admin-ajax.php'));


if (  is_admin() ) {
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

		// custom css and js

	if ( is_file(  plugin_dir_path(__FILE__) . 'class/menu.php') ) {
			require plugin_dir_path(__FILE__) . 'class/menu.php';
	};
	if ( is_file( plugin_dir_path(__FILE__) . 'ajax/ajax.php') ) {
		require plugin_dir_path(__FILE__) . 'ajax/ajax.php';
	}

	if (!function_exists('asemt_enqueue')) {
	  add_action('admin_enqueue_scripts', 'asemt_enqueue');
	  function asemt_enqueue($hook) {

	      if ( 'toplevel_page_asemt_hook-dashboard' != $hook ) {
	          return;
	      }

	    wp_enqueue_style('asemt_google', plugins_url('static/google.css',__FILE__ ));
	    wp_enqueue_style('asemt_material', plugins_url('static/material.css',__FILE__ ));
	    wp_enqueue_style('asemt_materialicons', plugins_url('static/materialdesignicons.css',__FILE__ ));
	    wp_enqueue_style('asemt_vuetify', plugins_url('static/vuetify.css',__FILE__ ));
	    wp_enqueue_style('asemt_main', plugins_url('static/main.css',__FILE__ ));
	    wp_enqueue_script('asemt_vue', plugins_url('static/vue.js',__FILE__ ));
	    wp_enqueue_script('asemt_vuetifyjs', plugins_url('static/vuetify.js',__FILE__ ));

	    $script = 'const asemt_root = "https://' . esc_js($_SERVER['HTTP_HOST']) .'/";
	             const asemt_ajax_url = "'.esc_js(ASEMT_AJAX).'";
	             ';
	    if(get_option('asemt_ads_hook')){
	      $script .= 'var asemt_option = '.json_encode(asemt_recursive_sanitize_text_field_update(json_decode(get_option('asemt_ads_hook'),true))).';';
	    }else{
	      $script .= 'var asemt_option = [];';
	    }
	    wp_register_script( 'asemt-utmscript', '' );
	    wp_enqueue_script( 'asemt-utmscript' );
	    wp_add_inline_script( 'asemt-utmscript',$script);
	  }
	}

}


if(!is_admin()){
  add_action( 'init', 'asemt_lead_setup_init' );


function asemt_lead_setup_init() {

   add_action( 'rest_api_init', 'asemt_lead_endpoint' );

   function asemt_lead_endpoint() {
    //wp-json/company_name/settings
    register_rest_route( 'asemt', '/adlead', array(
        'methods' => 'POST',
        'callback' => 'asemt_lead_callback',
    ));
}

   function asemt_lead_callback($request_data){
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

		   $parameters = $request_data->get_params();
			 $response = asemt_recursive_sanitize_text_field_update(json_decode(get_option('asemt_ads_hook'),true));

      //https://www.analytics-sem-tutorials.de/wp-json/asemt/adlead
			 if(isset($parameters["google_key"])){
						 foreach($response as $key){

										 if($key['code'] == $parameters["google_key"]){
											   $pass = $key['code']; //checked in loop 115
												 $sendto = sanitize_email($key['email']); //because wasnt in loop

										 }

						 }
		 }else{
			 exit();
		 }



			 if (isset($pass)){

							//create email body
			       $body = "";
						 if(isset($parameters["gcl_id"])){
						 $body .= "<p><b>Click ID:</b>".esc_html($parameters["gcl_id"])."</p>";
					 	 }
						 if(isset($parameters["lead_id"])){
						 $body .= "<p><b>Lead ID:</b>".esc_html($parameters["lead_id"])."</p>";
					 	 }
						 if(isset($parameters["campaign_id"])){
						 $body .= "<p><b>Campaign ID:</b>".esc_html($parameters["campaign_id"])."</p>";
					   }
						 if(isset($parameters["adgroup_id"])){
						 $body .= "<p><b>AdGroup ID:</b>".esc_html($parameters["adgroup_id"])."</p>";
					   }
						 if(isset($parameters["creative_id"])){
						 $body .= "<p><b>Creative ID:</b>".esc_html($parameters["creative_id"])."</p>";
					   }

						 	foreach ($parameters["user_column_data"] as $key) {
								if(isset($key["column_name"])){
							 		 $body .= "<p><b>".esc_html($key["column_name"]).":</b>".esc_html($key["string_value"])."</p>";
									 if($key["column_id"] == "LAST_NAME" || $key["column_id"] == "FULL_NAME"){
										 	$contactName = sanitize_text_field($key["string_value"]);
									 }
								}else{
									 $body .= "<p><b>".esc_html($key["column_id"]).":</b>".esc_html($key["string_value"])."</p>";
								}
						 	}


										 //user posted variables
										 $response = array(
							 			        'status'  => 304,
							 			        'message' => 'There was an error sending the form.'
							 			    );

							 			    $siteName = wp_strip_all_tags( trim( get_option( 'blogname' ) ) );
												if(!isset($contactName)){
												 $contactName = '';
												}
							 			    $contactEmail = $sendto;
							 			    $contactMessage = 'contact_message';

							 			    $subject = "Google Ads Lead Form  New message from $contactName";
							 			//  $body = "<h3>$subject</h3><br/>";
												$emailbody = $body;

												$to = $contactEmail;
							 			    //$to = get_option( 'admin_email' );
							 			    $headers = array(
							 			        'Content-Type: text/html; charset=UTF-8',
							 			        "Reply-To: $contactName <$contactEmail>",
							 			    );

							 			    if ( wp_mail( $to, $subject, $emailbody, $headers ) ) {
							 			        $response['status'] = 200;
							 			        $response['message'] = 'Form sent successfully.';
							 			        //$response['test'] = $emailbody;
														return json_encode($response);
							 			    }

		 }



   }
}
}
