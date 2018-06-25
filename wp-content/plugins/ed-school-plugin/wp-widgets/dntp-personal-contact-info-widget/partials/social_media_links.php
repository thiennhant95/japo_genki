<?php
echo '<p class="jsjr-pci-social-icons" >';
			
foreach ( $this->social_icons as $fa_class => $icon ) {
	$custom_image_name = $fa_class . '_custom_image';

	if (!empty( $instance[$custom_image_name])) {
		echo '<a href="' , $instance[$fa_class] , '" target="_blank" ><img src="' .$instance[$custom_image_name]  .'"></a>';
	} elseif ( !empty( $instance[$fa_class] ) ) {
		echo '<a href="' , $instance[$fa_class] , '" class="fa ' , $fa_class , '" target="_blank" ></a>';
	}
}
	
echo '</p>';