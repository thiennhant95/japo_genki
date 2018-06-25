<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Linp_Post_List {

	protected $textdomain = SCP_TEXT_DOMAIN;
	protected $namespace = 'linp_post_list';

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

		global $_wp_additional_image_sizes;
		$thumbnail_sizes = array();
		foreach ( $_wp_additional_image_sizes as $name => $settings ) {
			$thumbnail_sizes[ $name . ' (' . $settings['width'] . 'x' . $settings['height'] . ')' ] = $name;
		}

		$args       = array(
			'orderby' => 'name',
			'parent'  => 0
		);
		$categories = get_categories( $args );
		$cats       = array( 'All' => '' );
		foreach ( $categories as $category ) {

			$cats[ $category->name ] = $category->term_id;
		}


		/*
		Add your Visual Composer logic here.
		Lets call vc_map function to "register" our custom shortcode within Visual Composer interface.

		More info: http://kb.wpbakery.com/index.php?title=Vc_map
		*/
		vc_map( array(
			'name'        => __( 'Post List', $this->textdomain ),
			'description' => __( '', $this->textdomain ),
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
					'type'       => 'dropdown',
					'holder'     => '',
					'class'      => '',
					'heading'    => __( 'Category', $this->textdomain ),
					'param_name' => 'category',
					'value'      => $cats,
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Number of Posts', $this->textdomain ),
					'param_name' => 'number_of_posts',
					'value'      => '2',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Post Date Format', $this->textdomain ),
					'param_name' => 'post_date_format',
					'value'      => 'F d, Y',
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Post description word length', $this->textdomain ),
					'param_name'  => 'description_word_length',
					'description' => __( 'Enter number only.', $this->textdomain ),
					'value'       => '15'
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Link Text', $this->textdomain ),
					'param_name'  => 'link_text',
					'value'       => 'Read More',
					'description' => __( 'If you do not wish to display Read More link just leave this field blank.', $this->textdomain ),
				),
//				array(
//					'type'        => 'textfield',
//					'heading'     => __( 'Category Link Text', $this->textdomain ),
//					'param_name'  => 'cat_link_text',
//					'value'       => 'View All',
//					'description' => __( 'If you do not wish to display the Category Link just leave this field blank.', $this->textdomain ),
//				),
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
					'heading'    => __( 'Number of Columns', $this->textdomain ),
					'param_name' => 'number_of_columns',
					'value'      => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
					),
				),
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => __( 'Show Author?', $this->textdomain ),
					'param_name' => 'show_author',
					'value'      => array(
						'Yes' => '1',
						'No'  => '0',
					),
				),
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => __( 'Show Comment Count?', $this->textdomain ),
					'param_name' => 'show_comment_count',
					'value'      => array(
						'Yes' => '1',
						'No'  => '0',
					),
				),
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => __( 'Thumbnail Dimensions', $this->textdomain ),
					'param_name' => 'thumbnail_dimensions',
					'value'      => $thumbnail_sizes,
				),
				array(
					'type'       => 'colorpicker',
					'class'      => '',
					'heading'    => __( 'Meta Data Color', $this->textdomain ),
					'param_name' => 'meta_data_color',
					'value'      => '',
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
			'category'                => null,
			'number_of_posts'         => 2,
			'link_text'               => 'Read More',
			'cat_link_text'           => '',
			'layout'                  => 'layout_1',
			'number_of_columns'       => 1,
			'description_word_length' => '15',
			'thumbnail_dimensions'    => 'thumbnail',
			'post_date_format'        => 'F d, Y',
			'show_comment_count'      => '1',
			'show_author'             => '1',
			'el_class'                => '',
		), $atts ) );

		// $content = wpb_js_remove_wpautop($content); // fix unclosed/unwanted paragraph tags in $content

		$args = array(
			'numberposts'      => $number_of_posts,
			'category'         => $category,
			'orderby'          => 'post_date',
			'order'            => 'DESC',
			'suppress_filters' => false,
		);

		$posts = get_posts( $args );

		// If no posts let's bail
		if ( ! $posts ) {
			return;
		}

		$grid = array(
			'one whole',
			'one half',
			'one third',
			'one fourth',
			'one fifth',
			'one sixth',
		);

		$grid_class = $grid[ (int) $number_of_columns - 1 ];

		ob_start();

		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'linp-post-list ' . $el_class, $this->namespace, $atts );

		?>
		<div class="<?php echo $css_class; ?> <?php echo $layout; ?>">
			<?php foreach ( array_chunk( $posts, $number_of_columns ) as $chunk ): ?>
				<div class="vc_row">
					<?php foreach ( $chunk as $post ): ?>
						<?php if ($layout == 'layout_2' || $layout == 'layout_3') : ?>
							<?php include "templates/{$layout}.php"; ?>
						<?php else: ?>
							<?php include 'templates/layout_1.php'; ?>
						<?php endif; ?>

					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>
			<?php if ( $cat_link_text ): ?>
				<?php $category_link = get_category_link( $category ); ?>
				<a class="cbp_widget_link cbp_widget_button"
				   href="<?php echo esc_url( $category_link ); ?>"><?php echo $cat_link_text; ?></a>
			<?php endif; ?>
		</div>

		<?php
		$content = ob_get_clean();

		return $content;
	}

	/*
	Load plugin css and javascript files which you may need on front end of your site
	*/
	public function loadCssAndJs() {
//		wp_register_style( 'linp-post-list', plugins_url( 'assets/post-list.css', __FILE__ ) );
//		wp_enqueue_style( 'linp-post-list' );

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
new Linp_Post_List();
