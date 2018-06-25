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

class SCP_Schedule {

	protected $shortcode_name = 'scp_schedule';
	protected $title = 'Schedule';
	protected $description = 'Schedule';
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
					'type'        => 'textarea',
					'heading'     => __( 'Schedule', $this->textdomain ),
					'param_name'  => 'schedule',
					'description' => __( 'Pipe separated list of items. %% is row separator. Example: item 1 | time 1 %% item 2 | time 2', $this->textdomain ),
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

		wp_enqueue_script( 'jribbble' );

		extract( shortcode_atts( array(
			'schedule' => '',
			'css'      => '',
			'el_class' => '',
		), $atts ) );

		if ( ! $schedule ) {
			return;
		}

		$rows = explode( '%%', $schedule );

		$class_to_filter = 'schedule';
		$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' );
		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter . ' ' . $el_class, $this->shortcode_name, $atts );


		ob_start();
		?>
		<ul class="<?php echo $css_class; ?>">

			<?php foreach ( $rows as $key => $row ) : ?>
				<?php
				$parts = explode( '|', $row );
				?>
				<?php if ( count( $parts ) == 2 ) : ?>
					<li class="<?php echo $key % 2 ? 'even' : 'odd'; ?>">
						<span class="left">
							<?php echo trim($parts[0]); ?>
						</span>
						<span class="right">
							<?php echo trim($parts[1]); ?>
						</span>
					</li>
				<?php endif; ?>

			<?php endforeach; ?>
		</ul>


		<?php
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
//		wp_register_script( 'vc_extend_js', plugins_url( 'assets/vc_extend_js.min.js', __FILE__ ), array( 'jquery' ) );
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
new SCP_Schedule();
