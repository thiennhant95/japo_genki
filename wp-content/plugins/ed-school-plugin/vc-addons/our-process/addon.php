<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class SCP_Our_Process {

	protected $textdomain = SCP_TEXT_DOMAIN;
	protected $namespace = 'scp_our_process';
	protected $namespace_item = 'scp_our_process_item';


	function __construct() {
		// We safely integrate with VC with this hook
		add_action( 'init', array( $this, 'integrateWithVC' ) );

		// Use this when creating a shortcode addon
		add_shortcode( $this->namespace, array( $this, 'render_process' ) );
		add_shortcode( $this->namespace_item, array( $this, 'render_process_item' ) );

		// Register CSS and JS
		add_action( 'wp_enqueue_scripts', array( $this, 'loadCssAndJs' ) );
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
			'name'                    => __( 'Our Process', $this->textdomain ),
			'description'             => __( '', $this->textdomain ),
			'base'                    => $this->namespace,
			'class'                   => '',
			'controls'                => 'full',
			"as_parent"               => array( 'only' => 'scp_our_process_item' ),
			'icon'                    => plugins_url( 'assets/aislin-vc-icon.png', __FILE__ ),
			// or css class name which you can reffer in your css file later. Example: 'vc_extend_my_class'
			'category'                => __( 'Aislin', 'js_composer' ),
			'js_view'                 => 'VcColumnView',
			'content_element'         => true,
			'show_settings_on_create' => true,
			//'admin_enqueue_js' => array(plugins_url('assets/vc_extend.js', __FILE__)), // This will load js file in the VC backend editor
			'admin_enqueue_css'       => array( plugins_url( 'assets/css/admin.css', __FILE__ ) ), // This will load css file in the VC backend editor
			'params'                  => array(
				array(
					'type'       => 'textfield',
					'holder'     => 'h4',
					'class'      => 'our-process-container-title',
					'heading'    => __( 'Admin Title', $this->textdomain ),
					'param_name' => 'title',
					'value'      => 'Our Process Container',
				),
				array(
					'type'        => 'attach_image',
					'class'       => '',
					'heading'     => __( 'Background Image', $this->textdomain ),
					'param_name'  => 'bg_image',
					'value'       => '',
					'description' => __( 'Upload background image.', $this->textdomain )
				),
				array(
					'type'        => 'textfield',
					'class'       => '',
					'heading'     => __( 'Breakpoint', $this->textdomain ),
					'description' => __( 'Under this width boxes will be streched to 100%. (Value in px - enter nubmer only)', $this->textdomain ),
					'param_name'  => 'breakpoint',
					'value'       => '480',
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Extra class name', $this->textdomain ),
					'param_name'  => 'el_class',
					'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', $this->textdomain ),
				),
			),
		) );

		vc_map( array(
			'name'            => __( 'Our Process Item', $this->textdomain ),
			'description'     => '',
			'base'            => $this->namespace_item,
			'class'           => '',
			'controls'        => 'full',
			'as_child'        => array( 'only' => 'scp_our_process' ),
			'icon'            => plugins_url( 'assets/aislin-vc-icon.png', __FILE__ ),
			// or css class name which you can reffer in your css file later. Example: 'vc_extend_my_class'
			'category'        => __( 'Aislin', 'js_composer' ),
			'content_element' => true,
			//'admin_enqueue_js' => array(plugins_url('assets/vc_extend.js', __FILE__)), // This will load js file in the VC backend editor
			//'admin_enqueue_css' => array(plugins_url('assets/vc_extend_admin.css', __FILE__)), // This will load css file in the VC backend editor
			'params'          => array(
				array(
					'type'        => 'textfield',
					'holder'      => '',
					'class'       => '',
					'heading'     => __( 'Title', $this->textdomain ),
					'param_name'  => 'title',
					'admin_label' => true,
					'value'       => '',
					'description' => __( 'Widget title.', $this->textdomain ),
				),
				array(
					'type'       => 'dropdown',
					'holder'     => '',
					'class'      => '',
					'heading'    => __( 'Title Tag', $this->textdomain ),
					'param_name' => 'title_tag',
					'value'      => array(
						'h1' => 'h1',
						'h2' => 'h2',
						'h3' => 'h3',
						'h4' => 'h4',
						'h5' => 'h5',
						'h6' => 'h6',
					),
				),
				array(
					'type'        => 'textfield',
					'holder'      => '',
					'class'       => '',
					'heading'     => __( 'Width', $this->textdomain ),
					'param_name'  => 'width',
					'admin_label' => true,
					'value'       => __( '30%', $this->textdomain ),
					'description' => __( 'Enter with for the item. Percent recommended', $this->textdomain ),
				),
				array(
					'type'       => 'colorpicker',
					'class'      => '',
					'heading'    => __( 'Title Color', $this->textdomain ),
					'param_name' => 'title_color',
					'value'      => '',
					'group'      => 'Style',
				),
				array(
					'type'       => 'colorpicker',
					'class'      => '',
					'heading'    => __( 'Icon Color', $this->textdomain ),
					'param_name' => 'icon_color',
					'value'      => '',
					'group'      => 'Style',
				),
				array(
					'type'       => 'colorpicker',
					'class'      => '',
					'heading'    => __( 'Bar Color', $this->textdomain ),
					'param_name' => 'bar_color',
					'value'      => '',
					'group'      => 'Style',
				),
				array(
					'type'       => 'colorpicker',
					'class'      => '',
					'heading'    => __( 'Text Color', $this->textdomain ),
					'param_name' => 'text_color',
					'value'      => '',
					'group'      => 'Style',
				),
				array(
					'type'        => 'dropdown',
					'class'       => '',
					'heading'     => __( 'Icon to display:', $this->textdomain ),
					'param_name'  => 'icon_type',
					'value'       => array(
						'Font Icon Manager' => 'selector',
						'Custom Image Icon' => 'custom',
					),
					'description' => __( 'Use an existing font icon</a> or upload a custom image.', $this->textdomain )
				),
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
					'dependency' => array( 'element' => 'icon_type', 'value' => array( 'selector' ) ),
				),
				array(
					'type'        => 'attach_image',
					'class'       => '',
					'heading'     => __( 'Upload Image Icon:', $this->textdomain ),
					'param_name'  => 'icon_img',
					'value'       => '',
					'description' => __( 'Upload the custom image icon.', $this->textdomain ),
					'dependency'  => Array( 'element' => 'icon_type', 'value' => array( 'custom' ) ),
				),
				array(
					'type'       => 'textarea_html',
					'class'      => '',
					'heading'    => __( 'Descripion', $this->textdomain ),
					'param_name' => 'content',
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
	public function render_process( $atts, $content = null ) {

		extract( shortcode_atts( array(
			'bg_image'   => false,
			'breakpoint' => 480,
			'el_class'   => '',
		), $atts ) );

		$content = wpb_js_remove_wpautop( $content ); // fix unclosed/unwanted paragraph tags in $content
		$img     = wp_get_attachment_image_src( $bg_image, 'large' );

		ob_start();
		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'our-process ' . $el_class, $this->namespace, $atts );

		?>
		<div class="<?php echo $css_class; ?>">
			<?php if ($img[0]): ?>
				<div class="img-wrap">
					<img class="bg-img center" src="<?php echo $img[0]; ?>"/>
				</div>
			<?php endif; ?>
			<div class="dots">
				<?php echo do_shortcode( $content ); ?>
			</div>
		</div>
		<script>
			ed_school_plugin.data.vcWidgets.ourProcess.breakpoint = <?php echo (int) $breakpoint; ?>;
		</script>
		<?php

		$content = ob_get_clean();

		return $content;
	}

	public function render_process_item( $atts, $content = null ) {

		$title_style = $icon_style = $line_style = $triangle_style = $text_color = $text_style = '';
		extract( shortcode_atts( array(
			'title'       => '',
			'title_tag'   => 'h2',
			'width'       => '30%',
			'theme_icon'  => '',
			'icon_img'    => '',
			'title_color' => '',
			'icon_color'  => '',
			'bar_color'   => '',
			'text_color'  => '',
			'el_class'    => '',
		), $atts ) );

		$content = wpb_js_remove_wpautop( $content ); // fix unclosed/unwanted paragraph tags in $content



		if ( $title_color ) {
			$title_style = 'style="';
			$title_style .= 'color:' . $title_color;
			$title_style .= '"';
		}

		if ( $icon_color ) {
			$icon_style = 'style="';
			$icon_style .= 'color:' . $icon_color;
			$icon_style .= '"';
		}

		if ( $bar_color ) {
			$line_style = 'style="';
			$line_style .= 'background-color:' . $bar_color;
			$line_style .= '"';

			$triangle_style = 'style="';
			$triangle_style .= 'border-top-color:' . $bar_color;
			$triangle_style .= '"';
		}

		if ( $text_color ) {
			$text_style = 'style="';
			$text_style .= 'color:' . $text_color;
			$text_style .= '"';

		}


		ob_start();
		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'dot-container ' . $el_class, $this->namespace, $atts );
		?>
		<div class="<?php echo $css_class; ?>" style="width:<?php echo $width; ?>;">
			<div class="line" <?php echo $line_style; ?>></div>
			<div class="triangle" <?php echo $triangle_style; ?>></div>
			<div class="dot-wrap center">
				<div class="dot">
					<i class="<?php echo $theme_icon; ?>" <?php echo $icon_style; ?>></i>
				</div>
			</div>
			<div class="text" <?php echo $text_style; ?>>
				<?php echo $title ? '<' . $title_tag . ' class="title" ' . $title_style . '>' . $title . '</' . $title_tag . '>' : ''; ?>
				<?php echo $content; ?>
			</div>
		</div>
		<?php

		$content = ob_get_clean();

		return $content;
	}

	/*
	Load plugin css and javascript files which you may need on front end of your site
	*/
	public function loadCssAndJs() {
		//wp_register_style( 'scp_our_process', plugins_url( 'assets/css/scp-our-process.css', __FILE__ ) );
		//wp_enqueue_style( 'scp_our_process' );

		// If you need any javascript files on front end, here is how you can load them.
		wp_enqueue_script( 'jquery-appear', plugins_url( 'assets/js/jquery-appear.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script( 'scp_our_process', plugins_url( 'assets/js/scp-our-process.js', __FILE__ ), array( 'jquery' ) );
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

global $scp_our_process;
if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	class WPBakeryShortCode_scp_our_process extends WPBakeryShortCodesContainer {
		function content( $atts, $content = null ) {

		}
	}

	class WPBakeryShortCode_scp_our_process_item extends WPBakeryShortCode {
		function content( $atts, $content = null ) {

		}
	}
}
if ( class_exists( 'SCP_Our_Process' ) ) {
	$scp_our_process = new SCP_Our_Process();
}
