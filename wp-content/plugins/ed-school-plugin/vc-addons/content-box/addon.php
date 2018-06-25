<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Scp_Content_Box {

	protected $name = 'Content Box';
	protected $namespace = 'scp_content_box';
	protected $textdomain = SCP_TEXT_DOMAIN;

	function __construct() {
		// We safely integrate with VC with this hook
		add_action( 'init', array( $this, 'integrateWithVC' ) );

		// Use this when creating a shortcode addon
		add_shortcode( $this->namespace, array( $this, 'render' ) );

		// Register CSS and JS
		add_action( 'wp_enqueue_scripts', array( $this, 'loadCssAndJs' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'loadCssAndJs' ) );

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
//			'is_container'     => true,
			'js_view'     => 'VcColumnView',
			'as_parent'   => array( 'except' => $this->namespace ),
			'icon'        => plugins_url( 'assets/aislin-vc-icon.png', __FILE__ ),
			// or css class name which you can reffer in your css file later. Example: 'vc_extend_my_class'
			'category'    => __( 'Aislin', $this->textdomain ),
//			'admin_enqueue_js' => array( plugins_url( 'assets/admin-theme-icon.js', __FILE__ ) ),
			// This will load js file in the VC backend editor
			//'admin_enqueue_css' => array(plugins_url('assets/vc_extend_admin.css', __FILE__)), // This will load css file in the VC backend editor
			'params'      => array(
				array(
					'type'        => 'vc_link',
					'heading'     => __( 'URL (Link)', 'js_composer' ),
					'param_name'  => 'link',
					'description' => __( 'Add link to icon.', 'js_composer' ),
				),
				array(
					'type'       => 'dropdown',
					'param_name' => 'use_overlay',
					'heading'    => __( 'Use Overlay', $this->textdomain ),
					'value'      => array(
						'No'  => 'no',
						'Yes' => 'yes'
					),
				),
				array(
					'type'       => 'textfield',
					'param_name' => 'overlay_title',
					'heading'    => __( 'Overlay Title', $this->textdomain ),
					'dependency'  => array( 'element' => 'use_overlay', 'value' => 'yes' ),
				),
				array(
					'type'        => 'textfield',
					'param_name'  => 'el_class',
					'heading'     => __( 'Extra class name', $this->textdomain ),
					'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', $this->textdomain ),
				),
				array(
					'type'       => 'css_editor',
					'heading'    => __( 'CSS box', 'js_composer' ),
					'param_name' => 'css',
					'group'      => __( 'Design Options', 'js_composer' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Bg Color', $this->textdomain ),
					'param_name' => 'custom_background_color',
					'group'      => __( 'Design Options', 'js_composer' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Hover Bg Color', $this->textdomain ),
					'param_name' => 'hover_bg_color',
					'group'      => __( 'Design Options', 'js_composer' ),
				),
				array(
					'type'       => 'textfield',
					'param_name' => 'box_shadow_top',
					'heading'    => __( 'Top', $this->textdomain ),
					'group'      => __( 'Box Shadow', 'js_composer' ),
				),
				array(
					'type'       => 'textfield',
					'param_name' => 'box_shadow_left',
					'heading'    => __( 'Left', $this->textdomain ),
					'group'      => __( 'Box Shadow', 'js_composer' ),
				),
				array(
					'type'       => 'textfield',
					'param_name' => 'box_shadow_spread',
					'heading'    => __( 'Spread', $this->textdomain ),
					'group'      => __( 'Box Shadow', 'js_composer' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Box Shadow Color', $this->textdomain ),
					'param_name' => 'box_shadow_color',
					'group'      => __( 'Box Shadow', 'js_composer' ),
				),
				array(
					'type'       => 'textfield',
					'param_name' => 'box_shadow_top_hover',
					'heading'    => __( 'Top Hover', $this->textdomain ),
					'group'      => __( 'Box Shadow', 'js_composer' ),
				),
				array(
					'type'       => 'textfield',
					'param_name' => 'box_shadow_left_hover',
					'heading'    => __( 'Left Hover', $this->textdomain ),
					'group'      => __( 'Box Shadow', 'js_composer' ),
				),
				array(
					'type'       => 'textfield',
					'param_name' => 'box_shadow_spread_hover',
					'heading'    => __( 'Spread Hover', $this->textdomain ),
					'group'      => __( 'Box Shadow', 'js_composer' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Box Shadow Color Hover', $this->textdomain ),
					'param_name' => 'box_shadow_color_hover',
					'group'      => __( 'Box Shadow', 'js_composer' ),
				),
			)
		) );
	}


	/*
	Shortcode logic how it should be rendered
	*/
	public function render( $atts, $content = null ) {

		extract( shortcode_atts( array(
			'link'                    => '',
			'use_overlay'             => 'no',
			'overlay_title'           => '',
			'custom_background_color' => '', // bg_color name is vc default
			'hover_bg_color'          => '',
			'box_shadow_color'        => '',
			'box_shadow_top'          => '',
			'box_shadow_left'         => '',
			'box_shadow_spread'       => '',
			'box_shadow_color_hover'  => '',
			'box_shadow_top_hover'    => '',
			'box_shadow_left_hover'   => '',
			'box_shadow_spread_hover' => '',
			'css'                     => '',
			'el_class'                => '',
		), $atts ) );
		// $content = wpb_js_remove_wpautop($content); // fix unclosed/unwanted paragraph tags in $content

		$class_to_filter = 'wh-content-box';
		$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' );
		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter . ' ' . $el_class, $this->namespace, $atts );

		$link     = vc_build_link( $link );
		$a_href   = $link['url'];
		$a_title  = $link['title'];
		$a_target = $link['target'];

		$icon_style = '';

		/**
		 * Custom BG Color
		 */
		if ( $custom_background_color ) {
			$icon_style .= 'background-color:' . $custom_background_color . ';';
		}

		/**
		 * Box Shadow
		 */
		$box_shadow = '';
		if ( $box_shadow_color ) {
			$box_shadow_top    = $box_shadow_top ? (int) $box_shadow_top . 'px' : '0px';
			$box_shadow_left   = $box_shadow_left ? (int) $box_shadow_left . 'px' : '0px';
			$box_shadow_spread = $box_shadow_spread ? (int) $box_shadow_spread . 'px' : '5px';
			$box_shadow        = $box_shadow_top . ' ' . $box_shadow_left . ' ' . $box_shadow_spread . ' ' . $box_shadow_color;

			$icon_style .= 'box-shadow:' . $box_shadow . ';';
		}

		/**
		 * Box Shadow Hover
		 */
		$box_shadow_hover = '';
		if ( $box_shadow_color_hover ) {
			$box_shadow_top_hover    = $box_shadow_top_hover ? (int) $box_shadow_top_hover . 'px' : '0px';
			$box_shadow_left_hover   = $box_shadow_left_hover ? (int) $box_shadow_left_hover . 'px' : '0px';
			$box_shadow_spread_hover = $box_shadow_spread_hover ? (int) $box_shadow_spread_hover . 'px' : '5px';
			$box_shadow_hover        = $box_shadow_top_hover . ' ' . $box_shadow_left_hover . ' ' . $box_shadow_spread_hover . ' ' . $box_shadow_color_hover;
		}


		if ( $icon_style ) {
			$icon_style = 'style="' . $icon_style . '"';
		}


		/**
		 * BG Hover
		 */
		$hover = 'onMouseOver="this.style.backgroundColor=\'' . $hover_bg_color . '\'"';
		$hover .= ' onMouseOut="this.style.backgroundColor=\'' . $custom_background_color . '\'"';

		$uid = uniqid( 'content-box-' );

		ob_start();

		?>

		<div id="<?php echo $uid; ?>"
		     class="<?php echo esc_attr( $css_class ); ?>" <?php echo $icon_style; ?> <?php echo $hover; ?>>
			<?php if ($use_overlay == 'yes') : ?>
				<div class="overlay"><?php echo $overlay_title; ?></div>
			<?php endif; ?>
			<?php if ( $a_href ) : ?>
				<a class="wh-content-box-link"
				   href="<?php echo esc_attr( $a_href ); ?>"
					<?php if ( $a_title ) : ?>
						title="<?php echo esc_attr( $a_title ); ?>"
					<?php endif; ?>
					<?php if ( $a_target ) : ?>
						target="<?php echo esc_attr( $a_target ); ?>"
					<?php endif; ?>
					></a>
			<?php endif; ?>

			<?php echo do_shortcode( $content ); ?>
		</div>
		<?php if ( $box_shadow_hover ) : ?>
			<script>
				(function ($) {
					$('#<?php echo $uid; ?>').hover(function () {
						$(this).css({
							boxShadow: '<?php echo $box_shadow_hover; ?>'
						});
					}, function () {
						$(this).css({
							boxShadow: '<?php echo $box_shadow; ?>'
						});
					});
				})(jQuery);
			</script>
		<?php endif; ?>

		<?php
		$content = ob_get_clean();

		return $content;
	}

	/*
	Load plugin css and javascript files which you may need on front end of your site
	*/
	public function loadCssAndJs() {
//		wp_register_style( 'school-time-icons', get_template_directory_uri() . '/assets/css/school-time-icons.css', false );

//		wp_enqueue_style( 'school-time-icons' );
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
new Scp_Content_Box();

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	class WPBakeryShortCode_scp_content_box extends WPBakeryShortCodesContainer {
	}
}
