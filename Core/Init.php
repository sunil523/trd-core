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
    add_action( 'wp_enqueue_scripts', array( __CLASS__, 'Styles' ) );
    add_action( 'wp_enqueue_scripts', array( __CLASS__, 'Scripts' ) );
    Newsletter::Actions();
  }

  public static function Styles()
  {
    wp_enqueue_style(
      'trd-core-style',
      TRD_CORE_URL.'/assets/css/trd-core.min.css',
      false,
      '1.1.0'
    );
  }

  // Setup the Scritps
  public static function Scripts()
  {
    wp_enqueue_script(
      'trd-core-script',
      TRD_CORE_URL.'/assets/js/trd-core.min.js',
      array('jquery'),
      '1.0.0',
      true
    );
    wp_localize_script(
      'trd-core-script',
      'trd_ajax',
      array(
        'url'      => admin_url( 'admin-ajax.php' ),
        'security' => md5( 'trd-wp-ajax' )
      )
    );
  }
}
