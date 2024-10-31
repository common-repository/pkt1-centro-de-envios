<?php
/*
Plugin Name:  PKT1 Centro de Envíos
Plugin URI: https://enviospkt1.com/demo-plugin/
Description: A un click de tu envio 📦 Cotiza inteligente 🥇 evita Co2 🍃 y llega a mas lugares 🌎
Version: 1.2.1
Author: PKT1 WebCenter 📦🥇🍃🌎
Author URI: https://enviospkt1.com/ecommerce
License: GPLv2 or later
Text Domain: pkt1
*/

//Evitar el envío de datos si se accede directo a este archivo:
if(!function_exists('add_action')){
  echo 'No se permite el acceso directo.';
  exit;
}

/* ===============================
     Checar versión de wordpress
   =============================== */
if( version_compare(get_bloginfo('version'), '4.0','<')){
  $message = 'Es necesaria una versión de wordpress 4 o superior.';
  die($message);
}

/************
  CONSTANTES
 ************/
define('PKT1_PATH', plugin_dir_path(__FILE__));
define('PKT1_URI', plugin_dir_url(__FILE__));



/************
  Checar si WooCommerce está activo
 ************/

if(in_array('woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option('active_plugins')))){

  /************
    Carga JS & Styles
  ************/
  if(!function_exists('pkt1_plugin_scripts')){
    function pkt1_plugin_scripts(){
      wp_enqueue_script('pkt1-js', PKT1_URI.'assets/js/main.js', 'jQuery', '1.1.0', true);
      //Plugin Ajax JS
      wp_enqueue_script('pkt1-ajax', PKT1_URI.'assets/js/ajax.js', 'jQuery', '1.1.0', true);
      /*wp_localize_script( 'pkt1-ajax', 'pkt1_ajax_url', array(
        'ajax_url'=> admin_url('admin-ajax.php')
      ) );*/
    }
    add_action( 'admin_enqueue_scripts', 'pkt1_plugin_scripts');
  }


  if(!function_exists('pkt1_shop_plugin_scripts')){
    function pkt1_shop_plugin_scripts(){
      wp_enqueue_style('pkt1-css', PKT1_URI.'assets/css/style.css');
      wp_enqueue_script('pkt1-main-js', PKT1_URI.'assets/js/main.js', 'jQuery', '1.0.0', true);
    }
    add_action( 'wp_enqueue_scripts', 'pkt1_shop_plugin_scripts');
  }

  /************
    Funciones Ajax
  ************/
  if(!function_exists('pkt1_checksaldo_ajax_action')){
    function pkt1_checksaldo_ajax_action(){

    }
    add_action( 'wp_ajax_pkt1_checksaldo_ajax_action', 'pkt1_checksaldo_ajax_action_handle');
    add_action( 'wp_ajax_pkt1_checksaldo_ajax_action', 'pkt1_checksaldo_ajax_action_handle');
  }

  /**************
    Clase Core Administrativa
   *************/
  if(!class_exists('PKT1_core')){
    class PKT1_core{
      public function __construct(){

        /************
          Include files
        ************/
        require(PKT1_PATH.'/views/admin/settings_page.php');
        require(PKT1_PATH.'/views/front-end/pkt1_products_view.php');
        require(PKT1_PATH.'/includes/activation.php');
        require(PKT1_PATH.'/shortcodes/pkt1.php');

        /************
          Include classes
        ************/
        require(PKT1_PATH.'/classes/Pkt1_settings_page.php');
        require(PKT1_PATH.'/classes/Pkt1_save_settings.php');
        require(PKT1_PATH.'/classes/Pkt1.php');

        /************
          Hooks
        ************/
        register_activation_hook( __FILE__, 'pkt1_activation' );
        
        add_action('init', array(new Pkt1(), 'pkt1_start_session'),10);
        add_action('init', array(new Pkt1(), 'pkt1_init_session'),15);
        add_action('wp', array(new Pkt1(), 'pkt1_update_products'));
        add_action('admin_menu', array(new Pkt1_settings_page(),'pkt1_create_settings_page'));
        add_action('admin_post_pkt1_save_settings_field', array(new Pkt1_save_settings(),'pkt1_save_admin_field_settings'));
        
        
        /************
          Shortcodes
        ************/
        add_shortcode( 'pkt1', 'pkt1_shortcode' );

      }
    }
    $PKT1_core = new PKT1_core();
  }

  /**************
    Shipping Method Class
  *************/
  /*Cambiar CURL POR API HTTP DE WORDPRES */
  function pkt1_shipping_method() {
    if ( ! class_exists( 'Pkt1_Shipping_Method' ) ) {
        class Pkt1_Shipping_Method extends WC_Shipping_Method {
            /**
             * Constructor for your shipping class
             *
             * @access public
             * @return void
             */
            public function __construct() {
                $this->id                 = 'pkt1'; 
                $this->method_title       = __( 'PKT1', 'pkt1' );  
                $this->method_description = __( 'Envíos a través de PKT1 y sus alianzas.', 'pkt1' ); 

                // Availability & Countries
                $this->availability = 'including';
                $this->countries = array(
                    'MX', // México
                    'CL' // Chile
                    );

                $this->init();

                $this->enabled = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'yes';
                $this->title = isset( $this->settings['title'] ) ? $this->settings['title'] : __( 'PKT1 Shipping', 'pkt1' );
            }

            /**
             * Init your settings
             *
             * @access public
             * @return void
             */
            function init() {
                // Load the settings API
                $this->init_form_fields(); 
                $this->init_settings(); 

                // Save settings in admin if you have any defined
                add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
            }

            /**
             * Define settings field for this shipping
             * @return void 
             */
            function init_form_fields() { 
              $this->form_fields = array(
 
                'enabled' => array(
                  'title' => __( 'Habilitar', 'pkt1' ),
                  'type' => 'checkbox',
                  'description' => __( '¿Habilitar envíos con PKT1?.', 'pkt1' ),
                  'default' => 'yes'
                  ),
                  'darkmode' => array(
                    'title' => __( 'Modo oscuro', 'pkt1' ),
                    'type' => 'checkbox',
                    'default' => 'no'
                    ),
       
                'title' => array(
                   'title' => __( 'Envíar por PKT1', 'pkt1' ),
                     'type' => 'text',
                     'description' => __( 'Titulo que aparecerá en selección de envío.', 'pkt1' ),
                     'default' => __( 'PKT1', 'pkt1' )
                     ),
        
                );

            }

            /**
             * This function is used to calculate the shipping cost. Within this function we can check for weights, dimensions and other parameters.
             *
             * @access public
             * @param mixed $package
             * @return void
             */
            public function calculate_shipping( $package = array() ) {
              // We will add the cost, rate and logics in here
			  
              $settings = get_option( 'pkt1_settings');		  
              $post_data="";
              $pos=false;
              if(!isset($_POST['post_data'])){	
                if(is_checkout()){
                  return;				
                  //si es el chekout retornar ya que existe un error				
                }
                else{
                  $post_data ="";
                  $pos = false;      
                }
              }
              else{
                $post_data = sanitize_text_field( $_POST['post_data'] );
                $pos = strpos($post_data, "shipping_asegurar_envio=1");              
              }
              $store_country=get_country();
              $weight = 0;
              $cost = 0;
              $itemstotal=0;
              //Get cart cost an item totals
              foreach ($package['contents'] as $item_id => $values) {
                $_product = $values['data'];
                $cost = $cost +  $values['line_total'];
                for ($i = 0; $i <$values['quantity'] ; $i++) {
                  $itemstotal=$itemstotal+1;
                }					
              }
              //
              $country = $package["destination"]["country"];
              $details = "";
              $voltotal=0;
              $paqueteunico=false;
              if(isset($settings['pkt1_dimunique'])){
                if ($settings['pkt1_dimunique']=="enabled"){
                  $paqueteunico=true;
                }
              }
              $calcularcajas=false;
              if(isset($settings['pkt1_boxcalc'])){
                if ($settings['pkt1_boxcalc']=="enabled"){
                  $calcularcajas=true;
                }
              }
              if ($paqueteunico){
                $arrPackage=array();
                foreach ($package['contents'] as $item_id => $values) {
                  $_product = $values['data'];
                  //$cost = $cost +  $values['line_total'];
                  for ($i = 0; $i <$values['quantity'] ; $i++) {
                    $arrPackage[]=$values;
                  }					
                }	
                $cnfpieza=$settings['pkt1_cnfpieza'];
                $piezas=0;
                if($cnfpieza==""||$cnfpieza==0){
                  $piezas = 1;	
                }
                else{
                  $piezas = ceil(COUNT($arrPackage)/$cnfpieza);
                }
                //'Cnt': '".$settings['pkt1_contenido']."',  
                for ($i=0; $i < $piezas ; $i++) { 	
                  $details .= "{            
                    'Qty': 1,            
                    'Typ': 1,            
                    'Cnt': '".$settings['pkt1_contenido']."',            
                    'Hgt': '".$settings['pkt1_cnfalto']."',            
                    'Wdt': '".$settings['pkt1_cnfancho']."',            
                    'Lng': '".$settings['pkt1_cnflargo']."',            
                    'Wgt': '".$settings['pkt1_cnfpeso']."'        
                    },
                  ";
                }
			        }
              else if($calcularcajas){
                  $url_boxes = '';                  
                  if($store_country == "MX"){
                    $url_boxes = "https://api.pktuno.mx/Api/Cajas/Calcular/".$settings['pkt1_token'];
                  }
                  else if ($store_country == "CL"){
                    $url_boxes = "https://api.pktuno.cl/Api/Cajas/Calcular/".$settings['pkt1_token'];
                  }
                  $myfile = fopen("./last_request_test_resp.txt", "w") or die("Unable to open file 2!");
                  $txt = " url #:" . $url_boxes;
                  fwrite($myfile, $txt);
                  fclose($myfile);
                  foreach ($package['contents'] as $item_id => $values) {
                    $_product = $values['data'];                      
                    $cost = $cost +  $values['line_total'];	
                    //$volitem=0;	
                    $request= "[";			  
                    for ($i = 0; $i <$values['quantity'] ; $i++) {
                      $weight = $weight + $_product->get_weight();							
                      $request .= "{           
                        'alto': '".$_product->get_height()."',            
                        'ancho': '".$_product->get_width()."',            
                        'largo': '".$_product->get_length()."',            
                        'peso': '".$_product->get_weight()."'        
                        },
                      ";
                    }	
                    $request .= "]";					  
                  }	
                  $myfile = fopen("./last_request_test_err.txt", "w") or die("Unable to open file 2!");
                  $txt = " url #:" . $url_boxes." body:".$request;
                  fwrite($myfile, $txt);
                  fclose($myfile);											
                  $args = array(
                    'body'        => $request,
                    'timeout'     => '30',
                    'redirection' => '10',
                    'httpversion' => '1.1',
                    'blocking'    => true,
                    'headers'     => array(
                              'Cache-Control' => 'no-cache',    
                              'Content-Type' => 'application/json'),
                  );
                  $response = wp_remote_post($url_boxes, $args );
                  if ( is_wp_error( $response )) {
                    $error_message = $response->get_error_message();
                    echo "Something went wrong: $error_message";
                    $myfile = fopen("./last_request_test_err.txt", "w") or die("Unable to open file 2!");
                    $txt = " Error #:" . $error_message;
                    fwrite($myfile, $txt);
                    fclose($myfile);						
                  }
                  else{
                    $resp = $response;			
                    if($resp["response"]["code"]==404){
                      //si no enconto caja, agarrara la configuracion "etiqueta unica"
                      $etiquetaunica=true;
                      goto alternative;
                    }                    
                    $boxresp = json_decode($resp["body"], true);
                    $details .= "{     
                      'qty': '1',            
                      'typ': 1,            
                      'cnt': '".$settings['pkt1_contenido']."',            
                      'hgt': '".$boxresp["alto"]."',            
                      'wdt': '".$boxresp["ancho"]."',            
                      'lng': '".$boxresp["largo"]."',            
                      'wgt': '".$weight."'        
                      },
                    ";                    
                  }                                 
              }
              else{
                  $etiquetaunica=false;
                  if(isset($settings['pkt1_onelabel'])){
                    if ($settings['pkt1_onelabel']=="enabled"){
                      $etiquetaunica=true;
                    }
                  }
                  alternative:
                  if ($etiquetaunica&&$itemstotal>1) {                      
                    //etiqueta unica
                    $weight = 0;
                    $arrPackage=array();                
                    foreach ($package['contents'] as $item_id => $values) {
                      $_product = $values['data'];                      
                      //$cost = $cost +  $values['line_total'];	
                      $volitem=0;				
                      for ($i = 0; $i <$values['quantity'] ; $i++) {
                            $volitem=($_product->get_height()*$_product->get_width()*$_product->get_length())/4000;
                            $weight = $weight + $_product->get_weight();
                            $voltotal=$voltotal+$volitem;
                      }					
                    }
                    $lado=ceil(pow(($voltotal*4000), 1/3));
                    //sumar las alturas, 
                    //agregar paquete unico
                    $details .= "{     
                        'qty': '1',            
                        'typ': 1,            
                        'cnt': '".$settings['pkt1_contenido']."',            
                        'hgt': '".$lado."',            
                        'wdt': '".$lado."',            
                        'lng': '".$lado."',            
                        'wgt': '".$weight."'        
                    },
                    ";
                    //fin etiqueta unica
                  }
                  else{   
                    foreach ($package['contents'] as $item_id => $values) {
                      $_product = $values['data'];
                      $weight = $weight + $_product->get_weight() * $values['quantity'];
                      //$cost = $cost +  $values['line_total'];
                      //".$values['quantity']."',
                      $details .= "{            
                        'Qty': '".$values['quantity']."',            
                        'Typ': 1,            
                        'Cnt': '".$settings['pkt1_contenido']."',            
                        'Hgt': '".$_product->get_height()."',            
                        'Wdt': '".$_product->get_width()."',            
                        'Lng': '".$_product->get_length()."',            
                        'Wgt': '".$_product->get_weight()."'        
                      },
                      ";
                    }              
                  }
              }

              //echo $cost;
	   
														   
			   
									  
						 
									 
										 
								   
	 
              //die();
	   
            
              $weight = wc_get_weight( $weight, 'kg' );

              // The country/state
              $store_raw_country = get_option( 'woocommerce_default_country' );

              // Split the country/state
              $split_country = explode( ":", $store_raw_country );

              // Country and state separated:
              $store_country = $split_country[0];
              $store_state   = $split_country[1];
              $seguro = 0;


              // Nótese el uso de ===. Puesto que == simple no funcionará como se espera
              // porque la posición de 'a' está en el 1° (primer) caracter.
              if ($pos === false) {
                  $seguro = 0;
              } else {
                $seguro = ceil($cost);
              }
              $added_rate = 0;
             // GENERAR VARIABLES Y SANITIZE
              $address_1 = sanitize_text_field( $package["destination"]["address_1"] ) ;
              $address_2 = sanitize_text_field( $package["destination"]["address_2"] ) ;
              $postcode = sanitize_text_field( $package["destination"]["postcode"] ) ;
              $city = sanitize_text_field( $package["destination"]["city"] ) ;
              $Mnp = sanitize_text_field( $package["destination"]["city"] ) ;
              $state = sanitize_text_field( $package["destination"]["state"] ) ;
              $country = sanitize_text_field( $package["destination"]["country"] ) ;
              if ($store_country=="CL"){
                if ($city==""){
					        //no cotizar si va vacia la comuna
                  return;
                }			
                $json_comuna = wp_remote_get("https://api.pktuno.cl/Api/Cobertura/Comuna/".$state."/".$city);
                $response = wp_remote_retrieve_body( $json_comuna );
                $direccion = json_decode( $response, true );
                $address_2 = $direccion["comuna"];//Comuna			  
                $city = $direccion["comuna"];//Comuna
                $Mnp = $direccion["provincia"];//Provincia
                $postcode = $direccion["cp"];//Cp					
              }
              elseif($store_country=="MX"){				
				$post_data = sanitize_text_field( urldecode($_POST['post_data']));
				$get_array;
				parse_str($post_data, $get_array);				
				$colonia = isset($get_array["billing_colonia"]) ? $get_array["billing_colonia"] : "";
				if(isset($get_array["shipping_colonia"])){
					if($get_array["shipping_colonia"]!=""){
            if($get_array["shipping_colonia"]=!"Seleccione"){
						  $colonia=$get_array["shipping_colonia"];
            }
					}
				}
                if ($colonia==""){
                  return;
                }	
				//$colonia ="Centro";
                $json_datos = wp_remote_get("https://api.pktuno.mx/Api/Cobertura/".$postcode."/".$colonia);
                $response = wp_remote_retrieve_body( $json_datos );
                $direccion = json_decode( $response, true );
                $address_2 = $direccion["colonia"];	//colonia		  
                $city = $direccion["ciudad"];
                $Mnp = $direccion["municipio"];
                $state = $direccion["estado"];
                //reglas para mexico
              }

              //Regla envios gratuitos
              $enviosGratis=false;				  
              if(isset($settings['pkt1_enviosgratis'])){
                if ($settings['pkt1_enviosgratis']=="enabled"){
                  if($settings['pkt1_cartcost']>0){					  					  
                    $CartCost=WC()->cart->subtotal;
                      if($CartCost>$settings['pkt1_cartcost']){
                      $settings['pkt1_resultados']=2;
                      $enviosGratis=true;	                      
                    }          
                  }				  
                }				
              }
              ///si el cliente es zero clean y la region es la RM activar envios gratuitos
              if($settings['pkt1_token']=="63964329-67a1-11ea-95a0-00505628da54733"&&$state=="RM"){   
                $CartCost=WC()->cart->subtotal;
                if($CartCost>=24000){
                  $settings['pkt1_resultados']=2;
                  $enviosGratis=true;	                      
                }                	   
              }
              $cuerpo = "
              {     
                'Destination': {  
                  'Cp': '".$postcode."',        
                  'Str': '".$address_1."',        
                  'Col': '".$address_2."',        
                  'Cty': '".$city."',        
                  'Mnp': '".$Mnp."',        
                  'Ste': '".$state."',        
                  'Ctr': '".$country."'  
                },    
                'Security': {        
                  'Usr': '".$settings['pkt1_username']."',        
                  'Key': '".$settings['pkt1_token']."',        
                  'Cot': '".$settings['pkt1_resultados']."'   
                },    
                'ShippingData': [        
                  ".$details."
                ],    
                'ShipCnf': {        
                  'Isv': '".$seguro."',        
                  'Dtp': 1,        
                  'Din': '',
                  'CartCost':'".WC()->cart->subtotal."'
                },
                'CartData': {         
                  'TotalCost': '".ceil($cost)."',
                  'TotalItems':   '".$itemstotal."'
                }
              }";

                //echo $cuerpo;
                //echo $cost;
                //die();
              $url_country = '';
              if($store_country == "MX"){
                $url_country = "https://api.pktuno.mx/Api/Cotizaciones/Cotizar";
              }
              else if ($store_country == "CL"){
                $url_country = "https://api.pktuno.cl/Api/Cotizaciones/Cotizar";
              }

              $test = false;
              if($test){
                if($store_country == "MX"){
                  $url_country = "http://app.pktuno.mx:81/ws/Api/Cotizaciones/Cotizar";
                }
                else if ($store_country == "CL"){
                  $url_country = "http://app.pktuno.cl:81/ws/Api/Cotizaciones/Cotizar";
                }
              }
              
              $myfile = fopen("./last_request.json", "w") or die("Unable to open file!");
              
              $txt = 'URL: '.$url_country.'\n'.json_encode($cuerpo, JSON_UNESCAPED_UNICODE);
              fwrite($myfile, $txt);
              fclose($myfile);

              $args = array(
                'body'        => $cuerpo,
                'timeout'     => '30',
                'redirection' => '10',
                'httpversion' => '1.1',
                'blocking'    => true,
                'headers'     => array(
                                  'Cache-Control' => 'no-cache',    
                                  'Content-Type' => 'application/json'),
              );

              $response = wp_remote_post( $url_country, $args );
              
              $resp = "";
              if ( is_wp_error( $response ) ) {
                $error_message = $response->get_error_message();
                echo "Something went wrong: $error_message";

                $myfile = fopen("./last_request_test_err.txt", "w") or die("Unable to open file 2!");
                $txt = " Error #:" . $error_message;
                fwrite($myfile, $txt);
                fclose($myfile);
              }else {
                $resp = $response;
                //echo $cuerpo;
                //die();
                
                $myfile = fopen("./last_request_test_resp.txt", "w") or die("Unable to open file 2!");
                $txt = json_encode($response["body"], JSON_UNESCAPED_UNICODE);
                fwrite($myfile, $txt);
                fclose($myfile);
                
                $quotationresp = json_decode($resp["body"], true);
                //Obtener variable de impuestos
                $TaxCalc=false;				  
                if(isset($settings['pkt1_taxcalc'])){
                  if ($settings['pkt1_taxcalc']=="enabled"){
                    $TaxCalc=true;
                  }
                }
                foreach ((array)$quotationresp["data"] as $quote) {

                  $gastosenvio=false;
                  if(isset($settings['pkt1_gastosenvio'])){
                    if ($settings['pkt1_gastosenvio']!=""){
                      $gastosenvio=true;
                    }
                  }
                  if($gastosenvio){
                    $quote['PrecioNormal']=$quote['PrecioNormal']+$settings['pkt1_gastosenvio'];
                    $quote['Precio']=$quote['Precio']+$settings['pkt1_gastosenvio'];
                  }
                  if($enviosGratis){					  
                    $quote['Descuento'] = $quote['PrecioNormal'];
                    $quote['Precio'] =0;
                  }
                  $metadata = array(
                    'PrecioNormal' => $quote['PrecioNormal'],
                    'Descuento' => $quote['Descuento'],
                    'Precio' => $quote['Precio'],
                    'FechaEntregaDMY' => $quote['FechaEntregaDMY'],
                    'Alianza' => $quote['Alianza'],
                    'GlobalProductCode' => $quote['GlobalProductCode'],
                    'LocalProductCode' => $quote['LocalProductCode'],
                    'LogoEmp' => $quote['LogoEmp'],
                    'Dias' => $quote['Dias'],
                    'ServiceName' => $quote['ServiceName'],
					          'IdAlianza' => $quote['IdAlianza'],
                    'invocationid' => $quote['invocationid'],
                    'asegurado'=>$seguro > 0,
                    'envioGratuito'=>$enviosGratis
                  );
                  $quote['asegurado'] = $seguro > 0;       
                  $rate = array(
                    'id' => $this->id.'-'.$quote['invocationid'],
                    'label' => 'PKT1 - '.$quote['ServiceName'],//$this->title,
                    'cost' => round($quote['Precio'],2),
                    'meta_data' => $metadata
                    );				  
                  //incluir impuestos
                  if($TaxCalc){					             
                    $taxRate=0;
                    if($store_country == "MX")
                    {
                    $taxRate=16;
                    }
                    else if($store_country=="CL")
                    {
                    $taxRate=19;
                    }
                    $rate = array(
                    'id' => $this->id.'-'.$quote['invocationid'],
                    'label' => 'PKT1 - '.$quote['ServiceName'],//$this->title,
                    'cost' => round($quote['Precio']/(1+($taxRate/100)),2),
                    'taxes'=>$quote['Precio']-(round($quote['Precio']/(1+($taxRate/100)),2)),
                    'calc_tax'=>'per_order',
                    'meta_data' => $metadata
                    );
                  }
                  $this->add_rate($rate);
                }
              }                
            }
        }
    }
  }

  add_action( 'woocommerce_shipping_init', 'pkt1_shipping_method' );
  
  function add_pkt1_shipping_method( $methods ) {
      $methods[] = 'PKT1_Shipping_Method';
      return $methods;
  }
  add_filter( 'woocommerce_shipping_methods', 'add_pkt1_shipping_method' );
}

