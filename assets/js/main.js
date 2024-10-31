jQuery('#pkt1_existesucursal').click(()=>{
  if(jQuery('#pkt1_existesucursal').prop('checked')){
    jQuery('#pkt1_tridsucursal').css({'display':'table-row'});
  }
  else{
    jQuery('#pkt1_tridsucursal').css({'display':'none'});
  }
});

jQuery('#pkt1_boxcalc').click(()=>{
	if (document.getElementById('pkt1_boxcalc').checked) 
	{
	  document.getElementById('pkt1_onelabel').checked=false;
	  document.getElementById('pkt1_dimunique').checked=false;
	  jQuery('#pkt1_tridimunique').css({'display':'none'});
	}
});

jQuery('#pkt1_dimunique').click(()=>{
  if(jQuery('#pkt1_dimunique').prop('checked')){
    jQuery('#pkt1_tridimunique').css({'display':'table-row'});	
    document.getElementById('pkt1_onelabel').checked=false;      
	  document.getElementById('pkt1_boxcalc').checked=false;
  }
  else{
    jQuery('#pkt1_tridimunique').css({'display':'none'});
  }
});

jQuery('#pkt1_onelabel').click(()=>{
	if (document.getElementById('pkt1_onelabel').checked) 
	{
	  document.getElementById('pkt1_dimunique').checked=false;	      
	  document.getElementById('pkt1_boxcalc').checked=false;
	  jQuery('#pkt1_tridimunique').css({'display':'none'});
	}
});

jQuery('#pkt1_enviosgratis').click(()=>{
  if(jQuery('#pkt1_enviosgratis').prop('checked')){
    jQuery('#pkt1_cartcost').css({'display':'table-row'});	
    jQuery('#pkt1_trienviosgratislabel').css({'display':'block'});	
	jQuery('#pkt1_trienviosgratisfield').css({'display':'table-row'});		
  }
  else{
    jQuery('#pkt1_cartcost').css({'display':'none'});	
    jQuery('#pkt1_trienviosgratislabel').css({'display':'none'});	
	jQuery('#pkt1_trienviosgratisfield').css({'display':'table-row'});	
  }
});

function setInputFilter(textbox, inputFilter) {
  ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"].forEach(function(event) {
    textbox.addEventListener(event, function() {
      if (inputFilter(this.value)) {
        this.oldValue = this.value;
        this.oldSelectionStart = this.selectionStart;
        this.oldSelectionEnd = this.selectionEnd;
      } else if (this.hasOwnProperty("oldValue")) {
        this.value = this.oldValue;
        this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
      } else {
        this.value = "";
      }
    });
  });
}

jQuery( document ).ready(function() {
  jQuery('#asegurarenvio_field').appendTo(jQuery('.woocommerce-billing-fields__field-wrapper'));
  jQuery('#asegurarenvio').click(()=>{
    if(jQuery('#asegurarenvio').prop('checked')){
      jQuery('#billing_address_1').val(jQuery('#billing_address_1').val().replace(' .',''));
      jQuery('#billing_address_1').val(jQuery('#billing_address_1').val()+' .');
      jQuery('#shipping_address_1').val(jQuery('#shipping_address_1').val()+' .');
    }
    else{
      jQuery('#billing_address_1').val(jQuery('#billing_address_1').val().replace(' . .',' .'));
      jQuery('#billing_address_1').val(jQuery('#billing_address_1').val().replace(' .',''));
      jQuery('#shipping_address_1').val(jQuery('#shipping_address_1').val().replace(' .',''));
    }
    jQuery( 'body' ).trigger( 'update_checkout' );
  })
  var postalcodefield = document.getElementById("calc_shipping_postcode");
  if (!postalcodefield){
	  var cityfield = document.getElementById("calc_shipping_city");
	  if (cityfield){
		  cityfield.placeholder="Comuna";
	  };
  }else{
	  var cityfield = document.getElementById("calc_shipping_city");
	  if (cityfield){
		  cityfield.placeholder="Localidad / Ciudad / delegacion";
	  };
  }

  setTimeout(function(){
	var elementExists = document.getElementById("billing_address_1");
	if(elementExists){
		if(jQuery('#billing_address_1').val().indexOf(' .') == -1 && jQuery('#shipping_address_1').val().indexOf(' .') == -1)
		  jQuery('#asegurarenvio').prop('checked', false)
		else
		  jQuery('#asegurarenvio').prop('checked', true)

		setInputFilter(document.getElementById("billing_phone"), function(value) {
		  if(document.getElementById("billing_phone").value.length >= 12){
			document.getElementById("billing_phone").value = document.getElementById("billing_phone").value.substring(0,11)
		  }
		  return /^\d*?\d*$/.test(value); // Allow digits and '.' only, using a RegExp
		});

		document.getElementById("billing_phone").value = "";
	}
  },500);
});


