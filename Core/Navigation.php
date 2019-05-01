<?php
namespace TRD\Core;

class Navigation
{
  
  function __construct()
  {

  }

  public function load_style()
  {
    $file = TRD_CORE_PATH.'/scss/navigation.scss';
    if( file_exists( $file ) ){
      $content = file_get_contents( $file );
      echo sprintf('<style type="text/css">%s</style>', $content );
    }
  }

  public function display()
  {
    $this->load_style();
    $file = TRD_CORE_PATH.'/View/navigation.php';
    if( file_exists( $file ) ) require_once $file;
  }
}

?>