<?php

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// if plugin activates before VC
if ( ! class_exists( 'WPBakeryShortCode' ) ) {
	class WPBakeryShortCode {
	}
}

class ST_Video_Popup extends WPBakeryShortCode {
	protected $shortcode_name = 'st_video_popup';
	protected $title = 'Video Popup';
	protected $description = 'Video Popup';
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
		if ( is_array( $_wp_additional_image_sizes ) ) {
			foreach ( $_wp_additional_image_sizes as $name => $settings ) {
				$thumbnail_sizes[ $name . ' (' . $settings['width'] . 'x' . $settings['height'] . ')' ] = $name;
			}
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
					'type'        => 'textfield',
					'heading'     => __( 'Widget title', 'js_composer' ),
					'param_name'  => 'title',
					'description' => __( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
				),
				array(
					'type'        => 'textfield',
					'class'       => '',
					'heading'     => __( 'Video Url', $this->textdomain ),
					'param_name'  => 'video_url',
					'value'       => '',
					'description' => __( 'Add Youtube/Vimeo video url', $this->textdomain ),
				),
				array(
					'type'        => 'textfield',
					'class'       => '',
					'heading'     => __( 'Video Width', $this->textdomain ),
					'param_name'  => 'video_width',
					'value'       => '',
					'description' => __( 'Value in px. Enter number only.', $this->textdomain ),
				),
				array(
					'type'        => 'textfield',
					'class'       => '',
					'heading'     => __( 'Video Height', $this->textdomain ),
					'param_name'  => 'video_height',
					'value'       => '',
					'description' => __( 'Value in px. Enter number only.', $this->textdomain ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Play Badge Color', $this->textdomain ),
					'param_name' => 'play_badge_color',
					'value'      => '#fff',
				),
				array(
					'type'        => 'attach_image',
					'heading'     => __( 'Thumbnail', $this->textdomain ),
					'param_name'  => 'image',
					'value'       => '',
					'description' => __( 'Select image from media library.', $this->textdomain ),
					'dependency'  => array(
						'value' => 'media_library',
					),
				),
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => __( 'Image Size', $this->textdomain ),
					'param_name' => 'img_size',
					'value'      => $thumbnail_sizes,
				),
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
			'title'            => '',
			'video_url'        => '',
			'video_width'      => '',
			'video_height'     => '',
			'play_badge_color' => '#fff',
			'image'            => '',
			'img_size'         => 'full',
			'alignment'        => 'left',
			'css_animation'    => '',
			'css'              => '',
			'el_class'         => '',
		), $atts ) );
		// $content = wpb_js_remove_wpautop($content, true); // fix unclosed/unwanted paragraph tags in $content

		$default_src = vc_asset_url( 'vc/no_image.png' );

		$img_id = preg_replace( '/[^\d]/', '', $image );


		$img = wpb_getImageBySize( array(
			'attach_id'  => $img_id,
			'thumb_size' => $img_size,
			'class'      => 'vc_single_image-img',
		) );

		if ( ! $img ) {
			$img['thumbnail'] = '<img class="vc_img-placeholder vc_single_image-img" src="' . $default_src . '" alt="video-popup-image"/>';
		}

		wp_enqueue_script( 'prettyphoto' );
		wp_enqueue_style( 'prettyphoto' );

		$a_attrs['class'] = 'prettyphoto';
//		$a_attrs['rel']   = 'prettyPhoto[rel-' . get_the_ID() . '-' . rand() . ']';

		if ( $video_url ) {
			$video_width  = (int) $video_width;
			$video_height = (int) $video_height;
			if ( $video_width && $video_height ) {
				$video_url .= '&width=' . $video_width;
				$video_url .= '&height=' . $video_height;
			}

			$link = $video_url;
			// stripping alt tag so it don't show above the video
//			$img['thumbnail'] = preg_replace( '/alt="([^"]+)"/', '', $img['thumbnail'] );
		} else {
			$link = wp_get_attachment_image_src( $img_id, 'large' );
			$link = $link[0];
		}

		$wrapperClass = 'vc_single_image-wrapper';

		if ( $link ) {
			$a_attrs['href'] = $link;
//			$a_attrs['target'] = $img_link_target;
			if ( ! empty( $a_attrs['class'] ) ) {
				$wrapperClass .= ' ' . $a_attrs['class'];
				unset( $a_attrs['class'] );
			}

			$play_icon = '<div class="box" style="border-color:' . $play_badge_color . '">
                      <div class="tri" style="border-left-color:' . $play_badge_color . '"></div>
                    </div>';

			$html = '<a ' . vc_stringify_attributes( $a_attrs ) . ' class="' . $wrapperClass . '">' . $img['thumbnail'] . $play_icon . '</a>';
		} else {
			$html = '<div class="' . $wrapperClass . '">' . $img['thumbnail'] . '</div>';
		}

		$class_to_filter = 'wpb_single_image st-video-popup wpb_content_element vc_align_' . $alignment . ' ' . $this->getCSSAnimation( $css_animation );
		$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class );
		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

		$output = '
      	<div class="' . esc_attr( trim( $css_class ) ) . '">
      		' . wpb_widget_title( array( 'title' => $title, 'extraclass' => 'wpb_singleimage_heading' ) ) . '
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
		wp_register_style( 'st-video-popup', plugins_url( 'assets/video-popup.css', __FILE__ ) );
		wp_enqueue_style( 'st-video-popup' );

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
new ST_Video_Popup();
