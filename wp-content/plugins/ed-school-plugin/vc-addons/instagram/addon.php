<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Scp_Instagram {

	protected $textdomain = SCP_TEXT_DOMAIN;
	protected $name = 'Instagram';
	protected $namespace = 'scp_instagram';

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
			'name'        => __( $this->name, $this->textdomain ),
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
					'type'       => 'textfield',
					'holder'     => '',
					'class'      => '',
					'heading'    => __( 'Username', $this->textdomain ),
					'param_name' => 'username',
				),
				array(
					'type'       => 'textfield',
					'holder'     => '',
					'class'      => '',
					'heading'    => __( 'Number of photos', $this->textdomain ),
					'param_name' => 'number_of_photos',
				),
				array(
					'type'       => 'textfield',
					'holder'     => '',
					'class'      => '',
					'heading'    => __( 'Number of columns', $this->textdomain ),
					'param_name' => 'number_of_columns',
				),
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => __( 'Photo Size', $this->textdomain ),
					'param_name' => 'photo_size',
					'value'      => array(
						'Thumbnail' => 'thumbnail',
						'Small'      => 'small',
						'Large'      => 'large',
					),
				),
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => __( 'Open links in', $this->textdomain ),
					'param_name' => 'target',
					'value'      => array(
						'New Window'     => '_blank',
						'Current Window' => '_self',
					),
				),
				array(
					'type'       => 'textfield',
					'holder'     => '',
					'class'      => '',
					'heading'    => __( 'Link Text', $this->textdomain ),
					'param_name' => 'link_text',
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

		$atts = shortcode_atts( array(
			'username'          => '',
			'number_of_photos'  => '9',
			'number_of_columns' => '3',
			'photo_size'        => 'large',
			'target'            => '_blank',
			'link_text'         => '',
			'el_class'          => '',
		), $atts );

		// $content = wpb_js_remove_wpautop($content); // fix unclosed/unwanted paragraph tags in $content

		ob_start();

		$username = $atts['username'];
		$limit    = $atts['number_of_photos'];
		$size     = $atts['photo_size'];
		$target   = $atts['target'];
		$link     = $atts['link_text'];


		// Taken from WP Instagram Widget
		if ( $username != '' ) {

			$columns = array(
				'1'  => 'one whole',
				'2'  => 'one half',
				'3'  => 'one third',
				'4'  => 'one forth',
				'5'  => 'one fifth',
				'6'  => 'one sixth',
				'7'  => 'one seventh',
				'8'  => 'one eighth',
				'9'  => 'one ninth',
				'10' => 'one tenth',
				'11' => 'one eleventh',
				'12' => 'one twelfth',
			);

			$column_class = isset( $columns[ $atts['number_of_columns'] ] ) ? $columns[ $atts['number_of_columns'] ] : 'one third';


			$media_array = $this->scrape_instagram( $username );

			if ( is_wp_error( $media_array ) ) {

				echo wp_kses_post( $media_array->get_error_message() );

			} else {

				$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'scp-instagram-pics instagram-size-' . $size . $atts['el_class'], $this->namespace, $atts );


				// filter for images only?
//				if ( $images_only = apply_filters( 'wpiw_images_only', false ) ) {
//					$media_array = array_filter( $media_array, array( $this, 'images_only' ) );
//				}

				// slice list down to required limit
				$media_array = array_slice( $media_array, 0, $limit );

				// filters for custom classes
				$liclass       = $column_class . ' two-up-small-tablet two-up-mobile';
				$aclass        = '';
				$imgclass      = '';
				$template_part = apply_filters( 'scp_template_part', 'parts/wp-instagram-widget.php' );

				?>
				<ul class="<?php echo esc_attr( $css_class ); ?>"><?php
				foreach ( $media_array as $item ) {
					// copy the else line into a new file (parts/wp-instagram-widget.php) within your theme and customise accordingly
					if ( locate_template( $template_part ) != '' ) {
						include locate_template( $template_part );
					} else {
						echo '<li class="' . esc_attr( $liclass ) . '"><a href="' . esc_url( $item['link'] ) . '" target="' . esc_attr( $target ) . '"  class="' . esc_attr( $aclass ) . '"><img src="' . esc_url( $item[ $size ] ) . '"  alt="' . esc_attr( $item['description'] ) . '" title="' . esc_attr( $item['description'] ) . '"  class="' . esc_attr( $imgclass ) . '"/></a></li>';
					}
				}
				?></ul><?php
			}
		}

		$linkclass = apply_filters( 'wpiw_link_class', 'clear' );

		if ( $link != '' ) {
			?><p class="<?php echo esc_attr( $linkclass ); ?>"><a
				href="<?php echo trailingslashit( '//instagram.com/' . esc_attr( trim( $username ) ) ); ?>" rel="me"
				target="<?php echo esc_attr( $target ); ?>"><?php echo wp_kses_post( $link ); ?></a></p><?php
		}


		$content = ob_get_clean();

		return $content;
	}

	// based on https://gist.github.com/cosmocatalano/4544576
	function scrape_instagram( $username ) {

		$username = strtolower( $username );
		$username = str_replace( '@', '', $username );

		if ( false === ( $instagram = get_transient( 'instagram-scp-' . sanitize_title_with_dashes( $username ) ) ) ) {

			$remote = wp_remote_get( 'http://instagram.com/' . trim( $username ) );

			if ( is_wp_error( $remote ) ) {
				return new WP_Error( 'site_down', esc_html__( 'Unable to communicate with Instagram.', 'wp-instagram-widget' ) );
			}

			if ( 200 != wp_remote_retrieve_response_code( $remote ) ) {
				return new WP_Error( 'invalid_response', esc_html__( 'Instagram did not return a 200.', 'wp-instagram-widget' ) );
			}

			$shards      = explode( 'window._sharedData = ', $remote['body'] );
			$insta_json  = explode( ';</script>', $shards[1] );
			$insta_array = json_decode( $insta_json[0], true );

			if ( ! $insta_array ) {
				return new WP_Error( 'bad_json', esc_html__( 'Instagram has returned invalid data.', 'wp-instagram-widget' ) );
			}

			if ( isset( $insta_array['entry_data']['ProfilePage'][0]['user']['media']['nodes'] ) ) {
				$images = $insta_array['entry_data']['ProfilePage'][0]['user']['media']['nodes'];
			} else {
				return new WP_Error( 'bad_json_2', esc_html__( 'Instagram has returned invalid data.', 'wp-instagram-widget' ) );
			}

			if ( ! is_array( $images ) ) {
				return new WP_Error( 'bad_array', esc_html__( 'Instagram has returned invalid data.', 'wp-instagram-widget' ) );
			}

			$instagram = array();

			foreach ( $images as $image ) {

				$image['thumbnail_src'] = preg_replace( '/^https?\:/i', '', $image['thumbnail_src'] );
				$image['display_src']   = preg_replace( '/^https?\:/i', '', $image['display_src'] );

				// handle both types of CDN url
				if ( ( strpos( $image['thumbnail_src'], 's640x640' ) !== false ) ) {
					$image['thumbnail'] = str_replace( 's640x640', 's160x160', $image['thumbnail_src'] );
					$image['small']     = str_replace( 's640x640', 's320x320', $image['thumbnail_src'] );
				} else {
					$urlparts  = wp_parse_url( $image['thumbnail_src'] );
					$pathparts = explode( '/', $urlparts['path'] );
					array_splice( $pathparts, 3, 0, array( 's160x160' ) );
					$image['thumbnail'] = '//' . $urlparts['host'] . implode( '/', $pathparts );
					$pathparts[3]       = 's320x320';
					$image['small']     = '//' . $urlparts['host'] . implode( '/', $pathparts );
				}

				$image['large'] = $image['thumbnail_src'];

				if ( $image['is_video'] == true ) {
					$type = 'video';
				} else {
					$type = 'image';
				}

				$caption = __( 'Instagram Image', 'wp-instagram-widget' );
				if ( ! empty( $image['caption'] ) ) {
					$caption = $image['caption'];
				}

				$instagram[] = array(
					'description' => $caption,
					'link'        => trailingslashit( '//instagram.com/p/' . $image['code'] ),
					'time'        => $image['date'],
					'comments'    => $image['comments']['count'],
					'likes'       => $image['likes']['count'],
					'thumbnail'   => $image['thumbnail'],
					'small'       => $image['small'],
					'large'       => $image['large'],
					'original'    => $image['display_src'],
					'type'        => $type
				);
			}

			// do not set an empty transient - should help catch private or empty accounts
			if ( ! empty( $instagram ) ) {
				$instagram = base64_encode( serialize( $instagram ) );
				set_transient( 'instagram-scp-' . sanitize_title_with_dashes( $username ), $instagram, apply_filters( 'scp_instagram_cache_time', HOUR_IN_SECONDS * 2 ) );
			}
		}

		if ( ! empty( $instagram ) ) {

			return unserialize( base64_decode( $instagram ) );

		} else {

			return new WP_Error( 'no_images', esc_html__( 'Instagram did not return any images.', 'wp-instagram-widget' ) );

		}
	}

	function images_only( $media_item ) {

		if ( $media_item['type'] == 'image' ) {
			return true;
		}

		return false;
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

new Scp_Instagram();