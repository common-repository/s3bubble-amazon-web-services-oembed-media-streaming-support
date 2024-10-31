<?php
namespace ElementorS3BubbleOembed\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Hello World
 *
 * Elementor widget for hello world.
 *
 * @since 1.0.0
 */
class S3bubble_Oembed extends Widget_Base {

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		wp_register_script( 's3bubble-oembed-widget-scripts', plugins_url( '/assets/admin.js', dirname( __FILE__ ) ), ['jquery'], '1.0', true );

		wp_register_style('s3bubble-oembed-widget-css', plugins_url( '/assets/admin.css', dirname( __FILE__ ) ), [], '1.0' );

	}


	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 's3bubble-media';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'S3Bubble', 's3bubble-amazon-web-services-oembed-media-streaming-support' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'fas fa-video';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'general' ];
	}

	/**
	 * Retrieve the list of scripts the widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return [ 'jquery', 's3bubble-oembed-widget-scripts' ];
	}

	/**
	 * Retrieve the list of styles the widget depended on.
	 *
	 * Used to set styles dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget styles dependencies.
	 */
	public function get_style_depends() {
		return [ 'jquery', 's3bubble-oembed-widget-css' ];
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'S3Bubble', 's3bubble-amazon-web-services-oembed-media-streaming-support' ),
			]
		);

		$this->add_control(
			'code',
			[
				'label' => __( 'Player Code', 's3bubble-amazon-web-services-oembed-media-streaming-support' ),
				'type' => Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'poster',
			[
				'label' => __( 'Video Poster', 's3bubble-amazon-web-services-oembed-media-streaming-support' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				]
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$poster = '';

		if(!empty($settings['poster']['id'])){
			
			$poster_url = wp_get_attachment_image_url( $settings['poster']['id'], 's3bubble_oembed_poster_image' );
			
			$poster = ' data-poster="' . $poster_url . '"';

		}

		echo '<div class="s3bubble" data-code="' . $settings['code'] . '"' . $poster . '></div>';

	}

	/**
	 * Render the widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function _content_template() {

		?>
		
		<div class="s3bubble-oembed-preview">
		  	<div class="s3bubble-oembed-preview-overlay">
		    	<div class="s3bubble-oembed-preview-content">
		      		<h3><?php _e('Preview', 's3bubble-amazon-web-services-oembed-media-streaming-support'); ?></h3>
		      		<p><?php _e('You video will show here it will not display in preview mode.', 's3bubble-amazon-web-services-oembed-media-streaming-support'); ?></p>
		      		<p><?php _e('Open your post or page in another browser tab to view your video.', 's3bubble-amazon-web-services-oembed-media-streaming-support'); ?></p>
		    	</div>
		  	</div>
		</div>
		
		<?php

	}
}