<?php

	/**
	 * Plugin Name: Personal Contact Info Widget
	 * Description: Custom Widget for displaying your photo and personal contact information.
	 * Version: 1.0
	 * Author: Juan Sanchez Jr.
	 * License: GPLv2 or later
	 */
	 
	/**  
	 * Copyright 2014  Juan Sanchez Jr. ( email : bringmesupport@gmail.com )
	 * This program is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License, version 2, as 
	 * published by the Free Software Foundation.
	 *
	 * This program is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with this program; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	 */

	defined('ABSPATH') or die("No script kiddies please!");

	class DNTP_Personal_Contact_Info extends WP_Widget {

		protected $ver                  = '1.0';
		protected $domain               = 'pci_text_domain';
		protected $default_item_order   = 'profile_image|contact_details|social_media_links';
		protected $social_icons         = array(
			'fa-facebook-square' 	=> 'Facebook',
			'fa-youtube-square' 	=> 'YouTube',
			'fa-twitter-square' 	=> 'Twitter',
			'fa-linkedin-square' 	=> 'LinkedIn',
			'fa-google-plus-square' => 'Google Plus',
			'fa-skype'				=> 'Skype',
			'fa-dropbox'			=> 'Dropbox',
			'fa-yelp'				=> 'Yelp',
			'fa-instagram' 			=> 'Instagram',
			'fa-pinterest'			=> 'Pinterest',
			'fa-wordpress'			=> 'WordPress',
			'fa-vine'				=> 'Vine',
			'fa-vimeo-square'		=> 'Vimeo',
			'fa-tumblr-square'		=> 'Tumblr',
			'fa-foursquare'			=> 'Foursquare',
			'fa-digg'				=> 'Digg',
			'fa-skype'				=> 'Skype',
			'fa-github'				=> 'GitHub',
			'fa-bitbucket-square'	=> 'Bitbucket',
			'fa-stack-overflow'		=> 'Stack Overflow'
		);
		
		public function __construct() {
			$widget_ops = array(
				'description' => __( 'Custom Widget for displaying your photo, social media links and contact information.', $this->domain ),
				'customizer_support' => true
			);
			$control_ops = array( 'width' => 400 );
			parent::__construct( false,sprintf( __( '%s - Contact Info' , $this->domain ), SCP_PLUGIN_NAME), $widget_ops, $control_ops );
			add_action( 'wp_enqueue_scripts', array( $this, 'jsjr_pci_wp_styles_and_scripts' ));
			add_action( 'admin_enqueue_scripts', array( $this, 'jsjr_pci_admin_styles_and_scripts' ));
		}
		
		public function jsjr_pci_admin_styles_and_scripts( $hook ){
			if ( 'widgets.php' == $hook ) {
				wp_enqueue_media();
				wp_enqueue_script( 'jquery-ui-tooltip' );
				wp_enqueue_script( 'jsjr-pci-admin-scripts' , plugin_dir_url( __FILE__ ) . 'js/admin-scripts.js', array('jquery'), $this->ver , false );
				wp_enqueue_style( 'jsjr-pci-admin-css' , plugin_dir_url( __FILE__ ) . 'css/admin-styles.css' , array() , $this->ver , false );				
			}			
		}
		
		public function jsjr_pci_wp_styles_and_scripts(){
			wp_enqueue_style( 'jsjr-pci-wp-css' , plugin_dir_url( __FILE__ ) . 'css/wp-styles.css' , array() , $this->ver , false );
			if ( get_option('fa_existing') === "checked" ) {
				wp_enqueue_style( 'jsjr-pci-font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css', array(), '4.2.0', false );
			}
		}
		
		public function widget( $args, $instance ) {

			extract( $args );
			extract( $instance );
				
			echo $before_widget;
			
			echo '<div class="jsjr-pci-contact-section">';


			$item_order = !empty( $item_order ) ? $item_order : $this->default_item_order;

			$item_order = explode('|', $item_order);

			$partials_path = SCP_PLUGIN_PATH . 'wp-widgets/dntp-personal-contact-info-widget/partials';

			if ( !empty( $title ) ) {
				echo $before_title , $title , $after_title;
			}

			foreach ($item_order as $value) {
				
				switch ($value) {
					case 'profile_image':				
						if (file_exists($partials_path . '/profile_image.php')) {
	                        include $partials_path . '/profile_image.php';

	                    }
						break;
					case 'contact_details':
						if (file_exists($partials_path . '/contact_details.php')) {
	                        include $partials_path . '/contact_details.php';
	                    }
						break;
					case 'social_media_links':
						if (file_exists($partials_path . '/social_media_links.php')) {
	                        include $partials_path . '/social_media_links.php';
	                    }
						break;
				}
			}
			
			echo '</div>';
			
			echo $after_widget;
			
		}

		public function update( $new_instance, $old_instance ) {
			foreach ( $new_instance as $key => $value ) {
				$old_instance[ $key ] = trim( strip_tags( $value ) );
			}
			$old_instance[ 'profile_image_below' ] = isset( $new_instance[ 'profile_image_below' ] ) ? $new_instance[ 'profile_image_below' ] : 'unchecked';
			$old_instance[ 'fa_existing' ] = isset( $new_instance[ 'fa_existing' ] ) ? $new_instance[ 'fa_existing' ] : 'unchecked';
			update_option( 'fa_existing', $old_instance['fa_existing'] );
			return $old_instance;
		}

		public function form( $instance ) {	

			foreach ( $instance as $key => $value ) {
				$$key = esc_attr( $value );
			}
			
			$select_options = array (
				'jsjr-pci-photo-square'		=> 'Square',
				'jsjr-pci-photo-circle'		=> 'Round',
				'jsjr-pci-photo-rcorners'	=> 'Rounded Corners',
				'jsjr-pci-photo-thumbnail'	=> 'Thumbnail'
			);
			
			?>
			
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title (optional):', $this->domain ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php _e( isset( $title ) ?  $title : '', $this->domain ); ?>" />
			</p>
			<input class="item-order" id="<?php echo $this->get_field_id('item_order'); ?>" name="<?php echo $this->get_field_name('item_order'); ?>" type="hidden" value="<?php _e( isset( $item_order ) ?  $item_order : $this->default_item_order, $this->domain ); ?>" />
			<p>
				<?php _e('You can sort Profile Image, Contact Details and Social Media Links Groups by dragging them.', $this->domain ); ?>
			</p>
			<div class="jsjr-pci-accordion">

				<?php 
				$item_order = !empty( $item_order ) ? $item_order : $this->default_item_order;

				$item_order = explode('|', $item_order);

				$partials_path = SCP_PLUGIN_PATH . 'wp-widgets/dntp-personal-contact-info-widget/partials/form';

				foreach ($item_order as $value) {
					
					switch ($value) {
						case 'profile_image':				
							if (file_exists($partials_path . '/profile_image.php')) {
		                        include $partials_path . '/profile_image.php';
		                    }
							break;
						case 'contact_details':
							if (file_exists($partials_path . '/contact_details.php')) {
		                        include $partials_path . '/contact_details.php';
		                    }
							break;
						case 'social_media_links':
							if (file_exists($partials_path . '/social_media_links.php')) {
		                        include $partials_path . '/social_media_links.php';
		                    }
							break;
					}
				}
				?>
				
				<h3 class="jsjr-pci-toggle" ><?php _e('Advanced Options', $this->domain ); ?></h3>
				<div style="display:none;" >
					<p>
						<input id="<?php echo $this->get_field_id('fa_existing'); ?>" name="<?php echo $this->get_field_name('fa_existing'); ?>" type="checkbox" value="checked" <?php isset( $fa_existing ) ? checked( 'checked', $fa_existing) : ''; ?> />
						<label for="<?php echo $this->get_field_id('fa_existing'); ?>"><?php _e('Load Font Awesome.', $this->domain ); ?></label>
						<a href="#" class="jsjr-pci-question" title="<?php _e( 'Check this to load Font Awesome in case your theme is not loading it already.', $this->domain ) ?>" >?</a>
					</p>					
				</div>	

			</div>
			
			<?php
		}
		
	}
	register_widget('DNTP_Personal_Contact_Info');
