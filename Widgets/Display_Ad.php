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
		$this->orders = $this->get_orders();
	}
	
	/**
	 * output the widget content on the front-end
	 */
	public function widget( $args, $instance ) {
		if(empty($instance['ad_id'])) return '';
		$ad_id = $instance['ad_id'];
		$style = empty($instance['style']) ? '' : $instance['style'];
		$order = empty($instance['order']) ? '' : $instance['order'];
		$class = empty($instance['class']) ? '' : $instance['class'];
		?>
		<div id="<?php echo $ad_id; ?>" class="<?php echo $class; ?>" style="<?php echo $style; ?>" data-order="<?php echo $order; ?>">
		<script type="text/javascript">
			googletag.cmd.push( function() {
				googletag.display( "<?php echo $ad_id; ?>" );
			} );
		</script>
		</div>
		<?php
	}

	/**
	 * output the option form field in admin Widgets screen
	 */
	public function form( $instance ) {
		$ad_id = ! empty( $instance['ad_id'] ) ? $instance['ad_id'] : '';
		$style = ! empty( $instance['style'] ) ? $instance['style'] : '';
		$order = ! empty( $instance['order'] ) ? $instance['order'] : '';
		$class = ! empty( $instance['class'] ) ? $instance['class'] : '';
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
		<label for="<?php echo esc_attr( $this->get_field_id( 'class' ) ); ?>">
		<?php esc_attr_e( 'CSS Class Names:', 'trd' ); ?>
		</label> 
		
		<input 
			class="widefat" 
			id="<?php echo esc_attr( $this->get_field_id( 'class' ) ); ?>" 
			name="<?php echo esc_attr( $this->get_field_name( 'class' ) ); ?>" 
			type="text" 
			value="<?php echo esc_attr( $class ); ?>"
		>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>">
				<?php esc_attr_e( 'Order', 'trd' ); ?>
			</label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>" required>
				<?php $this->build_options( $this->orders, $order ); ?>
			</select>
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
		$instance['style'] = empty( $new_instance['style'] ) ? '' : strip_tags( $new_instance['style'] );
		$instance['ad_id'] = empty( $new_instance['ad_id'] ) ? '' : strip_tags( $new_instance['ad_id'] );
		$instance['class'] = empty( $new_instance['class'] ) ? '' : strip_tags( $new_instance['class'] );
		$instance['order'] = empty( $new_instance['order'] ) ? '' : $new_instance['order'];
		return $instance;
	}

	public function get_orders()
	{
		$orders = array();
		for ($i=1; $i <= 14; $i++) { 
			$order             = new \stdClass;
			$order->ID         = $i;
			$order->post_title = $i;
			array_push( $orders, $order );
		}
		return $orders;
	}
	
	/**
	 * Build option tag for select tags
	 * @return void
	 */
	private function build_options( $options, $value = '' )
	{
		if ( empty( $options ) ) echo '<option value="" disabled>--no post found--</option>';
		echo '<option value="" disabled>--Select your content--</option>';
		echo '<option value="">--No content--</option>';
		foreach ( $options as $post ) {
			$selected = ( $value == $post->ID ) ? 'selected' : '';
			echo sprintf( '<option value="%s" %s>%s</option>', $post->ID, $selected, $post->post_title );
		}
	}
}

?>