/**************
  Postal Code Enable/Disable
*************/
function get_country(){
  $store_raw_country = get_option( 'woocommerce_default_country' );

    // Split the country/state
    $split_country = explode( ":", $store_raw_country );

    // Country and state separated:
    $store_country = $split_country[0];
  return $store_country;
}

function enable_postal_code( $active ){
  $store_country=get_country();
    if ($store_country=="CL"){
      $active=false;
    }
  return $active;
}

add_filter( 'woocommerce_shipping_calculator_enable_postcode', 'enable_postal_code' );


/**************
  Shipping Method Custom Label
*************/
function custom_shipping_method_label( $label, $method ){
  $rate_id = $method->id; // The Method rate ID (Method Id + ':' + Instance ID)
  $settings = get_option( 'pkt1_settings');
      // The country/state
  $store_raw_country = get_option( 'woocommerce_default_country' );

  // Split the country/state
  $split_country = explode( ":", $store_raw_country );

  // Country and state separated:
  $store_country = $split_country[0];
  $store_state   = $split_country[1];
  // Continue only if it is "flat rate"
  $pos = strpos($method->method_id, 'pkt1');
  if( $pos === false ) return '
  <div class="pkt1_quote" onclick="marcarseleccionado()">
    '.$label.'
  </div>';

  switch ( $method->meta_data["Alianza"] ) {
      case 'Fedex':
          $txt = __('Est delivery: 2-5 days'); // <= Additional text
          break;
      case 'DHL':
          $txt =  __('Est delivery: 1 day'); // <= Additional text
          break;
      case 'UPS':
          $txt =  __('Est delivery: 2-3 days'); // <= Additional text
          break;
      // for case '2' and others 'flat rates' (in case of)
      default:
          $txt =  __('Est delivery: 2-400 days'); // <= Additional text
  }
  //<img src="'.$method->meta_data["LogoEmp"].'" width="100"> 
  $ocultartiempos=false;
  if (isset($settings['pkt1_tiempos']))
  {
    if($settings['pkt1_tiempos'] == "enabled" ){
      $ocultartiempos=true;
    }
  }
  $TaxCalc=false;				  
  if(isset($settings['pkt1_taxcalc'])){
    if ($settings['pkt1_taxcalc']=="enabled"){
      $TaxCalc=true;
    }
  }
  $ClassicFrame=false;				  
  if(isset($settings['pkt1_StyledFrame'])){
    if ($settings['pkt1_StyledFrame']=="enabled"){
      $ClassicFrame=true;
    }
  }
  if ($ocultartiempos){
    if($ClassicFrame){
      return '
      <div class="pkt1_quote'.(isset($settings['pkt1_modooscuro']) ? ($settings['pkt1_modooscuro'] == "enabled" ? ' pkt1_darkmode':''):'').(isset($settings['pkt1_visualizacion']) ? ($settings['pkt1_visualizacion'] == "tarjeta" ? ' pkt1_tarjeta':''):'').'" onclick="marcarseleccionado()"  style="display: inline-block;">			
        <div style="min-width:75%">
        <strong>PKT1 '.$method->meta_data["ServiceName"].'</strong>
        </div>
        <div class="">
        <div>
          '.
          (
          $method->meta_data["envioGratuito"] ? 
            '<div style="text-decoration:line-through;">$ '.($store_country=="MX"?number_format($method->meta_data["PrecioNormal"],2,'.',','):number_format($method->meta_data["PrecioNormal"],2,',','.')).'<small>'.($store_country=="MX"?"MXN":"CLP").'</small></div> Envío sin costo! '				  										
          :
            '$ '.($store_country=="MX"?number_format($method->meta_data["Precio"],2,'.',','):number_format($method->meta_data["Precio"],2,',','.')).' <small>'.($store_country=="MX"?"MXN":"CLP").'</small>'					
          )
          .'			
          <div style="display:block; margin-top:-10px">
          <small style="font-size:0.5em">Alianza: '.$method->meta_data["Alianza"].'</small>'
          .($method->meta_data["asegurado"] ? ',<small style="font-size:0.5em">Envío asegurado</small>':'').'
          '.($TaxCalc ? ',<small style="font-size:0.5em">Impuestos Inc.</small>':'').'
          </div>
        </div>
        </div>
      </div>';
    }
    else{
    return '
      <div class="pkt1_quote'.(isset($settings['pkt1_modooscuro']) ? ($settings['pkt1_modooscuro'] == "enabled" ? ' pkt1_darkmode':''):'').(isset($settings['pkt1_visualizacion']) ? ($settings['pkt1_visualizacion'] == "tarjeta" ? ' pkt1_tarjeta':''):'').'" onclick="marcarseleccionado()"  style="display: inline-block;">
        <img src="'.$method->meta_data["LogoEmp"].'" width="100"> 
        <div class="pkt1_detalle pkt1_dias hiderow" style="min-width:75%">
        <strong style="color:white">'.$method->meta_data["ServiceName"].'</strong>
        </div>		  
        <div class="pkt1_detalle pkt1_dias showtarjeta" style="min-width:75%">
        <strong style="color:white">'.$method->meta_data["ServiceName"].'</strong>
        </div>
        <div class="pkt1_diasprecio">
        <div class="pkt1_precio'.($method->meta_data["PrecioNormal"] == $method->meta_data["Precio"] ? '':' pkt1_descuento').'">
          '.
          (
          $method->meta_data["envioGratuito"] ? 
            '<div style="text-decoration:line-through;">$ '.($store_country=="MX"?number_format($method->meta_data["PrecioNormal"],2,'.',','):number_format($method->meta_data["PrecioNormal"],2,',','.')).'<small>'.($store_country=="MX"?"MXN":"CLP").'</small></div> Envío sin costo! '				  										
          :
            '$ '.($store_country=="MX"?number_format($method->meta_data["Precio"],2,'.',','):number_format($method->meta_data["Precio"],2,',','.')).' <small>'.($store_country=="MX"?"MXN":"CLP").'</small>'					
          )
          .'				  
          '.($method->meta_data["asegurado"] ? '<div style="display:block; margin-top:-8px"><small style="font-size:0.5em">Envío asegurado</small></div>':'').'
          '.($TaxCalc ? '<div style="display:block; margin-top:-3px"><small style="font-size:0.5em">Impuestos Inc.</small></div>':'').'
        </div>
        </div>
      </div>';
    }
  }
  else{
    if($ClassicFrame){
      return '
      <div class="pkt1_quote'.(isset($settings['pkt1_modooscuro']) ? ($settings['pkt1_modooscuro'] == "enabled" ? ' pkt1_darkmode':''):'').(isset($settings['pkt1_visualizacion']) ? ($settings['pkt1_visualizacion'] == "tarjeta" ? ' pkt1_tarjeta':''):'').'" onclick="marcarseleccionado()"  style="display: inline-block;">			  			 		  
        <div>
        <strong>PKT1 '.$method->meta_data["ServiceName"].'</strong>
        </div>
        <div >
        <div style="min-width:65%"><small>De '.($method->meta_data["Dias"]).' a '.($method->meta_data["Dias"]+1).' '.($method->meta_data["Dias"]+1 > 1 ? 'Días' : 'Día') .' </small></div>
        <div>
        '.
          (
          $method->meta_data["envioGratuito"] ? 
            '<div style="text-decoration:line-through;">$ '.($store_country=="MX"?number_format($method->meta_data["PrecioNormal"],2,'.',','):number_format($method->meta_data["PrecioNormal"],2,',','.')).'<small>'.($store_country=="MX"?"MXN":"CLP").'</small></div> Envío sin costo! '				  										
          :
            '$ '.($store_country=="MX"?number_format($method->meta_data["Precio"],2,'.',','):number_format($method->meta_data["Precio"],2,',','.')).' <small>'.($store_country=="MX"?"MXN":"CLP").'</small>'					
          )
          .'
          <div style="display:block; margin-top:-10px">
          <small style="font-size:0.5em">Alianza: '.$method->meta_data["Alianza"].'</small>'
          .($method->meta_data["asegurado"] ? ',<small style="font-size:0.5em">Envío asegurado</small>':'').'
          '.($TaxCalc ? ',<small style="font-size:0.5em">Impuestos Inc.</small>':'').'
          </div>
        </div>
        </div>
      </div>';
    }
    else{
      return '
      <div class="pkt1_quote'.(isset($settings['pkt1_modooscuro']) ? ($settings['pkt1_modooscuro'] == "enabled" ? ' pkt1_darkmode':''):'').(isset($settings['pkt1_visualizacion']) ? ($settings['pkt1_visualizacion'] == "tarjeta" ? ' pkt1_tarjeta':''):'').'" onclick="marcarseleccionado()"  style="display: inline-block;">
        <img src="'.$method->meta_data["LogoEmp"].'" width="100"> 
        <div class="pkt1_detalle hiderow">
        <strong>'.$method->meta_data["ServiceName"].'</strong>
        </div>		  
        <div class="pkt1_detalle showtarjeta">
        <strong>'.$method->meta_data["ServiceName"].'</strong>
        </div>
        <div class="pkt1_diasprecio">
        <div class="pkt1_dias" style="min-width:65%">De '.($method->meta_data["Dias"]).' a '.($method->meta_data["Dias"]+1).' '.($method->meta_data["Dias"]+1 > 1 ? 'Días' : 'Día') .' </div>
        <div class="pkt1_precio'.($method->meta_data["PrecioNormal"] == $method->meta_data["Precio"] ? '':' pkt1_descuento').'">
          '.
          (
          $method->meta_data["envioGratuito"] ? 
            '<div style="text-decoration:line-through;">$ '.($store_country=="MX"?number_format($method->meta_data["PrecioNormal"],2,'.',','):number_format($method->meta_data["PrecioNormal"],2,',','.')).'<small>'.($store_country=="MX"?"MXN":"CLP").'</small></div> Envío sin costo! '				  										
          :
            '$ '.($store_country=="MX"?number_format($method->meta_data["Precio"],2,'.',','):number_format($method->meta_data["Precio"],2,',','.')).' <small>'.($store_country=="MX"?"MXN":"CLP").'</small>'					
          )
          .'
          '.($method->meta_data["asegurado"] ? '<div style="display:block; margin-top:-8px"><small style="font-size:0.5em">Envío asegurado</small></div>':'').'
          '.($TaxCalc ? '<div style="display:block; margin-top:-3px"><small style="font-size:0.5em">Impuestos Inc.</small></div>':'').'
        </div>
        </div>
      </div>';			
    }
  }
//Original
// return '
  // <div class="pkt1_quote'.(isset($settings['pkt1_modooscuro']) ? ($settings['pkt1_modooscuro'] == "enabled" ? ' pkt1_darkmode':''):'').(isset($settings['pkt1_visualizacion']) ? ($settings['pkt1_visualizacion'] == "tarjeta" ? ' pkt1_tarjeta':''):'').'" onclick="marcarseleccionado()"  style="display: inline-block;">
    // <img src="'.$method->meta_data["LogoEmp"].'" width="100">  
    // <div class="pkt1_detalle hiderow">
    // Entrega estimada:<strong>'.$method->meta_data["FechaEntregaDMY"].'<br>'.$method->meta_data["ServiceName"].'</strong>
    // </div>		  
    // <div class="pkt1_detalle showtarjeta">
    // Entrega estimada:<br><strong>'.$method->meta_data["FechaEntregaDMY"].'<br>'.$method->meta_data["ServiceName"].'</strong>
    // </div>
    // <div class="pkt1_diasprecio">
    // <div class="pkt1_dias" style="min-width:75%">De '.($method->meta_data["Dias"]).' a '.($method->meta_data["Dias"]+1).' '.($method->meta_data["Dias"] > 1 ? 'Días' : 'Día') .' </div>
    // <div class="pkt1_precio'.($method->meta_data["PrecioNormal"] == $method->meta_data["Precio"] ? '':' pkt1_descuento').'">
      // $ '.($store_country=="MX"?number_format($method->meta_data["Precio"],2,'.',','):number_format($method->meta_data["Precio"],2,',','.')).' <small>'.($store_country=="MX"?"MXN":"CLP").'</small>
      // '.($method->meta_data["asegurado"] ? '<div style="display:none; margin-top:-46px"><small>Envío asegurado</small></div>':'').'
    // </div>
    // </div>
  // </div>';
}


