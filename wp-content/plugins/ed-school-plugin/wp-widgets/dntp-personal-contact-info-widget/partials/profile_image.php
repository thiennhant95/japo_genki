<?php
if( !empty( $profile_image_url ) && !empty( $profile_image ) ){
	if ( $profile_image_below  === 'unchecked' ) {
		echo '<img src="' , $profile_image_url , '" class="jsjr-pci-photo ', $profile_image , '" alt="Profile Photo" />';
	} 
} 