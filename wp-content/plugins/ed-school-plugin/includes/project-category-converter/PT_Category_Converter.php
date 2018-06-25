<?php

class PT_Category_Converter {

	public $conversion_done_key = 'ed_school_project_category_conversion_done';
	protected $original_categories = array();

	public function should_convert() {

		if ( ! $this->is_converted() && count( $this->original_categories ) ) {
			return true;
		}

		return false;
	}

	public function is_converted() {
		return get_option( $this->conversion_done_key );
	}

	public function set_converted() {
		update_option( $this->conversion_done_key, 1 );
	}

	public function set_categories( $categories ) {
		$this->original_categories = $categories;
	}

	public function get_original_categories() {
		return $this->original_categories;
	}

	public function convert( $original_id ) {

		if ( ! $this->should_convert() ) {
			return $original_id;
		}

		// if comma separated string of ids - recurse
		if ( is_string( $original_id ) ) {
			$out = array();
			$ids = explode( ',', $original_id );

			foreach ( $ids as $id ) {
				$cat_id = $this->convert( (int) $id );
				if ( $cat_id ) {
					$out[] = $cat_id;
				}
			}

			return implode( ',', $out );
		}

		$original_slug      = $this->get_original_slug( $original_id );
		$current_categories = $this->get_current_categories();

		foreach ( $current_categories as $category ) {
			if ( $category->slug == $original_slug ) {
				return $category->term_id;
			}
		}

		return 0;
	}

	protected function get_current_categories() {

		$args = array(
			'hide_empty' => false,
			'taxonomy'   => 'project_category',
		);

		return get_categories( $args );
	}


	public function get_original_slug( $original_id ) {

		foreach ( $this->get_original_categories() as $category ) {
			if ( isset( $category['id'] ) && isset( $category['slug'] ) && $category['id'] == (int) $original_id ) {
				return $category['slug'];
			}
		}

		return null;
	}

	public function replace( $text ) {

		// https://regex101.com/r/eZ1gT7/445
		$regex = "/cat_ids=\"([0-9,\\s]+)\"/";

		$out = preg_replace_callback( $regex, array( $this, 'parse' ), $text );

		return $out;
	}

	public function parse( $matches ) {

		// as usual: $matches[0] is the complete match
		// $matches[1] the match for the first subpattern
		// enclosed in '(...)' and so on

		$new_ids = $this->convert( $matches[1] );


		$out = 'cat_ids="' . $new_ids . '"';

		return $out;

	}

	public function convert_all_pages() {

		if ( ! $this->should_convert() ) {
			return 0;
		}

		$pages = get_pages();

		foreach ( $pages as $page ) {
			$new_values = array(
				'ID'           => $page->ID,
				'post_content' => $this->replace( $page->post_content ),
			);

			wp_update_post( $new_values );
		}

		$this->set_converted();

		return 1;

	}

}

