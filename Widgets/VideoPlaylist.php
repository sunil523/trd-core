<?php
namespace TRD\Widgets;

class VideoPlaylist extends \WP_Widget
{
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
  }

  /**
   * output the widget content on the front-end
   */
  public function widget( $args, $instance ) {
    $title = $instance['title'];
    echo '<section class="video-playlist">';
		for ( $i = 0, $n = count( $post_ids ); $i < $n; $i++ ) {
			$post_id = $post_ids[ $i ];
			$link    = get_the_permalink( $post_id );
			$title   = get_post_meta( $post_id, 'Featured Image Headline', true );
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
			$this->display_story( $title, $image, $link, $class );
		}
		$nl = new \TRD\Widgets\Newsletter();
		$nl->widget( array(), array('name' => 'homepage_feature_posts', 'theme' => true ) );
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
    </p
    <?php
  }

  /**
   * Update the value for the widget
   */
  public function update( $new_instance, $old_instance ) {
    $instance = array('title' => 'Videos');
    return array_merge( $instance, $new_instance );
  }

}

?>