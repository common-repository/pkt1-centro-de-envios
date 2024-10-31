
function pkt1_checksaldo_ajax(apikey,username){ 
  return;
  var settings = {
    "async": true,
    "url": "https://web.pktuno.mx/ws/Api/wslogin",
    "method": "POST",
    "headers": {
      "Content-Type": "application/json"
    },
    "processData": false,
    "data": "{'User': '"+username+"','ApiKey': '"+apikey+"','Secret': 'abc'}"
  }

  jQuery.ajax(settings).done(function (response) {
    console.log({response});
    if(response.isError){
      alert(response.Message);
      return;
    }

    var settingsCredit = {
      "url": "https://web.pktuno.mx/ws/Api/Clientes/credito/"+apikey,
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

      //alert('si tiene saldo');
    });
  });
}
