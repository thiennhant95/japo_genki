<?php
/**
 * Plugin Name: Ed School Plugin
 * Plugin URI:  http://wordpress.org/plugins
 * Description: Ed School theme helper plugin
 * Version:     1.2.3
 * Author:      Aislin Themes
 * Author URI:  http://themeforest.net/user/Aislin/portfolio
 * License:     GPLv2+
 * Text Domain: chp
 * Domain Path: /languages
 */
define( 'SCP_PLUGIN_VERSION', '1.2.3' );
define( 'SCP_PLUGIN_NAME', 'Ed School' );
define( 'SCP_PLUGIN_PREFIX', 'scp_' );
define( 'SCP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SCP_PLUGIN_PATH', dirname( __FILE__ ) . '/' );
define( 'SCP_TEXT_DOMAIN', 'ed-school' );

register_activation_hook( __FILE__, 'scp_activate' );
register_deactivation_hook( __FILE__, 'scp_deactivate' );

add_action( 'plugins_loaded', 'scp_init' );
add_action( 'widgets_init', 'scp_register_wp_widgets' );
add_action( 'wp_enqueue_scripts', 'scp_enqueue_scripts', 100 );
add_action( 'admin_init', 'scp_register_wp_widgets' );
add_action( 'admin_init', 'scp_vc_editor_set_post_types' );
add_action( 'wp_head', 'scp_set_js_global_var' );
add_action( 'wp_head', 'scp_theme_debugging_info', 999);

add_filter( 'pre_get_posts', 'scp_portfolio_posts' );
add_filter( 'widget_text', 'do_shortcode' );

add_filter( 'vc_iconpicker-type-theme-icons', 'scp_theme_icons' );
add_filter( 'vc_base_build_shortcodes_custom_css', 'scp_filter_vc_base_build_shortcodes_custom_css' );
add_filter( 'pll_get_post_types', 'scp_add_cpt_to_pll', 10, 2 );




require_once 'shortcodes.php';


 // $vc_template_importer = get_template_directory() . '/dev/vc-template-importer/init.php';
 // if ( file_exists($vc_template_importer)) {
 // 	require_once $vc_template_importer;
 // }


function scp_init() {
	scp_add_extensions();
	scp_add_vc_custom_addons();

	require_once 'extensions/CPT.php';
	$layout_blocks = new CPT('layout_block', array(
		'public'        => false,
		'show_ui'       => true,
		'show_in_menu'  => true,
		'menu_position' => 29,
		'supports' => array('title', 'editor', 'revisions')
	));
	$layout_blocks->register_taxonomy(array(
		'taxonomy_name' => 'layout_block_type',
		'singular' => 'Type',
		'plural' => 'Type',
		'slug' => 'type',
	));
	$layout_blocks->filters(array('layout_block_type'));

}

function scp_activate() {
	scp_init();
	flush_rewrite_rules();
}

function scp_deactivate() {

}

function scp_add_vc_custom_addons() {

	require_once 'vc-addons/content-box/addon.php';
	require_once 'vc-addons/video-popup/addon.php';
	require_once 'vc-addons/logo/addon.php';
	require_once 'vc-addons/theme-button/addon.php';
	require_once 'vc-addons/theme-icon/addon.php';
	require_once 'vc-addons/theme-map/addon.php';
	require_once 'vc-addons/menu/addon.php';
	require_once 'vc-addons/post-list/addon.php';
	require_once 'vc-addons/share-this/addon.php';
	require_once 'vc-addons/wc-mini-cart/addon.php';
	require_once 'vc-addons/search/addon.php';
	require_once 'vc-addons/quick-sidebar-trigger/addon.php';
	require_once 'vc-addons/dribbble-shots/addon.php';
	require_once 'vc-addons/events/addon.php';
	require_once 'vc-addons/schedule/addon.php';
	require_once 'vc-addons/instagram/addon.php';
	require_once 'vc-addons/teachers/addon.php';
	require_once 'vc-addons/our-process/addon.php';
	require_once 'vc-addons/hexagon-icon/addon.php';
}

