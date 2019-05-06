<?php
namespace TRD\Core;

class Init
{
  public function __construct() {
    $this->register();
  }

  public function register()
  {
    CrossPost::Register();
    Navigation::Register();
  }
}
