<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Scp_Theme_Map {

	protected $name = 'Theme Map';
	protected $namespace = 'scp_theme_map';
	protected $textdomain = SCP_TEXT_DOMAIN;

	function __construct() {
		// We safely integrate with VC with this hook
		add_action( 'init', array( $this, 'integrateWithVC' ) );

		// Use this when creating a shortcode addon
		add_shortcode( $this->namespace, array( $this, 'render' ) );

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
					'type'        => 'textfield',
					'heading'     => __( 'Map Height', $this->textdomain ),
					'param_name'  => 'height',
					'value'       => '400',
					'description' => __( 'Value in px. Enter number only.', $this->textdomain ),

				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Latitude', $this->textdomain ),
					'param_name'  => 'latitude',
					'value'       => '40.7143528',
					'description' => sprintf( __( 'Visit %s to get coordinates.' ), '<a href="http://www.mapcoordinates.net/en" target="_blank">' . __( 'this site', $this->textdomain ) . '</a>' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Longitude', $this->textdomain ),
					'param_name'  => 'longitude',
					'value'       => '-74.0059731',
					'description' => sprintf( __( 'Visit %s to get coordinates.' ), '<a href="http://www.mapcoordinates.net/en" target="_blank">' . __( 'this site', $this->textdomain ) . '</a>' ),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Zoom Level', $this->textdomain ),
					'param_name' => 'zoom',
					'value'      => '10',
				),
				array(
					'type'        => 'textarea_safe',
					'heading'     => __( 'Snazzy Maps Style', $this->textdomain ),
					'param_name'  => 'snazzy_style',
					'description' => sprintf( __( 'Visit %s to create your map style. Copy JavaScript Style Array and paste here.' ), '<a href="https://snazzymaps.com/style/15/subtle-grayscale" target="_blank">' . __( 'Example', $this->textdomain ) . '</a>' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Disable Map Zoom Scroll', $this->textdomain ),
					'param_name' => 'disable_scroll',
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
			'height'         => '400',
			'latitude'       => '40.7143528',
			'longitude'      => '-74.0059731',
			'zoom'           => '10',
			'snazzy_style'   => 'false',
			'disable_scroll' => '',
			'el_class'       => '',
		), $atts ) );
		// $content = wpb_js_remove_wpautop($content); // fix unclosed/unwanted paragraph tags in $content

		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $el_class, $this->namespace, $atts );

		$uid = uniqid( 'theme-map-' );

		$snazzy_style = trim( vc_value_from_safe( $snazzy_style ) );
		$snazzy_style = str_replace( '`', '', $snazzy_style );

		// make sure it is properly formated
		$snazzy_style = json_decode( $snazzy_style );

		ob_start();
		?>

		<script type="text/javascript">

			jQuery(function () {


				// When the window has finished loading create our google map below
				google.maps.event.addDomListener(window, 'load', function () {


					// Basic options for a simple Google Map
					// For more options see: https://developers.google.com/maps/documentation/javascript/reference#MapOptions
					var mapOptions = {
						// How zoomed in you want the map to start at (always required)
						zoom: <?php echo (int) $zoom; ?>,

						// The latitude and longitude to center the map (always required)
						center: new google.maps.LatLng(<?php echo  $latitude; ?>, <?php echo $longitude; ?>),


						<?php if ($disable_scroll == 'true') : ?>
						scrollwheel: false,
						<?php endif; ?>

						<?php if ($snazzy_style) : ?>
						// How you would like to style the map.
						// This is where you would paste any style found on Snazzy Maps.
						styles: <?php echo json_encode($snazzy_style); ?>

						<?php else: ?>
						<?php echo 'styles:[]'; ?>
						<?php endif; ?>
					};


					// Get the HTML DOM element that will contain your map
					// We are using a div with id="map" seen below in the <body>
					var mapElement = document.getElementById('<?php echo $uid; ?>');

					// Create the Google Map using our element and options defined above
					var map = new google.maps.Map(mapElement, mapOptions);

					// Let's also add a marker while we're at it
					var marker = new google.maps.Marker({
						position: new google.maps.LatLng(<?php echo $latitude; ?>, <?php echo $longitude; ?>),
						map: map,
						title: ''
					});


				});
			});

		</script>

		<div id="<?php echo $uid; ?>" style="width: 100%; height:<?php echo (int) $height; ?>px;"
		     class="<?php echo $css_class; ?>"></div>


		<?php
		$content = ob_get_clean();

		return $content;
	}

	/*
	Load plugin css and javascript files which you may need on front end of your site
	*/
	public function loadCssAndJs() {

		$url = 'https://maps.googleapis.com/maps/api/js';

		$user_api_key = scp_get_wheels_option('gmaps_api_key');
		if ($user_api_key) {
			$url = $url . '?key=' . $user_api_key;
		}
		wp_enqueue_script( 'gmaps', $url, array( 'jquery' ) );

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
new Scp_Theme_Map();
