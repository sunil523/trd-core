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
    Ajax::Add_Action( array( __CLASS__, 'Slide_Form' ) );
  }

  public static function Slide_Form( $ajax = true )
  {
    if( $ajax ){
      global $wpdb;
      $security = $_POST['security'];
      if( $security !== md5( 'trd-wp-ajax' ) ){
        $message = array('success' => false, 'message' => 'You are not allowed.');
      }
      self::Slide_Display();
      wp_die();
    } else{ 
      self::Slide_Display();
    }
  }

  public static function Slide_Display()
  {
    $self = new self;
    echo '<section class="newsletter-slide">';
    echo '<div class="newsletter-slide-container">';
    echo '<button class="newsletter-slide-close-btn"><i class="fa fa-close fa-times"></i></button>';
    echo '<h2 class="newsletter-form-title">Sign up for T<span class="trd-color">R</span>D news!</h2>';
    $self->display( 'slide' );
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
        'FNAME'   => $data['firstname'],
        'LNAME'   => $data['lastname'],
        'COMPANY' => $data['company'],
        'MERGE3'  => $data['place']
      ),
    );
    unset($data['newsletter']);
    unset($data['firstname']);
    unset($data['lastname']);
    unset($data['company']);
    unset($data['email']);
    unset($data['place']);
    foreach ($data as $key => $value) {
      $data[ $key ] = ( $value === 'true');
    }
    $fields['interests'] = $data;

    return $fields;
  }

  private function set_fields()
  {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    return array(
      array(
        'label' => 'Email Address',
        'name' => 'email',
        'type' => 'email',
        'value' => $email,
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

  public function display( $place='page' )
  {
    $fields = $this->set_fields();
    $this->load_style();
    ?>
    <form class="newsletter-form <?php echo $place; ?>" method="post" action="">
      <div class="newsletter-form-container">
        <div class="newsletter-form-col fields">
          <?php $this->display_fields( $fields ); ?>
          <div class="break"></div>
          <div class="newsletter-form-footer">
            <button type="submit" class="newsletter-form-button newsletter-form-submit">Subscribe</button>
            <div class="newsletter-form-success">You are now subscribed.</div>
            <div class="newsletter-form-error">We are having some technical difficulties. Try again later.</div>
            <p class="newsletter-form-agree">By clicking <em>Subscribe</em> you agree to our <a href="/privacy-policy/" target="_blank">Privacy Policy</a>.</p>
          </div>
        </div>
        <div class="newsletter-form-col break"></div>
        <div class="newsletter-form-col interests">
          <?php
            $this->display_section( 'Daily Newsletters', $this->interests['daily'] );
            $this->display_section( 'Weekly Newsletters', $this->interests['weekly'] );
            $this->display_section( 'TRData Updates (Weekly)', $this->interests['trdata'] );
          ?>
        </div>
      </div>
      <input type="hidden" name="newsletter" value="<?php echo $this->list_id; ?>"> 
      <input type="hidden" name="place" value="website_<?php echo ($place === 'page') ? 'landing_page' : $place; ?>">
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
