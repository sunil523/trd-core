<?php
namespace TRD\Components;

class Footer
{
  
  function __construct(){}

  public function display()
  {
    $file = TRD_CORE_PATH.'/View/footer.php';
    if( file_exists( $file ) ) require_once $file;
  }
}

?>