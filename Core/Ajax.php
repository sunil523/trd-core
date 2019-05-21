<?php
namespace TRD\Core;

class Ajax
{
  function __construct()
  {
  }

  public static function Add_Action( $method )
  {
    if ( is_array( $method ) )
    {
      $action = $method;
      $action[0] = ( new \ReflectionClass($action[0]) )->getShortName();
      $action = implode( '_', $action );
    }
    $action = strtolower( $action );
    add_action( 'wp_ajax_'.$action,        $method );
    add_action( 'wp_ajax_nopriv_'.$action, $method );
  }

}

?>