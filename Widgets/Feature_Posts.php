<?php
namespace TRD\Widgets;

class Feature_Posts extends \WP_Widget {

	private $posts      = array(); // cache the posts we query
	private $count      = 5;       // total post for the widget
	private $limit      = 100;     // total post to get from query
	private $placeholder_image_url = '';

	/**
	 * class constructor
	 */
	public function __construct()
	{
		parent::__construct(
			'trd_weidgets_feature_posts',
			'TRD: Feature Posts',
			array(
				'description' => 'Feature posts on the page.',
			)
		);
		$this->posts = $this->get_posts();
		$this->placeholder_image_url = esc_url( TRD_CORE_URL.'assets/images/placeholder.svg' );
	}

	/**
 	 * output the widget content on the front-end
	 */
	public function widget( $args, $instance )
	{
		if( empty( $instance ) || empty( $instance[ 'post_ids' ] ) ) return;
		// Display
		$post_ids = $instance[ 'post_ids' ];
		$video_tags = array('videos', 'video', 'trd-videos', 'trd-video');
		echo '<section class="feature-posts">';
		for ( $i = 0, $n = count( $post_ids ); $i < $n; $i++ ) {
			$post_id = $post_ids[ $i ];
			$link    = get_the_permalink( $post_id );
			$title   = get_post_meta( $post_id, 'Featured Image Headline', true );
			$video   = ( has_category( $video_tags, $post_id ) || has_tag( $video_tags, $post_id ) );
			// get the original title
			if( empty( $title ) ) $title = get_the_title( $post_id );
			// change image and it's class
			if ( $i === 0 ) {
				$image_id = get_post_meta( $post_id, 'second_featured_image', true );
				$image    = $this->get_image( $image_id, 'feature-story-cover' );
				$class    = 'cover';
			} else {
				$image_id = get_post_thumbnail_id( $post_id );
				$image    = $this->get_image( $image_id, 'feature-story-top' );
				$class    = 'top';
			}
			// Display
			$this->display_story( $title, $image, $link, $video, $class );
		}
		$nl = new \TRD\Widgets\Newsletter();
		$nl->widget( array(), array('name' => 'homepage_feature_posts', 'theme' => true ) );
		echo '</section>';
	}

	/**
	 * output the option form field in admin Widgets screen
	 */
	public function form( $instance )
	{
			$post_ids = ! empty( $instance['post_ids'] ) ? $instance['post_ids'] : array();
			for ( $i=0; $i<$this->count; $i++ ) {
					?>
					<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'post_ids_'.$i ) ); ?>">
					<?php esc_attr_e( 'Post #'.($i+1), 'trd' ); ?>
					</label>

					<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_ids'.$i ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_ids' ) ); ?>[]" required>
							<?php $this->build_options( $this->posts, $post_ids[ $i ] ); ?>
					</select>
					</p>
					<?php
			}
			?>
			<!-- <p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'newsletter' ) ); ?>">
							<input class="widefat" type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'newsletter' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'newsletter' ) ); ?>" <?php echo $instance['newsletter'] ? 'checked' : ''; ?>>
							<?php esc_attr_e( 'Display newsletter form?', 'trd' ); ?>
					</label>
			</p> -->
			<?php
	}

/**
	 * Update the value for the widget
	 */
	public function update( $new_instance, $old_instance )
	{
			$instance = array();
			$instance['post_ids']   = empty( $new_instance['post_ids'] ) ? array() : $new_instance['post_ids'];
			$instance['newsletter'] = empty( $new_instance['newsletter'] ) ? false : true;
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
					'post_type'        => 'post',
					'post_status'      => 'publish',
					'suppress_filters' => true,
					// 'date_query' => array(
					//     array(
					//         'before'    => date( 'Y-m-d', strtotime('-2 days') ),
					//         'after'     => date('Y-m-d'),
					//         'inclusive' => true,
					//     ),
					// ),
			);
			return get_posts( $args );
	}

	/**
	 * Build option tag for select tags
	 * @return void
	 */
	private function build_options( $options, $value = '' )
	{
		if ( empty( $options ) ) echo '<option value="" disabled>--no post found--</option>';
		echo '<option value="" disabled>--Select a Post--</option>';
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