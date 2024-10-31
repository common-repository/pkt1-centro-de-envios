<?php

if(!function_exists('pkt1_products_view')){
  function pkt1_products_view($col_num = 0, $products_num = 0){
    $products = new Pkt1();
    $products_ids = $products->pkt1_get_products();
    
    if(!$products_ids){
      return false;
    }

    if(count($products_ids) <= 0){
      return false;
    }

    $pkt1_settings = get_option('pkt1_settings');

    if($products_num == 0){
      $num_of_display_products = isset($pkt1_settings['pkt1_numb_products']) ? $pkt1_settings['pkt1_numb_products'] : 4;
    }else{
      $num_of_display_products = $products_num = 0;
    }
    $ids = array();
    if(count($products_ids) > $num_of_display_products){
      $ids = array_slice($products_ids, -1 *  $num_of_display_products, $num_of_display_products, true);

    }else{
      $ids = $products_ids;
    }


    $the_query = new WP_Query(array(
        'post_type'=> 'product',
        'post_status'=> 'publish',
        'post__in'=> array_reverse($ids),
        'orderby'=>'post__in' 
      )
    );

    if($the_query->have_posts()){
      ?>
      <script>
      var quotations = <?php echo $response; ?>;
      console.log({quotations});

      alert(window.location);
      var settings = {
        "async": true,
        "url": "https://web.pktuno.mx/ws/Api/wslogin",
        "method": "POST",
        "headers": {
          "Content-Type": "application/json"
        },
        "processData": false,
        "data": "{'User': 'BruceWayne','ApiKey': '374467cb-1068-11ea-ace4-00090faa000114211','Secret': 'abc'}"
      }

      jQuery.ajax(settings).done(function (response) {
        console.log({response});
        if(response.isError){
          alert(response.Message);
          return;
        }

        var settingsCredit = {
          "url": "https://web.pktuno.mx/ws/Api/Clientes/credito/374467cb-1068-11ea-ace4-00090faa000114211",
          "method": "GET",
          "headers": {
            "Content-Type": "application/json",
            "Authorization": "Bearer "+response.data.Token,
          }
        }

        jQuery.ajax(settingsCredit).done(function (responseCredit) {
          console.log({responseCredit});
        });
        
        var data ={
          origin: {
            iddom: 0,
            cp: '81200',
            str: 'Allende Sur',
            col: 'Centro',
            numExt: 14,
            numInt: null,
            cty: 'Los Mochis',
            mnp: 'Ahome',
            ste: 'Sinaloa',
            ctr: 'MX'
          },
          destination: {
            iddom: 0,
            cp: '06600',
            str: 'Juarez',
            col: 'Juarez',
            numExt: 5050,
            numInt: null,
            cty: 'Ciudad De Mexico',
            mnp: 'Cuauhtemoc',
            ste: 'Ciudad de Mexico',
            ctr: 'MX'
          },
          shippingData: [
            {
              qty: 1,
              typ: 1,
              cnt: 'Ropa Infantil',
              hgt: 10,
              wdt: 10,
              lng: 10,
              wgt: 1
            },
            {
              qty: 1,
              typ: 1,
              cnt: 'Ropa',
              hgt: 15,
              wdt: 15,
              lng: 15,
              wgt: 1.5
            }
          ],
          quotation: {
            alianza: 'Paquetexpress',
            idAlianza: 2,
            serviceName: 'Servicio Estandar',
            timeTicks: 0,
            dias: 3,
            logoEmp: null,
            fechaEntrega: null,
            fechaEntregaDMY: null,
            precioNormal: 0,
            descuento: 0,
            precio: 0,
            globalProductCode: 'STD',
            localProductCode: 'STD',
            invocationid: 1137
          },
          shipCnf: {
            isv: 1000,
            dtp: 1,
            din: 'Atn el Señor de la casa',
            cvl: 0,
            acn: false
          },
          originCnf: {
            branch: 80, //Sucursal
            foreignbranch: 0
          },
          clientOrgData: {
            id: 0,
            email: 'jorgedaniel.moscosa@gmail.com',
            firstname: 'Bruce',
            secondname: '',
            lastname: 'Wayne',
            secondlastname: '',
            phone: '6681130709',
            rfc: 'FARC891206NZ7'
          },
          clientDestData: {
            id: 0,
            email: 'correoDestino@domdestino.com',
            firstname: 'Pavel',
            secondname: null,
            lastname: 'Pineda',
            secondlastname: 'Paez',
            phone: '6681505050',
            rfc: null
          },
          security: {
            usr: 'BruceWayne',
            key: '374467cb-1068-11ea-ace4-00090faa000114211',
            cot: 0
          }
        };
        var settingsShip = {
          "async": true,
          "crossDomain": true,
          "url": "https://web.pktuno.mx/ws/Api/WebBooking",
          "method": "POST",
          "headers": {
            "Content-Type": "application/json",
            "Authorization": "Bearer "+response.data.Token
          },
          "processData": false,
          "data": JSON.stringify(data) 
        }

        jQuery.ajax(settingsShip).done(function (responseShip) {
          console.log({responseShip});
        });
      });
      </script>
      <section class="products site-main">
        <h2><?= isset($pkt1_settings['pkt1_token']) ? $pkt1_settings['pkt1_token'] : '¿Qué?' ?></h2>
        <?php 
          if($col_num == 0)
            $col = 4;
          else 
            $col = $col_num;
        ?>
        <ul class="products columns-<?=$col?>">
        <?php
          while($the_query->have_posts()){
            $the_query->the_post();
            wc_get_template_part( 'content', 'product');
          }
        ?>
        </ul>
      </section>
      <?php
      wp_reset_postdata(  );
      


    }

  }

  if(!function_exists('pkt1_products_add_pkt1_columns')){
    function pkt1_products_add_pkt1_columns(){
      
      ?>
        <script>
          if(window.location.href.toLowerCase().indexOf('post_type=shop_order') >= 0){
            
            jQuery('#posts-filter > table > thead > tr').append('<th>Acciones</th>');
            jQuery.each(jQuery('#posts-filter > table > tbody > tr'), function(i,el){
                    jQuery('#posts-filter > table > tbody tr').append('<th id="pkt1_order_'+i+'" style="color:#666666">Validando crédito...</th>');
              }
            )
            var settings = {
                  "async": true,
                  "url": "https://web.pktuno.mx/ws/Api/wslogin",
                  "method": "POST",
                  "headers": {
                    "Content-Type": "application/json"
                  },
                  "processData": false,
                  "data": "{'User': 'BruceWayne','ApiKey': '374467cb-1068-11ea-ace4-00090faa000114211','Secret': 'abc'}"
                }

            jQuery.ajax(settings).done(function (response) {
              console.log({response});
              if(response.isError){
                alert(response.Message);
                return;
              }
            
              var settingsCredit = {
                "url": "https://web.pktuno.mx/ws/Api/Clientes/credito/374467cb-1068-11ea-ace4-00090faa000114211",
                "method": "GET",
                "headers": {
                  "Content-Type": "application/json",
                  "Authorization": "Bearer "+response.data.Token,
                }
              }

              jQuery.ajax(settingsCredit).done(function (responseCredit) {
                console.log({responseCredit});
                if(responseCredit.isError){
                  alert('Error: '+responseCredit.Message);
                  return;
                }
                else if(!responseCredit.data.consaldo){
                  alert('No tiene saldo disponible.');
                  return;
                }

                jQuery.each(jQuery('#posts-filter > table > tbody > tr'), function(i,el){
                    var id = jQuery('#posts-filter > table > tbody tr')[i.toString()].attributes["id"].textContent.replace("post-","");
                    jQuery('#pkt1_order_'+i).remove();
                    jQuery('#posts-filter > table > tbody tr').append('<th>'+id+'</th>');
                  }
                )
              });

            });
            jQuery('#posts-filter > table > tfoot > tr').append('<td>Acciones</td>');
          }
        </script>
      <?php
    }
  }  

  if(!function_exists('pkt1_ws_codpost')){
    function pkt1_ws_codpost(){
      ?>

      <script>
        let urlws = "https://web.pktuno.mx/ws";
        jQuery.getJSON(urlws+"/Api/Cps/Autocomplete?term=81200").done(
          function(data){
            console.log('codpost',data);
            //Ejemplo de json respuesta		
            //[
            //  {
            //	"value": "81200-Centro-Los Mochis-Ahome-Sinaloa",
            //	"label": "81200-Centro-Los Mochis-Ahome-Sinaloa"
            //  },
            //  {
            //	"value": "81200-Centro-Los Mochis-Ahome-Sinaloa",
            //	"label": "81200-Centro-Los Mochis-Ahome-Sinaloa"
            //  }.
            //]
          }
        ).fail(
          function(err){
            console.log('error codpost',err);
          }
        );
      </script>
      
      <?php
    }
  }

  if(!function_exists('pkt1_ws_insertclient')){
    function pkt1_ws_insertclient(){
      ?>

      <script>

        var settings = {
            "async": true,
            "url": "https://web.pktuno.mx/ws/Api/wslogin",
            "method": "POST",
            "headers": {
              "Content-Type": "application/json"
            },
            "processData": false,
            "data": "{'User': 'BruceWayne','ApiKey': '374467cb-1068-11ea-ace4-00090faa000114211','Secret': 'abc'}"
          }

        jQuery.ajax(settings).done(function (response) {
          console.log({response});
          if(response.isError){
            alert(response.Message);
            return;
          }
            
              
          let urlWS = "https://web.pktuno.mx/ws";
          let apiKey = '374467cb-1068-11ea-ace4-00090faa000114211';
          let idUser = '3557';
          //Este arreglo es para insertar y/o editar un cliente requiere autorizacion

          var cliente = {};
          cliente.id = 0;// 0 o el id que se va a modificar
          cliente.tipo = 'Fisico';// tipoCliente;//Fisico o Moral 
          cliente.nombre = "Juan Pedro";
          cliente.apellidom = "Leyva";
          cliente.apellidop = "Navarro";
          cliente.rfc = "XAXX010101000";//Default
          cliente.calle = "Allente";
          cliente.noint = "25";
          cliente.noext = "3 ";//Mandar vacio si no hay
          cliente.cp = 81200;
          cliente.telefono = 6681130709;
          cliente.ciudad = "Los Mochis";
          cliente.municipio = "Ahome";
          cliente.colonia = "Centro";
          cliente.tipodomicilio = "fiscal";
          cliente.activo = "Si";
          cliente.observacion = "";//vacio
          cliente.idfranquicia = 1;
          cliente.pais = "MX"
          cliente.estado = "Sinaloa";
          cliente.correo = "Correo@Dominio.com";

          //Si es nuevo cliente usamos esta url
          var url = urlWS + "/Api/Clientes/insertar/" + apiKey + "?iduser=" + idUser;
          //si es edicion usamos esta otra
          //var url = urlWS + "/Api/Clientes/modificar/" + apikey + "?iduser=" + iduser;

          //ejemplo con jQuery.ajax
          jQuery.ajax({
            url: url,
            type: "put",
            headers: {
              "Content-Type": "application/json",
              "Authorization": "Bearer "+response.data.Token,
            },
            data: JSON.stringify(cliente),
            datatype: 'JSON',
            contentType: 'application/json',
            success: function(data) {		
              console.log('insertclient',data);	
              //ejemplo de respuesta : {"key":"","isError":false,"Message":"Proceso Exitoso","totalRecords":0,"data":[{"idcliente":12683},{"iddireccion":"14045"}]}
              //siempre validar el campo isError
            },
            error: function(err) {
              console.log('error insertclient',err);
              //Error no controlado
            }
          });

        });
      </script>
      
      <?php
    }
  }

  if(!function_exists('pkt1_ws_insertdireccion')){
    function pkt1_ws_insertdireccion(){
      ?>

      <script>
        var settingsDIR = {
            "async": true,
            "url": "https://web.pktuno.mx/ws/Api/wslogin",
            "method": "POST",
            "headers": {
              "Content-Type": "application/json"
            },
            "processData": false,
            "data": "{'User': 'BruceWayne','ApiKey': '374467cb-1068-11ea-ace4-00090faa000114211','Secret': 'abc'}"
          }

        jQuery.ajax(settingsDIR).done(function (response) {
          console.log({response});
          if(response.isError){
            alert(response.Message);
            return;
          }

          let urlwebs = "https://web.pktuno.mx/ws";
          let apikeyDir = '374467cb-1068-11ea-ace4-00090faa000114211';
          let iduserDir = '3557';
          //Este arreglo es para insertar y/o editar una direccion requiere autorizacion
          var datosDirecciones = {};
          datosDirecciones.idcliente = 14229;//obligatorio enviar idcliente
          datosDirecciones.id = 0; //idDireccion; // 0 si es nuevo
          datosDirecciones.calle = "Independencia";
          datosDirecciones.noext = "33";
          datosDirecciones.noint = "";
          datosDirecciones.colonia = "Centro";
          datosDirecciones.localidad = "Los Mochis";
          datosDirecciones.municipio = "Ahome";
          datosDirecciones.estado = "Sinaloa";
          datosDirecciones.pais = "MX";
          datosDirecciones.cp = 81200;
          datosDirecciones.correo = "CORREO@DOMINIO.COM";
          datosDirecciones.telefono = 6681130709
          datosDirecciones.original = "No";

          //url para insertar
          var urlajax = urlwebs+"/Api/Clientes/Direcciones/Insertar/" + apikeyDir + "?iduser=" + iduserDir;
          //url para modificar
          //var url = urlWS+"/Api/Clientes/Direcciones/modificar/" + {apikey} + "?iduser=" + {idusuario};


          jQuery.ajax({
            url: urlajax,
            type: "post",
            data: JSON.stringify(datosDirecciones),
            datatype: 'JSON',
            contentType: 'application/json',
            headers: {
              "Content-Type": "application/json",
              "Authorization": "Bearer "+response.data.Token,
            },
            success: function(data) {		
              console.log('insert dir client',data);	
              //ejemplo de respuesta : {"key":"","isError":false,"Message":"Proceso Exitoso","totalRecords":0,"data":[{"idcliente":12683},{"iddireccion":"14045"}]}
              //siempre validar el campo isError
            },
            error: function(err) {
              console.log('error insert dir client',err);
              //Error no controlado
            }
          });

        });
        </script>
      
      <?php
    }
  }
  
  if(!function_exists('pkt1_apply_darkmode')){
    function pkt1_apply_darkmode(){
      ?>

      <script>
        function apply_pkt1_darkmode(){
          let pkt1_tarjeta = jQuery('.pkt1_darkmode');
          if(pkt1_tarjeta.length >= 1){
              try {
                jQuery('.pkt1_darkmode')[0].parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.classList.add('pkt1_container_darkmode');
              } catch (error) {
                
              }
              try {
                jQuery('.pkt1_darkmode')[0].parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.classList.add('pkt1_container_darkmode');
              } catch (error) {
                
              }
          }

          /*if(jQuery('.woocommerce-shipping-methods .pkt1_darkmode').length > 1){
              jQuery('.woocommerce-shipping-methods')["0"].parentElement.classList.add('pkt1_container_darkmode');
          }
          else
            jQuery('.woocommerce-shipping-methods')["0"].parentElement.classList.add('pkt1_container');
          */
        }
        setTimeout( function(){  
          apply_pkt1_darkmode();},3000
        );

        setInterval(function(){
          apply_pkt1_darkmode();
        }, 100)
        
      </script>
      
      <?php
    }
  }

  if(!function_exists('pkt1_apply_visualization')){
    function pkt1_apply_visualization(){
      ?>

      <script>
        function apply_pkt1_visualization(){
          let pkt1_tarjeta = jQuery('.pkt1_tarjeta');
          if(pkt1_tarjeta.length >= 1){
              try {
                jQuery('.pkt1_tarjeta')[0].parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.classList.add('pkt1_container_tarjeta');
              } catch (error) {
                
              }
              try {
                jQuery('.pkt1_tarjeta')[0].parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.classList.add('pkt1_container_tarjeta');
              } catch (error) {
                
              }
          }
        }

        setInterval(function(){
          apply_pkt1_visualization();
        }, 100)
        
        jQuery(document).ready(function(){
          apply_pkt1_visualization();
          setTimeout( function(){  
            apply_pkt1_visualization();
            },500
          );

          setTimeout( function(){  
            apply_pkt1_visualization();
            },800
          );

          setTimeout( function(){  
            apply_pkt1_visualization();
            },1000
          );

          setTimeout( function(){  
            apply_pkt1_visualization();
            },2000
          );

          setTimeout( function(){  
            apply_pkt1_visualization();
            },3000
          );

          setTimeout( function(){  
            apply_pkt1_visualization();
            },5000
          );

          setTimeout( function(){  
            apply_pkt1_visualization();
            },8000
          );

          setTimeout( function(){  
            apply_pkt1_visualization();
            },10000
          );

          setTimeout( function(){  
            apply_pkt1_visualization();
            },15000
          );

          setTimeout( function(){  
            apply_pkt1_visualization();
            },20000
          );
          
        })
        

      </script>
      
      <?php
      
    }
  }
}