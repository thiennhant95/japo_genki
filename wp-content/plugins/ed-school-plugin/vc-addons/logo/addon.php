<?php

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class ST_Logo extends WPBakeryShortCode {
	protected $shortcode_name = 'st_logo';
	protected $title = 'Logo';
	protected $description = 'Uses logo image set in Theme Options';
	protected $textdomain = 'vc_extend';

	public function __construct() {
		// We safely integrate with VC with this hook
		add_action( 'init', array( $this, 'integrateWithVC' ) );

		// Use this when creating a shortcode addon
		add_shortcode( $this->shortcode_name, array( $this, 'render' ) );

		// Register CSS and JS
//		add_action( 'wp_enqueue_scripts', array( $this, 'loadCssAndJs' ) );
	}

	public function integrateWithVC() {
		// Check if Visual Composer is installed
		if ( ! defined( 'WPB_VC_VERSION' ) ) {
			// Display notice that Visual Compser is required
			add_action( 'admin_notices', array( $this, 'showVcVersionNotice' ) );

			return;
		}

		global $_wp_additional_image_sizes;
		$thumbnail_sizes         = array();
		$thumbnail_sizes['Full'] = 'full';
		foreach ( $_wp_additional_image_sizes as $name => $settings ) {
			$thumbnail_sizes[ $name . ' (' . $settings['width'] . 'x' . $settings['height'] . ')' ] = $name;
		}

		/*
		Add your Visual Composer logic here.
		Lets call vc_map function to "register" our custom shortcode within Visual Composer interface.

		More info: http://kb.wpbakery.com/index.php?title=Vc_map
		*/
		vc_map( array(
			'name'        => __( $this->title, $this->textdomain ),
			'description' => __( $this->description, $this->textdomain ),
			'base'        => $this->shortcode_name,
			'class'       => '',
			'controls'    => 'full',
			'icon'        => plugins_url( 'assets/aislin-vc-icon.png', __FILE__ ),
			// or css class name which you can reffer in your css file later. Example: "vc_extend_my_class"
			'category'    => __( 'Aislin', 'js_composer' ),
			//'admin_enqueue_js' => array(plugins_url('assets/vc_extend.js', __FILE__)), // This will load js file in the VC backend editor
			//'admin_enqueue_css' => array(plugins_url('assets/vc_extend_admin.css', __FILE__)), // This will load css file in the VC backend editor
			'params'      => array(
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Image alignment', 'js_composer' ),
					'param_name'  => 'alignment',
					'value'       => array(
						__( 'Left', 'js_composer' )   => 'left',
						__( 'Right', 'js_composer' )  => 'right',
						__( 'Center', 'js_composer' ) => 'center',
					),
					'description' => __( 'Select image alignment.', 'js_composer' ),
				),
				vc_map_add_css_animation(),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Extra class name', 'js_composer' ),
					'param_name'  => 'el_class',
					'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
				),
				array(
					'type'       => 'css_editor',
					'heading'    => __( 'CSS box', 'js_composer' ),
					'param_name' => 'css',
					'group'      => __( 'Design Options', 'js_composer' ),
				),
			),
		) );
	}

	/*
	Shortcode logic how it should be rendered
	*/
	public function render( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'alignment'      => 'left',
			'css'            => '',
			'el_class'       => '',
		), $atts ) );
		// $content = wpb_js_remove_wpautop($content, true); // fix unclosed/unwanted paragraph tags in $content

		if ( function_exists( 'ed_school_get_logo_url' ) ) {
			$logo_url = ed_school_get_logo_url();
		}

		if ( ! $logo_url ) {
			$logo_url = vc_asset_url( 'vc/no_image.png' );
		}


		$logo_width = '';
		if ( function_exists( 'ed_school_get_option' ) ) {
			$logo_width_settings = ed_school_get_option( 'logo-width-exact', '' );
			if ( $logo_width_settings && isset( $logo_width_settings['width'] ) && (int) $logo_width_settings['width'] ) {
				$logo_width = 'style="width:' . $logo_width_settings['width'] . '"';
			}
		}

		$html = '<img class="vc_img-placeholder vc_single_image-img" src="' . $logo_url . '" alt="logo"/>';

		$html = '<a href="' . esc_url( home_url( '/' ) ) . '">' . $html . '</a>';


		$class_to_filter = 'wpb_single_image wpb_content_element vc_align_' . $alignment;
		$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class );
		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );


		$output = '
          	<div ' . $logo_width . ' class="' . esc_attr( trim( $css_class ) ) . '">
          		<figure class="wpb_wrapper vc_figure">
          			' . $html . '
          		</figure>
          	</div>
          ';

		return $output;

	}

	/*
	Load plugin css and javascript files which you may need on front end of your site
	*/
	public function loadCssAndJs() {
		wp_register_style( 'vc_extend_style', plugins_url( 'assets/vc_extend.css', __FILE__ ) );
		wp_enqueue_style( 'vc_extend_style' );

		// If you need any javascript files on front end, here is how you can load them.
		//wp_enqueue_script( 'vc_extend_js', plugins_url('assets/vc_extend.js', __FILE__), array('jquery') );
	}

	/*
	Show notice if your plugin is activated but Visual Composer is not
	*/
	public function showVcVersionNotice() {
		$plugin_data = get_plugin_data( __FILE__ );
		echo '
        <div class="updated">
          <p>' . sprintf( __( '<strong>%s</strong> requires <strong><a href="http://bit.ly/vcomposer" target="_blank">Visual Composer</a></strong> plugin to be installed and activated on your site.', $this->textdomain ), $plugin_data['Name'] ) . '</p>
        </div>';
	}
}

// Finally initialize code
new ST_Logo();
