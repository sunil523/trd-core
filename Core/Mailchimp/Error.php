<?php
namespace TRD\Core\Mailchimp;

class Error{

  public $result = null;
  function __construct( $result )
  {
    $this->result = $result;
  }
}
?>