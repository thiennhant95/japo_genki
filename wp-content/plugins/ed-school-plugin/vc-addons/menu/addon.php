<?php
/*
Plugin Name: Extend Visual Composer Plugin Example
Plugin URI: http://wpbakery.com/vc
Description: Extend Visual Composer with your own set of shortcodes.
Version: 0.1.1
Author: WPBakery
Author URI: http://wpbakery.com
License: GPLv2 or later
*/

/*
This example/starter plugin can be used to speed up Visual Composer plugins creation process.
More information can be found here: http://kb.wpbakery.com/index.php?title=Category:Visual_Composer
*/

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class SCP_Menu {

	protected $shortcode_name = 'scp_menu';
	protected $title = 'Menu';
	protected $description = 'Choose a menu';
	protected $textdomain = 'ed-school';

	function __construct() {
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

		/*
		Add your Visual Composer logic here.
		Lets call vc_map function to "register" our custom shortcode within Visual Composer interface.

		More info: http://kb.wpbakery.com/index.php?title=Vc_map
		*/
		vc_map( array(
			"name"        => __( $this->title, $this->textdomain ),
			"description" => __( $this->description, $this->textdomain ),
			"base"        => $this->shortcode_name,
			"class"       => "",
			"controls"    => "full",
			"icon"        => plugins_url( 'assets/aislin-vc-icon.png', __FILE__ ),
			// or css class name which you can reffer in your css file later. Example: "vc_extend_my_class"
			"category"    => __( 'Aislin', 'js_composer' ),
			//'admin_enqueue_js' => array(plugins_url('assets/vc_extend.js', __FILE__)), // This will load js file in the VC backend editor
			//'admin_enqueue_css' => array(plugins_url('assets/vc_extend_admin.css', __FILE__)), // This will load css file in the VC backend editor
			"params"      => array(
				array(
					'type'        => 'textfield',
					'holder'      => '',
					'class'       => '',
					'heading'     => __( 'Depth', $this->textdomain ),
					'param_name'  => 'depth',
					'value'       => __( '3', $this->textdomain ),
					'description' => __( 'Depth of the menu.', $this->textdomain )
				),
				array(
					'type'        => 'dropdown',
					'holder'      => '',
					'class'       => '',
					'heading'     => __( 'Type', $this->textdomain ),
					'param_name'  => 'menu_type',
					'admin_label' => true,
					'value'       => array(
						'Custom Menu'        => 'menu_custom',
						'Main Menu'          => 'menu_main',
						'Top Menu'           => 'menu_top',
						'Mobile Menu'        => 'menu_mobile',
						'Quick Sidebar Menu' => 'menu_quick_sidebar',
					),
					'description' => __( 'Select menu type.', $this->textdomain )
				),
				array(
					'type'        => 'dropdown',
					'holder'      => '',
					'class'       => '',
					'heading'     => __( 'Main Menu Orientation', $this->textdomain ),
					'param_name'  => 'menu_orientation',
					'value'       => array(
						'Horizontal' => 'horizontal',
						'Vertical'   => 'vertical',
					),
					'dependency'  => array(
						'element' => 'menu_type',
						'value'   => 'menu_main',
					),
				),
				array(
					'type'        => 'dropdown',
					'holder'      => '',
					'class'       => '',
					'heading'     => __( 'Menu', $this->textdomain ),
					'param_name'  => 'menu',
//					'admin_label' => true,
					'value'       => array_flip( get_registered_nav_menus() ),
					'dependency'  => array(
						'element' => 'menu_type',
						'value'   => 'menu_custom',
					),
				),
				array(
					'type'        => 'dropdown',
					'holder'      => '',
					'class'       => '',
					'heading'     => __( 'Container', $this->textdomain ),
					'param_name'  => 'container',
					'value'       => array(
						'div'   => 'div',
						'nav'   => 'nav',
						'false' => 'false',
					),
					'description' => __( 'Container element.', $this->textdomain ),
					'dependency'  => array(
						'element' => 'menu_type',
						'value'   => 'menu_custom',
					),
				),
				array(
					'type'       => 'textfield',
					'holder'     => '',
					'class'      => '',
					'heading'    => __( 'Container Class', $this->textdomain ),
					'param_name' => 'container_class',
					'value'      => '',
					'dependency' => array(
						'element' => 'menu_type',
						'value'   => 'menu_custom',
					),
				),
				array(
					'type'       => 'textfield',
					'holder'     => '',
					'class'      => '',
					'heading'    => __( 'Container ID', $this->textdomain ),
					'param_name' => 'container_id',
					'value'      => '',
					'dependency' => array(
						'element' => 'menu_type',
						'value'   => 'menu_custom',
					),
				),
				array(
					'type'       => 'textfield',
					'holder'     => '',
					'class'      => '',
					'heading'    => __( 'Menu Class', $this->textdomain ),
					'param_name' => 'menu_class',
					'value'      => __( 'sf-menu', $this->textdomain ),
					'dependency' => array(
						'element' => 'menu_type',
						'value'   => 'menu_custom',
					),
				),
				array(
					'type'        => 'dropdown',
					'holder'      => '',
					'class'       => '',
					'heading'     => __( 'Position', $this->textdomain ),
					'param_name'  => 'position',
					'value'       => array(
						'Left'   => 'vc_pull-left',
						'Right'  => 'vc_pull-right',
						'Center' => 'wh-menu-center',
					),
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
			'depth'            => 3,
			'menu'             => 'primary_navigation',
			'menu_type'        => 'menu_custom',
			'menu_orientation' => 'horizontal',
			'container'        => 'div',
			'container_class'  => '',
			'container_id'     => '',
			'menu_class'       => 'sf-menu',
			'position'         => 'vc_pull-left',
			'css'              => ''
		), $atts ) );

		if ( $menu_orientation == 'vertical' ) {
			$menu_class = $menu_class . ' wh-menu-vertical';
		}

		$args = array(
			'theme_location' => $menu,
			'menu_class'     => $menu_class,
			'depth'          => $depth,
			'container'      => $container != 'false' ? $container : false,
			'container_id'   => $container_id,
			'fallback_cb'    => false
		);


		if ( $menu_type == 'menu_main' ) {
			$args['theme_location'] = 'primary_navigation';
			$args['menu_class']     = 'sf-menu wh-menu-main';
			$args['container']      = 'div';
			$args['container_id']   = 'cbp-menu-main';

			$container_class = 'cbp-container';

		} elseif ( $menu_type == 'menu_top' ) {
			$args['theme_location'] = 'secondary_navigation';
			$args['menu_class']     = 'sf-menu wh-menu-top';
			$args['container']      = 'div';

		} elseif ( $menu_type == 'menu_mobile' ) {

			$args['theme_location'] = 'primary_navigation';

			if ( has_nav_menu( 'mobile_navigation' ) ) {
				$args['theme_location'] = 'mobile_navigation';
			}

			$args['menu_class']     = 'respmenu';
			if ( class_exists( 'Ed_School_Mobile_Menu_Walker' ) ) {
				$args['walker'] = new Ed_School_Mobile_Menu_Walker();
			}
			ob_start();
			include 'templates/menu-mobile.php';

			return ob_get_clean();

		} elseif ( $menu_type == 'menu_quick_sidebar' ) {
			$args['theme_location'] = 'quick_sidebar_navigation';
			$args['menu_class']     = 'sf-menu wh-menu-vertical';
			$args['container']      = 'div';
			$position = '';

		}

		global $post_id;
		if (
			($menu_type == 'menu_custom' || $menu_type == 'menu_main')
			&& $menu == 'primary_navigation'
		) {
			if ( function_exists( 'rwmb_meta' ) && (int) rwmb_meta( 'ed_school_use_custom_menu', array(), $post_id ) ) {
				$custom_menu_location = rwmb_meta( 'ed_school_custom_menu_location', array(), $post_id );
				if ( ! empty( $custom_menu_location ) ) {
					$args['theme_location'] = $custom_menu_location;
				}
			}

		}

		$container_class = $container_class . ' ' . $position;
		$container_class .= vc_shortcode_custom_css_class( $css, ' ' );
		$args['container_class'] = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $container_class, $this->shortcode_name, $atts );

		ob_start();

		wp_nav_menu( $args );

		$content = ob_get_clean();

		return $content;
	}

	/*
	Load plugin css and javascript files which you may need on front end of your site
	*/
	public function loadCssAndJs() {
//		wp_register_style( 'vc_extend_style', plugins_url( 'assets/vc_extend.css', __FILE__ ) );
//		wp_enqueue_style( 'vc_extend_style' );

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
new SCP_Menu();
