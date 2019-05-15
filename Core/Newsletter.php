<?php
namespace TRD\Core;

class Newsletter
{
  public function __construct( $list_id = '6e806bb87a', $email='' ) {
    $list = new Mailchimp\Lists( $list_id, $email );
    // $this->regions = $list->get_list_regions();
    // $this->frequency = $list->get_frequency();
    $this->interests = $list->get_list_interests();
  }

  private function set_fields( $email='', $fname='', $lname='', $company='' )
  {
    return array(
      array(
        'label' => 'Email Address',
        'name' => 'email',
        'type' => 'email',
        'value' => $email
      ),
      array(
        'label' => 'First Name',
        'name' => 'firstname',
        'type' => 'text',
        'value' => $fname
      ),
      array(
        'label' => 'Last Name',
        'name' => 'lastname',
        'type' => 'text',
        'value' => $lname
      ),
      array(
        'label' => 'Company',
        'name' => 'company',
        'type' => 'text',
        'value' => $company
      ),
    );
  }

  public function display( $place='page' )
  {
    $title = "Please select the newsletter(s) you'd like to receive for the latest real estate news and analysis.";
    $fields = $this->set_fields();
    $this->load_style();
    ?>
    <form class="newsletter-form <?php echo $place; ?>" method="post" action="">
      <div class="newsletter-form-col fields">
        <?php $this->display_fields( $fields ); ?>
      </div>
      <div class="newsletter-form-col break"></div>
      <div class="newsletter-form-col interests">
        <?php
          $this->display_section( 'Daily Newsletters', $this->interests['daily'] );
          $this->display_section( 'Weekly Newsletters', $this->interests['weekly'] );
          $this->display_section( 'TRData Updates (Weekly)', $this->interests['trdata'] );
        ?>
      </div>
      <div class="newsletter-form-col button">
        <button type="submit">Subscribe</button>
      </div>
    </form>
    <?php
    $this->load_script();
  }

  private function display_fields( $fields )
  {
    foreach ($fields as $field) {
      $id = 'newsletter_'.$field['name'];
      ?>
      <div class="input-field">
        <label for="<?php $id; ?>"><?php echo $field['label']; ?></label>
        <input type="<?php echo $field['type']; ?>" name="<?php echo $field['name']; ?>" id="<?php echo $id; ?>" value="<?php echo $field['value']; ?>">
      </div>
      <?php
    }
  }

  private function display_section( $title, $fields )
  {
    ?>
    <h4><?php echo $title; ?></h4>
    <div class="checkboxes"><?php $this->display_checkboxes( $fields ); ?></div>
    <?php
  }

  private function display_checkboxes( $fields )
  {
    foreach( $fields as $field ) {
      $id   = 'newsletter_'.$field['id'];
      $name = $field['name'];
      ?>
      <div class="checkbox-field">
        <input type="checkbox" name="<?php echo $field['id']; ?>" id="<?php echo $id; ?>" value="<?php echo $field['id']; ?>" <?php echo (empty($field['value']) ? '' : 'checked' ); ?>>
        <label for="<?php echo $id; ?>"><?php echo $field['name']; ?></label>
      </div>
      <?php
    }
  }

  public function load_style()
  {
    $file = 'assets/css/newsletter.min.css';
    $path = TRD_CORE_PATH.'/'.$file;
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
    $file = TRD_CORE_PATH.'/js/_newsletter.js';
    if( file_exists( $file ) ){
      $content = file_get_contents( $file );
      echo sprintf('<script type="text/javascript">%s</script>', $content );
    }
  }
}
