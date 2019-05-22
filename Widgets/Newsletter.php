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
    if( empty( $instance['name'] ) ) return '';
    $title = empty( $instance['title'] ) ? 'T<span class="trd-color">R</span>D News delivered straight to your inbox' : $instance['title'];
    $classes = array('newsletter', 'widget-embed');
    if ( !empty( $instance['theme'] ) ) array_push($classes, 'widget-dark');
    if ( !empty( $instance['size'] ) ) array_push($classes, 'widget-large');
    if ( !empty( $instance['fixed'] ) ) array_push($classes, 'widget-fixed');
    // $this->load_style();
    ?>
    <div class="<?php echo implode(' ', $classes ); ?>">
      <div class="widget-embed-header">
        <h3 class="widget-embed-title"><?php echo $title; ?></h3>
        <div class="widget-embed-close"><i class="fa fa-close fa-times"></i></div>
      </div>
      <?php if( !empty( $instance['description'] ) ) { ?> 
        <p class="widget-embed-description"><?php echo $instance['description']; ?></p>
      <?php } ?>
      <form method="post" class="widget-embed-form" action="/newsletter/" name="<?php echo $instance['name']; ?>">
        <input type="email" id="email" name="email" placeholder="Email Address">
        <button type="submit" class="trd-color-btn">sign up</button>
      </form>
    </div>
    <?php
  }

  /**
   * output the option form field in admin Widgets screen
   */
  public function form( $instance ) {
    $name = ! empty( $instance['name'] ) ? $instance['name'] : '';
    $title = ! empty( $instance['title'] ) ? $instance['title'] : '';
    $description = ! empty( $instance['description'] ) ? $instance['description'] : '';
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
    <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'name' ) ); ?>">
        <?php esc_attr_e( 'Name', 'trd' ); ?>
      </label>
      <input 
        class="widefat" 
        id="<?php echo esc_attr( $this->get_field_id( 'name' ) ); ?>" 
        name="<?php echo esc_attr( $this->get_field_name( 'name' ) ); ?>" 
        type="text" 
        value="<?php echo esc_attr( $name ); ?>"
        required
      >
    </p>
    <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>">
        <?php esc_attr_e( 'Description', 'trd' ); ?>
      </label>
      <textarea 
        class="widefat" 
        id="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>" 
        name="<?php echo esc_attr( $this->get_field_name( 'description' ) ); ?>"
        row="5"
      ><?php echo esc_attr( $description ); ?></textarea>
    </p>
    <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'theme' ) ); ?>">
        <input 
          type="checkbox" 
          id="<?php echo esc_attr( $this->get_field_id( 'theme' ) ); ?>" 
          name="<?php echo esc_attr( $this->get_field_name( 'theme' ) ); ?>" 
          <?php echo $instance['theme'] ? 'checked' : ''; ?>
        >
        <?php esc_attr_e( 'Dark Theme', 'trd' ); ?>
      </label>
      <label for="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>">
        <input 
          type="checkbox" 
          id="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>" 
          name="<?php echo esc_attr( $this->get_field_name( 'size' ) ); ?>" 
          <?php echo $instance['size'] ? 'checked' : ''; ?>
        >
        <?php esc_attr_e( 'Large Size', 'trd' ); ?>
      </label>
      <label for="<?php echo esc_attr( $this->get_field_id( 'fixed' ) ); ?>">
        <input 
          type="checkbox" 
          id="<?php echo esc_attr( $this->get_field_id( 'fixed' ) ); ?>" 
          name="<?php echo esc_attr( $this->get_field_name( 'fixed' ) ); ?>" 
          <?php echo $instance['fixed'] ? 'checked' : ''; ?>
        >
        <?php esc_attr_e( 'Fixed to right.', 'trd' ); ?>
      </label>
    </p>
    <?php
  }

  /**
   * Update the value for the widget
   */
  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['name'] = ( !empty( $new_instance['name'] ) ) ? $new_instance['name'] : '';
    $instance['title'] = ( !empty( $new_instance['title'] ) ) ? $new_instance['title'] : '';
    $instance['description'] = ( !empty( $new_instance['description'] ) ) ? $new_instance['description'] : '';
    $instance['theme'] = !empty( $new_instance['theme'] );
    $instance['size'] = !empty( $new_instance['size'] );
    $instance['fixed'] = !empty( $new_instance['fixed'] );
    return $instance;
  }

  public function load_style()
  {
    $file = '/assets/css/newsletter.min.css';
    $path = TRD_CORE_PATH.$file;
    if( file_exists( $path ) ){
      if( getenv('WP_ENV') == 'local' ){
        echo sprintf('<link rel="stylesheet" href="%s%s">', TRD_CORE_URL, $file);
      } else {
        echo sprintf('<style type="text/css">%s</style>', file_get_contents( $path ) );
      }
    }
  }

  public function load_script()
  {
    // $file = get_template_directory().'/js/_newsletter.js';
    // if( file_exists( $file ) ){
    //   $content = file_get_contents( $file );
    //   echo sprintf('<script type="text/javascript">%s</script>', $content );
    // }
  }
}

?>