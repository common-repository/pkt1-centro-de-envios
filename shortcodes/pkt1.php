<?php

if(!function_exists('pkt1_shortcode')){
  function pkt1_shortcode($atts, $content = null){
    extract(shortcode_atts( array(
      'column'=>4,
      'num_products'=>4
    ), $atts, 'pkt1' ));

    return pkt1_products_view($column, $num_products);
  }
}