add_filter('woocommerce_cart_shipping_method_full_label', 'custom_shipping_method_label', 10, 2);

/**************
  Shipping Method After Success Checkout
*************/
function pkt1_booking( $order_id ) {
  if ( ! $order_id )
      return;


  // Allow code execution only once 
  if( ! get_post_meta( $order_id, '_thankyou_action_done', true ) ) {
      // Get an instance of the WC_Order object
      $order = wc_get_order( $order_id );
      /*echo '<h1>ORDER : </h1>';
      var_dump();
      echo '<br><br><br><br>';*/
      if($order->is_paid())
          $paid = __('yes');
      else
          $paid = __('no');

      // Loop through order items
      foreach ( $order->get_items() as $item_id => $item ) {
          // Get the product object
          $product = $item->get_product();
          // Get the product Id
          $product_id = $product->get_id();
          // Get the product name
          $product_id = $item->get_name();
      }

      // Output some data
      //echo '<p>Order ID: '. $order_id . ' — Order Status: ' . $order->get_status() . ' — Order is paid: ' . $paid . '</p>';

      // Flag the action as done (to avoid repetitions on reload for example)
      //$order->update_meta_data( '_thankyou_action_done', true );
      //$order->save();
      
      $tmpalianza='';
      $tmpidalianza='';
      $tmpservicename='';
      $tmptimeticks='';
      $tmpdias='';
      $tmplogoEmp='';
      $tmpfechaEntega='';
      $tmpfechaEntregaDMY='';
      $tmpprecioNormal='';
      $tmpprecio='';
      $tmpdescuento='';
      $tmpglobalProductCode='';
      $tmplocalProductCode='';
      $tmpinvocationid='';
      //echo '<div>';
      foreach($order->get_shipping_methods() as $shipping_method ){
          /*echo '<p><strong>Shipping Method ID:</strong> '. $shipping_method->get_method_id().'<br>
          <strong>Shipping Method name:</strong> '. $shipping_method->get_name().'<br>
          <strong>Shipping Method title:</strong> '. $shipping_method->get_method_title().'<br>
          <strong>Shipping Method Total:</strong> '. $shipping_method->get_total().'<br>
          <strong>Shipping Method Total tax:</strong> '. $shipping_method->get_total_tax().'</p><br>';
          */
          foreach($shipping_method->get_meta_data() as $id_meta => $meta ){
            switch($meta->key){
              case 'Alianza': $tmpalianza = $meta->value; break;
              case 'IdAlianza': $tmpidalianza = $meta->value; break;
              case 'ServiceName': $tmpservicename = $meta->value; break;
              case 'GlobalProductCode': $tmpglobalProductCode = $meta->value; break;
              case 'LocalProductCode': $tmplocalProductCode = $meta->value; break;
              case 'invocationid': $tmpinvocationid = $meta->value; break;
            }
            //echo '<strong> '.var_dump($meta->key).':</strong> '.'</p><br>';
          }
          //echo json_encode($shipping_method->get_meta_data());
      }
      //echo '</div>';

      $quotationdata = "{
        'alianza': '".$tmpalianza."',
        'idAlianza': ".$tmpidalianza.",
        'serviceName': '".$tmpservicename."',        
        'globalProductCode': '".$tmpglobalProductCode."',
        'localProductCode': '".$tmplocalProductCode."',
        'invocationid': ".$tmpinvocationid."
      }";

      //echo '<strong> '.var_dump($quotationdata).':</strong> '.'</p><br>';

      //INICIA CURL
      
        $settings = get_option( 'pkt1_settings');
        // The main address pieces:
        $blogname     = get_option( 'blogname' );
        $adminemail     = get_option( 'admin_email' );
        $store_address     = get_option( 'woocommerce_store_address' );
        $store_address_2   = get_option( 'woocommerce_store_address_2' );
        $store_city        = get_option( 'woocommerce_store_city' );
        $store_postcode    = get_option( 'woocommerce_store_postcode' );

        // The country/state
        $store_raw_country = get_option( 'woocommerce_default_country' );

        // Split the country/state
        $split_country = explode( ":", $store_raw_country );

        // Country and state separated:
        $store_country = $split_country[0];
        $store_state   = $split_country[1];


        $url_country = '';
        if($store_country == "MX"){
          $url_country = "https://api.pktuno.mx/Api/wslogin";
        }
        else if ($store_country == "CL"){
          $url_country = "https://api.pktuno.cl/Api/wslogin";
        }


        $test = false;
        if($test){
          if($store_country == "MX"){
            $url_country = "http://app.pktuno.mx:81/ws/Api/wslogin";
          }
          else if ($store_country == "CL"){
            $url_country = "http://app.pktuno.cl:81/ws/Api/wslogin";
          }
        }

        $cuerpo = "{
          'User': '".$settings['pkt1_username']."',
          'ApiKey': '".$settings['pkt1_token']."',
          'Secret': '".$settings['pkt1_secret']."'
        }";

        $args = array(
          'body'        => $cuerpo,
          'timeout'     => '30',
          'redirection' => '10',
          'httpversion' => '1.1',
          'blocking'    => true,
          'headers'     => array(   
                            'Content-Type' => 'application/json'),
        );

        $response = wp_remote_post( $url_country, $args );
        
        $resp = "";
        if ( is_wp_error( $response ) ) {
          $error_message = $response->get_error_message();
          //echo "cURL Error #:" . $err;
          echo '<script>alert("Ocurrió un error:  +'.$error_message.'");</script>'; 
        } else {
          $resp = $response;
          $quotationresp = json_decode($resp["body"], true);
          if($quotationresp['isError']){
            echo " Error #:" . $quotationresp['Message'];
            echo '<script>alert("Ocurrió un error: quotationresp -  +'.$quotationresp['Message'].'");</script>'; 
            return;
          }
          else{
            $resp = $response;
            $quotationresp = json_decode($resp["body"], true);
            //echo '<div><h2>WS LOGIN RESPONSE</h2>';
            //echo json_encode($quotationresp);
            //echo '<script>alert("'.$quotationresp['Message'].'");</script>'; 
            //INSERT BOOKING
            //echo '<div><h2>ORDER DATA()</h2>';
            $weight = 0;
            $cost = 0;
            $itemstotal=0;
            //Get cart cost an item totals
            foreach ($order->get_items() as $item_id => $item) {
                      $_product = $item->get_product();
                      $cost = $cost +  $item->get_total();//['line_total'];					
                      for ($i = 0; $i <$item->get_quantity() ; $i++) {
                          $itemstotal=$itemstotal+1;
                      }					
            }
            //
            $country = $order->get_shipping_country();
            $details = "";
            $voltotal=0;
            // Loop through order items
			      //Paquete Unico
            $paqueteunico=false;
            if(isset($settings['pkt1_dimunique'])){
              if ($settings['pkt1_dimunique']=="enabled"){
                $paqueteunico=true;
              }
            }
            $calcularcajas=false;
            if(isset($settings['pkt1_boxcalc'])){
              if ($settings['pkt1_boxcalc']=="enabled"){
                $calcularcajas=true;
              }
            }
            //Detalles del paquete
            if ($paqueteunico){
              $arrPackage=array();
              foreach ($order->get_items() as $item_id => $item) {
                $_product = $item->get_product();
                //$cost = $cost +  $item->get_total();//['line_total'];					
                for ($i = 0; $i <$item->get_quantity() ; $i++) {
                  $arrPackage[]=$item;
                }					
              }	
              $cnfpieza=$settings['pkt1_cnfpieza'];
              $piezas=0;
              if($cnfpieza==""||$cnfpieza==0){
                $piezas = 1;	
              }
              else{
                $piezas = ceil(COUNT($arrPackage)/$cnfpieza);
              }
              for ($i=0; $i < $piezas ; $i++) { 	
                $details .= "{            
                  'Qty': 1,            
                  'Typ': 1,            
                  'Cnt': '".$settings['pkt1_contenido']."',            
                  'Hgt': '".$settings['pkt1_cnfalto']."',            
                  'Wdt': '".$settings['pkt1_cnfancho']."',            
                  'Lng': '".$settings['pkt1_cnflargo']."',            
                  'Wgt': '".$settings['pkt1_cnfpeso']."'        
                  },
                ";
              }
            }
            else if($calcularcajas){				
              $url_boxes = '';
              if($store_country == "MX"){
                $url_boxes = "https://api.pktuno.mx/Api/Cajas/Calcular/".$settings['pkt1_token'];
              }
              else if ($store_country == "CL"){
                $url_boxes = "https://api.pktuno.cl/Api/Cajas/Calcular/".$settings['pkt1_token'];
              }
              foreach ($order->get_items() as $item_id => $item) {
                $_product = $item->get_product();                     
                $cost = $cost +  $item->get_total();//['line_total'];	
                //$volitem=0;	
                $request= "[";			  
                for ($i = 0; $i <$item->get_quantity() ; $i++) {						
                  $weight = $weight + $_product->get_weight();						
                  $request .= "{           
                    'alto': '".$_product->get_height()."',            
                    'ancho': '".$_product->get_width()."',            
                    'largo': '".$_product->get_length()."',            
                    'peso': '".$_product->get_weight()."'        
                    },
                  ";
                }	
                $request .= "]";					  
              }												
              $args = array(
              'body'        => $request,
              'timeout'     => '30',
              'redirection' => '10',
              'httpversion' => '1.1',
              'blocking'    => true,
              'headers'     => array(
                        'Cache-Control' => 'no-cache',    
                        'Content-Type' => 'application/json'),
              );
              $response = wp_remote_post($url_boxes, $args );
              if ( is_wp_error( $response )) {
                $error_message = $response->get_error_message();
                echo "Something went wrong: $error_message";	
                $myfile = fopen("./last_request_test_err.txt", "w") or die("Unable to open file 2!");
                $txt = " Error #:" . $error_message;
                fwrite($myfile, $txt);
                fclose($myfile);						
              }
              else{
                $resp = $response;	
                if($resp["response"]["code"]!=200){
                  //si no enconto caja, agarrara la configuracion "etiqueta unica"
                  $etiquetaunica=true;
                  goto alternativeBooking;
                }  											
                $boxresp = json_decode($resp["body"], true);
                $details .= "{     
                  'qty': '1',            
                  'typ': 1,            
                  'cnt': '".$settings['pkt1_contenido']."',            
                  'hgt': '".$boxresp["alto"]."',            
                  'wdt': '".$boxresp["ancho"]."',            
                  'lng': '".$boxresp["largo"]."',            
                  'wgt': '".$weight."'        
                  },
                ";
              } 
            }
            else{
                $etiquetaunica=false;
                if(isset($settings['pkt1_onelabel'])){
                  if ($settings['pkt1_onelabel']=="enabled"){
                    $etiquetaunica=true;
                  }
                }
                alternativeBooking:
                if ($etiquetaunica&&$itemstotal>1) {
                  //etiqueta unica
                  $arrPackage=array();                
                  foreach ($order->get_items() as $item_id => $item) {
                    $_product = $item->get_product();
                    //$cost = $cost +  $item->get_total();//['line_total'];	
                    $volitem=0;				
                    for ($i = 0; $i <$item->get_quantity() ; $i++) {
                          $volitem=($_product->get_height()*$_product->get_width()*$_product->get_length())/4000;
                          $weight = $weight + $_product->get_weight();
                          $voltotal=$voltotal+$volitem;
                    }					
                  }
                  $lado=ceil(pow(($voltotal*4000), 1/3));
                  //sumar las alturas, 
                  //agregar paquete unico
                  $details .= "{     
                      'qty': '1',            
                      'typ': 1,            
                      'cnt': '".$settings['pkt1_contenido']."',            
                      'hgt': '".$lado."',            
                      'wdt': '".$lado."',            
                      'lng': '".$lado."',            
                      'wgt': '".$weight."'        
                  },
                  ";
                  //fin etiqueta unica
                }
                else{
                  //traidicional
                  foreach ($order->get_items() as $item_id => $item) {
                      //$cost = $cost +  $item->get_total();//['line_total'];
                      // Get the product object
                      $product = $item->get_product();
                      // Get the product Id
                      $product_id = $product->get_id();
                      // Get the product name
                      $product_id = $item->get_name();

                      $weight = $weight + $product->get_weight() * $item->get_quantity();
                      $details .= "
                    {     
                      'qty': '".$item->get_quantity()."',            
                      'typ': 1,            
                      'cnt': '".$settings['pkt1_contenido']."',            
                      'hgt': '".$product->get_height()."',            
                      'wdt': '".$product->get_width()."',            
                      'lng': '".$product->get_length()."',            
                      'wgt': '".$product->get_weight()."'        
                    },
                  ";
                  }
                  //fin tradicional
                }  
            }

            $weight = wc_get_weight( $weight, 'kg' );

            $order_data = $order->get_data();

            $url_country_booking = '';
            if($store_country == "MX"){
              $url_country_booking = "https://api.pktuno.mx/Api/WebBooking";
            }
            else if ($store_country == "CL"){
              $url_country_booking = "https://api.pktuno.cl/Api/WebBooking";
            }

            $test = false;
            if($test){
              if($store_country == "MX"){
                $url_country_booking = "http://app.pktuno.mx:81/ws/Api/WebBooking";
              }
              else if ($store_country == "CL"){
                $url_country_booking = "http://app.pktuno.cl:81/ws/Api/WebBooking";
              }
            }

            $phone = $order->get_data()['billing']["phone"];
            if ($store_country=="CL"){
              if(strlen($phone) >= 4){
                $phone = substr($phone, 0, 3).'-'.substr($phone, 3);
              }
            } 
            $phonestore="6681130709";         
            if ($store_country == "CL"){
              $phonestore="562-28819585";
            }
            //validacion seguro
            $pos = 0;
            foreach ($order_data["meta_data"] as $key => $value) {
              if ($value->key=="_shipping_asegurar_envio"){
                if($value->value==1){
                  $pos=1;
                }
              }				
            }
            if ($pos == 0) {
              $seguro = 0;
            } else {
              $seguro = ceil($cost);
            }
            //Datos Chile 
            //$pkt1_modooscuro = sanitize_text_field( $_POST['pkt1_modooscuro'] ) ;
            $CustomData1 ="";//Mexico Colonia Chile Comuna

            $CustomData2 ="";//Solo chile, numero interior

            $CustomData3="";//Chile Provincia Mexico Municipio o ciudad			
            if ($store_country == "MX"){
              //Colonia
              $CustomData1 = sanitize_text_field( $order_data['shipping']['address_2'] );
              //Municipio
              $CustomData3 = sanitize_text_field( $order_data['shipping']['city'] );
            }
            else if ($store_country == "CL"){
              //Comuna
              $CustomData1 = sanitize_text_field( $order_data['shipping']['city'] );
              //Num Int
              $CustomData2=sanitize_text_field( $order_data['shipping']['address_2'] );
              //Provincia
            }            
            $postcode = sanitize_text_field( $order_data["shipping"]["postcode"] );
            $address_1 = sanitize_text_field( $order_data["shipping"]["address_1"] );
            $city = sanitize_text_field( $order_data["shipping"]["city"] );
            $state = sanitize_text_field( $order_data["shipping"]["state"] );
			//Claves de direccion
			if ($store_country=="CL"){
				if ($city==""){
					return;
				}
				$json_comuna = wp_remote_get("https://api.pktuno.cl/Api/Cobertura/Comuna/".$state."/".$city);
				$response = wp_remote_retrieve_body( $json_comuna );
				$direccion = json_decode( $response, true );
				$CustomData1 = $direccion["comuna"];//Comuna			  
				$city = $direccion["comuna"];//Comuna
				$CustomData3 = $direccion["provincia"];//Provincia
				$postcode = $direccion["cp"];//Cp				  
			}
			elseif($store_country=="MX"){
				foreach ($order_data["meta_data"] as $key => $value) {
					if ($value->key=="_shipping_colonia"){
						if($value->value!=""){
              if($value->value!="Seleccione"){
							  $CustomData1=$value->value;
              }            
						}
					}				
				}
				$CustomData2=sanitize_text_field( $order_data['shipping']['address_2'] );
        $json_datos = wp_remote_get("https://api.pktuno.mx/Api/Cobertura/".$postcode."/".$CustomData1);
        $response = wp_remote_retrieve_body( $json_datos );
        $direccion = json_decode( $response, true );
        $CustomData1 = $direccion["colonia"];	//colonia		  
        $city = $direccion["ciudad"];
        $CustomData3 = $direccion["municipio"];
        $state = $direccion["estado"];
        //reglas para mexico

			}
            if ($state=="" || $state==null){
              $state=$order->get_data()["billing"]["state"];
            }
            $country = sanitize_text_field( $order_data["shipping"]["country"] );
            if ($country=="" || $country==null){
              $country=$order->get_data()["billing"]["country"];
            }
            $first_name = sanitize_text_field( $order_data["shipping"]["first_name"] );
            $last_name = sanitize_text_field( $order_data["shipping"]["last_name"] );
            if ($first_name=="" || $first_name==null){
              $first_name=$order->get_data()["billing"]["first_name"];
            }
            if ($last_name=="" || $last_name==null){
              $first_name=$order->get_data()["billing"]["last_name"];
            }
            if($CustomData2==""){
              $CustomData2=substr("ATN ".$first_name." ".$last_name, 0, 35) ;
            }
            $Token = sanitize_text_field( $quotationresp["data"]["Token"] );
            $cuerpo = "
            {              
              'destination': {
                'iddom': 0,
                'cp': '".$postcode."',
                'str': '".$address_1."',
                'col': '".$CustomData1."',
                'numExt': 0,
                'numInt': '',
                'cty': '".$city."',
                'mnp': '".$CustomData3."',
                'ste': '".$state."',
                'ctr': '".$country."'
              },
              'shippingData': [
                ".$details."
              ],
              'quotation': ".$quotationdata.",
              'shipCnf': {
                'isv': ".$seguro.",
                'dtp': 1,
                'din': '".$CustomData2."',
                'cvl': 0,
                'acn': false
              },
              'originCnf': {
                'nopedidoecom': '".$order_id."'
              },
              'clientDestData': {
                'id': 0,
                'email': '".$order->get_data()["billing"]["email"]."',
                'firstname': '".$first_name."',
                'secondname': '',
                'lastname': '".$last_name."',
                'secondlastname': '',
                'phone': '".$phone."',
                'rfc': null
              },
              'security': {
                'usr': '".$settings["pkt1_username"]."',
                'key': '".$settings["pkt1_token"]."',
                'cot': 0
              }
            }";
            $args = array(
              'body'        => $cuerpo,
              'timeout'     => '30',
              'redirection' => '10',
              'httpversion' => '1.1',
              'blocking'    => true,
              'headers'     => array(   
                              'Content-Type' => 'application/json',
                              'Authorization' => 'Bearer '.$Token),
            );
            $response = wp_remote_post( $url_country_booking, $args );
            $resp = "";
            if (is_wp_error( $response )) {
              $error_message = $response->get_error_message();
              echo '<div class="notice is-dismissible notice-danger">
                <p><strong>No se pudo generar la guía.</strong> Error #: '.$error_message.'.</p>
              <button type="button" class="notice-dismiss"><span class="screen-reader-text">Descartar este aviso.</span></button></div>';
            } else {
              $resp = $response;
              $arrResp = json_decode($response["body"], true);
              $order = wc_get_order( $order_id );
              if(!$arrResp['IsError']){
                $order->update_meta_data( 'pkt1_id', $arrResp['IdBooking'] );
                $order->update_meta_data( 'pkt1_noguia', $arrResp['Data']['Guia'] );
                $order->update_meta_data( 'pkt1_norastreo', $arrResp['Data']['Rastreo'] );
                $order->update_meta_data( 'pkt1_branchid', $arrResp['Branch'] );
                $order->update_meta_data( 'pkt1_IdBookingLocal', $arrResp['IdBookingLocal'] );
                //$order->update_meta_data( 'pkt1_id', $arrResp['Branch'] );

                $order->save();
              }
              else{
                echo '<div class="notice is-dismissible notice-danger">
                <p><strong>No se pudo generar la guía.</strong> '.$arrResp['Message'].'.</p>
              <button type="button" class="notice-dismiss"><span class="screen-reader-text">Descartar este aviso.</span></button></div>';
              }
              
            return;
            }
            //TERMINA INSERT BOOKING

          }
          
        }

      }
}