function marcarseleccionado(){
  jQuery('.woocommerce-shipping-methods li').removeClass('seleccionado');
  apply_pkt1_darkmode();
  if(jQuery('.woocommerce-shipping-methods li input[checked="checked"]').length>0 )
    jQuery('.woocommerce-shipping-methods li input[checked="checked"]')["0"].parentElement.classList.add('seleccionado');
  setTimeout( function(){
    apply_pkt1_darkmode();
    if(jQuery('.woocommerce-shipping-methods li input[checked="checked"]').length>0 )
    jQuery('.woocommerce-shipping-methods li input[checked="checked"]')["0"].parentElement.classList.add('seleccionado');
  },50);
  setTimeout( function(){
    apply_pkt1_darkmode();
    if(jQuery('.woocommerce-shipping-methods li input[checked="checked"]').length>0 )
    jQuery('.woocommerce-shipping-methods li input[checked="checked"]')["0"].parentElement.classList.add('seleccionado');
  },100);
  setTimeout( function(){
    apply_pkt1_darkmode();
    if(jQuery('.woocommerce-shipping-methods li input[checked="checked"]').length>0 )
    jQuery('.woocommerce-shipping-methods li input[checked="checked"]')["0"].parentElement.classList.add('seleccionado');
  },200);
  setTimeout( function(){
        apply_pkt1_darkmode();
    if(jQuery('.woocommerce-shipping-methods li input[checked="checked"]').length>0 )
    jQuery('.woocommerce-shipping-methods li input[checked="checked"]')["0"].parentElement.classList.add('seleccionado');
  },300);
  setTimeout( function(){
        apply_pkt1_darkmode();
    if(jQuery('.woocommerce-shipping-methods li input[checked="checked"]').length>0 )
      jQuery('.woocommerce-shipping-methods li input[checked="checked"]')["0"].parentElement.classList.add('seleccionado');
  },400);
  setTimeout( function(){
        apply_pkt1_darkmode();
    if(jQuery('.woocommerce-shipping-methods li input[checked="checked"]').length>0 )
    jQuery('.woocommerce-shipping-methods li input[checked="checked"]')["0"].parentElement.classList.add('seleccionado');
  },500);
  setTimeout( function(){
        apply_pkt1_darkmode();
    if(jQuery('.woocommerce-shipping-methods li input[checked="checked"]').length>0 )
    jQuery('.woocommerce-shipping-methods li input[checked="checked"]')["0"].parentElement.classList.add('seleccionado');
  },1000);
  setTimeout( function(){
        apply_pkt1_darkmode();
    if(jQuery('.woocommerce-shipping-methods li input[checked="checked"]').length>0 )
    jQuery('.woocommerce-shipping-methods li input[checked="checked"]')["0"].parentElement.classList.add('seleccionado');
  },1500);
  setTimeout( function(){
        apply_pkt1_darkmode();
    if(jQuery('.woocommerce-shipping-methods li input[checked="checked"]').length>0 )
    jQuery('.woocommerce-shipping-methods li input[checked="checked"]')["0"].parentElement.classList.add('seleccionado');
  },2000);
  setTimeout( function(){
    apply_pkt1_darkmode();
    if(jQuery('.woocommerce-shipping-methods li input[checked="checked"]').length>0 )
    jQuery('.woocommerce-shipping-methods li input[checked="checked"]')["0"].parentElement.classList.add('seleccionado');
  },3000);
}

//jQuery('.woocommerce-shipping-methods')["0"].parentElement.classList.add('pkt1_container');


