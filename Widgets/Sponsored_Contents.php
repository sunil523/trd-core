<?php
namespace TRD\Widgets;

class Sponsored_Contents extends \WP_Widget {

	private $posts      = array(); // cache the posts we query
	private $count      = 4;       // total post for the widget
	private $limit      = 100;     // total post to get from query
	private $placeholder_image_url = '';

	/**
	 * class constructor
	 */
	public function __construct()
	{
		parent::__construct(
			'trd_weidgets_sponsored_contents',
			'TRD: Sponsored Contents',
			array(
				'description' => 'Feature sponsored content on the homepage.',
			)
		);
		$this->posts = $this->get_posts();
		$this->orders = $this->get_orders();
		$this->placeholder_image_url = esc_url( TRD_CORE_URL.'assets/images/placeholder.svg' );
	}

	/**
 	 * output the widget content on the front-end
	 */
	public function widget( $args, $instance )
	{
		global $post;
		if( empty( $instance ) || empty( $instance[ 'post_ids' ] ) ) return;
		if( empty( $instance ) || empty( $instance[ 'post_orders' ] ) ) return;
		// Display
		$post_ids = $instance[ 'post_ids' ];
		$post_orders = $instance[ 'post_orders' ];
		for ( $i = 0, $n = count( $post_ids ); $i < $n; $i++ ) {
			$post_id = $post_ids[ $i ];
			if( empty( $post_id ) ) continue;
			$post = get_post( $post_id );
			$post_order = $post_orders[ $i ];
			$article = new \TRD\Core\Components\ArticleList( array('sponsored'), array( 'data-order' => $post_order ) );
			$article->display();
		}
	}

	/**
	 * output the option form field in admin Widgets screen
	 */
	public function form( $instance )
	{
			$post_ids = ! empty( $instance['post_ids'] ) ? $instance['post_ids'] : array();
			$post_orders = ! empty( $instance['post_orders'] ) ? $instance['post_orders'] : array();
			for ( $i=0; $i<$this->count; $i++ ) {
					?>
					<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'post_ids_'.$i ) ); ?>">
					<?php esc_attr_e( 'Sponsored Content #'.($i+1), 'trd' ); ?>
					</label>
					<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_ids'.$i ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_ids' ) ); ?>[]" required>
							<?php $this->build_options( $this->posts, $post_ids[ $i ] ); ?>
					</select>
					</p>
					<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'post_orders_'.$i ) ); ?>">
					<?php esc_attr_e( 'Sponsored Order #'.($i+1), 'trd' ); ?>
					</label>
					<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_orders'.$i ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_orders' ) ); ?>[]" required>
							<?php $this->build_options( $this->orders, $post_orders[ $i ] ); ?>
					</select>
					</p>
					<?php
			}
	}

/**
	 * Update the value for the widget
	 */
	public function update( $new_instance, $old_instance )
	{
			$instance = array();
			$instance['post_ids']    = empty( $new_instance['post_ids'] ) ? array() : $new_instance['post_ids'];
			$instance['post_orders'] = empty( $new_instance['post_orders'] ) ? array() : $new_instance['post_orders'];
			return $instance;
	}

	/**
	 * PRIVATE METHODS
	 */

	/**
	 * Get posts for users to select from
	 * @return array  of WP_Post Objects
	 */
	private function get_posts()
	{
			$args = array(
					'posts_per_page'   => $this->limit,
					'orderby'          => 'date',
					'order'            => 'DESC',
					'post_type'        => 'page',
					'post_status'      => 'publish',
					'suppress_filters' => true,
					'meta_key'         => '_wp_page_template',
    			'meta_value'       => array('page-sponsored-content.php', 'page-sponsored.php')
			);
			return get_posts( $args );
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

	/**
	 * Display cover story for given post
	 * @return void
	 */
	private function display_story( $title, $image, $link, $video, $class='top' )
	{
		$video_class = $video ? 'trd-video' : '';
		?>
		<div class="feature-posts-<?php echo $class.' '.$video_class; ?>">
			<?php echo sprintf( '<a class="feature-posts-%s-title" href="%s">%s</a>', $class, $link, $title ); ?>
			<figure><?php 
				echo sprintf( '<a href="%s">%s</a>', $link, $image['tag'] );
				if( ! empty( $image['caption'] ) && $class != 'top' ) {
					echo sprintf( '<figcaption>%s</figcaption>', $image['caption'] );
				}
			?></figure>
		</div>
		<?php
	}

	private function get_image( $image_id, $size )
	{
		// get the image size
		$image = wp_get_attachment_image_src( $image_id, $size, false );
		if( ! empty( $image ) ) {
			$image = array(
				'url'    => $image[0],
				'width'  => $image[1],
				'height' => $image[2],
				'alt'    => get_post_meta( $image_id, '_wp_attachment_image_alt', true ),
			);
			$image_caption = wp_get_attachment_caption( $image_id );
			return $this->image_setup( $image, $image_caption );
		}
		// get original image
		$image_url = wp_get_attachment_url( $image_id );
		if( ! empty( $image_url ) ) {
			$image_caption = wp_get_attachment_caption( $image_id );
			$image = array(
				'url'    => $image_url,
				'alt'    => get_post_meta( $image_id, '_wp_attachment_image_alt', true ),
				'width'  => 690,
				'height' => 430,
			);
			return $this->image_setup( $image, $image_caption );
		}
		// get placeholder image
		return $this->image_setup();		
	}

	private function image_setup( $image = array(), $caption = '' )
	{
		$default = array(
			'url'    => $this->placeholder_image_url,
			'alt'    => 'Placeholder image',
			'width'  => 690,
			'height' => 430,
		);
		$image = array_merge( $default, $image );
		return array(
			'caption' => $caption,
			'tag'     => sprintf(
				'<img class="lazyload" src="%s" data-src="%s" alt="%s" width="%s" height="%s" />', 
				$this->placeholder_image_url,
				esc_url( $image['url'] ),
				esc_attr( $image['alt'] ),
				$image['width'],
				'auto'
			)
		);
	}
}

?>