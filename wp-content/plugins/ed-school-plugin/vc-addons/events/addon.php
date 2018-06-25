<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Scp_Events {

	protected $textdomain = SCP_TEXT_DOMAIN;
	protected $namespace = 'scp_events';

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
			'name'        => __( 'Tribe Events', $this->textdomain ),
			'description' => __( '', $this->textdomain ),
			'base'        => $this->namespace,
			'class'       => '',
			'controls'    => 'full',
			'icon'        => plugins_url( 'assets/aislin-vc-icon.png', __FILE__ ),
			// or css class name which you can reffer in your css file later. Example: 'vc_extend_my_class'
			'category'    => __( 'Aislin', 'js_composer' ),
			//'admin_enqueue_js' => array(plugins_url('assets/vc_extend.js', __FILE__)), // This will load js file in the VC backend editor
			//'admin_enqueue_css' => array(plugins_url('assets/vc_extend_admin.css', __FILE__)), // This will load css file in the VC backend editor
			'params'      => array(
				array(
					'type'        => 'textfield',
					'holder'      => '',
					'class'       => '',
					'heading'     => __( 'Widget Title', $this->textdomain ),
					'param_name'  => 'title',
					'admin_label' => true,
				),
				array(
					'type'        => 'textfield',
					'holder'      => '',
					'class'       => '',
					'heading'     => __( 'Start Date Format', $this->textdomain ),
					'param_name'  => 'start_date_format',
					'admin_label' => true,
					'value'       => 'M d, Y',
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Nubmer of events to display', $this->textdomain ),
					'param_name'  => 'limit',
					'description' => __( 'Enter number only.', $this->textdomain ),
				),
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => __( 'Layout', $this->textdomain ),
					'param_name' => 'layout',
					'value'      => array(
						'Layout 1' => 'layout_1',
						'Layout 2' => 'layout_2',
						'Layout 3' => 'layout_3',
					),
				),
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => __( 'Show Description', $this->textdomain ),
					'param_name' => 'show_description',
					'value'      => array(
						'No'  => '0',
						'Yes' => '1',
					),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Event description word length', $this->textdomain ),
					'param_name'  => 'description_word_length',
					'description' => __( 'Enter number only.', $this->textdomain ),
					'dependency'  => Array( 'element' => 'show_description', 'value' => array( '1' ) ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'View All Events Link Text', $this->textdomain ),
					'param_name'  => 'view_all_events_link_text',
					'description' => __( 'If Left Blank link will not show.', $this->textdomain ),
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

		$main_heading_style_inline = $sub_heading_style_inline = $date_style_inline = $date_heading_style_inline = $outer_circle_style = $inner_circle_style = $info_style_inline = '';

		extract( shortcode_atts( array(
			'title'                     => '',
			'limit'                     => '3',
			'layout'                    => 'layout_1',
			'description_word_length'   => '20',
			'start_date_format'         => '',
			'show_description'          => '0',
			'view_all_events_link_text' => '',
			'el_class'                  => '',
		), $atts ) );

		// $content = wpb_js_remove_wpautop($content); // fix unclosed/unwanted paragraph tags in $content

		ob_start();

		// Temporarily unset the tribe bar params so they don't apply
		$hold_tribe_bar_args = array();
		foreach ( $_REQUEST as $key => $value ) {
			if ( $value && strpos( $key, 'tribe-bar-' ) === 0 ) {
				$hold_tribe_bar_args[ $key ] = $value;
				unset( $_REQUEST[ $key ] );
			}
		}

		if ( ! function_exists( 'tribe_get_events' ) ) {
			return;
		}

		$posts = tribe_get_events( apply_filters( 'tribe_events_list_widget_query_args', array(
			'eventDisplay'   => 'list',
			'posts_per_page' => $limit
		) ) );

		// If no posts let's bail
		if ( ! $posts ) {
			return;
		}

		
		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'scp-tribe-events-wrap ' . $el_class, $this->namespace, $atts );

		//Check if any posts were found
		if ( $posts ) {
			?>
			<div class="<?php echo esc_attr( $css_class ); ?> <?php echo $layout; ?>">
				<?php if ( $title ) : ?>
					<h3 class="widget-title">
						<i class="icon-Calendar-New"></i> <?php echo esc_html( $title ); ?>
					</h3>
				<?php endif; ?>
				<ul class="scp-tribe-events">
					<?php
					foreach ( $posts as $post ) :
						setup_postdata( $post );
						?>
						<?php if ($layout == 'layout_2' || $layout == 'layout_3') : ?>
							<?php // they use the same template, only have diff style ?>
							<?php include "templates/layout_2.php"; ?>
						<?php else: ?>
							<?php include 'templates/layout_1.php'; ?>
						<?php endif; ?>
						
						
					<?php
					endforeach;
					?>
				</ul>
				<?php if ( ! empty( $view_all_events_link_text ) ) : ?>
					<p class="scp-tribe-events-link">
						<a href="<?php echo tribe_get_events_link(); ?>"
						   rel="bookmark"><?php echo $view_all_events_link_text; ?></a>
					</p>
				<?php endif; ?>
			</div>
			<?php
			//No Events were Found
		} else {
			?>
			<p><?php _e( 'There are no upcoming events at this time.', $this->textdomain ); ?></p>
		<?php
		}

		wp_reset_query();
		$content = ob_get_clean();

		return $content;
	}

	/*
	Load plugin css and javascript files which you may need on front end of your site
	*/
	public function loadCssAndJs() {
//		wp_register_style( 'vc_addon_events', plugins_url( 'assets/main.css', __FILE__ ) );
//		wp_enqueue_style( 'vc_addon_events' );

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

new Scp_Events();