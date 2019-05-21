<?php
namespace TRD\Core;

class Newsletter
{
  public function __construct( $list_id = '6e806bb87a' ) {
    $this->list_id = $list_id;
    $list = new Mailchimp\Lists( $this->list_id );
    $this->interests = $list->get_list_interests();
  }

  public static function Actions()
  {
    Ajax::Add_Action( array( __CLASS__, 'Subscribe' ) );
    Ajax::Add_Action( array( __CLASS__, 'Widget_Form' ) );
  }

  public static function Widget_Form( $ajax = true )
  {
    if( $ajax ){
      global $wpdb;
      $security = $_POST['security'];
      if( $security !== md5( 'trd-wp-ajax' ) ){
        $message = array('success' => false, 'message' => 'You are not allowed.');
      }
      self::Short_Form();
      wp_die();
    } else{ 
      self::Short_Form();
    }
  }

  public static function Short_Form()
  {
    $self = new self;
    echo '<section class="newsletter-slide">';
    echo '<div class="newsletter-slide-container">';
    $self->widget_title();
    $self->display( 'widget' );
    echo '</div>';
    echo '</section>';
  }

  /**
   * @see https://codex.wordpress.org/AJAX_in_Plugins
   */
  public static function Subscribe()
  {
    global $wpdb;
    $security = $_POST['security'];
    if( $security !== md5( 'trd-wp-ajax' ) ){
      $message = array('success' => false, 'message' => 'You are not allowed.');
    }
    else if( empty($_POST['data']) ){
      $message = array('success' => false, 'message' => 'No data send.');
    } else {
      $data = self::sanatize_data( $_POST['data'] );
      $list_id = $data['list_id'];
      unset($data['list_id']);
      $mc = new Mailchimp\Subscribe( $list_id, $data );
      $result = $mc->subscribed();
      if( $result ){
        $message = array('success' => true, 'message' => 'You are subscribed.');
      } else{
        $message = array('success' => false, 'message' => 'Error subscribing.');
      }
    }
    header('Content-Type: application/json');
    echo json_encode($message);
    wp_die();
  }

  public static function sanatize_data( $data )
  {
    if( empty($data) ) return array();
    $fields = array(
      'list_id'       => $data['newsletter'],
      'email_address' => $data['email'],
      'interests'     => array(),
      'merge_fields'  => array(
        'FNAME' =>   $data['firstname'],
        'LNAME' =>   $data['lastname'],
        'COMPANY' => $data['company'],
      ),
    );
    unset($data['newsletter']);
    unset($data['firstname']);
    unset($data['lastname']);
    unset($data['company']);
    unset($data['email']);
    foreach ($data as $key => $value) {
      $data[ $key ] = ( $value === 'true');
    }
    $fields['interests'] = $data;

    return $fields;
  }

  private function set_fields()
  {
    return array(
      array(
        'label' => 'Email Address',
        'name' => 'email',
        'type' => 'email',
        'value' => '',
        'required' => true,
      ),
      array(
        'label' => 'First Name',
        'name' => 'firstname',
        'type' => 'text',
        'value' => '',
        'required' => false,
      ),
      array(
        'label' => 'Last Name',
        'name' => 'lastname',
        'type' => 'text',
        'value' => '',
        'required' => false,
      ),
      array(
        'label' => 'Company',
        'name' => 'company',
        'type' => 'text',
        'value' => '',
        'required' => false,
      ),
    );
  }

  public function widget_title()
  {
    ?>
    <p class="newsletter-form-title">Please select the newsletter(s) you'd like to receive for the latest real estate news and analysis.</p>
    <?php
  }

  public function display( $place='page' )
  {
    $fields = $this->set_fields();
    $this->load_style();
    ?>
    <form class="newsletter-form <?php echo $place; ?>" method="post" action="">
      <div class="newsletter-form-container">
        <div class="newsletter-form-col fields">
          <?php $this->display_fields( $fields ); ?>
        </div>
        <div class="newsletter-form-col break"></div>
        <div class="newsletter-form-col interests">
          <?php
            $this->display_section( '', array( array(
              'id'    => 'subscribe_all',
              'name'  => 'Subscribe to All',
              'value' => 'yes'
            ) ) );
            $this->display_section( 'Daily Newsletters', $this->interests['daily'] );
            $this->display_section( 'Weekly Newsletters', $this->interests['weekly'] );
            $this->display_section( 'TRData Updates (Weekly)', $this->interests['trdata'] );
          ?>
        </div>
      </div>
      <div class="newsletter-form-button">
        <input type="hidden" name="newsletter" value="<?php echo $this->list_id; ?>"> 
        <?php if( $place == 'widget' ){ ?>
          <button type="reset" class="newsletter-form-close-btn">Close</button>
        <?php } ?>
        <button type="submit">Subscribe</button>
      </div>
      <div class="newsletter-form-message">
        <div class="newsletter-form-success">Thank you! You are now subscribed to newsletter.</div>
        <div class="newsletter-form-error">We are having some technical difficulties. Try again later.</div>
      </div>
    </form>
    <?php
  }

  private function display_fields( $fields )
  {
    foreach ($fields as $field) {
      $id = 'newsletter_'.$field['name'];
      ?>
      <div class="input-field">
        <label for="<?php $id; ?>"><?php echo $field['label']; ?></label>
        <input type="<?php echo $field['type']; ?>" name="<?php echo $field['name']; ?>" id="<?php echo $id; ?>" value="<?php echo $field['value']; ?>" <?php echo $field['required'] ? "required" : ''; ?>>
      </div>
      <?php
    }
  }

  private function display_section( $title, $fields )
  {
    if( !empty($title) ) { ?>
      <h4><?php echo $title; ?></h4>
    <?php } ?>
    <div class="checkboxes"><?php $this->display_checkboxes( $fields ); ?></div>
    <?php
  }

  private function display_checkboxes( $fields )
  {
    foreach( $fields as $field ) {
      $id   = 'newsletter_'.$field['id'];
      $name = $field['name'];
      $field['value'] = 'yes';
      ?>
      <div class="checkbox-field" id="<?php echo $field['id']; ?>">
        <input type="checkbox" name="<?php echo $field['id']; ?>" id="<?php echo $id; ?>" value="<?php echo $field['id']; ?>" <?php echo (empty($field['value']) ? '' : 'checked' ); ?>>
        <label for="<?php echo $id; ?>"><?php echo $field['name']; ?></label>
      </div>
      <?php
    }
  }

  public function load_style()
  {
    $file = '/assets/css/newsletter.min.css';
    $path = get_template_directory().$file;
    if( file_exists( $path ) ){
      if( getenv('WP_ENV') == 'local' ){
        echo sprintf('<link rel="stylesheet" href="%s%s">', get_template_directory_uri(), $file);
      } else {
        echo sprintf('<style type="text/css">%s</style>', file_get_contents( $path ) );
      }
    }
  }
}