//Hook para generar guias
//add_action('woocommerce_thankyou', 'pkt1_booking', 10, 1);
add_action( 'woocommerce_order_status_processing', 'pkt1_booking',10,1);
add_action( 'woocommerce_order_status_completed', 'pkt1_booking',10,1);


// ADDING 2 NEW COLUMNS WITH THEIR TITLES (keeping "Total" and "Actions" columns at the end)
add_filter( 'manage_edit-shop_order_columns', 'custom_shop_order_column', 20 );
function custom_shop_order_column($columns)
{
    $reordered_columns = array();

    // Inserting columns to a specific location
    foreach( $columns as $key => $column){
        $reordered_columns[$key] = $column;
        if( $key ==  'order_status' ){
            // Inserting after "Status" column
            $reordered_columns['pkt1-column1'] = __( 'Paqueteria','pkt1');
            $reordered_columns['pkt1-column2'] = __( 'Etiqueta','pkt1');
            $reordered_columns['pkt1-column3'] = __( 'Trackig','pkt1');
        }
    }
    return $reordered_columns;
}

// Adding custom fields meta data for each new column (example)
add_action( 'manage_shop_order_posts_custom_column' , 'custom_orders_list_column_content', 20, 2 );
/* cambiar llamas a apis a api htto de wordpress */
function custom_orders_list_column_content( $column, $post_id )
{
  $settings = get_option( 'pkt1_settings');
                    
    switch ( $column )
    {
        case 'pkt1-column1' :
              //Validacion de add shop y existencia de shipping lines
            $order = wc_get_order( $post_id );
            $orderData = $order->get_data();
            if (count($orderData['shipping_lines'])==0){
              echo '-';
              break;
            }
            // Get custom post meta data
            $pkt1_id = get_post_meta( $post_id, 'pkt1_id', true );
            $pkt1_branchid = get_post_meta( $post_id, 'pkt1_branchid', true );
            $pkt1_noguia = get_post_meta( $post_id, 'pkt1_noguia', true );
            $pkt1_norastreo = get_post_meta( $post_id, 'pkt1_norastreo', true );

            $order = wc_get_order( $post_id );
            $shipping_method = @array_shift($order->get_shipping_methods());
            $shipping_method = $shipping_method->get_meta_data();
            $shipping_method = json_decode(json_encode($shipping_method), true);
            $alianza = '';
            foreach ($shipping_method as $item) {
              if(isset($item['key'])){
                if($item['key'] == "IdAlianza"){
                  $alianza = $item['value'];
                }
              }
            }
            // The country/state
            $store_raw_country = get_option( 'woocommerce_default_country' );

            // Split the country/state
            $split_country = explode( ":", $store_raw_country );

            // Country and state separated:
            $store_country = $split_country[0];
            $store_state   = $split_country[1];
            
            if($alianza!="")
              if($store_country == "MX")
                echo '<img src="https://web.pktuno.mx//PKT1/uploads/alianzas/'.$alianza.'.png" width="80">';
              elseif($store_country == "CL")
                echo '<img src="https://web.pktuno.cl/CLPKT1/uploads/alianzas/'.$alianza.'.png" width="80">';              
              else                
                echo 'no country configured';              
            else
                echo '-';				
            break;
        case 'pkt1-column2' :
            //Validacion de add shop y existencia de shipping lines
            $order = wc_get_order( $post_id );
            $orderData = $order->get_data();
            if (count($orderData['shipping_lines'])==0){
              echo '-';
              break;
            }
            // Get custom post meta data
            $pkt1_id = get_post_meta( $post_id, 'pkt1_id', true );
            $pkt1_branchid = get_post_meta( $post_id, 'pkt1_branchid', true );
            $pkt1_noguia = get_post_meta( $post_id, 'pkt1_noguia', true );
            $pkt1_norastreo = get_post_meta( $post_id, 'pkt1_norastreo', true );

            $order = wc_get_order( $post_id );
            $shipping_method = @array_shift($order->get_shipping_methods());
            $shipping_method = $shipping_method->get_meta_data();
            $shipping_method = json_decode(json_encode($shipping_method), true);
            $alianza = '';
            foreach ($shipping_method as $item) {
              if(isset($item['key'])){
                if($item['key'] == "Alianza"){
                  $alianza = $item['value'];
                }
              }
            }

            // The country/state
            $store_raw_country = get_option( 'woocommerce_default_country' );

            // Split the country/state
            $split_country = explode( ":", $store_raw_country );

            // Country and state separated:
            $store_country = $split_country[0];
            $store_state   = $split_country[1];

            
            
            if(!empty($pkt1_norastreo))
              echo '
              <button class="pkt1_btn_guia" onclick="showLabel(\''.$settings['pkt1_username'].'\',\''.$settings['pkt1_secret'].'\',\''.$settings['pkt1_token'].'\',\''.$alianza.'\',\''.$pkt1_norastreo.'\','.$pkt1_id.',\''.$store_country.'\')" type="button" role="tab" tabindex="-1" aria-selected="false" aria-controls="activity-panel-orders" id="activity-panel-tab-orders" class="components-button components-icon-button woocommerce-layout__activity-panel-tab has-unread has-text"><svg class="gridicon gridicons-pages" height="24" width="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g><path d="M16 8H8V6h8v2zm0 2H8v2h8v-2zm4-6v12l-6 6H6c-1.105 0-2-.895-2-2V4c0-1.105.895-2 2-2h12c1.105 0 2 .895 2 2zm-2 10V4H6v16h6v-4c0-1.105.895-2 2-2h4z"></path></g></svg><span class="screen-reader-text">unread activity</span></button>
              ';

            // Testing (to be removed) - Empty value case
            else
              echo '-';

            break;

        case 'pkt1-column3' :
            //Validacion de add shop y existencia de shipping lines
            $order = wc_get_order( $post_id );
            $orderData = $order->get_data();
            if (count($orderData['shipping_lines'])==0){
              echo '-';
              break;
            }
            // Get custom post meta data
            // The country/state
            $store_raw_country = get_option( 'woocommerce_default_country' );

            // Split the country/state
            $split_country = explode( ":", $store_raw_country );

            // Country and state separated:
            $store_country = $split_country[0];
            $store_state   = $split_country[1];

            $pkt1_id = get_post_meta( $post_id, 'pkt1_id', true );
            $pkt1_branchid = get_post_meta( $post_id, 'pkt1_branchid', true );
            $pkt1_branchid = str_pad($pkt1_branchid,3,'0',STR_PAD_LEFT);
            $pkt1_norastreo = get_post_meta( $post_id, 'pkt1_norastreo', true );
            if(!empty($pkt1_id)){
              if (!empty($pkt1_IdBookingLocal)){
                if($store_country == "MX"){
                  echo '<a href="https://enviospkt1.com/?ticket='.$pkt1_IdBookingLocal.'" target="_blank">'.$pkt1_IdBookingLocal.'</a>';
                  }
                  else if($store_country == "CL"){
                  echo '<a href="https://pkt1.cl/?ticket='.$pkt1_IdBookingLocal.'" target="_blank">'.$pkt1_IdBookingLocal.'</a>';
                  }	
              }
              else
              {
                //Get Ticket Rastreo
                  $url_country = '';
                  if($store_country == "MX"){
                  $url_country = "https://web.pktuno.mx/PKT1/getOsticket.php?id=".$pkt1_id;
                  }
                  else if ($store_country == "CL"){
                  $url_country = "https://web.pktuno.cl/CLPKT1/getOsticket.php?id=".$pkt1_id;
                  }
      
      
                  $test = false;
                  if($test){
                  if($store_country == "MX"){
                    $url_country = "http://app.pktuno.mx:81/PKT1/getOsticket.php?id=".$pkt1_id;
                  }
                  else if ($store_country == "CL"){
                    $url_country = "http://app.pktuno.cl:81/CLPKT1/getOsticket.php?id=".$pkt1_id;
                  }
                  }
                  $ticketOS  = wp_remote_get($url_country);
                    //$myfile = fopen("./last_request_test_resp.txt", "w") or die("Unable to open file 2!");
                    //$txt = json_encode($ticketOS, JSON_UNESCAPED_UNICODE);
                  //fwrite($myfile, $txt);
                  //fclose($myfile);
                  $response = wp_remote_retrieve_body( $ticketOS );
                  $array = json_decode( $response, true );
                  if($store_country == "MX"){
                  echo '<a href="https://enviospkt1.com/?ticket='.$pkt1_branchid.$array["idticket"].'" target="_blank">'.$pkt1_norastreo.'</a>';
                  }
                  else if($store_country == "CL"){
                  echo '<a href="https://pkt1.cl/?ticket='.$pkt1_branchid.$array["idticket"].'" target="_blank">'.$pkt1_norastreo.'</a>';
                  }					
              }
            }
            else
                echo '-';
            break;
    }
}

  /**************
    Shipping Method Admin Testing
  *************/
