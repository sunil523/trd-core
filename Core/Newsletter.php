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

  public function display()
  {
    $title = "Please select the newsletter(s) you'd like to receive for the latest real estate news and analysis.";
    $fields = array(
      array(
        'label' => 'Email Address',
        'name' => 'email',
        'type' => 'email',
        'value' => ''
      ),
      array(
        'label' => 'First Name',
        'name' => 'firstname',
        'type' => 'text',
        'value' => ''
      ),
      array(
        'label' => 'Last Name',
        'name' => 'lastname',
        'type' => 'text',
        'value' => ''
      ),
      array(
        'label' => 'Company',
        'name' => 'company',
        'type' => 'text',
        'value' => ''
      ),
    );
    $this->display_style();
    ?>
    <form class="newsletter" method="post" action="">
      <p><?php echo $title; ?></p>
      <?php $this->display_fields( $fields ); ?>
      <hr>
      <?php
        $this->display_section( 'Daily Newsletters', $this->interests['daily'] );
        $this->display_section( 'Weekly Newsletters', $this->interests['weekly'] );
        $this->display_section( 'TRData Updates (Weekly)', $this->interests['trdata'] );
      ?>
      <?php 
        /*$this->display_section( 'Which region(s)?', $this->regions );
        $this->display_section( 'How often?', $this->frequency ); */
      ?>
      <button type="submit">Subscribe</button>
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

  private function display_style()
  {
    $path = TRD_CORE_PATH.'scss/newsletter.scss';
    $content = file_get_contents( $path );
    echo '<style type="text/css">'.$content.'</style>';
  }
}
