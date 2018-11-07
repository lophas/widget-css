<?php
/*
    Plugin Name: Widgetmaster Widget CSS
    Description: provides custom CSS field for every widget, use "$this" as widget container id
    Version: 0.2
    Plugin URI: https://github.com/lophas/widget-css
    GitHub Plugin URI: https://github.com/lophas/widget-css
    Author: Attila Seres
    Author URI:
*/
if (!class_exists('widget_css')):
class widget_css
{
    private static $_instance;
    public function instance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance =  new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        if (is_admin()) {
            add_filter('in_widget_form', array($this,'in_widget_form'), 10, 3);
            add_filter('widget_update_callback', array($this,'widget_update_callback'), 10, 4);
        } //else {
        add_action('wp_head', array($this,'wp_head'), 999);
        add_filter('widget_display_callback', array( $this, 'widget_display_callback' ), 10, 3);
        //}
    }
    public function widget_display_callback($instance, $widget, $args)
    {
        // Don't return the widget
        if (false === $instance || ! is_subclass_of($widget, 'WP_Widget')) {
            return $instance;
        }
        //		if ( false === $instance || ! is_subclass_of( $widget, 'WP_Widget' ) || is_a( $widget, 'WP_Widget_Recent_Posts_multi' ) ) return $instance;
        $custom_css     = trim(isset($instance['css']) ?  $instance['css']  : (isset($instance['custom_css']) ?  $instance['custom_css']   : ''));

        if ($custom_css) {
            echo '<!-- '.$widget->id.' custom css --><style type="text/css">'.self::compress_css(str_replace('$this', '#'.$widget->id, $custom_css)).'</style>';
        }
        return $instance;
    }

    public function widget_update_callback($instance, $new_instance, $old_instance, $widget)
    {
        //		if ( false === $instance || ! is_subclass_of( $widget, 'WP_Widget' ) || is_a( $widget, 'WP_Widget_Recent_Posts_multi' ) ) return $instance;
        if (false === $instance || ! is_subclass_of($widget, 'WP_Widget')) {
            return $instance;
        }
        $instance['custom_css'] = $new_instance['custom_css'];
        unset($instance['css']);

        return $instance;
    }

    public function in_widget_form($widget, $return, $instance)
    {
        if (false === $instance || ! is_subclass_of($widget, 'WP_Widget')) {
            return;
        }
        $custom_css     = isset($instance['css']) ? $instance['css']  : (isset($instance['custom_css']) ? $instance['custom_css']  : '');        //echo '<pre>'.htmlspecialchars(var_export($instance,true)).'</pre>';?>
				<label for="<?php echo $widget->get_field_id('custom_css'); ?>"><?php _e('Custom CSS:'); ?></label>
				<textarea rows="1" onfocus="this.rows = '10';" class="widefat" id="<?php echo $widget->get_field_id('custom_css'); ?>" name="<?php echo $widget->get_field_name('custom_css'); ?>"><?php echo esc_attr($custom_css) ; ?></textarea>
<?php
    }

    public function wp_head()
    {
        global $wp_registered_sidebars;
        foreach ($wp_registered_sidebars as $id=>$sidebar) {
            if (strpos($wp_registered_sidebars[$id]['before_widget'], ' id=')===false) {
                $wp_registered_sidebars[$id]['before_widget'] .= '<span id="%1$s">';
                $wp_registered_sidebars[$id]['after_widget'] = '</span>'.$wp_registered_sidebars[$id]['after_widget'];
            }
        }
    }

    public static function compress_css($buffer)
    {
        return $buffer;
        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*'.'/!', '', $buffer);
        $buffer = preg_replace('/[\r\n\s\t]+/', ' ', $buffer);
        $buffer = preg_replace('/\s?([,:;{}>])\s?/', '\1', $buffer);
        $buffer = str_replace(';}', '}', $buffer);
        return $buffer;
    }
}//class
widget_css::instance();
endif;
