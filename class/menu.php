<?php
if(!class_exists('ASEMT_Admin')){
  class ASEMT_Admin {



      public static function init() {

         add_action( 'admin_menu', array( __CLASS__, 'adminMenu' ) );


      }

      public static function adminMenu() {
          add_menu_page(
              __( 'ASEMT Ads hook', 'asemt_hook-dashboard' ),
              __( 'ASEMT Ads hook ', 'asemt_hook-dashboard' ),
              'manage_options',
              'asemt_hook-dashboard',
              array( __CLASS__, 'menuPage' ),
              'dashicons-chart-bar',
              6
          );

      }

      public static function menuPage() {

          if ( is_file( ASEMT_ROOT_INCLUDE . 'options.php' ) ) {
              include_once ASEMT_ROOT_INCLUDE . 'options.php';
          }
      }




  }

  ASEMT_Admin::init();
}
