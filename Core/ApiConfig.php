<?php
namespace TRD\Core;

class ApiConfig
{

  const ENV_LOCAL = 'local';
  const ENV_BETA  = 'beta';
  const ENV_LIVE  = 'live';

  protected $api_url   = 'wp-json/wp/v2';
  protected $site_root = null;
  protected $apis      = array();
  protected $env       = null;
  protected $api_user  = null;
  protected $api_pass  = null;

  private $envs = array( self::ENV_LOCAL, self::ENV_BETA, self::ENV_LIVE );

  public function __construct()
  {
    $this->site_root = getenv('WP_SITE_ROOT');
    $this->api_user  = getenv('WP_API_USER');
    $this->api_pass  = getenv('WP_API_PASS');
    if( in_array( getenv('WP_ENV'), $this->envs) ) $this->env = getenv('WP_ENV');
    $this->set_apis_endpoints();
  }

  private function set_apis_endpoints()
  {
    $this->apis = array(
      'ny' => array(
        'root' => sprintf('%s/%s', $this->site_root, $this->api_url),
        'crosspost_field' => 'A3_trd_ny',
        'base' => '',
      ),
      'la' => array(
        'root' => sprintf('%s/la/%s', $this->site_root, $this->api_url),
        'crosspost_field' => 'A3_trd_la',
        'base' => '/la',
      ),
      'chicago' => array(
        'root' => sprintf('%s/chicago/%s', $this->site_root, $this->api_url),
        'crosspost_field' => 'A3_trd_chicago',
        'base' => '/chicago',
      ),
      'miami' => array(
        'root' => sprintf('%s/miami/%s', $this->site_root, $this->api_url),
        'crosspost_field' => 'A3_trd_miami',
        'base' => '/miami',
      ),
      'national' => array(
        'root' => sprintf('%s/national/%s', $this->site_root, $this->api_url),
        'crosspost_field' => 'A3_trd_national',
        'base' => '/national',
      ),
      'tristate' => array(
        'root' => sprintf('%s/tristate/%s', $this->site_root, $this->api_url),
        'crosspost_field' => 'A3_trd_tristate',
        'base' => '/tristate',
      ),
    );
  }

}