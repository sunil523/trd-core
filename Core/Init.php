<?php
namespace TRD\Core;

class Init
{
  public function __construct() {
    $this->register();
    $this->actions();
  }

  public function register()
  {
    CrossPost::Register();
    Navigation::Register();
  }

  public function actions()
  {
    add_action( 'wp_enqueue_scripts', array( __CLASS__, 'Styles' ), 5 );
    add_action( 'wp_enqueue_scripts', array( __CLASS__, 'Scripts' ), 5 );
    Newsletter::Actions();
  }

  public static function Styles()
  {
    wp_enqueue_style(
      'trd-core-style',
      TRD_CORE_URL.'assets/css/trd-core.min.css',
      false,
      filemtime( TRD_CORE_PATH.'assets/css/trd-core.min.css' )
    );
  }

  // Setup the Scritps
  public static function Scripts()
  {
    wp_enqueue_script(
      'trd-core-script',
      TRD_CORE_URL.'assets/js/trd-core.min.js',
      array('jquery', 'trd-parent-script'),
      filemtime( TRD_CORE_PATH.'assets/js/trd-core.min.js' ),
      false
    );
  }
}
?>