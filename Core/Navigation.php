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
      'header_primary'  => 'Header Primary',
      'header_secondry' => 'Header Secondry',
      'header_social'   => 'Header Social',
      'header_account'  => 'Header My Account',
    ) );
  }

  public static function Create_Navs()
  {
    $navs = array(
      'Nav Regions' => array(
        'location' => 'header_primary',
        'items' => array(
          '/'         => 'New York',
          '/la'       => 'Los Angeles',
          '/miami'    => 'South Florida',
          '/chicago'  => 'Chicago',
          '/national' => 'National',
          '/tristate' => 'Tri-State',
        )
      ),
      'Nav Sections' => array(
        'location' => 'header_secondry',
        'items' => array(
          '/#' => 'News',
          '/#' => 'Nagazine',
          '/#' => 'Research',
          '/#' => 'Events',
          // '/#' => 'Videos',
          // '/#' => 'Listing',
        )
      ),
      'Nav Social' => array(
        'location' => 'header_social',
        'items' => array(
          'https://facebook.com/'  => 'Facebook',
          'https://twitter.com/'   => 'Twitter',
          'https://youtube.com/'   => 'YouTube',
          'https://instagram.com/' => 'Instagram',
          'https://linkedin.com/'  => 'Linkedin',
        )
      ),
      'My Account' => array(
        'location' => 'header_account',
        'items' => array(
          '/subscribe/'  => 'Subscribe',
          '/my-account/' => 'My Account',
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

  public function load_style()
  {
    $file = TRD_CORE_PATH.'/assets/css/navigation.min.css';
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