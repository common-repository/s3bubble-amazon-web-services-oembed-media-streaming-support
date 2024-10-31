<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
* Run a filter on content to replace s3bubble div with iframe
* @author s3bubble
* @none
*/ 
function s3bubble_oembed_amp_filter_content( $content ) {

	// Option in menu can force iframes
	$options = (is_array(get_option( 's3bubble_oembed_plugin_options' ))) ? get_option( 's3bubble_oembed_plugin_options' ) : [];

    if(isset($options['use_iframes']) && $options['use_iframes'] == 'on'){

    	preg_match_all('/data-code="([^"]*)"/', $content, $code);
		
		foreach ($code[1] as $index => $player) {

			$content = preg_replace('#<div[^>]*data-code="' . $player . '"[^>]*>.*?</div>#is', '<div style="position:relative;padding-bottom:56.25%;"><iframe style="width:100%;height:100%;position:absolute;left:0px;top:0px;" src="' . site_url() . '/s3frame?code=' . $player . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>', $content);


		}

    }else{

    	if(function_exists('amp_is_request')){
 
			if(amp_is_request()){

				preg_match_all('/data-code="([^"]*)"/', $content, $code);
				
				foreach ($code[1] as $index => $player) {

					$content = preg_replace('#<div[^>]*data-code="' . $player . '"[^>]*>.*?</div>#is', '<div style="position:relative;padding-bottom:56.25%;"><iframe style="width:100%;height:100%;position:absolute;left:0px;top:0px;" src="' . site_url() . '/s3frame?code=' . $player . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>', $content);


				}

			}

		}

    }

    return $content;

}

add_filter( 'the_content', 's3bubble_oembed_amp_filter_content' );

/*
* Add an iframe to be used to load the videos
* @author s3bubble
* @none
*/ 
add_filter('template_include', function($template) {

	$url_path = trim(parse_url(add_query_arg(array()), PHP_URL_PATH), '/');
	  
	if ( $url_path === 's3frame' ) {

	    return dirname( __FILE__ ) . '/amp-iframe.php';

  	}else{
  		
  		return $template;

  	}

});

/*
* Include scripts in the player iframe template
* @author s3bubble
* @none
*/ 
function s3bubble_oembed_amp_iframe_scripts(){

	echo "<script type='text/javascript' id='s3bubble-hosted-cdn-js-extra'>
		var s3bubble_oembed = " . json_encode(array(
			'ajax' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce('ajax-nonce'),
			'is_amp' => true
		)
	) . "; 
	</script>";	

}  