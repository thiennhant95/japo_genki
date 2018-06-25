<div class="jsjr-pci-accordion-item" data-id="contact_details">
	<h3 class="jsjr-pci-toggle" ><?php _e('Contact Details', $this->domain ); ?></h3>
	<div style="display:none;" >
		<p>
			<label for="<?php echo $this->get_field_id('full_name'); ?>"><?php _e('Your Full Name:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('full_name'); ?>" name="<?php echo $this->get_field_name('full_name'); ?>" type="text" value="<?php _e( isset( $full_name ) ?  $full_name : '', $this->domain ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('slogan'); ?>"><?php _e('Slogan:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('slogan'); ?>" name="<?php echo $this->get_field_name('slogan'); ?>" type="text" value="<?php _e( isset( $slogan ) ?  $slogan : '', $this->domain ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('email'); ?>"><?php _e('Email:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('email'); ?>" name="<?php echo $this->get_field_name('email'); ?>" type="text" value="<?php _e( isset( $email ) ?  $email : '', $this->domain ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('phone'); ?>"><?php _e('Phone:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('phone'); ?>" name="<?php echo $this->get_field_name('phone'); ?>" type="text" value="<?php _e( isset( $phone ) ?  $phone : '', $this->domain ); ?>" />
		</p>			
		<p>
			<label for="<?php echo $this->get_field_id('website'); ?>"><?php _e('Alternate Website (optional):', $this->domain ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'website' ); ?>" name="<?php echo $this->get_field_name( 'website' ); ?>" type="text" value="<?php _e( isset( $website ) ?  $website : '', $this->domain ); ?>" />
		</p>
	</div>
</div>