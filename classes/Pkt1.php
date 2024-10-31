<?php
/*
* @package WooCommerce PKT1 Centro de Envíos
*/

if(!class_exists('Pkt1')){
  class PKT1{
    //Start Session if not started before
    public function pkt1_start_session(){
      if(!session_id()){
        session_start();
      }
    }

    // create session name
    public function pkt1_session_name(){
      $pkt1_session_name = '';
      if(is_user_logged_in()){
        $user_id = get_current_user();
        $pkt1_session_name = 'pkt1_products_'.$user_id;
      }
      else{
        $pkt1_session_name = 'pkt1_products_guest';
      }

      return $pkt1_session_name;
    }

    //init session for current user
    public function pkt1_init_session(){
      $session_name = $this->pkt1_session_name();
      if(!isset($_SESSION[$session_name])){
        $_SESSION[$session_name] = serialize(array());
      }
    }

    //get current user session
    public function pkt1_get_products(){
      $session_name = $this->pkt1_session_name();
      if(!isset($_SESSION[$session_name])){
        return false;
      }

      return unserialize($_SESSION[$session_name]);
    }

    //update user products
    public function pkt1_update_products(){
      $session_name = $this->pkt1_session_name();
      if(!is_product()){
        return false;
      }

      $viewed_products = $this->pkt1_get_products();
      if(!in_array(get_the_ID(), $viewed_products)){
        $viewed_products[] = get_the_ID( );
      }else{
        $current_product_key = array_search(get_the_ID(), $viewed_products);
        unset($viewed_products[$current_product_key]);
        $viewed_products[] = get_the_ID();
      }

      $_SESSION[$session_name] = serialize($viewed_products);

    }
  }
}