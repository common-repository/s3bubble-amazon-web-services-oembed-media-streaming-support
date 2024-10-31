<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
* Check if the old S3Bubble plugin is installed
* @author s3bubble
* @none
*/
function s3bubble_oembed_plugins_loaded() {

    if(function_exists('is_plugin_active')){

        if( is_plugin_active( 'drm-protected-video-streaming/drm-protected-video-streaming.php' ) ) {

            function s3bubble_oembed_admin_notice(){
                echo '<div class="notice notice-error s3bubble-oembed-double-plugin-notice">
                    <p>' . __('VERY IMPORTANT! You have another S3Bubble plugin installed please remove it, this plugin will not work with this plugin.', 's3bubble-amazon-web-services-oembed-media-streaming-support') . '</p>
                </div>';
            }

            add_action('admin_notices', 's3bubble_oembed_admin_notice');

        }

    }
    

}

add_action( 'admin_init', 's3bubble_oembed_plugins_loaded' );

/* 
* Adds a admin page for the Media plugin
* @author s3bubble
* @none
*/ 
function s3bubble_oembed_menu_page(){
    
    add_menu_page( 'S3Bubble', 'S3Bubble', 'manage_options', 's3bubble-oembed-admin', 's3bubble_oembed_dashboard_page', 'dashicons-video-alt3', 10 );

}

/*
* Output the player page to the admin
* @author s3bubble
* @none
*/ 
function s3bubble_oembed_dashboard_page(){ 

    global $wpdb;

    ?>

    <div class="wrap s3bubble-oembed-admin-wrapper">
     
        <h2><?php _e( 'S3Bubble Media Streaming', 's3bubble-amazon-web-services-oembed-media-streaming-support' ); ?></h2>

        <?php settings_errors(); ?>
         
        <?php
            
            if( isset( $_GET[ 'tab' ] ) ) {
            
                $active_tab = $_GET[ 'tab' ];
            
            }else{

            	$active_tab = 's3bubble_oembed_display_tutorial';

            }

        ?>
         
        <h2 class="nav-tab-wrapper">

            <a href="?page=s3bubble-oembed-admin&tab=s3bubble_oembed_display_tutorial" class="nav-tab <?php echo $active_tab == 's3bubble_oembed_display_tutorial' ? 'nav-tab-active' : ''; ?>">Tutorial</a>

            <a href="?page=s3bubble-oembed-admin&tab=s3bubble_oembed_display_options" class="nav-tab <?php echo $active_tab == 's3bubble_oembed_display_options' ? 'nav-tab-active' : ''; ?>">Plugin Options</a>

        </h2>

        <?php if( $active_tab == 's3bubble_oembed_display_tutorial' ) { ?>

        	<div class="s3bubble-oembed-tab-wrapper">
	        
		        <div class="s3bubble-oembed-aspect">
										
					<iframe src="https://www.youtube.com/embed/tLLhq0kg2ME" frameborder="0" allowfullscreen></iframe>

				</div> 

				<pre><?php echo admin_url( 'admin-ajax.php' ); ?>?action=s3bubble_proxy</pre>

			</div>

		<?php }else{ ?>
	        
	        <div class="s3bubble-oembed-tab-wrapper">

		        <form method="post" action="options.php">
	            	
	            	<?php settings_fields( 's3bubble_oembed_plugin_options' ); ?>
	    			
	    			<?php do_settings_sections( 's3bubble_oembed_plugin_options' ); ?>        
	            	
	            	<?php submit_button(); ?>
	        	
	        	</form>

        	</div>

        <?php } ?>
	            
    </div>

    <?php

}

add_action( 'admin_menu', 's3bubble_oembed_menu_page' );

/*
* Setup plugin options
* @author s3bubble
* @none
*/ 
function s3bubble_oembed_initialize_plugin_options() {
 
    if( false == get_option( 's3bubble_oembed_plugin_options' ) ) {  
        
        add_option( 's3bubble_oembed_plugin_options' );

    } 

    add_settings_section(
        's3bubble_oembed_general_settings_section',         
        'Plugin Options',                 
        's3bubble_oembed_general_options_callback',
        's3bubble_oembed_plugin_options'    
    );

    add_settings_field( 
        'use_iframes',                     
        'Use Iframe Useful For AMP Setups',                         
        's3bubble_oembed_form_field_use_iframes_callback',    
        's3bubble_oembed_plugin_options', 
        's3bubble_oembed_general_settings_section'          
    );

    register_setting(
        's3bubble_oembed_plugin_options',
        's3bubble_oembed_plugin_options'
    );
     
} 

add_action( 'admin_init', 's3bubble_oembed_initialize_plugin_options' );
 
/*
* Setup plugin options header
* @author s3bubble
* @none
*/ 
function s3bubble_oembed_general_options_callback() {
    
    echo '<p>Here you can change some plugin options.</p>';

}

/*
* Use iframes
* @author s3bubble
* @none
*/ 
function s3bubble_oembed_form_field_use_iframes_callback() {
     
    $options = get_option( 's3bubble_oembed_plugin_options' );
     
    $checked = ' ';
    if( isset( $options['use_iframes'] ) ) {
        $checked = " checked='checked' ";
    }
    
    echo '<input type="checkbox" id="use_iframes" name="s3bubble_oembed_plugin_options[use_iframes]" ' . $checked . ' /><small>' . __('EXPERIMENTAL: Check this if you would like to use iframes instead of html this can be useful for AMP setups', 's3bubble-amazon-web-services-oembed-media-streaming-support') . '</small>';
     
}