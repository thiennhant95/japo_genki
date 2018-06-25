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

class SCP_Quick_Sidebar_Trigger {

	protected $shortcode_name = 'scp_quick_sidebar_trigger';
	protected $title = 'Quick Sidebar Trigger';
	protected $description = 'A trigger button for Quick Sidebar';
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


		$layout_blocks       = get_posts( array( 'post_type' => 'layout_block' ) );
		$layout_blocks_array = array();
		foreach ( $layout_blocks as $layout_block ) {
			$layout_blocks_array[ $layout_block->post_title ] = $layout_block->ID;
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
					'type'        => 'dropdown',
					'holder'      => '',
					'class'       => '',
					'heading'     => __( 'Position', $this->textdomain ),
					'param_name'  => 'position',
					'value'       => array(
						'Left'   => 'vc_pull-left',
						'Right'  => 'vc_pull-right',
						'Center' => 'vc_txt_align_center',
					),
					'description' => __( 'Float.', $this->textdomain )
				),
				array(
					'type'        => 'dropdown',
					'holder'      => '',
					'class'       => '',
					'heading'     => __( 'Quick Sidebar Position', $this->textdomain ),
					'param_name'  => 'layout_block_position',
					'value'       => array(
						'Left'  => 'left',
						'Right' => 'right',
					),
					'description' => __( 'Float.', $this->textdomain ),
					'group'       => __( 'Quick Sidebar', $this->textdomain ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Layout Block Width', $this->textdomain ),
					'param_name'  => 'layout_block_width',
					'description' => __( 'Value in px. Enter number only.', $this->textdomain ),
					'group'       => __( 'Quick Sidebar', $this->textdomain ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Bg Color', $this->textdomain ),
					'param_name' => 'layout_block_background_color',
					'group'      => __( 'Quick Sidebar', $this->textdomain ),
				),
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

			)
		) );
	}

	/*
	Shortcode logic how it should be rendered
	*/
	public function render( $atts, $content = null ) {

		extract( shortcode_atts( array(
			'position'                      => 'right',
			'layout_block_width'            => '350',
			'layout_block_position'         => 'right',
			'layout_block_background_color' => '',
			'css'                           => '',
			'el_class'                      => '',
		), $atts ) );

		$class_to_filter = 'wh-quick-sidebar-toggler-wrapper ' . $position;
		$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' );
		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter . ' ' . $el_class, $this->shortcode_name, $atts );


		if ( function_exists( 'ed_school_get_layout_block' ) ) {
			$layout_block = ed_school_get_layout_block( 'quick-sidebar-layout-block' );
		}
		if ( ! $layout_block ) {
			return;
		}
//		$layout_block = get_post( $layout_block );



		$quick_sidebar_style = '';

		/**
		 * Width
		 */
		if ( $layout_block_width ) {
			$layout_block_width = (int) $layout_block_width;
			$quick_sidebar_style .= "width:{$layout_block_width}px;";

			if ( $layout_block_position == 'left' ) {

				$quick_sidebar_style .= "left:-{$layout_block_width}px;";
			} else {
				$quick_sidebar_style .= "right:-{$layout_block_width}px;";

			}
		}

		/**
		 * Background Color
		 */
		if ( $layout_block_background_color ) {
			$quick_sidebar_style .= "background-color:{$layout_block_background_color};";
		}

		if ( $quick_sidebar_style ) {
			$quick_sidebar_style = "style=\"{$quick_sidebar_style}\"";
		}

		ob_start();
		?>

		<div class="<?php echo $css_class; ?>">
			<a href="#" class="wh-quick-sidebar-toggler">
				<i class="icon-menu"></i>
			</a>
		</div>
		<?php if ( $layout_block->post_content ) : ?>
			<div class="wh-quick-sidebar" <?php echo $quick_sidebar_style; ?>>
				<span class="wh-close"><i class="icon-close-1"></i></span>
				<?php echo do_shortcode( $layout_block->post_content ); ?>
			</div>
			<script>
				var wheels = wheels || {};
				wheels.data = wheels.data || {};
				wheels.data.quickSidebar = {
					position: <?php echo json_encode($layout_block_position); ?>
				};

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
new SCP_Quick_Sidebar_Trigger();