function pkt1_admin_test(){
  return;
}

add_action('admin_footer', 'pkt1_admin_test');
//Activar Desactivar para tiendas de chile
//add_filter( 'woocommerce_shipping_calculator_enable_postcode', '__return_false' );

function pkt1_shop(){
  pkt1_apply_darkmode();
  pkt1_apply_visualization();
  //pkt1_products_view();
  //pkt1_products_add_pkt1_columns();
  //pkt1_ws_codpost();
  //pkt1_ws_insertclient();
  //pkt1_ws_insertdireccion();

}

add_action('wp_footer', 'pkt1_shop');

add_filter( 'woocommerce_default_address_fields', 'custom_override_default_checkout_fields', 10, 1 );
function custom_override_default_checkout_fields( $address_fields ) {
    $store_raw_country = get_option( 'woocommerce_default_country' );
    $split_country = explode( ":", $store_raw_country );
    $store_country = $split_country[0];
    $store_state   = $split_country[1];
    if ($store_country=="MX"){
      // Remove labels for "address 2" shipping fields
      unset($address_fields['address_1']['placeholder']);
      unset($address_fields['address_2']['placeholder']);	  
      unset($address_fields['address_1']['label']);
      unset($address_fields['address_2']['label']);
      unset($address_fields['address_2']['label_class']);
      unset($address_fields['city']['label']);
      unset($address_fields['state']['label']);	
      //$address_fields['address_2']['required']=true;
    }
    elseif($store_country=="CL"){
      // Remove labels for "address 2" shipping fields
      unset($address_fields['address_1']['placeholder']);
      unset($address_fields['address_2']['placeholder']);  
      unset($address_fields['address_1']['label']);
      unset($address_fields['address_2']['label']);
      unset($address_fields['address_2']['label_class']);
      unset($address_fields['city']['label']);
      unset($address_fields['state']['label']);	 
    }
    return $address_fields;
}

