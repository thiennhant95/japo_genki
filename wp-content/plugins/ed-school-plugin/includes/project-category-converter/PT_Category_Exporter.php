<?php

class PT_Category_Exporter {

	public $formatted = array();
	public $GET_param = 'export_project_categories';
	protected $filename = '';

	public function __construct( $filename = 'export' ) {

		// filename can be changed in testing environment
		$this->filename = plugin_dir_path( __FILE__ ) . $filename . '.php';

		if ( is_admin() && isset( $_GET[ $this->GET_param ] ) && $_GET[ $this->GET_param ] == '1' ) {
			$this->format();
			$this->create_file();
		}

	}

	protected function create_file() {

		$contents = '<?php return ';
		$contents .= var_export( $this->get_formatted(), true );
		$contents .= ';';
		file_put_contents( $this->filename, $contents );
	}

	protected function format() {

		foreach ( $this->get_current_categories() as $category ) {
			$this->formatted[] = array(
				'id'   => $category->term_id,
				'slug' => $category->slug,
			);
		}

	}

	public function get_formatted() {
		return $this->formatted;
	}

	protected function get_current_categories() {

		$args = array(
			'hide_empty' => false,
			'taxonomy'   => 'project_category',
		);

		return get_categories( $args );
	}

}