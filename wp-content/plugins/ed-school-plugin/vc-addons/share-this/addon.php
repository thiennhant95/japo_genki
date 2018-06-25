<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class SCP_Share_This {

	protected $textdomain = SCP_TEXT_DOMAIN;
	protected $namespace = 'scp_share_this';

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
			'name'        => __( 'Share This', $this->textdomain ),
			'description' => __( '', $this->textdomain ),
			'base'        => $this->namespace,
			'class'       => '',
			'controls'    => 'full',
			'icon'        => plugins_url( 'assets/aislin-vc-icon.png', __FILE__ ),
			// or css class name which you can reffer in your css file later. Example: 'vc_extend_my_class'
			'category'    => __( 'Aislin', $this->textdomain ),
			//'admin_enqueue_js' => array(plugins_url('assets/vc_extend.js', __FILE__)), // This will load js file in the VC backend editor
			//'admin_enqueue_css' => array(plugins_url('assets/vc_extend_admin.css', __FILE__)), // This will load css file in the VC backend editor
			'params'      => array( //
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
			'el_class'                => '',
		), $atts ) );

		// $content = wpb_js_remove_wpautop($content); // fix unclosed/unwanted paragraph tags in $content
		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'scp-share-this share-this' . $el_class, $this->namespace, $atts );


		ob_start();
		?>
		<div class="<?php echo $css_class; ?>">
			<!-- http://simplesharingbuttons.com/ -->
			<ul class="share-buttons">
				<li><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode( site_url() ); ?>&t="
				       target="_blank" title="Share on Facebook"
				       onclick="window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(document.URL) + '&t=' + encodeURIComponent(document.URL)); return false;"><i
							class="fa fa-facebook"></i></a></li>
				<li>
					<a href="https://twitter.com/intent/tweet?source=<?php echo urlencode( site_url() ); ?>&text=:%20<?php echo urlencode( site_url() ); ?>"
					   target="_blank" title="Tweet"
					   onclick="window.open('https://twitter.com/intent/tweet?text=' + encodeURIComponent(document.title) + ':%20' + encodeURIComponent(document.URL)); return false;"><i
							class="fa fa-twitter"></i></a></li>
				<li><a href="https://plus.google.com/share?url=<?php echo urlencode( site_url() ); ?>"
				       target="_blank" title="Share on Google+"
				       onclick="window.open('https://plus.google.com/share?url=' + encodeURIComponent(document.URL)); return false;"><i
							class="fa fa-google-plus"></i></a></li>
				<li>
					<a href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode( site_url() ); ?>&description="
					   target="_blank" title="Pin it"
					   onclick="window.open('http://pinterest.com/pin/create/button/?url=' + encodeURIComponent(document.URL) + '&description=' +  encodeURIComponent(document.title)); return false;"><i
							class="fa fa-pinterest"></i></a></li>
				<li>
					<a href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode( site_url() ); ?>&title=&summary=&source=<?php echo urlencode( site_url() ); ?>"
					   target="_blank" title="Share on LinkedIn"
					   onclick="window.open('http://www.linkedin.com/shareArticle?mini=true&url=' + encodeURIComponent(document.URL) + '&title=' +  encodeURIComponent(document.title)); return false;"><i
							class="fa fa-linkedin"></i></a></li>
			</ul>
		</div>
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
		//wp_enqueue_script( 'vc_extend_js', plugins_url('assets/vc_extend.js', __FILE__), array('jquery') );
	}

	/*
	Show notice if your plugin is activated but Visual Composer is not
	*/
	public function showVcVersionNotice() {
		$plugin_data = get_plugin_data( __FILE__ );
		echo '
        <div class="updated">
          <p>' . sprintf( __( '<strong>%s</strong> requires <strong><a href="http://bit.ly/vcomposer" target="_blank">Visual Composer</a></strong> plugin to be installed and activated on your site.', SCP_TEXT_DOMAIN ), $plugin_data['Name'] ) . '</p>
        </div>';
	}

}

// Finally initialize code
new SCP_Share_This();
