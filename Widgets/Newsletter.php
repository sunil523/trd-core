<?php
namespace TRD\Widgets;

class Newsletter extends \WP_Widget {

  /**
   * class constructor
   */
  public function __construct() {
    parent::__construct(
      'trd_newsletter',
      'TRD: Newsletter',
      array( 
        'description' => 'Display signup for newsletter form anywhere.',
      )
    );
  }

  /**
   * output the widget content on the front-end
   */
  public function widget( $args, $instance ) {
    if(empty($instance['title'])) $instance['title'] = "TRD news delivered straight to your inbox";
    ?>
    <div class="newsletter widget">
      <h3 class="title"><?php echo $instance['title']; ?></h3>
      <form method="post" action="/newsletter/">
        <input type="email" id="newsletter_email" name="newsletter_email" placeholder="email address">
        <button type="submit">Newsletter sign up</button>
      </form>
    </div>
    <?php
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
      >
    </p>
    <?php
  }

  /**
   * Update the value for the widget
   */
  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['title'] = ( !empty( $new_instance['title'] ) ) ? $new_instance['title'] : '';
    return $instance;
  }
}

?>