add_action( 'woocommerce_after_checkout_validation', 'misha_validate_fname_lname', 11, 2);
function misha_validate_fname_lname( $fields, $errors ){
  $store_raw_country = get_option( 'woocommerce_default_country' );
  $split_country = explode( ":", $store_raw_country );
  $store_country = $split_country[0];
  if ($store_country=="CL") {
      if (strlen($fields['shipping_address_2'])>30) {
          $errors->add('validation', 'No. Piso, Departamento, Etc, Maximo 10 caracteres');
      }
      //validacion de telefono
      if (strlen($fields['billing_phone'])!=11) {
            //$errors->add('validation', 'Telefono, 3 caracteres para el prefijo y 8 para el telefono');
        }
  }
  else if($store_country="MX"){
    if (strlen($fields['shipping_address_2'])>50||strlen($fields['shipping_address_2'])==0) {
        //$errors->add('validation', 'Colonia, Obligatorio Maximo 50 caracteres');
    }
    //validacion de telefono
    if (strlen($fields['billing_phone'])!=10) {
          $errors->add('validation', 'Telefono, 10 digitos obligatorio');
      }
  }
}

add_filter( 'woocommerce_checkout_fields', 'custom_override_checkout_fields', 12, 1 );
function custom_override_checkout_fields( $fields ) {
	$store_raw_country = get_option( 'woocommerce_default_country' );
    // Split the country/state
    $split_country = explode( ":", $store_raw_country );
    // Country and state separated:
    $store_country = $split_country[0];
    $store_state   = $split_country[1];
    if ($store_country=="MX") {
		//
		$fields['billing']['billing_address_1']['label'] = __('Calle y numero', 'woocommerce');
        $fields['shipping']['shipping_address_1']['label'] = __('Calle y numero', 'woocommerce');		
        ///
        $fields['billing']['billing_address_1']['placeholder'] = __('Nombre de la calle y el numero de la casa', 'woocommerce');
        $fields['shipping']['shipping_address_1']['placeholder'] = __('Nombre de la calle y el numero de la casa', 'woocommerce');		
        ///
		$fields['billing']['billing_address_2']['label'] = __('No. Piso, Departamento', 'woocommerce');
        $fields['shipping']['shipping_address_2']['label'] = __('No. Piso, Departamento', 'woocommerce');
		//
        $fields['billing']['billing_address_2']['placeholder'] = __('No. Piso, Departamento, Etc', 'woocommerce');
        $fields['shipping']['shipping_address_2']['placeholder'] = __('No. Piso, Departamento, Etc', 'woocommerce');
        ///
        //$fields['billing']['billing_address_2']['placeholder'] = __('Colonia', 'woocommerce');
        //$fields['shipping']['shipping_address_2']['placeholder'] = __('Colonia', 'woocommerce');
        //        
        //$fields['billing']['billing_address_2']['label'] = __('Colonia', 'woocommerce');		
        //$fields['shipping']['shipping_address_2']['label'] = __('Colonia', 'woocommerce');
		//$fields['billing']['billing_address_2']['required']	= true;
		//$fields['shipping']['shipping_address_2']['required'] =true;
        //
        $fields['billing']['billing_city']['placeholder'] = __('Ciudad/Localidad', 'woocommerce');
        $fields['shipping']['shipping_city']['placeholder'] = __('Ciudad/Localidad', 'woocommerce');
        //
        $fields['billing']['billing_city']['label'] = __('Ciudad', 'woocommerce');		
        $fields['shipping']['shipping_city']['label'] = __('Ciudad', 'woocommerce');		
        //
		$fields['billing']['billing_city']['custom_attributes'] = array('readonly'=>'readonly');
        $fields['shipping']['shipping_city']['custom_attributes'] = array('readonly'=>'readonly');
        //
        $fields['billing']['billing_state']['label'] = __('Estado', 'woocommerce');		
        $fields['shipping']['shipping_state']['label'] = __('Estado', 'woocommerce');
        //
		$fields['billing']['billing_state']['placeholder'] = __('Estado/Provincia', 'woocommerce');
        $fields['shipping']['shipping_state']['placeholder'] = __('Estado/Provincia', 'woocommerce');
		//
	    $fields['billing']['billing_phone']['label'] = __('Telefono a 10 digitos', 'woocommerce');
        $fields['billing']['billing_phone']['placeholder'] = __('EJ: 5555444444', 'woocommerce');
		
        //return $fields;
        $fields['billing']['billing_state']['type']="text";
        $fields['shipping']['shipping_state']['type']="text";
        $fields['billing']['billing_state']['custom_attributes'] = array('readonly'=>'readonly');
        $fields['shipping']['shipping_state']['custom_attributes'] = array('readonly'=>'readonly');
		//Campo personalizado de colonia
		$fields['shipping']['shipping_colonia'] = array(
		  'id' => 'shipping_colonia',
		  'name' => 'shipping_colonia',
		  'label'     => __('Colonia', 'woocommerce'),
		  'placeholder'   => _x('Seleccione una colonia', 'placeholder', 'woocommerce'),
		  'options'=>array(
                'Seleccione' => 'Seleccione una colonia',	
          ),
		  'type'  => 'select',
		  'required'  => true,
		  'class' => array( 'update_totals_on_change' ),
		  'input_class' => array( 'wc-enhanced-select')//, 'update_totals_on_change' ) //,'woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'
		);
		$fields['billing']['billing_colonia'] = array(
		  'id' => 'billing_colonia',
		  'name' => 'billing_colonia',
		  'label'     => __('Colonia', 'woocommerce'),
		  'placeholder'   => _x('Seleccione una colonia', 'placeholder', 'woocommerce'),
		  'options'=>array(
                'Seleccione' => 'Seleccione una colonia',	
          ),
		  'type'  => 'select',
		  'required'  => true,
		  'class' => array( 'update_totals_on_change' ),
		  'input_class' => array( 'wc-enhanced-select')//, 'update_totals_on_change' ) //,'woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'
		);		
    /* CODIGO PERSONALIZADO PARA CAFE BARISTI no incluir en produccion */
    /*
		$fields['billing']['billing_RFC'] = array(
		  'id' => 'billing_RFC',
		  'name' => 'billing_RFC',
		  'label'     => __('RFC(Solo si requieres factura))', 'woocommerce'),
		  'placeholder'   => _x('Seleccione una colonia', 'placeholder', 'woocommerce'),		 
		  'type'  => 'text',
		  'required'  => false
		);
    //fin
    */
		/*
        $postal_args = wp_parse_args( array(
			'class' => array( 'update_totals_on_change' ),
          ), $fields['billing']['billing_postcode'] );
        $fields['billing']['billing_postcode'] = $postal_args; //Cambiamos el dropbox del shipping info
        $fields['shipping']['shipping_postcode']=$postal_args;
		*/
		/*
        $colonia_args = wp_parse_args( array(
            'type' => 'select',
			'placeholder'=>'Seleccione una colonia',
            'options'=>array(
                'Seleccione' => 'Seleccione una colonia',	
            ),
            'input_class' => array(
              'wc-enhanced-select',
            )
          ), $fields['billing']['billing_colonia'] );
        $fields['billing']['billing_colonia'] = $colonia_args; //Cambiamos el dropbox del shipping info
        $fields['shipping']['shipping_colonia']=$colonia_args;
		*/
      wc_enqueue_js( "	
		jQuery(document).ready(function() {
		  let objColonias=[];	
          jQuery( ':input.wc-enhanced-select' ).filter( ':not(.enhanced)' ).each( function() {
            var select2_args = { minimumResultsForSearch: 5 };
            jQuery( this ).select2( select2_args ).addClass( 'enhanced' );
          });	
          jQuery('#billing_postcode')
            .keyup(function () {
              if (jQuery('#billing_postcode').val().length>4){
                change_comuneLocale($(this).val(),'#billing');
              }
            })
            .keyup();
          jQuery('#shipping_postcode')
            .keyup (function () {
              if (jQuery('#shipping_postcode').val().length>4){
                change_comuneLocale($(this).val(),'#shipping');
              }         
            })
            .keyup();
          
            function change_comuneLocale(cp,field){            
              jQuery(field+'_colonia').empty();
			  jQuery(field+'_state').val(null);
			  jQuery(field+'_city').val(null);
              jQuery.getJSON('https://api.pktuno.mx/Api/Cobertura/'+cp).done(function(data){
				objColonias=data;
                let base = \"<option value=''>Selecciona tu colonia</option>\";
                jQuery(field+'_colonia').append(base);
                data.forEach(element => {
                  let option = \"<option>\"+element.colonia+\"</option>\";
                  jQuery(field+'_colonia').append(option);
                });
              });
            }
        jQuery('#shipping_colonia')
        .change(function () {
			var str = '';
			jQuery( '#shipping_colonia option:selected' ).each(function() {
			  putcity_state($(this).val(),'#shipping');
			});
        });
        jQuery('#billing_colonia')
        .change(function () {
			var str = '';
			jQuery( '#billing_colonia option:selected' ).each(function() {
			  putcity_state($(this).val(),'#billing');
			});
        });
        function putcity_state(cp,field){ 			
			var result =objColonias.filter(element=>element.colonia==jQuery(field+'_colonia').val());
			console.log(result);			
			let city=result[0].ciudad;
			let state=result[0].estado;			
			jQuery(field+'_city').val(city);        
			jQuery(field+'_state').val(state);
        }
		});" );
      
    }
    elseif($store_country=="CL") {
		//
		$fields['billing']['billing_address_1']['label'] = __('Calle y numero', 'woocommerce');
        $fields['shipping']['shipping_address_1']['label'] = __('Calle y numero', 'woocommerce');
		//
        $fields['billing']['billing_address_1']['placeholder'] = __('Nombre de la calle y el numero de la casa', 'woocommerce');
        $fields['shipping']['shipping_address_1']['placeholder'] = __('Nombre de la calle y el numero de la casa', 'woocommerce');		
        ///
		$fields['billing']['billing_address_2']['label'] = __('No. Piso, Departamento', 'woocommerce');
        $fields['shipping']['shipping_address_2']['label'] = __('No. Piso, Departamento', 'woocommerce');
		//
        $fields['billing']['billing_address_2']['placeholder'] = __('No. Piso, Departamento, Etc', 'woocommerce');
        $fields['shipping']['shipping_address_2']['placeholder'] = __('No. Piso, Departamento, Etc', 'woocommerce');
        //
        $fields['billing']['billing_city']['placeholder'] = __('Comuna', 'woocommerce');
        $fields['shipping']['shipping_city']['placeholder'] = __('Comuna', 'woocommerce');
        //
        $fields['billing']['billing_city']['label'] = __('Comuna', 'woocommerce');		
        $fields['shipping']['shipping_city']['label'] = __('Comuna', 'woocommerce');		
        //
	      //$fields['billing']['billing_phone']['label'] = __(get_country(), 'woocommerce');
        //$fields['billing']['billing_phone']['label'] = __('Telefono con prefijo', 'woocommerce');
        $fields['billing']['billing_phone']['label'] = __('Telefono', 'woocommerce');
        //$fields['billing']['billing_phone']['label'] = __(json_encode($fields), 'woocommerce');

        //$fields['billing']['billing_phone']['placeholder'] = __('569XXXXXXXX  o 562XXXXXXXX', 'woocommerce');
        $fields['billing']['billing_phone']['placeholder'] = __('912345678  o 234567890', 'woocommerce');

        $city_args = wp_parse_args( array(
          'type' => 'select',
          'options'=>array(
              'SELECCIONE' => 'SELECCIONE',	
          ),
          'input_class' => array(
            'wc-enhanced-select',
          )
        ), $fields['shipping']['shipping_city'] );

        $fields['shipping']['shipping_city'] = $city_args; //Cambiamos el dropbox del shipping info
        $fields['billing']['billing_city'] = $city_args; //Cambiamos el dropbox del billing info
        
        wc_enqueue_js( "
        jQuery( ':input.wc-enhanced-select' ).filter( ':not(.enhanced)' ).each( function() {
          var select2_args = { minimumResultsForSearch: 5 };
          jQuery( this ).select2( select2_args ).addClass( 'enhanced' );
        });	
        jQuery('#billing_state')
          .change(function () {
          var str = '';
          jQuery( '#billing_state option:selected' ).each(function() {
            change_comuneLocale($(this).val(),'#billing');
          });
          })
          .change();
        jQuery('#shipping_state')
          .change(function () {
          var str = '';
          jQuery( '#shipping_state option:selected' ).each(function() {
            change_comuneLocale($(this).val(),'#shipping');
          });
          })
          .change();
        
          function change_comuneLocale(state,field){
            jQuery(field+'_city').empty();
            jQuery.getJSON('https://api.pktuno.cl/Api/Cobertura/Comunas/'+state).done(function(data){
              let base = \"<option value=''>Selecciona Tu Comuna</option>\";
              jQuery(field+'_city').append(base);
              data.forEach(element => {
                let option = \"<option>\"+element.comuna+\"</option>\";
                jQuery(field+'_city').append(option);
              });
            });
          }
        " ); 		
    }
	
    $fields['billing']['shipping_asegurar_envio'] = array(
      'id' => 'asegurarenvio',
      'name' => 'asegurarenvio',
      'label'     => __('Asegurar envío', 'woocommerce'),
      'placeholder'   => _x('Field Value', 'placeholder', 'woocommerce'),
      'type'  => 'checkbox',
      'checked' => false,
      'class' => array( 'form-row-wide')//, 'update_totals_on_change' ) //,'woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'
    );	
  return $fields;
  
}

add_filter( 'woocommerce_default_address_fields', 'reorganize', 13, 1 );
 function reorganize($fields){	 
	$store_raw_country = get_option( 'woocommerce_default_country' );
    // Split the country/state
    $split_country = explode( ":", $store_raw_country );
    // Country and state separated:
    $store_country = $split_country[0];
    $store_state   = $split_country[1];
	$priority=$fields['company']['priority'];
	if ($store_country=="CL") {
		$fields['country']['priority'] = $priority+1;
		$fields['state']['priority'] = $priority+2;
		$fields['city']['priority'] = $priority+3;
		$fields['address_1']['priority'] = $priority+4;
		$fields['address_2']['priority'] = $priority+5;
	}
	elseif($store_country=="MX"){
		$fields['country']['priority'] = $priority+1;		
		$fields['postcode']['priority'] = $priority+2;
		$fields['colonia']['priority'] = $priority+3;
		$fields['city']['priority'] = $priority+4;
		$fields['state']['priority'] = $priority+5;		
		$fields['address_1']['priority'] = $priority+6;	
		$fields['address_2']['priority'] = $priority+7;		
		
	}
    return $fields;
 }
 
 
function comuns_chile($cities){	
  $cities = array(
          'Seleccionar' => 'Selecciona tu comuna'
  );			
  return $cities;
}
add_filter('woocommerce_cities', 'comuns_chile');
 
//Información de regiones, ciudades y comunas sacadas de https://es.wikipedia.org/wiki/Anexo:Ciudades_de_Chile
function region_chile( $states ) {
    $states['CL'] = array(
		    'Seleccionar' => 'Selecciona tu Región',
            'AP' => 'Región de Arica y Parinacota', //Región de Arica y Parinacota ó AP
            'TA' => 'Región de Tarapacá', //Región de Tarapacá ó TA
            'AN' => 'Región de Antofagasta', //Región de Antofagasta ó AN
            'AT' => 'Región de Atacama', //Región de Atacama ó AT
            'CO' => 'Región de Coquimbo', //Región de Coquimbo ó CO
            'VA' => 'Región de Valparaíso', //Región de Valparaíso ó VA O VS
            'RM' => 'Región Metropolitana de Santiago', //Región Metropolitana de Santiago ó RM
            'OH' => 'Región del Libertador General Bernardo O\'Higgins', //Región del Libertador General Bernardo O'Higgins ó OH
            'MA' => 'Región del Maule', //Región del Maule ó ML
            'NB' => 'Región de Ñuble', //Región de Ñuble ó NB
            'BI' => 'Región del Biobío', //Región del Biobío ó BI
            'AR' => 'Región de La Araucanía', //Región de La Araucanía ó AR
            'LR' => 'Región de Los Ríos', //Región de Los Ríos ó LR
            'LL' => 'Región de Los Lagos', //Región de Los Lagos ó LL
            'AI' => 'Región de Aysén del General Carlos Ibáñez del Campo', //Región de Aysén del General Carlos Ibáñez del Campo ó AI
            'MG' => 'Región de Magallanes y de la Antártica Chilena' //Región de Magallanes y de la Antártica Chilena ó MA

    );
    if (is_checkout()) {
      $states['MX']=null;
    }	  
    return $states;
}

add_filter('woocommerce_states', 'region_chile');

function wf_sort_shipping_methods($available_shipping_methods, $package)
{
	// Organice los métodos de envío según sus requisitos
	$sort_order	= array(
		'pkt1'	=>	array(),
		'free_shipping'		=>	array(),
		'local_pickup'		=>	array(),
		'flat_rate'	=>	array(),		
	);
	
	// desarmar todos los métodos que necesitan ser ordenados
	foreach($available_shipping_methods as $carrier_id => $carrier){
		$carrier_name	=	current(explode(":",$carrier_id));		
		if(array_key_exists($carrier_name,$sort_order)){
			$sort_order[$carrier_name][$carrier_id]	=		$available_shipping_methods[$carrier_id];
			unset($available_shipping_methods[$carrier_id]);
		}
	}
	
	// agregar métodos nuevamente de acuerdo con la matriz de orden de clasificación
	foreach($sort_order as $carriers){
		$available_shipping_methods	=	array_merge($available_shipping_methods,$carriers);
	}
	return $available_shipping_methods;
}
add_filter('woocommerce_package_rates', 'wf_sort_shipping_methods', 10, 2);

//por defecto cada que cotice seleccionar la primer opcion de envio 
function reset_default_shipping_method( $method, $available_methods ) {

  $method = key($available_methods);
 return $method;
}

add_filter('woocommerce_shipping_chosen_method', 'reset_default_shipping_method',99,2);