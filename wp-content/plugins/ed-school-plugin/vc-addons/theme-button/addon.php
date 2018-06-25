<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Scp_Theme_Button {

	protected $name = 'Theme Button';
	protected $namespace = 'scp_theme_button';
	protected $textdomain = SCP_TEXT_DOMAIN;

	function __construct() {
		// We safely integrate with VC with this hook
		add_action( 'init', array( $this, 'integrateWithVC' ) );

		// Use this when creating a shortcode addon
		add_shortcode( $this->namespace, array( $this, 'render' ) );

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


		/*
		Add your Visual Composer logic here.
		Lets call vc_map function to "register" our custom shortcode within Visual Composer interface.

		More info: http://kb.wpbakery.com/index.php?title=Vc_map
		*/
		vc_map( array(
			'name'        => esc_html( $this->name, $this->textdomain ),
			'description' => '',
			'base'        => $this->namespace,
			'class'       => '',
			'controls'    => 'full',
			'icon'        => plugins_url( 'assets/aislin-vc-icon.png', __FILE__ ),
			// or css class name which you can reffer in your css file later. Example: 'vc_extend_my_class'
			'category'    => __( 'Aislin', $this->textdomain ),
			//'admin_enqueue_js' => array(plugins_url('assets/vc_extend.js', __FILE__)), // This will load js file in the VC backend editor
			//'admin_enqueue_css' => array(plugins_url('assets/vc_extend_admin.css', __FILE__)), // This will load css file in the VC backend editor
			'params'      => array(
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Text', 'js_composer' ),
					'param_name' => 'title',
					'holder'     => 'div',
					// fully compatible to btn1 and btn2
					'value'      => __( 'Text on the button', 'js_composer' ),
				),
				array(
					'type'        => 'vc_link',
					'heading'     => __( 'URL (Link)', 'js_composer' ),
					'param_name'  => 'link',
					'description' => __( 'Add link to button.', 'js_composer' ),
				),
				array(
					'type'        => 'dropdown',
					'holder'      => '',
					'class'       => '',
					'heading'     => __( 'Style', $this->textdomain ),
					'param_name'  => 'style',
					'value'       => array(
						'Default'      => 'wh-button',
						'Alt Button'   => 'wh-alt-button',
						'Alt Button 2' => 'wh-alt-button-2',
					),
					'description' => __( 'Theme Button Styling form Theme Options/Other Settings.', $this->textdomain )
				),
				array(
					'type'       => 'dropdown',
					'holder'     => '',
					'class'      => '',
					'heading'    => __( 'Align', $this->textdomain ),
					'param_name' => 'align',
					'value'      => array(
						'left'   => 'left',
						'center' => 'center',
						'right'  => 'right',
					),
					'group'      => __( 'Overrides', $this->textdomain ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Bg Color', $this->textdomain ),
					'param_name'       => 'custom_background_color',
					'edit_field_class' => 'vc_col-sm-3',
					'group'            => __( 'Overrides', $this->textdomain ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Text Color', $this->textdomain ),
					'param_name'       => 'text_color',
					'edit_field_class' => 'vc_col-sm-3',
					'group'      => __( 'Overrides', $this->textdomain ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Hover Bg Color', $this->textdomain ),
					'param_name'       => 'bg_color_hover',
					'edit_field_class' => 'vc_col-sm-3',
					'group'      => __( 'Overrides', $this->textdomain ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Hover Text Color', $this->textdomain ),
					'param_name'       => 'text_color_hover',
					'edit_field_class' => 'vc_col-sm-3',
					'group'            => __( 'Overrides', $this->textdomain ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Font Size', $this->textdomain ),
					'param_name'  => 'font_size',
					// fully compatible to btn1 and btn2
					'value'       => '',
					'description' => __( 'Override font size.', $this->textdomain ),
					'group'       => __( 'Overrides', $this->textdomain ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Width', $this->textdomain ),
					'param_name'  => 'width',
					// fully compatible to btn1 and btn2
					'value'       => '',
					'description' => __( 'Override button width.', $this->textdomain ),
					'group'       => __( 'Overrides', $this->textdomain ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Padding Top', $this->textdomain ),
					'param_name'  => 'padding_top',
					// fully compatible to btn1 and btn2
					'value'       => '',
					'description' => __( 'Override padding top.', $this->textdomain ),
					'group'       => __( 'Overrides', $this->textdomain ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Padding Left', $this->textdomain ),
					'param_name'  => 'padding_left',
					// fully compatible to btn1 and btn2
					'value'       => '',
					'description' => __( 'Override padding left.', $this->textdomain ),
					'group'       => __( 'Overrides', $this->textdomain ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Padding Bottom', $this->textdomain ),
					'param_name'  => 'padding_bottom',
					// fully compatible to btn1 and btn2
					'value'       => '',
					'description' => __( 'Override padding bottom.', $this->textdomain ),
					'group'       => __( 'Overrides', $this->textdomain ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Padding Right', $this->textdomain ),
					'param_name'  => 'padding_right',
					// fully compatible to btn1 and btn2
					'value'       => '',
					'description' => __( 'Override padding right.', $this->textdomain ),
					'group'       => __( 'Overrides', $this->textdomain ),
				),
				array(
					'type'       => 'css_editor',
					'heading'    => __( 'CSS box', 'js_composer' ),
					'param_name' => 'css',
					'group'      => __( 'Design Options', 'js_composer' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Extra class name', $this->textdomain ),
					'param_name'  => 'el_class',
					'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', $this->textdomain ),
				),
			)
		) );
	}

	/*
	Shortcode logic how it should be rendered
	*/
	public function render( $atts, $content = null ) {

		extract( shortcode_atts( array(
			'title'                   => 'Text on the button',
			'align'                   => '',
			'custom_background_color' => '', // had to be named like this because of the confilct with VC
			'bg_color_hover'          => '',
			'text_color'              => '',
			'text_color_hover'        => '',
			'style'                   => 'wh-button',
			'width'                   => '',
			'font_size'               => '',
			'padding_top'             => '',
			'padding_right'           => '',
			'padding_bottom'          => '',
			'padding_left'            => '',
			'link'                    => '',
			'css'                     => '',
			'el_class'                => '',
		), $atts ) );
		// $content = wpb_js_remove_wpautop($content); // fix unclosed/unwanted paragraph tags in $content


		$class_to_filter = $style;
		$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' );
		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter . ' ' . $el_class, $this->namespace, $atts );

		$link     = vc_build_link( $link );
		$a_href   = $link['url'];
		$a_title  = $link['title'];
		$a_target = $link['target'];

		$anim = '';
		if ( strstr( $el_class, 'hoverable' ) ) {
			$anim = '<span class="anim"></span>';
		}

		$uid = uniqid( 'theme-button-' );
		$style = '';
		$style_hover = '';

		/**
		 * Custom BG Color
		 */
		if ( $custom_background_color ) {
			$style .= 'background-color:' . $custom_background_color . ';';
		}
		if ( $text_color ) {
			$style .= 'color:' . $text_color . ';';
		}

		if ( $bg_color_hover ) {
			$style_hover .= 'background-color:' . $bg_color_hover . ';';
		}
		if ( $text_color_hover ) {
			$style_hover .= 'color:' . $text_color_hover . ';';
		}


		if ( $font_size ) {
			$style .= 'font-size:' . scp_sanitize_size( $font_size ) . ';';
		}

		if ( $align ) {
			if ($align == 'left') {
				$style .= 'float:left;';
			} elseif ($align == 'right') {
				$style .= 'float:right;';
			} elseif ($align == 'center') {
				if ($a_href) {
					$style .= 'display:inline-block;';
				} else {
					$style .= 'display:block;margin:0 auto;';
				}
			}
		}
		if ( $width ) {
			$style .= 'width:' . scp_sanitize_size( $width ) . ';';
		}
		if ( $padding_top ) {
			$style .= 'padding-top:' . scp_sanitize_size( $padding_top ) . ';';
		}
		if ( $padding_right ) {
			$style .= 'padding-right:' . scp_sanitize_size( $padding_right ) . ';';
		}
		if ( $padding_bottom ) {
			$style .= 'padding-bottom:' . scp_sanitize_size( $padding_bottom ) . ';';
		}
		if ( $padding_left ) {
			$style .= 'padding-left:' . scp_sanitize_size( $padding_left ) . ';';
		}

		// if ( $style ) {
		// 	$style = 'style="' . $style . '"';
		// }


		if ($a_href) {
			$html = '<a';
			$html .= ' id="' . esc_attr( $uid ) . '"';
			$html .= ' href="' . esc_attr( $a_href ) . '"';
			$html .= ' class="' . esc_attr( trim( $css_class ) ) . '"';
			if ($a_title) {
				$html .= ' title="' . esc_attr( $a_title ) . '"';
			}
			if ($a_target) {
				$html .= ' target="' . esc_attr( $a_target ) . '"';
			}
			// $html .= ' ' . $style;
			$html .= '>';
			$html .= $anim;
			$html .= $title;
			$html .= '</a>';

			if ($align && $align == 'center') {
				$html = '<div style="width:100%;text-align:center">' . $html . '</div>';
			}

		} else {
			$html = '<button';
			$html .= ' id="' . esc_attr( $uid ) . '"';
			$html .= ' class="' . esc_attr( trim( $css_class ) ) . '"';
			// $html .= ' ' . $style;
			$html .= '>';
			$html .= $anim;
			$html .= $title;
			$html .= '</button>';
		}

		if ($style) {

		$final_style = '';
			$final_style .= "#$uid{";
			$final_style .= $style;
			$final_style .= '}';

			if ($style_hover) {
				$final_style .= "#$uid:hover{";
				$final_style .= $style_hover;
				$final_style .= '}';
			}

			$html .= '<script>ed_school_plugin.data.styles.push(' . json_encode($final_style) . ');</script>';
		}

		return $html;
	}

	/*
	Load plugin css and javascript files which you may need on front end of your site
	*/
	public function loadCssAndJs() {

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
new Scp_Theme_Button();
