<?php
if ( !empty( $full_name ) ) {
	echo '<h2 class="jsjr-pci-name" >' , $full_name , '</h2>';
}
if ( !empty( $slogan  ) ) {
	echo '<p class="jsjr-pci-slogan" >' , $slogan , '</p>';
}
if ( !empty( $email ) ) {
	echo '<p class="jsjr-pci-email" ><span class="fa fa-envelope" ></span> ' , $email , '</p>';
}

if ( !empty( $phone ) ) {
	echo '<p class="jsjr-pci-phone" ><span class="fa fa-phone"></span> ' , $phone , '</p>';
}

if ( !empty( $website ) ) {
	echo '<p class="jsjr-pci-website" ><span class="fa fa-link"></span> ' , $website , '</p>';
}