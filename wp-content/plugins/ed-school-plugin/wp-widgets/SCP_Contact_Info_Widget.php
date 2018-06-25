<?php

class SCP_Contact_Info_Widget extends WP_Widget
{

    protected $textdomain = SCP_TEXT_DOMAIN;

    /**
     * Constructor
     */
    public function __construct()
    {

        $widget_ops = array(
            'classname' => 'widget-contact-info',
            'description' => __('Contact Info. ( for footer section)', $this->textdomain),
        );

        parent::__construct(
            'scp_contact_info', SCP_PLUGIN_NAME . ' - Contact Info Widget', $widget_ops
        );
    }

    /**
     * Outputs the options form on admin
     * @see WP_Widget::form()
     * @param $instance current settings
     */
    public function form($instance)
    {

        //Get Posts from first category (current one)
        $default = array(
            'title' => __('Contact Info', $this->textdomain),
            'text' => 'Praesent quis risus nec mi feugiat vehicula. Sed nec feugiat arcu. Ut ligula metus, dapibus in sagittis lobortis, rhoncus nec libero.',
            'address' => 'Address Line 1',
            'address_alt' => '',
            'email' => 'email@example.com',
            'email_alt' => '',
            'telephone' => '(123) 456 789',
            'telephone_alt' => '',
        );

        $instance = wp_parse_args((array)$instance, $default);
        ?>
        <p>
            <label
                for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', $this->textdomain); ?></label><br/>
            <input class="widefat" name="<?php echo $this->get_field_name('title'); ?>"
                   id="<?php echo $this->get_field_id('title'); ?>"
                   value="<?php echo esc_attr($instance['title']); ?>"/>
        </p>
        <p>
            <label
                for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Text', $this->textdomain); ?></label><br/>
            <textarea name="<?php echo $this->get_field_name('text'); ?>" id="<?php echo $this->get_field_id('text'); ?>" cols="30" rows="10"><?php echo esc_attr($instance['text']); ?></textarea>
        </p>
        <p>
            <label
                for="<?php echo $this->get_field_id('address'); ?>"><?php _e('Address', $this->textdomain); ?></label><br/>
            <input class="widefat" name="<?php echo $this->get_field_name('address'); ?>"
                   id="<?php echo $this->get_field_id('address'); ?>"
                   value="<?php echo esc_attr($instance['address']); ?>"/>
        </p>
        <p>
            <label
                for="<?php echo $this->get_field_id('address_alt'); ?>"><?php _e('Alt Address', $this->textdomain); ?></label><br/>
            <input class="widefat" name="<?php echo $this->get_field_name('address_alt'); ?>"
                   id="<?php echo $this->get_field_id('address_alt'); ?>"
                   value="<?php echo esc_attr($instance['address_alt']); ?>"/>
        </p>
        <p>
            <label
                for="<?php echo $this->get_field_id('email'); ?>"><?php _e('Email', $this->textdomain); ?></label><br/>
            <input class="widefat" name="<?php echo $this->get_field_name('email'); ?>"
                   id="<?php echo $this->get_field_id('email'); ?>"
                   value="<?php echo esc_attr($instance['email']); ?>"/>
        </p>
        <p>
            <label
                for="<?php echo $this->get_field_id('email_alt'); ?>"><?php _e('Alt Email', $this->textdomain); ?></label><br/>
            <input class="widefat" name="<?php echo $this->get_field_name('email_alt'); ?>"
                   id="<?php echo $this->get_field_id('email_alt'); ?>"
                   value="<?php echo esc_attr($instance['email_alt']); ?>"/>
        </p>
        <p>
            <label
                for="<?php echo $this->get_field_id('telephone'); ?>"><?php _e('Telephone', $this->textdomain); ?></label><br/>
            <input class="widefat" name="<?php echo $this->get_field_name('telephone'); ?>"
                   id="<?php echo $this->get_field_id('telephone'); ?>"
                   value="<?php echo esc_attr($instance['telephone']); ?>"/>
        </p>
        <p>
            <label
                for="<?php echo $this->get_field_id('telephone_alt'); ?>"><?php _e('Fax', $this->textdomain); ?></label><br/>
            <input class="widefat" name="<?php echo $this->get_field_name('telephone_alt'); ?>"
                   id="<?php echo $this->get_field_id('telephone_alt'); ?>"
                   value="<?php echo esc_attr($instance['telephone_alt']); ?>"/>
        </p>

    <?php
    }

    /**
     * processes widget options to be saved
     * @see WP_Widget::update()
     */
    public function update($new_instance, $old_instance)
    {

        $instance = array();
        if (empty($old_instance)) {
            $old_instance = $new_instance;
        }

        foreach ($old_instance as $k => $value) {
            $instance[$k] = trim(strip_tags($new_instance[$k]));
        }
        return $instance;
    }

    /**
     * Front-end display of widget.
     * @see WP_Widget::widget()
     * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
     * @param array $instance The settings for the particular instance of the widget
     */
    public function widget($args, $instance)
    {
        extract($args);

        $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);

        $out = '';
        $out .= $before_widget;

        $icons = array(
            'address'       => 'icon-edplaceholder',
            'address_alt'   => 'placeholder',
            'telephone'     => 'icon-edtelephone',
            'telephone_alt' => 'placeholder',
            'email'         => 'icon-edletter',
            'email_alt'     => 'placeholder',
        );


        if ($title) {
            $out .= $before_title . $title . $after_title;
        }
        if ( $instance['text'] ) {
            $out .= $instance['text'] ;
        }
        $out .= '<ul>';
        if ( $instance['address'] ) {
            $out .= '<li><i class="' . $icons['address'] . '"></i>';
            $out .= $instance['address'] ;
            $out .= '</li>';
        }
        if ( $instance['address_alt'] ) {
            $out .= '<li class="empty"><i class="' . $icons['address_alt'] . '"></i>';
            $out .= $instance['address_alt'] ;
            $out .= '</li>';
        }
        if ( $instance['telephone'] ) {
            $out .= '<li><i class="' . $icons['telephone'] . '"></i>';
            $out .= $instance['telephone'] ;
            $out .= '</li>';
        }
        if ( $instance['telephone_alt'] ) {
            $out .= '<li><i class="' . $icons['telephone_alt'] . '"></i>';
            $out .= $instance['telephone_alt'] ;
            $out .= '</li>';
        }
        if ( $instance['email'] ) {
            $out .= '<li><i class="' . $icons['email'] . '"></i>';
            $out .= $instance['email'] ;
            $out .= '</li>';
        }
        if ( $instance['email_alt'] ) {
            $out .= '<li class="empty"><i class="' . $icons['email_alt'] . '"></i>';
            $out .= $instance['email_alt'] ;
            $out .= '</li>';
        }

        $out .= '</ul>';
        $out .= $after_widget;

        echo $out;

    }

}

register_widget('SCP_Contact_Info_Widget');
