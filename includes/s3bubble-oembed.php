<?php

if (!class_exists("s3bubble_oembed")) {

	class s3bubble_oembed {

		/*
		 * Constructor method to initiate the class
		 * @author s3bubble
		 * @params none
		 */
		public function  __construct(){ 

			/*
			 * Depreciated
			 * @author s3bubble
			 * @params none
			 */ 
			add_action( 'admin_init', array($this, 'register_s3bubble_aws_self_hosted_settings') );

			/*
			 * Add css to the header of the document
			 * @author s3bubble
			 * @params none
			 */ 
			add_action( 'wp_enqueue_scripts', array( $this, 's3bubble_oembed_scripts' ), 12 );

			/*
			 * Add javascript to the frontend footer connects to wp_footer
			 * @author s3bubble
			 * @params none
			 */ 
			add_action( 'admin_enqueue_scripts', array( $this, 's3bubble_oembed_admin_scripts' ) );
			
			/*
			 * Setup website connection
			 */
			add_action( 'init', array( $this, 's3bubble_oembed_connection' ));

			/*
			 * Oembed support fix
			 */
			add_action( 'init', array( $this, 's3bubble_oembed_iframes' ));

			/*
			 * Setup shortcodes for the plugin
			 * @author s3bubble
			 * @params none
			 */ 
			add_shortcode( 's3bubble', array( $this, 's3bubble_aws_self_hosted' ) );

			/*
			 * Load the languages file
			 * @author s3bubble
			 * @params none
			 */ 
			add_action( 'plugins_loaded', array( $this, 's3bubble_amazon_web_services_oembed_media_streaming_support_textdomain' ) );

			/*
			 * Set the DRM token
			 * @author s3bubble
			 * @params none
			 */
			add_action('wp_ajax_s3bubble_oembed_set_cookie', array( $this, 's3bubble_oembed_set_cookie' )); 
			add_action('wp_ajax_nopriv_s3bubble_oembed_set_cookie', array( $this, 's3bubble_oembed_set_cookie' ));

			/*
			 * Get the DRM token
			 * @author s3bubble
			 * @params none
			 */
			add_action('wp_ajax_s3bubble_proxy', array( $this, 's3bubble_oembed_proxy_token' )); 
			add_action('wp_ajax_nopriv_s3bubble_proxy', array( $this, 's3bubble_oembed_proxy_token' ));
			add_action('wp_ajax_s3bubble_proxy_token', array( $this, 's3bubble_oembed_proxy_token' )); 
			add_action('wp_ajax_nopriv_s3bubble_proxy_token', array( $this, 's3bubble_oembed_proxy_token' ));

			/*
			 * Admin notices
			 * @author s3bubble
			 * @params none
			 */
			add_action( 'admin_init', array( $this, 's3bubble_oembed_check_installation_date_exists' ) );
			add_action( 'admin_init', array( $this, 's3bubble_oembed_check_installation_date' ) );
			add_action( 'admin_init', array( $this, 's3bubble_oembed_set_no_bug' ) );

		}

		/**
		* Check date on admin initiation and add to admin notice if it was over 10 days ago.
		* @return null
		*/
		function s3bubble_oembed_check_installation_date_exists() {

			if(!get_option('s3bubble_oembed_activation_date')){

				$now = strtotime( "now" );

			    add_option( 's3bubble_oembed_activation_date', $now );

			}

		}

		/**
		* Check date on admin initiation and add to admin notice if it was over 10 days ago.
		* @return null
		*/
		function s3bubble_oembed_check_installation_date() {

		    $install_date = get_option( 's3bubble_oembed_activation_date' );

		    $nobug = get_option('s3bubble_oembed_no_bug');

		    if (!$nobug) {

			    $past_date = strtotime( '-12 days' );

			    if ( $past_date >= $install_date ) {

			        add_action( 'admin_notices', array( $this, 's3bubble_oembed_display_admin_notice' ) );

			    }

			}

		}

		/**
		* Display Admin Notice, asking for a review
		* @return null
		*/
		function s3bubble_oembed_display_admin_notice() {

		    // Review URL - Change to the URL of your plugin on WordPress.org
		    $reviewurl = 'https://wordpress.org/support/plugin/s3bubble-amazon-web-services-oembed-media-streaming-support/reviews/?rate=5#new-post';

		    $nobugurl = get_admin_url() . '?s3bubble_oembed_review_dismiss=1';

		    echo '<div class="notice notice-info s3bubble-oembed-notice"><img height="60" src="https://ps.w.org/s3bubble-amazon-web-services-oembed-media-streaming-support/assets/icon-128x128.jpg" /><p>'; 

		    	printf( __( "<b>Help Development!</b> We have noticed that you have been using <b>S3Bubble Media Plugin</b> for some time. We hope you love it, and we would really appreciate it if you would give us a 5 stars rating! <a href='%s' target='_blank'>Leave A Review</a> | <a href='%s'>Leave Me Alone</a>" ), $reviewurl, $nobugurl ); 

		    echo "</p></div>";
		}

		/**
		* Set the plugin to no longer bug users if user asks not to be.
		* @return null
		*/
		function s3bubble_oembed_set_no_bug() {

		    $nobug = "";

		    if ( isset( $_GET['s3bubble_oembed_review_dismiss'] ) ) {

		        $nobug = esc_attr( $_GET['s3bubble_oembed_review_dismiss'] );
		    
		    }

		    if ( 1 == $nobug ) {

		        add_option( 's3bubble_oembed_no_bug', TRUE );

		    }

		} 

		/*
		 * Loads the path to languages folder
		 * @author s3bubble
		 * @params none
		 */ 
		function s3bubble_amazon_web_services_oembed_media_streaming_support_textdomain() {
		 
		    load_plugin_textdomain( 's3bubble-amazon-web-services-oembed-media-streaming-support', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
		
		}


		/*
		 * Create a connected website option
		 * @author s3bubble
		 * @params none
		 */ 
		function s3bubble_oembed_connection(){

			add_image_size( 's3bubble_oembed_poster_image', 1280, 720, true );

			if(isset($_SERVER['HTTP_HOST'])){

				$host = (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] == "127.0.0.1") ? "localhost" : $_SERVER['HTTP_HOST'];
		
				$host = preg_replace('#^www\.(.+\.)#i', '$1', $host); // remove the www
		
				update_option("s3bubble_oembed_connected_website", $host);

			}

			if ( current_user_can( 'manage_options' ) )  {
		
				add_filter( 'mce_external_plugins', array( $this, 's3bubble_oembed_add_buttons' ) ); 
		
				add_filter( 'mce_buttons', array( $this, 's3bubble_oembed_register_buttons' ) );
		
			} 

		}

		/*
		 * Self hosted code
		 * @author s3bubble
		 * @params none
		 */ 
		function s3bubble_aws_self_hosted($atts){

			// Extract the vars from the shortcode
			extract( shortcode_atts( array(
				'code'   => '', // Playlists
				'codes'   => '', // Media
				'source'   => '', // source data
				'options'   => '', // Options
				'meta'   => '', // meta data
				'brand'   => '', // brand data
				'modal'   => '', // modal data
				'popit'  => '', // popit data
				'stream'   => '', // Stream
				'type' => 'video',
				'media' => 'video'
			), $atts, 's3bubble' ) );

			// Make sure its unique
			$id = uniqid();

			if(!empty($stream)){
				$type = "stream";
			}

			if(!empty($code)){
				return '<div id="s3bubble-' . $id . '" class="s3bubble-playlist" data-setup=\'{"code": "' . $code . '","type": "' . $type . '"}\'></div>';
			}

			// Check for multiple codes
			if(strpos($codes, ',') !== false) {
				$codes = explode(",", $codes);
				$codes = json_encode($codes);
			}else{
				$codes = json_encode(array($codes));
			}

			// Check for the popit plugin
			if (array_key_exists("popit",$atts)){
				$popit = ',"popit": {' . $popit . '}';
			}

			switch ($type) {
				case 'video':
					// Video single
					return '<div id="s3bubble-' . $id . '" class="s3bubble" data-setup=\'{"codes": ' . $codes . ',"source": {' . $source . '},"options": {' . $options . '},"meta": {' . $meta . '},"brand": {' . $brand . '}' . $popit . '}\'></div>';
					break;
				case 'audio':
					// Audio single
					return '<div id="s3bubble-' . $id . '" class="s3bubble-audio" data-setup=\'{"codes": ' . $codes . ',"source": {' . $source . '},"options": {' . $options . '},"meta": {' . $meta . '},"brand": {' . $brand . '}' . $popit . '}\'></div>';
					break;
				case 'service':
				    // Youtube links
					return '<div id="s3bubble-' . $id . '" class="s3bubble-service" data-setup=\'{"codes": ' . $codes . ',"source": {' . $source . '},"options": {' . $options . '},"meta": {' . $meta . '},"brand": {' . $brand . '}' . $popit . '}\'></div>';
					break;
				case 'stream':
					// This needs to be different 
					return '<div id="s3bubble-' . $id . '" class="s3bubble-live" data-setup=\'{"stream": "' . $stream . '","source": {' . $source . '},"options": {' . $options . '},"meta": {' . $meta . '},"brand": {' . $brand . '}' . $popit . '}\'></div>';
					break;
				case 'modal':
					// Modal popup
					return '<div id="s3bubble-' . $id . '" class="s3bubble-modal" data-setup=\'{"codes": ' . $codes . ',"source": {' . $source . '},"options": {' . $options . '},"meta": {' . $meta . '},"brand": {' . $brand . '},"modal": {' . $modal . '}}\'></div>';
					break;
				default:
					
					break;
			}

		}

		/*
		 * Add default option to database
		 * @author s3bubble
		 * @params none
		 */ 
		function s3bubble_oembed_iframes(){

			// Audio progressive
			wp_embed_register_handler( 
		        's3bubble-audio-progressive', 
		        '#https://media.s3bubble\.com/embed/aprogressive/id/([a-zA-Z0-9_-]+)$#i',   // <-- Adjust this to your needs!
		        array( $this, 's3bubble_audio_oembed_progressive_embed_handler' ) 
		    );
		    
		    // Audio hls
			wp_embed_register_handler( 
		        's3bubble-audio-hls', 
		        '#https://media.s3bubble\.com/embed/ahls/id/([a-zA-Z0-9_-]+)$#i',   // <-- Adjust this to your needs!
		        array( $this, 's3bubble_audio_oembed_hls_embed_handler' ) 
		    );

			// Video progressive
		    wp_embed_register_handler( 
		        's3bubble-video-progressive', 
		        '#https://media.s3bubble\.com/embed/progressive/id/([a-zA-Z0-9_-]+)$#i',   // <-- Adjust this to your needs!
		        array( $this, 's3bubble_video_oembed_progressive_embed_handler' )
		    );

		    // 360 Degree Video pano
		    wp_embed_register_handler( 
		        's3bubble-video-pano', 
		        '#https://media.s3bubble\.com/embed/pano/id/([a-zA-Z0-9_-]+)$#i',   // <-- Adjust this to your needs!
		        array( $this, 's3bubble_video_oembed_pano_embed_handler' )
		    );

			// Video hls
		    wp_embed_register_handler( 
		        's3bubble-video-hls', 
		        '#https://media.s3bubble\.com/embed/hls/id/([a-zA-Z0-9_-]+)$#i',   // <-- Adjust this to your needs!
		        array( $this, 's3bubble_video_oembed_hls_embed_handler' )
		    );

		    // Video hls playlist
		    wp_embed_register_handler( 
		        's3bubble-video-playlist', 
		        '#https://media.s3bubble\.com/embed/playlist/id/([a-zA-Z0-9_-]+)$#i',   // <-- Adjust this to your needs!
		        array( $this, 's3bubble_video_oembed_playlist_embed_handler' )
		    );

		    // Audio hls playlist
		    wp_embed_register_handler( 
		        's3bubble-audio-playlist', 
		        '#https://media.s3bubble\.com/embed/aplaylist/id/([a-zA-Z0-9_-]+)$#i',   // <-- Adjust this to your needs!
		        array( $this, 's3bubble_audio_oembed_playlist_embed_handler' )
		    );

		    // Live streaming
		    wp_embed_register_handler( 
		        's3bubble-video-live-streaming', 
		        '#https://media.s3bubble\.com/embed/live/username/([a-zA-Z0-9_-]+)$#i',   // <-- Adjust this to your needs!
		        array( $this, 's3bubble_video_oembed_live_embed_handler' )
		    );

		}

		/*
		 * Adds progressive oembed audio iframe support
		 * @author s3bubble
		 * @params none
		 */ 
		function s3bubble_audio_oembed_progressive_embed_handler( $matches, $attr, $url, $rawattr )
		{
		
		    $embed = sprintf(
		        '<iframe class="s3bubble-audio-oembed-iframes" src="https://media.s3bubble.com/embed/aprogressive/id/%1$s" height="160" frameborder="0" allowfullscreen></iframe>',
		        esc_attr( $matches[1] )
		    );
		
		    return apply_filters( 's3bubble_audio_oembed_progressive_embed_handler', $embed, $matches, $attr, $url, $rawattr );
		
		}

		/*
		 * Adds adaptive bitrate oembed audio iframe support
		 * @author s3bubble
		 * @params none
		 */ 
		function s3bubble_audio_oembed_hls_embed_handler( $matches, $attr, $url, $rawattr )
		{
		
		    $embed = sprintf(
		        '<iframe class="s3bubble-audio-oembed-iframes" src="https://media.s3bubble.com/embed/ahls/id/%1$s" height="160" frameborder="0" allowfullscreen></iframe>',
		        esc_attr( $matches[1] )
		    );
		
		    return apply_filters( 's3bubble_audio_oembed_hls_embed_handler', $embed, $matches, $attr, $url, $rawattr );
		
		}
        
        /*
		 * Adds progressive oembed video iframe support
		 * @author s3bubble
		 * @params none
		 */ 
		function s3bubble_video_oembed_progressive_embed_handler( $matches, $attr, $url, $rawattr )
		{

			$id = uniqid();

			if(get_option( 's3bubble_selfhosted_switch' )){

				return '<div id="s3bubble-' . $id . '" class="s3bubble" data-setup=\'{"codes": ["' . esc_attr( $matches[1] ) . '"]}\'></div>';

			}else{

				$embed = sprintf(
			        '<iframe height="360" class="s3bubble-video-oembed-iframes" src="https://media.s3bubble.com/embed/progressive/id/%1$s" frameborder="0" webkitAllowFullScreen="true" mozallowfullscreen="true" allowFullScreen="true"></iframe>',
			        esc_attr( $matches[1] )
			    );
		
			    return apply_filters( 's3bubble_video_oembed_progressive_embed_handler', $embed, $matches, $attr, $url, $rawattr );

			}

		}

		/*
		 * Adds pano 360 degress oembed video iframe support
		 * @author s3bubble
		 * @params none
		 */ 
		function s3bubble_video_oembed_pano_embed_handler( $matches, $attr, $url, $rawattr )
		{
		    $embed = sprintf(
		        '<iframe height="360" class="s3bubble-video-oembed-iframes" src="https://media.s3bubble.com/embed/pano/id/%1$s" frameborder="0" webkitAllowFullScreen="true" mozallowfullscreen="true" allowFullScreen="true"></iframe>',
		        esc_attr( $matches[1] )
		    );
		
		    return apply_filters( 's3bubble_video_oembed_pano_embed_handler', $embed, $matches, $attr, $url, $rawattr );
		
		}
        
        /*
		 * Adds adaptive bitrate oembed video iframe support
		 * @author s3bubble
		 * @params none
		 */ 
		function s3bubble_video_oembed_hls_embed_handler( $matches, $attr, $url, $rawattr )
		{

			// Make sure its unique
			$id = uniqid();

			if(get_option( 's3bubble_selfhosted_switch' )){

				return '<div id="s3bubble-' . $id . '" class="s3bubble" data-setup=\'{"codes": ["' . esc_attr( $matches[1] ) . '"]}\'></div>';

			}else{

				$embed = sprintf(
			        '<iframe height="360" class="s3bubble-video-oembed-iframes" src="https://media.s3bubble.com/embed/hls/id/%1$s" frameborder="0" webkitAllowFullScreen="true" mozallowfullscreen="true" allowFullScreen="true"></iframe>',
			        esc_attr( $matches[1] )
			    );
		
			    return apply_filters( 's3bubble_video_oembed_hls_embed_handler', $embed, $matches, $attr, $url, $rawattr );

			}
		    
		}

		/*
		 * Adds adaptive bitrate oembed video playlist iframe support
		 * @author s3bubble
		 * @params none
		 */ 
		function s3bubble_video_oembed_playlist_embed_handler( $matches, $attr, $url, $rawattr )
		{
		    $embed = sprintf(
		        '<iframe height="360" class="s3bubble-video-oembed-iframes" src="https://media.s3bubble.com/embed/playlist/id/%1$s" frameborder="0" webkitAllowFullScreen="true" mozallowfullscreen="true" allowFullScreen="true"></iframe>',
		        esc_attr( $matches[1] )
		    );
		
		    return apply_filters( 's3bubble_video_oembed_playlist_embed_handler', $embed, $matches, $attr, $url, $rawattr );
		
		}

		/*
		 * Adds adaptive bitrate oembed audio playlist iframe support
		 * @author s3bubble
		 * @params none
		 */ 
		function s3bubble_audio_oembed_playlist_embed_handler( $matches, $attr, $url, $rawattr )
		{
		    $embed = sprintf(
		        '<iframe height="290" class="s3bubble-audio-oembed-iframes" src="https://media.s3bubble.com/embed/aplaylist/id/%1$s" frameborder="0" webkitAllowFullScreen="true" mozallowfullscreen="true" allowFullScreen="true"></iframe>',
		        esc_attr( $matches[1] )
		    );
		
		    return apply_filters( 's3bubble_audio_oembed_playlist_embed_handler', $embed, $matches, $attr, $url, $rawattr );
		
		}

		/*
		 * Adds live streaming iframe support
		 * @author s3bubble
		 * @params none
		 */ 
		function s3bubble_video_oembed_live_embed_handler( $matches, $attr, $url, $rawattr )
		{
		    $embed = sprintf(
		        '<iframe height="360" class="s3bubble-video-oembed-iframes" src="https://media.s3bubble.com/embed/live/username/%1$s" frameborder="0" webkitAllowFullScreen="true" mozallowfullscreen="true" allowFullScreen="true"></iframe>',
		        esc_attr( $matches[1] )
		    );
		    
		    return apply_filters( 's3bubble_video_oembed_live_embed_handler', $embed, $matches, $attr, $url, $rawattr );
		
		}

		/*
		* Depreciated
		* @author s3bubble
		* @none
		*/ 
		function register_s3bubble_aws_self_hosted_settings() {
		
			register_setting( 's3bubble-aws-self-hosted-plugin-settings-group', 's3bubble_selfhosted_switch' );
		
		}

    	/*
		* Add css to wordpress admin to run colourpicker
		* @author s3bubble
		* @none
		*/ 
		function s3bubble_oembed_admin_scripts(){
			
			$s3bubble_oembed_connected_website = get_option("s3bubble_oembed_connected_website");
			
			wp_enqueue_style( 's3bubble-oembed-admin-css', plugins_url('/dist/css/admin.min.css', dirname( __FILE__ )), array(), S3BUBBLE_OEMBED_PLUGIN_VERSION );
			
			wp_enqueue_style( 's3bubble-oembed-chosen-css', plugins_url('/dist/css/chosen.min.css', dirname( __FILE__ )), array(), S3BUBBLE_OEMBED_PLUGIN_VERSION );
			
			wp_enqueue_style( 's3bubble-oembed-sweet-css', plugins_url('/dist/css/sweetalert.min.css', dirname( __FILE__ )), array(), S3BUBBLE_OEMBED_PLUGIN_VERSION );

			wp_enqueue_script( 'buttons-github-js', 'https://buttons.github.io/buttons.js', array(),  S3BUBBLE_OEMBED_PLUGIN_VERSION, false );
			
			wp_enqueue_script( 's3bubble-oembed-chosen-js', plugins_url('/dist/js/chosen.jquery.min.js', dirname( __FILE__ ) ), array( 'jquery' ),  S3BUBBLE_OEMBED_PLUGIN_VERSION, true );
			
			wp_enqueue_script( 's3bubble-oembed-sweetalert-js', plugins_url('/dist/js/sweetalert.min.js', dirname( __FILE__ ) ), array( 'jquery' ),  S3BUBBLE_OEMBED_PLUGIN_VERSION, true );
			
			wp_localize_script('s3bubble-oembed-sweetalert-js', 's3bubble_oembed_uid', array(
				's3website' => (!empty($s3bubble_oembed_connected_website) ? $s3bubble_oembed_connected_website : ""),
				's3bubbleSelfHosted' => get_option( 's3bubble_selfhosted_switch' ) ? "true" : "false"
			));

		}
		
		/*
		* Add css ties into wp_head() function
		* @author s3bubble
		* @params none
        */ 
		function s3bubble_oembed_scripts(){
			
			wp_enqueue_style('s3bubble-oembed-css', plugins_url('/dist/css/styles.min.css', dirname( __FILE__ )), array(),  S3BUBBLE_OEMBED_PLUGIN_VERSION );

			wp_enqueue_script( 's3bubble-oembed-js', plugins_url('/dist/js/scripts.min.js', dirname( __FILE__ )), array( 'jquery' ),  S3BUBBLE_OEMBED_PLUGIN_VERSION, true );

			wp_enqueue_style('s3bubble-hosted-cdn', plugins_url('/dist/css/s3bubble.min.css', dirname( __FILE__ )), array(), S3BUBBLE_OEMBED_PLUGIN_VERSION);

        	wp_enqueue_script('s3bubble-hosted-cdn', plugins_url('/dist/js/s3bubble.min.js', dirname( __FILE__ )), array(), S3BUBBLE_OEMBED_PLUGIN_VERSION, true);

        	wp_localize_script( 's3bubble-hosted-cdn', 's3bubble_oembed', array(
				'ajax' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce('ajax-nonce'),
				'options' => ((is_array(get_option( 's3bubble_oembed_plugin_options' ))) ? get_option( 's3bubble_oembed_plugin_options' ) : [])
			));

		}
		 
		/*
		* Adds the menu item to the tiny mce
		* @author s3bubble
		* @none
		*/ 
		function s3bubble_oembed_add_buttons( $plugin_array ) {
		
		    $plugin_array['S3bubbleOembed'] = plugins_url('/dist/js/tinymce.min.js', dirname( __FILE__ ));
		
		    return $plugin_array;
		
		}
		
		/*
		* Registers the amount of buttons
		* @author s3bubble
		* @none
		*/ 
		function s3bubble_oembed_register_buttons( $buttons ) {
		    
		    array_push( $buttons, 's3bubble_oembed_global_shortcode' ); 
		    
		    return $buttons;

		}

       	/*
		 * Set the DRM token
		 * @author s3bubble
		 * @params none
		 */
		function s3bubble_oembed_set_cookie(){

			// AMP HACK 
			if(function_exists('amp_is_request')){
		 
				header("Access-Control-Allow-Origin: *"); 

			}

			if(!empty($_POST['type']) && !empty($_POST['token'])){

				set_transient( 's3bubble_oembed_get_type', $_POST['type'] );

				set_transient( 's3bubble_oembed_get_token', $_POST['token'] );

				wp_send_json(array(
			        'status' => true,
			        'message' => __('Token set', 's3bubble-amazon-web-services-oembed-media-streaming-support'),
			    ));

			}else{

				wp_send_json(array(
			        'status' => false,
			        'message' => __('No token skipping', 's3bubble-amazon-web-services-oembed-media-streaming-support'),
			    ));

			}

		}

		/*
		 * Get the DRM token
		 * @author s3bubble
		 * @params none
		 */
       	function s3bubble_oembed_proxy_token(){

       		header("Access-Control-Allow-Origin: *");

			$header = getallheaders();    

       		$response = wp_remote_post( 'https://s3bubbleapi.com/proxy/token', array(
       			//'sslverify' => FALSE,
       			'timeout'     => 10,
			    'headers' => array(
			        'Authorization' => base64_encode(get_transient('s3bubble_oembed_get_token'))
			    )
			));

			if ( is_wp_error( $response ) ) {

			    echo $response->get_error_message();

			} else { 

			   	if(isset($response['body'])){

			   		if(get_transient('s3bubble_oembed_get_type') == 'm' || isset($header['Cast-Device-Capabilities'])){

			   			echo $response['body'];

			   		}else{

			   			echo $this->s3bubble_oembed_crypto_aes_encrypt(bin2hex($response['body']));

			   		}

			   	}else{

			   		echo "Authorization failed:";

			   	}

			} 

			die(); // !IMPORTANT

       }

       	/**
		 *  DRM crypto token
		 *
		 * @return bool
		 * @author  @s3bubble
		 */
		function s3bubble_oembed_crypto_aes_encrypt($plain_text){

			$passphrase = uniqid();

		    $salt = openssl_random_pseudo_bytes(16);

		    $iv = openssl_random_pseudo_bytes(16);

		    $iterations = 999;  
		    
		    $key = hash_pbkdf2("sha512", $passphrase, $salt, $iterations, 64);

		    $encrypted_data = openssl_encrypt($plain_text, 'aes-256-cbc', hex2bin($key), OPENSSL_RAW_DATA, $iv);

		    $data = array(
		    	"passphrase" => $passphrase, 
		    	"ciphertext" => base64_encode($encrypted_data), 
		    	"iv" => bin2hex($iv), 
		    	"salt" => bin2hex($salt)
		    );

		    return base64_encode(json_encode($data));

		}

    }

	/*
	* Initiate the class
	* @author s3bubble
	* @none
	*/ 
	$s3bubble_oembed = new s3bubble_oembed();
	
}