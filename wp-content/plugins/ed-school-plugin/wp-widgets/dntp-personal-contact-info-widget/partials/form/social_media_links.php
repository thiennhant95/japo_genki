<div class="jsjr-pci-accordion-item" data-id="social_media_links">
	<h3 class="jsjr-pci-toggle" ><?php _e('Social Media Links', $this->domain ); ?></h3>
	<div style="display:none;">
		<a href="#" class="jsjr-pci-question" title="<?php _e( 'Enter the internet links (URL) for your social media websites below (I.E \'http://facebook.com/myfacebookpage\')', $this->domain ) ?>" >?</a>
		<?php foreach ( $this->social_icons as $fa_class => $icon ) { ?>
		<p>
			<label for="<?php echo $this->get_field_id( $fa_class ); ?>"><?php _e( $icon.':', $this->domain ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( $fa_class ); ?>" name="<?php echo $this->get_field_name( $fa_class ); ?>" type="text" value="<?php _e( isset( $$fa_class ) ?  $$fa_class : '', $this->domain ); ?>" />
			<label for="<?php echo $this->get_field_id( $fa_class . '_custom_image'); ?>"><?php echo sprintf( __( 'Custom %s icon:', $this->domain ),  $icon); ?></label>
			<?php $custom_image_name = $fa_class . '_custom_image'; ?>
			<input class="widefat upload-input" id="<?php echo $this->get_field_id( $custom_image_name); ?>" name="<?php echo $this->get_field_name( $custom_image_name ); ?>" type="text" value="<?php _e( isset( $$custom_image_name ) ?  $$custom_image_name : '', $this->domain ); ?>" />
			<input type="button" name="submit" id="submit" class="button-primary upload-button" value="Select image" rel="<?php echo $this->get_field_id( $custom_image_name); ?>">
		</p>
		<?php } ?>								
	</div>
</div>