function scp_add_extensions() {

	require_once 'extensions/teacher-post-type/teacher-post-type.php';
	require_once 'extensions/mega-submenu/mega-submenu.php';

	if ( ! scp_is_plugin_activating( 'breadcrumb-trail/breadcrumb-trail.php' ) && ! function_exists( 'breadcrumb_trail_theme_setup' ) ) {
		require_once 'extensions/breadcrumb-trail/breadcrumb-trail.php';
	}

	/**
	 * Events Settings the first time
	 */
	add_option( 'tribe_events_calendar_options', array(
		'tribeEventsTemplate' => 'template-fullwidth.php',
	) );
}

function scp_get_wheels_option( $option_name, $default = false ) {
	if ( function_exists( 'ed_school_get_option' ) ) {
		return ed_school_get_option( $option_name, $default );
	}

	return $default;
}

function scp_set_js_global_var() {
	?>
	<script>
		var ed_school_plugin = ed_school_plugin ||
			{
				data: {
					vcWidgets: {
						ourProcess: {
							breakpoint: 480
						}
					},
					styles: []
				}
			};
	</script>
<?php
}

function scp_register_wp_widgets() {
	require_once 'wp-widgets/SCP_Latest_Posts_Widget.php';
	require_once 'wp-widgets/SCP_Contact_Info_Widget.php';
//	require_once 'wp-widgets/SCP_Working_Hours_Widget.php';
	require_once 'wp-widgets/SCP_Banner_Widget.php';
	require_once 'wp-widgets/twitter-widget/recent-tweets-widget.php';
}

function scp_portfolio_posts( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( is_tax() && isset( $query->tax_query ) && $query->tax_query->queries[0]['taxonomy'] == 'portfolio_category' ) {
		$query->set( 'posts_per_page', 10 );

		return;
	}
}

function scp_vc_editor_set_post_types() {

	if ( is_admin() && function_exists( 'vc_set_default_editor_post_types' ) ) {
		vc_set_default_editor_post_types( array(
			'page', 'layout_block', 'project', 'events', 'msm_mega_menu'
		) );
	}
}

function scp_enqueue_scripts() {
    // wp_enqueue_style('linp-css', SCP_PLUGIN_URL . '/public/css/scp-style.css', false);
	wp_enqueue_script( 'linp-main-js', SCP_PLUGIN_URL . '/public/js/linp-main.js', array( 'jquery' ), false, true );
}

function scp_is_plugin_activating( $plugin ) {
	if ( isset( $_GET['action'] ) && $_GET['action'] == 'activate' && isset( $_GET['plugin'] ) ) {
		if ( $_GET['plugin'] == $plugin ) {
			return true;
		}
	}

	return false;
}

function scp_fpc( $filename, $filecontent ) {
	file_put_contents( $filename, $filecontent );
}

function scp_fgc( $filename ) {
	return file_get_contents( $filename );
}

function scp_theme_icons( $icons ) {

	return array_merge( $icons, scp_get_theme_icon_list() );
}


