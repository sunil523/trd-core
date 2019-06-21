<?php
namespace TRD\Core;

class Navigation
{
  
  function __construct()
  {

  }

  public static function Register()
  {
    add_action( 'after_setup_theme', array( __CLASS__, 'Register_Location') );
    add_action( 'after_setup_theme', array( __CLASS__, 'Create_Navs') );
  }

  public static function Register_Location()
  {
    register_nav_menus( array(
      'header_primary'        => 'Header Primary',
      'header_secondry'       => 'Header Secondry',
      'header_social'         => 'Header Social',
      'header_account_login'  => 'Header My Account Login',
      'header_account_logout' => 'Header My Account Logout',
    ) );
  }

  public static function Create_Navs()
  {
    $navs = array(
      'Nav Regions' => array(
        'location' => 'header_primary',
        'items' => array(
          '/'          => 'New York',
          '/la/'       => 'Los Angeles',
          '/miami/'    => 'South Florida',
          '/chicago/'  => 'Chicago',
          '/national/' => 'National',
          '/tristate/' => 'Tri-State',
        )
      ),
      // 'Nav Sections' => array(
      //   'location' => 'header_secondry',
      //   'items' => array(
      //     '/#news' => 'News',
      //     '/#magazine' => 'Magazine',
      //     '/#research' => 'Research',
      //     '/#events' => 'Events',
      //     '/tag/trd-video/' => 'Video',
      //     // '/#' => 'Listing',
      //   )
      // ),
      'Nav Social' => array(
        'location' => 'header_social',
        'items' => array(
          '/newsletter/' => 'Newsletter',
          'https://www.facebook.com/' => 'Facebook',
          'https://www.twitter.com/' => 'Twitter',
          'https://www.youtube.com/TheRealDealMagazineNewYork' => 'YouTube',
          'https://www.instagram.com/therealdeal' => 'Instagram',
          'https://www.linkedin.com/company/therealdeal' => 'Linkedin',
        )
      ),
      'My Account Login' => array(
        'location' => 'header_account_login',
        'items' => array(
          '/subscribe/'  => 'Subscribe',
          '/#login' => 'My Account',
        )
      ),
      'My Account Logout' => array(
        'location' => 'header_account_logout',
        'items' => array(
          '/#' => 'My Account',
          '/my-account/' => 'Manage My Account',
          '/#logout' => 'Logout',
        )
      ),
    );

    foreach ($navs as $menu_name => $menu) {
      $menu_exists = wp_get_nav_menu_object( $menu_name );
      if( !$menu_exists ){
        $menu_id = wp_create_nav_menu($menu_name);
        // create menu items
        foreach ($menu['items'] as $item_link => $item_name) {
          wp_update_nav_menu_item( $menu_id, 0, array(
            'menu-item-title'  =>  __($item_name),
            'menu-item-url'    => $item_link, 
            'menu-item-status' => 'publish',
          ) );
        }
        // set menu locations
        if( !empty($menu['location']) ) {
          $locations = get_theme_mod('nav_menu_locations');
          $location  = $menu['location'];
          $locations[ $location ] = $menu_id;
          set_theme_mod( 'nav_menu_locations', $locations );
        }
      }
    }
  }

  public static function SiteBase( $backup_region='' )
  {
    $regions = array('chicago', 'national', 'la', 'miami', 'tristate');
    $current = __DIR__;
    $folders = explode('/', $current);
    $folders = array_splice($folders, 6);
    $region  = $folders[0];
    return in_array($region, $regions) ? $region : $backup_region;
  }

  public static function Logo()
  {
    $region = self::SiteBase( 'ny' );
    $logo_path = sprintf('assets/images/trd-%s-logo.svg', $region);
    return TRD_CORE_URL.$logo_path;
  }

  public static function SearchLink( $path )
  {
    $region = self::SiteBase();
    $prefix = empty($region) ? '' : '/';
    return sprintf('%s%s%s', $prefix, $region, $path);
  }

  public function load_style()
  {
    $file = 'assets/css/navigation.min.css';
    $path = TRD_CORE_PATH.'/'.$file;
    if( file_exists( $path ) ){
      if( getenv('WP_ENV') == 'local' ){
        echo sprintf('<link rel="stylesheet" href="%s%s">', TRD_CORE_URL, $file);
      } else {
        echo sprintf('<style type="text/css">%s</style>', file_get_contents( $path ) );
      }
    }
  }

  public function load_script()
  {
    $file = TRD_CORE_PATH.'/js/_navigation.js';
    if( file_exists( $file ) ){
      $content = file_get_contents( $file );
      echo sprintf('<script type="text/javascript">%s</script>', $content );
    }
  }

  public function display()
  {
    $file = TRD_CORE_PATH.'/View/navigation.php';
    if( file_exists( $file ) ){
      $this->load_style();
      require_once $file;
      $this->load_script();
    }
  }
}

?>