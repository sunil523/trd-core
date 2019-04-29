<?php
namespace TRD\Core\Mailchimp;

class Response{

  public $result = null;
  public function __construct( $result )
  {
    $this->result = $result;
  }

}
?>