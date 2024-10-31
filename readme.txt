=== PKT1 Centro de envios ===
Contributors: Intergradora de franquicias PKT1 
Tags: woocommerce, chile, mexico, regiones, envios, shipping, estados, logistica
Requires at least: 4.0
Tested up to: 6.4.2
Requires PHP: 7.0.33
Stable tag: 1.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Calcule tarifas de envio en tiempo real con los principales agentes de paqueteria regionales y mundiales

== Description ==

PKT1 te permite calcular tarifas y tiempos de entrega en base a la configuracion de tu producto y 
el destino seleccionado con los principales agentes de paqueteria regionales y mundiales
Guia de Embalaje:
https://enviospkt1.com/wp-content/uploads/2021/10/GUIA_DE_EMBALAJE_PKT1.pdf
Mercancias Prohibidas:
https://enviospkt1.com/wp-content/uploads/2021/10/Mercancias_prohibidas.pdf
UTILIZAMOS APIS/ENLACES INTERNAS DE PKT1 PARA PROCESAR SUS DATOS DE COTIZACION Y ENVIO, LEA EL AVISO DE PRIVACIDAD EN EL SIGUIENTE ENLACE:
https://enviospkt1.com/wp-content/uploads/2020/03/AVISO-DE-PRIVACIDAD-PKT1-PLUG-IN-MARZO2020.pdf
Enlacea a los servicios Internos: Chile: HTTPS://WEB.PKTUNO.CL, Mexico: HTTPS://WEB.PKTUNO.MX

== Installation ==

Para instalar este plugin haga lo siguiente:

1. Suba `pkt1-centro-de-envios-X.X.X` a la carpeta `/wp-content/plugins/`
2. Active el plugin a traves del menú 'Plugins' dentro de la administración de WordPress
3. Contacte a la oficina regional de PKT1 para obtener credenciales o envie un correo
a soportewoocommerce@pktuno.com

== Changelog ==

= 1.1.0 =

Catalogo dinamico de comunas y regiones (CL)
Reacomodo de campos de checkout
Habilitacion/inhabilitacion de tiempos de entrega
Configuracion para Dimensiones y peso Unicos

= 1.1.1 =

Correccion en clave de comuna (CL)

= 1.1.2 =

Configuracion de etiqueta unica
Mejora en muestreo de etiqueta
Catalogo dinamico de colonias (MX)
Reacomodo de campos en Checkout (MX)

= 1.1.3 =

Bulto unico al cotizar activado con la etiqueta unica
Mejora en ordenamiento de opciones de envio
Agregar gastos de envio adicionales
Mejoras de rendimiento y procesamiento de datos

= 1.1.4 =

Optimizacion de rutas API
Se incluye vista clasica en opciones de envio
Control de errores en Js personalizado para el plugin
Mejoras de rendimiento y procesamiento de datos

= 1.1.5 =

Mejora en generacion de etiqueta esta solo se generara si el pedido paso a estatus Procesando/Completado
Mejora en etiqueta unica al cotizar 1 solo producto
Se incluye el objeto interno CartCost (Revisar documentacion API)
Cambio de concepto Días Habiles por Días 
Correcion en tipo de cotizacion para chile
Remocion de validacion de telefono para chile

= 1.1.5 =

Configuracion de envios gratuitos por costo del carrito para mexico y chile

= 1.1.6 =

Configuracion de envios gratuitos por costo del carrito para mexico y chile

= 1.1.7 =

El campo Addres2 deja de contener la colonia, ahora contendra el numero interior para mexico
Se crea el campo colonia, independiente para mexico
La validacion de numero interior ahora es de 35 caracteres para chile y mexico
Los datos indicados en numero interior viajaran como Comentarios/Observaciones en las guias de despacho

= 1.1.8 =

Correcciones y validaciones adicionales para mexico y chile

= 1.1.9 =

Inclusion de variable adicional para determinacion de seguro automatico
Correccion para mostrar precios desde el carro de compra

= 1.2.0 =

Correccion de bug en parametro shipping_colonia

= 1.2.1 =

Hook forzado de seleccion primer envio cotizado en todos los casos , para chile y mexico
Atencion a Warning por casteo de array no declarado
parche menor sobre campos personalizados


== Frequently Asked Questions ==

= ¿Tienen cobertura a todo el mundo? =

Siempre que exista una oficina regional de PKT1 tendremos cobertura regional y mundial

= ¿Puedo usarlo con otros Plugis de despacho/envio? =

Siempre y cuando no se interfiera con las clases base, hemos notado conflictos
con el plugin de chileexpress y ups al momento.

= ¿Es compatible con otros plugins? =

Con la mayoria , pero aqui te dejamos una lista con los plugins que tenemos problemas: 
Regiones y Comunas de Chile
Regiones de Chile
chileexpress
shipit