function scp_get_theme_icon_list() {

	// http://regexr.com/

	// class="(icon-\S+)\b" regex
	// .(icon-\S+)\b: regex (copy css for icons)
	// array('$1' => '$1'),\n format

	$theme_icons = array(
		array('icon-edarrow-right-circle' => 'icon-edarrow-right-circle'),
		array('icon-edlong-arrow-pointing-to-the-right' => 'icon-edlong-arrow-pointing-to-the-right'),
		array('icon-ednext' => 'icon-ednext'),
		array('icon-edright-arrow' => 'icon-edright-arrow'),
		array('icon-edright-arrow-1' => 'icon-edright-arrow-1'),
		array('icon-edright-arrow-3' => 'icon-edright-arrow-3'),
		array('icon-edright-arrow-4' => 'icon-edright-arrow-4'),
		array('icon-edright-arrow-5' => 'icon-edright-arrow-5'),
		array('icon-edright-arrow-6' => 'icon-edright-arrow-6'),
		array('icon-edright-arrow-8' => 'icon-edright-arrow-8'),
		array('icon-edright-arrow-9' => 'icon-edright-arrow-9'),
		array('icon-edright-arrow-angle' => 'icon-edright-arrow-angle'),
		array('icon-edright-chevron' => 'icon-edright-chevron'),
		array('icon-edright-chevron-1' => 'icon-edright-chevron-1'),
		array('icon-edagenda' => 'icon-edagenda'),
		array('icon-edagenda-one' => 'icon-edagenda-one'),
		array('icon-edamerican-football' => 'icon-edamerican-football'),
		array('icon-edapple' => 'icon-edapple'),
		array('icon-edarrow' => 'icon-edarrow'),
		array('icon-edarrow-one' => 'icon-edarrow-one'),
		array('icon-edbackpack' => 'icon-edbackpack'),
		array('icon-edbadge' => 'icon-edbadge'),
		array('icon-edball' => 'icon-edball'),
		array('icon-edbasketball' => 'icon-edbasketball'),
		array('icon-edbell' => 'icon-edbell'),
		array('icon-edbell-black-shape' => 'icon-edbell-black-shape'),
		array('icon-edbell-one' => 'icon-edbell-one'),
		array('icon-edbig-church-bell' => 'icon-edbig-church-bell'),
		array('icon-edbook' => 'icon-edbook'),
		array('icon-edbook1' => 'icon-edbook1'),
		array('icon-edbook2' => 'icon-edbook2'),
		array('icon-edbook-and-computer-mouse' => 'icon-edbook-and-computer-mouse'),
		array('icon-edbookmark' => 'icon-edbookmark'),
		array('icon-edbook-one' => 'icon-edbook-one'),
		array('icon-edbook-with-white-bookmark' => 'icon-edbook-with-white-bookmark'),
		array('icon-edbus' => 'icon-edbus'),
		array('icon-edcalendar' => 'icon-edcalendar'),
		array('icon-edcalendar2' => 'icon-edcalendar2'),
		array('icon-edcertification' => 'icon-edcertification'),
		array('icon-edcheck' => 'icon-edcheck'),
		array('icon-edcheck-icon' => 'icon-edcheck-icon'),
		array('icon-edclock' => 'icon-edclock'),
		array('icon-edclock2' => 'icon-edclock2'),
		array('icon-edclock-one' => 'icon-edclock-one'),
		array('icon-edcomment' => 'icon-edcomment'),
		array('icon-edcomputer' => 'icon-edcomputer'),
		array('icon-edcup' => 'icon-edcup'),
		array('icon-edcustomer' => 'icon-edcustomer'),
		array('icon-eddark-eye' => 'icon-eddark-eye'),
		array('icon-eddiploma' => 'icon-eddiploma'),
		array('icon-eddiploma-one' => 'icon-eddiploma-one'),
		array('icon-ededit-draw-pencil' => 'icon-ededit-draw-pencil'),
		array('icon-edflask' => 'icon-edflask'),
		array('icon-edfood5' => 'icon-edfood5'),
		array('icon-edfootball' => 'icon-edfootball'),
		array('icon-edgps' => 'icon-edgps'),
		array('icon-edhand' => 'icon-edhand'),
		array('icon-edhtml' => 'icon-edhtml'),
		array('icon-edicon' => 'icon-edicon'),
		array('icon-edleft-arrow' => 'icon-edleft-arrow'),
		array('icon-edletter' => 'icon-edletter'),
		array('icon-edlight-bulb' => 'icon-edlight-bulb'),
		array('icon-edmeasuring' => 'icon-edmeasuring'),
		array('icon-edmedal' => 'icon-edmedal'),
		array('icon-edmedal-1' => 'icon-edmedal-1'),
		array('icon-edmedal-one' => 'icon-edmedal-one'),
		array('icon-edmegaphone' => 'icon-edmegaphone'),
		array('icon-edmusic1' => 'icon-edmusic1'),
		array('icon-ednotification-bell' => 'icon-ednotification-bell'),
		array('icon-edpencil' => 'icon-edpencil'),
		array('icon-edplaceholder' => 'icon-edplaceholder'),
		array('icon-eddesk' => 'icon-eddesk'),
		array('icon-edicon-bus' => 'icon-edicon-bus'),
		array('icon-edplant' => 'icon-edplant'),
		array('icon-edschool-flag' => 'icon-edschool-flag'),
		array('icon-edquality' => 'icon-edquality'),
		array('icon-eduniversity-flag' => 'icon-eduniversity-flag'),
		array('icon-edribbon' => 'icon-edribbon'),
		array('icon-edribbon-badge-award' => 'icon-edribbon-badge-award'),
		array('icon-edright-arrow2' => 'icon-edright-arrow2'),
		array('icon-eduniversity-campus' => 'icon-eduniversity-campus'),
		array('icon-edsaturn-rings' => 'icon-edsaturn-rings'),
		array('icon-eduniversity-with-a-flag' => 'icon-eduniversity-with-a-flag'),
		array('icon-edarrow-pointing-to-right' => 'icon-edarrow-pointing-to-right'),
		array('icon-edschool' => 'icon-edschool'),
		array('icon-edscissors' => 'icon-edscissors'),
		array('icon-edsearch' => 'icon-edsearch'),
		array('icon-edshape' => 'icon-edshape'),
		array('icon-edbig-map-placeholder' => 'icon-edbig-map-placeholder'),
		array('icon-edsharing-interface' => 'icon-edsharing-interface'),
		array('icon-edsigns' => 'icon-edsigns'),
		array('icon-edbook-with-white-bookmark2' => 'icon-edbook-with-white-bookmark2'),
		array('icon-edsigns3' => 'icon-edsigns3'),
		array('icon-edsigns4' => 'icon-edsigns4'),
		array('icon-eddialog' => 'icon-eddialog'),
		array('icon-edelementary-school' => 'icon-edelementary-school'),
		array('icon-edfacebook-letter-logo' => 'icon-edfacebook-letter-logo'),
		array('icon-edfacebook-logo-button' => 'icon-edfacebook-logo-button'),
		array('icon-edglasses' => 'icon-edglasses'),
		array('icon-edgoogle-plus' => 'icon-edgoogle-plus'),
		array('icon-edgoogle-plus-logo-button' => 'icon-edgoogle-plus-logo-button'),
		array('icon-edvimeo' => 'icon-edvimeo'),
		array('icon-edinstagram-logo' => 'icon-edinstagram-logo'),
		array('icon-edinstagram-symbol' => 'icon-edinstagram-symbol'),
		array('icon-edknife-and-spoon-crossed' => 'icon-edknife-and-spoon-crossed'),
		array('icon-edlinkedin-logo' => 'icon-edlinkedin-logo'),
		array('icon-edlong-arrow-pointing-to-the-right2' => 'icon-edlong-arrow-pointing-to-the-right2'),
		array('icon-edmortarboard' => 'icon-edmortarboard'),
		array('icon-edpinterest-circular-logo-symbol2' => 'icon-edpinterest-circular-logo-symbol2'),
		array('icon-edreading-sign' => 'icon-edreading-sign'),
		array('icon-edright' => 'icon-edright'),
		array('icon-edright-arrow-thin' => 'icon-edright-arrow-thin'),
		array('icon-edright-arrow-long' => 'icon-edright-arrow-long'),
		array('icon-edright-arrow22' => 'icon-edright-arrow22'),
		array('icon-edright-arrow-in-black-circular-button' => 'icon-edright-arrow-in-black-circular-button'),
		array('icon-edright-arrow-signal' => 'icon-edright-arrow-signal'),
		array('icon-edright-chevron2' => 'icon-edright-chevron2'),
		array('icon-edschool-with-a-flag' => 'icon-edschool-with-a-flag'),
		array('icon-edsocial-buttons-skype' => 'icon-edsocial-buttons-skype'),
		array('icon-edsocial-instagram-circle' => 'icon-edsocial-instagram-circle'),
		array('icon-edsocial-rss' => 'icon-edsocial-rss'),
		array('icon-edsocial-rss-circle-internet' => 'icon-edsocial-rss-circle-internet'),
		array('icon-edsocial-vimeo-in-a-circle-logo' => 'icon-edsocial-vimeo-in-a-circle-logo'),
		array('icon-edsuitcase' => 'icon-edsuitcase'),
		array('icon-edtarget-arrow' => 'icon-edtarget-arrow'),
		array('icon-edtelegram' => 'icon-edtelegram'),
		array('icon-edthin-right-arrow' => 'icon-edthin-right-arrow'),
		array('icon-edtime-almost-full' => 'icon-edtime-almost-full'),
		array('icon-edtrophy' => 'icon-edtrophy'),
		array('icon-edtwitter-black-shape' => 'icon-edtwitter-black-shape'),
		array('icon-edtwitter-logo-button' => 'icon-edtwitter-logo-button'),
		array('icon-eduniversity' => 'icon-eduniversity'),
		array('icon-eduniversity-with-a-flag3' => 'icon-eduniversity-with-a-flag3'),
		array('icon-eduser' => 'icon-eduser'),
		array('icon-edyoutube-logo-play' => 'icon-edyoutube-logo-play'),
		array('icon-edyoutube-logo' => 'icon-edyoutube-logo'),
		array('icon-edyoutube-logotype' => 'icon-edyoutube-logotype'),
		array('icon-edic_check_box' => 'icon-edic_check_box'),
		array('icon-edic_star' => 'icon-edic_star'),
		array('icon-edic_notifications2' => 'icon-edic_notifications2'),
		array('icon-edic_notifications' => 'icon-edic_notifications'),
		array('icon-edic_people' => 'icon-edic_people'),
		array('icon-edic_person' => 'icon-edic_person'),
		array('icon-edic_poll' => 'icon-edic_poll'),
		array('icon-edic_public' => 'icon-edic_public'),
		array('icon-edic_school' => 'icon-edic_school'),
		array('icon-edic_event_note' => 'icon-edic_event_note'),
		array('icon-edic_phone_in_talk' => 'icon-edic_phone_in_talk'),
		array('icon-edic_arrow_back2' => 'icon-edic_arrow_back2'),
		array('icon-edic_arrow_drop_down' => 'icon-edic_arrow_drop_down'),
		array('icon-edic_arrow_drop_down_circle' => 'icon-edic_arrow_drop_down_circle'),
		array('icon-edic_arrow_drop_down_circle_1' => 'icon-edic_arrow_drop_down_circle_1'),
		array('icon-edic_arrow_drop_up' => 'icon-edic_arrow_drop_up'),
		array('icon-edsigns5' => 'icon-edsigns5'),
		array('icon-edic_arrow_forward' => 'icon-edic_arrow_forward'),
		array('icon-edsmall-camera' => 'icon-edsmall-camera'),
		array('icon-edsocial' => 'icon-edsocial'),
		array('icon-edsocial-media' => 'icon-edsocial-media'),
		array('icon-edsocial-media1' => 'icon-edsocial-media1'),
		array('icon-edsocial-media2' => 'icon-edsocial-media2'),
		array('icon-edsocial-media3' => 'icon-edsocial-media3'),
		array('icon-edsquare' => 'icon-edsquare'),
		array('icon-edsymbol' => 'icon-edsymbol'),
		array('icon-edtelephone' => 'icon-edtelephone'),
		array('icon-edtime' => 'icon-edtime'),
		array('icon-edtime1' => 'icon-edtime1'),
		array('icon-edtime2' => 'icon-edtime2'),
		array('icon-edtime3' => 'icon-edtime3'),
		array('icon-edtool1' => 'icon-edtool1'),
		array('icon-edtool3' => 'icon-edtool3'),
		array('icon-edic_arrow_upward' => 'icon-edic_arrow_upward'),
		array('icon-edic_check' => 'icon-edic_check'),
		array('icon-edic_chevron_left' => 'icon-edic_chevron_left'),
		array('icon-edic_chevron_right' => 'icon-edic_chevron_right'),
		array('icon-edic_beenhere' => 'icon-edic_beenhere'),
		array('icon-edic_directions_bus' => 'icon-edic_directions_bus'),
		array('icon-edic_local_dining' => 'icon-edic_local_dining'),
		array('icon-edic_local_florist' => 'icon-edic_local_florist'),
		array('icon-edic_local_library' => 'icon-edic_local_library'),
		array('icon-edic_local_printshop' => 'icon-edic_local_printshop'),
		array('icon-edic_person_pin' => 'icon-edic_person_pin'),
		array('icon-edic_pin_drop' => 'icon-edic_pin_drop'),
		array('icon-edic_place' => 'icon-edic_place'),
		array('icon-edic_assistant_photo' => 'icon-edic_assistant_photo'),
		array('icon-edic_burst_mode2' => 'icon-edic_burst_mode2'),
		array('icon-edic_collections_bookmark' => 'icon-edic_collections_bookmark'),
		array('icon-edic_color_lens2' => 'icon-edic_color_lens2'),
		array('icon-edic_filter1' => 'icon-edic_filter1'),
		array('icon-edic_filter_2' => 'icon-edic_filter_2'),
		array('icon-edic_filter_3' => 'icon-edic_filter_3'),
		array('icon-edic_filter_4' => 'icon-edic_filter_4'),
		array('icon-edic_filter_5' => 'icon-edic_filter_5'),
		array('icon-edic_photo_camera2' => 'icon-edic_photo_camera2'),
		array('icon-edic_photo_library' => 'icon-edic_photo_library'),
		array('icon-edic_picture_as_pdf' => 'icon-edic_picture_as_pdf'),
		array('icon-edic_portrait' => 'icon-edic_portrait'),
		array('icon-edic_slideshow' => 'icon-edic_slideshow'),
		array('icon-edic_folder' => 'icon-edic_folder'),
		array('icon-edic_folder_open' => 'icon-edic_folder_open'),
		array('icon-edic_attach_file' => 'icon-edic_attach_file'),
		array('icon-edic_format_quote' => 'icon-edic_format_quote'),
		array('icon-edic_format_size' => 'icon-edic_format_size'),
		array('icon-edic_insert_photo' => 'icon-edic_insert_photo'),
		array('icon-edic_add' => 'icon-edic_add'),
		array('icon-edic_add_circle' => 'icon-edic_add_circle'),
		array('icon-edic_flag' => 'icon-edic_flag'),
		array('icon-edic_link' => 'icon-edic_link'),
		array('icon-edic_email' => 'icon-edic_email'),
		array('icon-edic_forum' => 'icon-edic_forum'),
		array('icon-edic_import_contacts' => 'icon-edic_import_contacts'),
		array('icon-edic_location_on' => 'icon-edic_location_on'),
		array('icon-edic_message' => 'icon-edic_message'),
		array('icon-edic_phone' => 'icon-edic_phone'),
		array('icon-edic_ring_volume' => 'icon-edic_ring_volume'),
		array('icon-edic_equalizer' => 'icon-edic_equalizer'),
		array('icon-edic_library_add' => 'icon-edic_library_add'),
		array('icon-edic_library_books' => 'icon-edic_library_books'),
		array('icon-edic_play_arrow' => 'icon-edic_play_arrow'),
		array('icon-edic_play_circle_filled' => 'icon-edic_play_circle_filled'),
		array('icon-edic_subscriptions' => 'icon-edic_subscriptions'),
		array('icon-edic_video_library' => 'icon-edic_video_library'),
		array('icon-edic_videocam' => 'icon-edic_videocam'),
		array('icon-edic_volume_down' => 'icon-edic_volume_down'),
		array('icon-edic_accessibility' => 'icon-edic_accessibility'),
		array('icon-edic_account_balance' => 'icon-edic_account_balance'),
		array('icon-edic_account_balance_wallet_24px2' => 'icon-edic_account_balance_wallet_24px2'),
		array('icon-edic_account_box' => 'icon-edic_account_box'),
		array('icon-edic_account_circle' => 'icon-edic_account_circle'),
		array('icon-edic_assignment_ind' => 'icon-edic_assignment_ind'),
		array('icon-edic_book' => 'icon-edic_book'),
		array('icon-edic_bookmark' => 'icon-edic_bookmark'),
		array('icon-edic_class' => 'icon-edic_class'),
		array('icon-edic_date_range' => 'icon-edic_date_range'),
		array('icon-edic_done_all' => 'icon-edic_done_all'),
		array('icon-edic_favorite' => 'icon-edic_favorite'),
		array('icon-edic_question_answer' => 'icon-edic_question_answer'),
		array('icon-edic_record_voice_over' => 'icon-edic_record_voice_over'),
		array('icon-edic_room2' => 'icon-edic_room2'),
		array('icon-edic_schedule' => 'icon-edic_schedule'),
		array('icon-edic_search' => 'icon-edic_search'),
		array('icon-edic_settings_phone' => 'icon-edic_settings_phone'),
		array('icon-edic_speaker_notes' => 'icon-edic_speaker_notes'),
		array('icon-edic_stars' => 'icon-edic_stars'),
		array('icon-edic_supervisor_account' => 'icon-edic_supervisor_account'),
		array('icon-edic_thumb_up' => 'icon-edic_thumb_up'),
		array('icon-edic_today' => 'icon-edic_today'),
		array('icon-edic_trending_down' => 'icon-edic_trending_down'),
		array('icon-edic_trending_flat2' => 'icon-edic_trending_flat2'),
		array('icon-edic_trending_flat' => 'icon-edic_trending_flat'),
		array('icon-edic_turned_in' => 'icon-edic_turned_in'),
		array('icon-edic_verified_user' => 'icon-edic_verified_user'),
		array('icon-edic_visibility' => 'icon-edic_visibility'),
		array('icon-edic_watch_later' => 'icon-edic_watch_later'),
		array('icon-edic_zoom_in' => 'icon-edic_zoom_in'),
		array('icon-edhexagon' => 'icon-edhexagon'),
		array('icon-edhexagon-line' => 'icon-edhexagon-line'),
		array('icon-edline-testimonials-bottom' => 'icon-edline-testimonials-bottom'),
		array('icon-edline-testimonials-top' => 'icon-edline-testimonials-top'),
		array('icon-edtestimonials-line' => 'icon-edtestimonials-line'),
		array('icon-eddown-chevron-down' => 'icon-eddown-chevron-down'),
		array('icon-edtwo-quotes' => 'icon-edtwo-quotes'),
		array('icon-ednumber-1' => 'icon-ednumber-1'),
		array('icon-ednumber-2' => 'icon-ednumber-2'),
		array('icon-ednumber-3' => 'icon-ednumber-3'),
		array('icon-ednumber-4' => 'icon-ednumber-4'),

	);

	return $theme_icons;
}

