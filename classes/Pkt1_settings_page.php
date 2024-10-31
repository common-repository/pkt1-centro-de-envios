<?php
/*
* @package WooCommerce PKT1 Centro de Envíos
*/
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit si se accesa directamente
}

if(!class_exists('Pkt1_settings_page')){
  class Pkt1_settings_page{
    public function pkt1_create_settings_page(){
      add_submenu_page( 'woocommerce', __('WooCommerce PKT1 Centro de Envíos','pkt1'), __('PKT1','pkt1'), 'manage_options', 'pkt1_settings', 'pkt1_settings_page_callback');
    }
  }
}