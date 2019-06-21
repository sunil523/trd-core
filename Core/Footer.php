<?php
namespace TRD\Core;

class Footer
{
  
  function __construct()
  {

  }

  public static function Register()
  {
    add_action( 'after_setup_theme', array( __CLASS__, 'Register_Location') );
  }

  public static function Register_Location()
  {
    register_nav_menus( array(
      'footer_primary' => 'Footer Primary',
      'footer_social'  => 'Footer Social',
    ) );
  }

  public function display()
  {
    $file = TRD_CORE_PATH.'/View/footer.php';
    if( file_exists( $file ) ) require_once $file;
  }
}

?>