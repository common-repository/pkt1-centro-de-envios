<?php
/*
* @package WooCommerce PKT1 Centro de Envíos
*/

if(!class_exists('Pkt1_save_settings')){
  class Pkt1_save_settings{
    public function pkt1_save_admin_field_settings(){
      check_admin_referer('pkt1_save_settings_field_verify');

      if(!current_user_can('manage_options')){
        wp_die('No tienes permiso para editar estas opciones');
      }
	  //echo json_encode($_POST);
	  //exit;
      $pkt1_idcliente = sanitize_text_field( $_POST['pkt1_idcliente'] ) ;
      $pkt1_username = sanitize_text_field( $_POST['pkt1_username'] ) ;
      $pkt1_token = sanitize_text_field( $_POST['pkt1_token'] ) ;
      $pkt1_contenido = sanitize_text_field( $_POST['pkt1_contenido'] ) ;
      $pkt1_secret = sanitize_text_field( $_POST['pkt1_secret'] ) ;
      $pkt1_resultados = sanitize_text_field( $_POST['pkt1_resultados'] ) ;
      $pkt1_visualizacion = sanitize_text_field( $_POST['pkt1_visualizacion'] ) ;
      $pkt1_existesucursal = sanitize_text_field( $_POST['pkt1_existesucursal'] ) ;
      $pkt1_idsucursal = sanitize_text_field( $_POST['pkt1_idsucursal'] ) ;
      $pkt1_modooscuro = sanitize_text_field( $_POST['pkt1_modooscuro'] ) ;
      $pkt1_tiempos = sanitize_text_field( $_POST['pkt1_tiempos'] ) ;
      $pkt1_gastosenvio = sanitize_text_field( $_POST['pkt1_gastosenvio'] ) ;
      $pkt1_dimunique = sanitize_text_field( $_POST['pkt1_dimunique'] ) ; 
      $pkt1_cnflargo = sanitize_text_field( $_POST['pkt1_cnflargo'] ) ;	  
      $pkt1_cnfancho = sanitize_text_field( $_POST['pkt1_cnfancho'] ) ;
      $pkt1_cnfalto = sanitize_text_field( $_POST['pkt1_cnfalto'] ) ;
      $pkt1_cnfpeso = sanitize_text_field( $_POST['pkt1_cnfpeso'] ) ;
      $pkt1_cnfpieza = sanitize_text_field( $_POST['pkt1_cnfpieza'] ) ;
      $pkt1_onelabel = sanitize_text_field( $_POST['pkt1_onelabel'] ) ;
      $pkt1_boxcalc = sanitize_text_field( $_POST['pkt1_boxcalc'] ) ;
      $pkt1_taxcalc = sanitize_text_field( $_POST['pkt1_taxcalc'] ) ;
      $pkt1_StyledFrame = sanitize_text_field( $_POST['pkt1_StyledFrame'] ) ;
      $pkt1_enviosgratis = sanitize_text_field( $_POST['pkt1_enviosgratis'] ) ;	  
      $pkt1_cartcost = sanitize_text_field( $_POST['pkt1_cartcost'] ) ;

      if(!isset($_POST['pkt1_dimunique'])){
        $pkt1_cnflargo = "";  
        $pkt1_cnfancho = "";
        $pkt1_cnfalto = "";
        $pkt1_cnfpeso = "";
        $pkt1_cnfpieza="";		  
      }	 
	  if(!isset($_POST['pkt1_enviosgratis'])){
        $pkt1_cartcost = "0";          	  
      }

      $values = array(
        'pkt1_idcliente'=> $pkt1_idcliente,
        'pkt1_username'=> $pkt1_username,
        'pkt1_token'=> $pkt1_token,
        'pkt1_contenido'=> $pkt1_contenido,
        'pkt1_secret'=> $pkt1_secret,
        'pkt1_resultados'=> $pkt1_resultados,
        'pkt1_existesucursal'=> $pkt1_existesucursal,
        'pkt1_idsucursal'=> $pkt1_idsucursal,
        'pkt1_modooscuro'=> $pkt1_modooscuro,
        'pkt1_visualizacion'=> $pkt1_visualizacion,
        'pkt1_tiempos'=> $pkt1_tiempos,
        'pkt1_gastosenvio'=>$pkt1_gastosenvio,
        'pkt1_dimunique'=> $pkt1_dimunique,
        'pkt1_cnflargo'=> $pkt1_cnflargo,
        'pkt1_cnfancho'=> $pkt1_cnfancho,
        'pkt1_cnfalto'=> $pkt1_cnfalto,
        'pkt1_cnfpeso'=> $pkt1_cnfpeso,
        'pkt1_cnfpieza'=> $pkt1_cnfpieza,
        'pkt1_onelabel'=> $pkt1_onelabel,  
        'pkt1_boxcalc'=> $pkt1_boxcalc,        
		    'pkt1_taxcalc'=> $pkt1_taxcalc,         
		    'pkt1_StyledFrame'=> $pkt1_StyledFrame,  
		    'pkt1_enviosgratis'=> $pkt1_enviosgratis, 
		    'pkt1_cartcost'=> $pkt1_cartcost, 		
      );

      if(!isset($pkt1_idcliente) || empty($pkt1_idcliente) || $pkt1_idcliente == ''){
        wp_redirect( get_admin_url().'admin.php?page=pkt1_settings&error='.urlencode('La configuración no se guardó (error idcliente)'));
        exit();
      }


      if(!isset($pkt1_username) || empty($pkt1_username) || $pkt1_username == ''){
        wp_redirect( get_admin_url().'admin.php?page=pkt1_settings&error='.urlencode('La configuración no se guardó (error username)'));
        exit();
      }


      if(!isset($pkt1_secret) || empty($pkt1_secret) || $pkt1_secret == ''){
        wp_redirect( get_admin_url().'admin.php?page=pkt1_settings&error='.urlencode('La configuración no se guardó (error secret)'));
        exit();
      }

      if(!isset($pkt1_token) || empty($pkt1_token) || $pkt1_token == ''){
        wp_redirect( get_admin_url().'admin.php?page=pkt1_settings&error='.urlencode('La configuración no se guardó (error token)'));
        exit();
      }

      if((!isset($pkt1_idsucursal) || empty($pkt1_idsucursal) || $pkt1_idsucursal == '') && $pkt1_existesucursal == "enabled"){
        wp_redirect( get_admin_url().'admin.php?page=pkt1_settings&error='.urlencode('La configuración no se guardó (No se definió idsucursal)'));
        exit();
      }

      update_option( 'pkt1_settings', $values );
	  //echo json_encode( $values);
	  //exit;
      wp_redirect( get_admin_url().'admin.php?page=pkt1_settings&success='.urlencode('Configuraciones guardadas'));
    }
  }

}