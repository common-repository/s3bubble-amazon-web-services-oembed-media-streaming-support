<?php

/*
 * Plugin Name: S3Bubble Media Streaming
 * Plugin URI: https://s3bubble.com
 * Description: Create Stunning 4k, UHD, FHD Videos Streamed From The Most Powerful Streaming Service On The Planet Amazon Web Services (AWS). Also with Youtube & Vimeo Player Support
 * Version: 8.0
 * Author: S3Bubble
 * Author URI: https://s3bubble.com
 * Author Email: support@s3bubble.com
 * Requires at least: 3.8
 * Tested up to: 5.7.2
 * Text Domain: s3bubble-amazon-web-services-oembed-media-streaming-support
 * Domain Path: languages
 * License: GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
*/ 
 
// Exit if accessed directly. 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} 

/**
 * Globals
 */
define( 'S3BUBBLE_OEMBED_PLUGIN_VERSION', '211' );
define( 'S3BUBBLE_OEMBED_PLUGIN_URL', plugins_url('', __FILE__) ); 
 
/**
 * Initializer.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/helpers.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/menu.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/amp.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/s3bubble-oembed.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/elementor.php';