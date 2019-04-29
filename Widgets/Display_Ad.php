<?php
namespace TRD\Widgets;

class Display_Ad extends \WP_Widget {

	
	/**
     * class constructor
     */
	public function __construct() {
        parent::__construct(
            'trd_display_ad',
            'TRD: Display Ad',
            array( 
                'description' => 'Display Ad without any code.',
            )
        );
    }
	
	/**
	 * output the widget content on the front-end
     */
	public function widget( $args, $instance ) {
        if(empty($instance['ad_id'])) return '';
        $ad_id = $instance['ad_id'];
        $style = empty($instance['style']) ? '' : $instance['style'];
        ?>
        <div id="<?php echo $ad_id; ?>" style="<?php echo $style; ?>">
        <script type="text/javascript">doDisplay("<?php echo $ad_id; ?>");</script>
        </div>
        <?php
    }

	/**
     * output the option form field in admin Widgets screen
     */
	public function form( $instance ) {
        $ad_id = ! empty( $instance['ad_id'] ) ? $instance['ad_id'] : '';
        $style = ! empty( $instance['style'] ) ? $instance['style'] : '';
        ?>
        <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'ad_id' ) ); ?>">
        <?php esc_attr_e( 'Div ID:', 'trd' ); ?>
        </label> 
        
        <input 
            class="widefat" 
            id="<?php echo esc_attr( $this->get_field_id( 'ad_id' ) ); ?>" 
            name="<?php echo esc_attr( $this->get_field_name( 'ad_id' ) ); ?>" 
            type="text" 
            value="<?php echo esc_attr( $ad_id ); ?>"
            required
        >
        </p>
        <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>">
        <?php esc_attr_e( 'Inline Style:', 'trd' ); ?>
        </label> 
        
        <input 
            class="widefat" 
            id="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>" 
            name="<?php echo esc_attr( $this->get_field_name( 'style' ) ); ?>" 
            type="text" 
            value="<?php echo esc_attr( $style ); ?>"
        >
        </p>
        <?php
    }

	/**
     * Update the value for the widget
     */
	public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['ad_id'] = ( ! empty( $new_instance['ad_id'] ) ) ? strip_tags( $new_instance['ad_id'] ) : '';
        $instance['style'] = ( ! empty( $new_instance['style'] ) ) ? strip_tags( $new_instance['style'] ) : '';
        return $instance;
    }
}

?>