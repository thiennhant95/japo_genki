<?php

class SCP_Banner_Widget extends WP_Widget {

	protected $textdomain = SCP_TEXT_DOMAIN;

	/**
	 * Constructor
	 */
	public function __construct() {

		$widget_ops = array(
			'classname'   => 'widget-banner',
			'description' => __( 'Banner', $this->textdomain ),
		);

		parent::__construct( 'scp_banner', SCP_PLUGIN_NAME . ' - Banner Widget', $widget_ops );
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @see WP_Widget::form()
	 *
	 * @param $instance current settings
	 */
	public function form( $instance ) {

		//Get Posts from first category (current one)
		$default = array(
			'title'       => __( 'Banner', $this->textdomain ),
			'label'       => '',
			'text'        => 'Banner text',
			'button_text' => '',
			'button_link' => '',
		);

		$instance = wp_parse_args( (array) $instance, $default );
		?>
		<p>
			<label
				for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', $this->textdomain ); ?></label><br/>
			<input class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>"
			       id="<?php echo $this->get_field_id( 'title' ); ?>"
			       value="<?php echo esc_attr( $instance['title'] ); ?>"/>
		</p>
		<p>
			<label
				for="<?php echo $this->get_field_id( 'label' ); ?>"><?php _e( 'Label', $this->textdomain ); ?></label><br/>
			<input class="widefat" name="<?php echo $this->get_field_name( 'label' ); ?>"
			       id="<?php echo $this->get_field_id( 'label' ); ?>"
			       value="<?php echo esc_attr( $instance['label'] ); ?>"/>
		</p>
		<p>
			<label
				for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e( 'Text', $this->textdomain ); ?></label><br/>
			<textarea class="widefat" name="<?php echo $this->get_field_name( 'text' ); ?>"
			          id="<?php echo $this->get_field_id( 'text' ); ?>"><?php echo esc_attr( $instance['text'] ); ?></textarea>
		</p>
		<p>
			<label
				for="<?php echo $this->get_field_id( 'button_text' ); ?>"><?php _e( 'Button Text', $this->textdomain ); ?></label><br/>
			<input class="widefat" name="<?php echo $this->get_field_name( 'button_text' ); ?>"
			       id="<?php echo $this->get_field_id( 'button_text' ); ?>"
			       value="<?php echo esc_attr( $instance['button_text'] ); ?>"/>
		</p>
		<p>
			<label
				for="<?php echo $this->get_field_id( 'button_link' ); ?>"><?php _e( 'Button Link', $this->textdomain ); ?></label><br/>
			<input class="widefat" name="<?php echo $this->get_field_name( 'button_link' ); ?>"
			       id="<?php echo $this->get_field_id( 'button_link' ); ?>"
			       value="<?php echo esc_attr( $instance['button_link'] ); ?>"/>
		</p>
	<?php
	}

	/**
	 * processes widget options to be saved
	 *
	 * @see WP_Widget::update()
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = array();
		if ( empty( $old_instance ) ) {
			$old_instance = $new_instance;
		}

		if ( $new_instance['num'] > 8 ) {
			$new_instance['num'] = 8;
		}

		foreach ( $old_instance as $k => $value ) {
			$instance[ $k ] = trim( strip_tags( $new_instance[ $k ] ) );
		}

		return $instance;
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 */
	public function widget( $args, $instance ) {
		extract( $args );

		$title = empty( $instance['title'] ) ? '' : apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		?>

		<div class="title">
			<?php echo $title; ?>
			<span class="label">
				<?php echo $instance['label']; ?>
			</span>
		</div>
		<div class="text">
			<?php echo $instance['text']; ?>
		</div>

		<?php if ( ! empty( $instance['button_text'] ) ): ?>
			<a class="link hoverable"
			   href="<?php echo esc_url( $instance['button_link'] ); ?>"><div class="anim"></div><?php echo $instance['button_text']; ?></a>
		<?php endif; ?>
		<?php
		echo $after_widget;
	}

}

register_widget( 'SCP_Banner_Widget' );
