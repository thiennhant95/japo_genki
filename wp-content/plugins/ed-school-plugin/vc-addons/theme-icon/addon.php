<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Scp_Theme_Icon {

	protected $name = 'Theme Icon';
	protected $namespace = 'scp_theme_icon';
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
			'name'             => esc_html( $this->name, $this->textdomain ),
			'description'      => '',
			'base'             => $this->namespace,
			'class'            => '',
			'controls'         => 'full',
			'icon'             => plugins_url( 'assets/aislin-vc-icon.png', __FILE__ ),
			// or css class name which you can reffer in your css file later. Example: 'vc_extend_my_class'
			'category'         => __( 'Aislin', $this->textdomain ),
			'admin_enqueue_js' => array( plugins_url( 'assets/admin-theme-icon.js', __FILE__ ) ),
			// This will load js file in the VC backend editor
			//'admin_enqueue_css' => array(plugins_url('assets/vc_extend_admin.css', __FILE__)), // This will load css file in the VC backend editor
			'params'           => array(
				array(
					'type'        => 'iconpicker',
					'param_name'  => 'theme_icon',
					'heading'     => __( 'Icon', $this->textdomain ),
					'value'       => '', // default value to backend editor admin_label
					'class'       => 'scp-theme-icon-name',
					'holder'      => 'div',
					'settings'    => array(
						'emptyIcon'    => false,
						'type'         => 'theme-icons',
						// default true, display an "EMPTY" icon?
						'iconsPerPage' => 4000,
						// default 100, how many icons per/page to display, we use (big number) to display all icons in single page
					),
					'description' => __( 'Select icon from library.', $this->textdomain ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Font Size', $this->textdomain ),
					'param_name'  => 'icon_font_size',
					'description' => __( 'Value in px. Enter number only.', $this->textdomain ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Position Absolute?', $this->textdomain ),
					'param_name' => 'position_absolute',
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Icon alignment', 'js_composer' ),
					'param_name'  => 'alignment',
					'value'       => array(
						__( 'Left', 'js_composer' )   => 'left',
						__( 'Right', 'js_composer' )  => 'right',
						__( 'Center', 'js_composer' ) => 'center',
					),
					'description' => __( 'Select alignment.', 'js_composer' ),
				),
				array(
					'type'        => 'vc_link',
					'heading'     => __( 'URL (Link)', 'js_composer' ),
					'param_name'  => 'link',
					'description' => __( 'Add link to icon.', 'js_composer' ),
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Icon Color', $this->textdomain ),
					'param_name'  => 'color',
					'description' => __( 'If color is not set, theme accent color will be used.', $this->textdomain ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Use Theme Accent Color for Hover', $this->textdomain ),
					'param_name' => 'hover_accent_color',
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Icon Hover Color', $this->textdomain ),
					'param_name'  => 'hover_color',
					'description' => __( 'Will not be used if Use Accent Color is checked.', $this->textdomain ),
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
			)
		) );
	}


	/*
	Shortcode logic how it should be rendered
	*/
	public function render( $atts, $content = null ) {

		extract( shortcode_atts( array(
			'theme_icon'         => 'Text on the button',
			'icon_font_size'     => '',
			'position_absolute'  => '',
			'link'               => '',
			'alignment'          => 'left',
			'color'              => '',
			'hover_color'        => '',
			'hover_accent_color' => '',
			'css'                => '',
			'el_class'           => '',
		), $atts ) );
		// $content = wpb_js_remove_wpautop($content); // fix unclosed/unwanted paragraph tags in $content

		$class_to_filter = 'wh-theme-icon';
		$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' );
		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter . ' ' . $el_class, $this->namespace, $atts );

		$link     = vc_build_link( $link );
		$a_href   = $link['url'];
		$a_title  = $link['title'];
		$a_target = $link['target'];

		if ( $hover_accent_color == 'true' && function_exists( 'ed_school_get_option' ) ) {
			$theme_accent_color = ed_school_get_option( 'global-accent-color' );
			if ( $theme_accent_color ) {
				$hover_color = $theme_accent_color;
			}
		}

		$icon_style = '';

		if ( $icon_font_size ) {
			$icon_style .= 'font-size:' . (int) $icon_font_size . 'px;';
		}

		if ( $position_absolute == 'true' ) {
			$icon_style .= 'position:absolute;';
		}

		$hover = '';

		if ( $hover_color && $color ) {

			$hover = 'onMouseOver="this.style.color=\'' . $hover_color . '\'"';
			$hover .= ' onMouseOut="this.style.color=\'' . $color . '\'"';

		}

		if ( $color ) {
			$icon_style .= 'color:' . $color . '!important;';
		}

		if ( $alignment ) {
			if ( $alignment != 'left' ) {
				$icon_style .= 'text-align:' . $alignment . ';';
			}
		}

		if ( $icon_style ) {
			$icon_style = 'style="' . $icon_style . '"';
		}

		ob_start();
		?>

		<?php if ( $a_href ) : ?>
			<a
				href="<?php echo esc_attr( $a_href ); ?>"
				class="<?php echo esc_attr( trim( $css_class ) ); ?>"
				<?php if ( $a_title ) : ?>
					title="<?php echo esc_attr( $a_title ); ?>"
				<?php endif; ?>
				<?php if ( $a_target ) : ?>
					target="<?php echo esc_attr( $a_target ); ?>"
				<?php endif; ?>
				<?php echo $icon_style; ?>
				><i class="<?php echo $theme_icon; ?>" <?php echo $hover; ?>></i></a>
		<?php else: ?>
			<div class="<?php echo esc_attr( $css_class ); ?>" <?php echo $icon_style; ?>>
				<i class="<?php echo $theme_icon; ?>" <?php echo $hover; ?>></i>
			</div>
		<?php endif; ?>

		<?php
		$content = ob_get_clean();

		return $content;
	}

	/*
	Load plugin css and javascript files which you may need on front end of your site
	*/
	public function loadCssAndJs() {
		wp_register_style( 'ed-school-theme-icons', get_template_directory_uri() . '/assets/css/theme-icons.css', false );

		wp_enqueue_style( 'ed-school-theme-icons' );
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
new Scp_Theme_Icon();