function showLabel(username, secret, apikey, alianza, norastreo, idpkt1, country){
  let test = false;
  let urllogin = '';
  if(country == "MX")
    urllogin = "https://web.pktuno.mx/ws/Api/wslogin";
  else if(country == "CL")
    urllogin = "https://web.pktuno.cl/ws/Api/wslogin";

  if(test){
    if(country == "MX")
      urllogin = "http://app.pktuno.mx:81/ws/Api/wslogin";
    else if(country == "CL")
      urllogin = "http://app.pktuno.cl:81/ws/Api/wslogin";

  }

  var settings = {
    "async": true,
    "url": urllogin,
    "method": "POST",
    "headers": {
      "Content-Type": "application/json"
    },
    "processData": false,
    "data": "{'User': '"+username+"','ApiKey': '"+apikey+"','Secret': '"+secret+"'}"
  }

  jQuery.ajax(settings).done(function (response) {
  console.log({response});
  if(response.isError){
    alert(response.Message);
    return;
  }

  let urlcredit = '';
  if(country == "MX")
    urlcredit = "https://web.pktuno.mx/ws/Api/Clientes/credito/";
  else if(country == "CL")
    urlcredit = "https://web.pktuno.cl/ws/Api/Clientes/credito/";

  if(test){
    if(country == "MX")
      urlcredit = "http://app.pktuno.mx:81/ws/Api/Clientes/credito/";
    else if(country == "CL")
      urlcredit = "http://app.pktuno.cl:81/ws/Api/Clientes/credito/";
  }

  var settingsCredit = {
    "url": urlcredit+apikey,
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
    else if(responseCredit.data.estatuscredito == 0){
      alert('No tiene saldo disponible.');
      return;

      if(!responseCredit.data.consaldo){
        alert('No tiene saldo disponible.');
        return;
      }
    }
    else{
      let urlbase = '';
      if(country == "MX")
        urlbase = "https://web.pktuno.mx/";
      else if(country == "CL")
        urlbase = "https://web.pktuno.cl/";
      
      if(test){
        if(country == "MX")
          urlbase = "http://app.pktuno.mx:81/";
        else if(country == "CL")
          urlbase = "http://app.pktuno.cl:81/";
      }
      
        
      if (country=='MX'){
        window.open(urlbase+'PKT1/print64.php?iddocumentacion='+idpkt1+'&OnSite=Si','_blank');
        return;
      }
      else if (country=='CL'){
        window.open(urlbase+'CLPKT1/print64.php?iddocumentacion='+idpkt1+'&OnSite=Si','_blank');
        return;
      }
      if(alianza == 'Paquetexpress'){
        window.open(urlbase+'paqueteexpress/pruebasphp/pruebaimpresion.php?norastreo='+norastreo+'&GPDF=Si&Onsite','_blank');
      }
      else if(alianza == 'UPS'){
        if (country=='MX'){
          window.open(urlbase+'UPSApi/printpreview.php?id='+idpkt1+'&GPDF=Si&Onsite','_blank');
        }
        else{
          window.open(urlbase+'CLUPSApi/printpreview.php?id='+idpkt1+'&GPDF=Si&Onsite','_blank');
        }  
      }    
      else if(alianza == 'Fedex'){
        if (country=='MX'){
          window.open(urlbase+'DHL-API/samples/GB/ShipmentRePrintFedex.php?id='+idpkt1+'&GPDF=Si&Onsite','_blank');
        }
        else{
          window.open(urlbase+'CLDHL-API/samples/GB/ShipmentRePrintFedex.php?id='+idpkt1+'&GPDF=Si&Onsite','_blank');
        }
      }
      else if(alianza == 'DHL'){
        if (country=='MX'){
          window.open(urlbase+'DHL-API/samples/GB/ShipmentRePrint.php?id='+idpkt1+'&GPDF=Si&Onsite','_blank');
        }
        else{
          window.open(urlbase+'CLDHL-API/samples/GB/ShipmentRePrint.php?id='+idpkt1+'&GPDF=Si&Onsite','_blank');
        }
      }
      else if(alianza == 'Blue' || alianza == 'BlueExpress'|| alianza == 'Bluexpress'|| alianza == 'Blueexpress'){
        window.open(urlbase+'BLUEXPRESS/Api/impresiones/pdfos/1/'+idpkt1+'?Pt=1','_blank');
      }
      else if(alianza == 'Urbano' || alianza == 'Urbano'|| alianza == 'URBANO'){
        window.open(urlbase+'URBANO/Api/impresiones/pdfos/1/'+idpkt1+'?Pt=1','_blank');
      }
      else if(alianza == 'Pkt1' || alianza == 'pkt1'|| alianza == 'PKT1'){
        window.open(urlbase+'clPKT1/print64.php?iddocumentacion='+idpkt1+'&OnSite=Si','_blank');
      }
    }
    
  });

  });
}