function scp_sanitize_size( $value, $default = 'px' ) {

	return preg_match( '/(px|em|rem|\%|pt|cm)$/', $value ) ? $value : ( (int) $value ) . $default;
}


function scp_filter_vc_base_build_shortcodes_custom_css($css) {
	global $post;
	
	if (!$post) {
		return $css;
	}

	// had to be done like this so we get the post with newly saved content
	$post = get_post( $post->ID );


	if (!$post) {
		return $css;
	}

	$css .= scp_parseShortcodesCustomCss($post->post_content);

	return $css;
}

function scp_parseShortcodesCustomCss( $content ) {
		global $shortcode_tags;
		$css = '';

		WPBMap::addAllMappedShortcodes();
		preg_match_all( '/' . get_shortcode_regex() . '/', $content, $shortcodes );
		foreach ( $shortcodes[2] as $index => $tag ) {
			$shortcode = WPBMap::getShortCode( $tag );
			$attr_array = shortcode_parse_atts( trim( $shortcodes[3][ $index ] ) );

			if (isset($shortcode_tags[$tag]) && is_array($shortcode_tags[$tag]) && is_object($shortcode_tags[$tag][0])) {
				$widget = $shortcode_tags[$tag][0];
				if (method_exists($widget, 'generate_css')) {
					$css .= $widget->generate_css($attr_array);
				}
			}
		}
		foreach ( $shortcodes[5] as $shortcode_content ) {
			$css .= scp_parseShortcodesCustomCss( $shortcode_content );
		}

		return $css;
	}



function scp_add_cpt_to_pll( $post_types, $is_settings ) {

	if ( $is_settings ) {
		// hides 'my_cpt' from the list of custom post types in Polylang settings
		// unset( $post_types['my_cpt'] );
	} else {
		// enables language and translation management for 'my_cpt'
	}
	
	$post_types['layout_block'] = 'layout_block';
	$post_types['msm_mega_menu'] = 'msm_mega_menu';
	return $post_types;
}

function scp_theme_debugging_info() {
	$desc = 'Powered by ' . wp_get_theme() . ' WordPress theme - ' . 'Suitable for elementary school website, high school website or web presentation for teacher or tutor.';
	echo "<meta name=\"generator\" content=\"{$desc}\" />" . "\n";
}