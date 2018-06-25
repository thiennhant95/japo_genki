<div class="jsjr-pci-accordion-item" data-id="profile_image">
	<h3 class="jsjr-pci-toggle" ><?php _e('Profile Photo', $this->domain ); ?></h3>
	<div style="display:none;" >
		<p>
			<label for="<?php echo $this->get_field_id( 'profile_image_url' ); ?>"><?php _e( 'Link to Profile Image (URL):', $this->domain ); ?></label> 
			<input class="widefat upload-input" id="<?php echo $this->get_field_id( 'profile_image_url' ); ?>" name="<?php echo $this->get_field_name( 'profile_image_url' ); ?>" type="text" value="<?php _e( isset( $profile_image_url ) ?  $profile_image_url : '', $this->domain ); ?>" />
			<input type="button" name="submit" id="submit" class="button-primary upload-button" value="Select image" rel="<?php echo $this->get_field_id( 'profile_image_url' ); ?>">
			
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('profile_image'); ?>"><?php _e('Image Style:'); ?></label>
			<a href="#" class="jsjr-pci-question" title="<?php _e( 'NOTICE: These styles do not work on old internet browsers.', $this->domain ) ?>">?</a>
			<select name="<?php echo $this->get_field_name('profile_image'); ?>" id="<?php echo $this->get_field_id('profile_image'); ?>" class="widefat">
				<?php
				$profile_image = isset( $profile_image ) ? $profile_image : '';
				foreach ( $select_options as $key => $value ) {
					echo '<option value="' , $key , '" ', selected( $profile_image, $key ) , '>', __( $value, $this->domain ) , '</option>';
				}
				?>
			</select>
		</p>
	</div>
</div>