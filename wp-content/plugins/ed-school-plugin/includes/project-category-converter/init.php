<?php

require_once 'PT_Category_Converter.php';
require_once 'PT_Category_Exporter.php';

// needs to be called later so cats are initialized



//	$exporter                 = new PT_Category_Exporter();
	$ed_school_category_converter = new PT_Category_Converter();

	$original_categories = include dirname( __FILE__ ) . '/export.php';

	if ( is_array( $original_categories ) ) {
		$ed_school_category_converter->set_categories( $original_categories );
	}

	set_transient('qweqwe', 'qweqwe', 600);

	if ($ed_school_category_converter->should_convert()) {
		$ed_school_category_converter->convert_all_pages();
		$ed_school_category_converter->set_converted();
	}

