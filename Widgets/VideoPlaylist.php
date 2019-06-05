<?php
namespace TRD\Widgets;

class VideoPlaylist extends \WP_Widget
{

  private $placeholder_image_url = '';
  
  /**
   * class constructor
   */
  public function __construct() {
    parent::__construct(
      'trd_videoplaylist',
      'TRD: VideoPlaylist',
      array( 
        'description' => 'Display video playlist anywhere.',
      )
    );
		$this->placeholder_image_url = esc_url( TRD_CORE_URL.'assets/images/placeholder.svg' );
  }

  /**
   * output the widget content on the front-end
   */
  public function widget( $args, $instance ) {
    $title = $instance['title'];
    $args = array(
      'posts_per_page'   => 4,
      'category_name'    => 'trd-video',
      'orderby'          => 'date',
      'order'            => 'DESC',
      'post_type'        => 'post',
      'post_status'      => 'publish',
      'suppress_filters' => true,
    );
    $videos = get_posts( $args );
    if( empty( $videos ) ) return;
    echo '<section class="video-playlist">';
    // Display Cover
    $category = get_term_by( 'slug', 'trd-video', 'category' );
    $link  = esc_url( get_category_link( $category ) );
    $class = 'cover';
    ?>
    <div class="video-playlist-<?php echo $class; ?>">
			<?php echo sprintf( '<a class="video-playlist-%s-title" href="%s">%s</a>', $class, $link, $title ); ?>
      <div id="5cd4a33cb8bad33e88c90801" class="vdb_player vdb_5cd4a33cb8bad33e88c908015c79566e17e0e151b5b9cb90"><script type="text/javascript" src="https://delivery.vidible.tv/jsonp/pid=5cd4a33cb8bad33e88c90801/5c79566e17e0e151b5b9cb90.js?"></script></div>
		</div>
    <?php
    foreach ($videos as $video) {
      $post_id = $video->ID;
			$link    = get_the_permalink( $post_id );
			$title   = get_post_meta( $post_id, 'Featured Image Headline', true );
			// get the original title
			if( empty( $title ) ) $title = get_the_title( $post_id );
			// change image and it's class
			// if ( $i === 0 ) {
			// 	$image_id = get_post_meta( $post_id, 'second_featured_image', true );
			// 	$image    = $this->get_image( $image_id, 'feature-story-cover' );
			// 	$class    = 'cover';
			// } else {
				$image_id = get_post_thumbnail_id( $post_id );
				$image    = $this->get_image( $image_id, 'feature-story-top' );
				$class    = 'top';
			// }
			// Display
			$this->display_story( $title, $image, $link, $class );
		}
		echo '</section>';
  }

  /**
   * output the option form field in admin Widgets screen
   */
  public function form( $instance ) {
    $title = ! empty( $instance['title'] ) ? $instance['title'] : '';
    ?>
    <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
        <?php esc_attr_e( 'Title', 'trd' ); ?>
      </label>
      <input 
        class="widefat" 
        id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
        name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" 
        type="text" 
        value="<?php echo esc_attr( $title ); ?>"
        required
      >
    </p>
    <?php
  }

  /**
   * Update the value for the widget
   */
  public function update( $new_instance, $old_instance ) {
    $instance = array('title' => 'Videos');
    return array_merge( $instance, $new_instance );
  }
  
  /**
	 * Display cover story for given post
	 * @return void
	 */
	private function display_story( $title, $image, $link, $class='top' )
	{
    $link = esc_url( $link );
		?>
		<div class="video-playlist-<?php echo $class; ?> trd-video">
			<?php echo sprintf( '<a class="video-playlist-%s-title" href="%s">%s</a>', $class, $link, $title ); ?>
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