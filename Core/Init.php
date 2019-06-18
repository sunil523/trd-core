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
    add_action( 'wp_head',            array( __CLASS__, 'Head' ), 0 );
    add_action( 'wp_body_open',       array( __CLASS__, 'BodyOpen' ), 0 );
    Newsletter::Actions();
  }

  public static function Styles()
  {
    wp_enqueue_style(
      'trd-core-style',
      TRD_CORE_URL.'/assets/css/trd-core.min.css',
      false,
      filemtime( TRD_CORE_PATH.'assets/css/trd-core.min.css' )
    );
  }

  // Setup the Scritps
  public static function Scripts()
  {
    wp_enqueue_script(
      'trd-core-script',
      TRD_CORE_URL.'/assets/js/trd-core.min.js',
      array('jquery'),
      filemtime( TRD_CORE_PATH.'assets/js/trd-core.min.js' ),
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

  public static function Head()
  {
    self::FirstMeta();
    self::SetupGTM( 'head' );
    self::setupPiano();
    self::Meta();
  }

  public static function BodyOpen()
  {
    self::SetupGTM( 'body' );
    if( class_exists('\TRD\Core\Navigation') ) {
      $nav = new \TRD\Core\Navigation();
      $nav->display();
    }
  }

  public static function SetupGTM( $type = 'body' )
  {
    $gtm_id = 'GTM-K694XL6';
    $gtm_cookies_win = 'x';
    switch ( getenv( 'WP_ENV' ) ) {
      case 'live':
      case 'prod':
        $gtm_auth    = '2Dc6nz6-xSl7lnGU4UY2OA';
        $gtm_preview = 'env-2';
        break;

      case 'staging':
      case 'stag':
      case 'beta':
      case 'beta2':
        $gtm_auth    = 'wWiDaS0gGQ38MIzxist3Jg';
        $gtm_preview = 'env-33';
        break;
      case 'local':
      default:
        $gtm_auth    = 'zdLKPv48E9tIt3DVRTJgzw';
        $gtm_preview = 'env-34';
        break;
    }
    if( $type === 'head' ){
      echo sprintf("<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl+ '&gtm_auth=%s&gtm_preview=%s&gtm_cookies_win=%s';f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','%s');</script>", $gtm_auth, $gtm_preview, $gtm_cookies_win, $gtm_id);
    } else {
      echo sprintf('<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=%s&gtm_auth=%s&gtm_preview=%s&gtm_cookies_win=%s" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>', $gtm_id, $gtm_auth, $gtm_preview, $gtm_cookies_win);
    }
  }

  public static function setupPiano()
  {
    switch ( getenv( 'WP_ENV' ) ) {
      case 'live':
      case 'prod':
        $piano_url = 'https://experience.tinypass.com/xbuilder/experience/load?aid=p7sVIGTDn5';
        break;
      default:
        $piano_url = 'https://sandbox.tinypass.com/xbuilder/experience/load?aid=yVAGWJfOMP';
        break;
    }
	  echo sprintf('<script>(function(src){var a=document.createElement("script");a.type="text/javascript";a.async=true;a.src=src;var b=document.getElementsByTagName("script")[0];b.parentNode.insertBefore(a,b)})("%s");</script>', $piano_url);
  }

  public static function FirstMeta()
  {
    $favicons_path = get_bloginfo('template_url') . '/images/favicons';
    ?>
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
    <meta name="viewport"           content="width=device-width, initial-scale=1">
    <title><?php wp_title(' | ', true, 'right'); ?><?php bloginfo('name'); ?></title>
    <link rel="icon" type="image/png" href="<?php echo $favicons_path; ?>/favicon-32x32.png">
    <link rel="icon" type="image/png" href="<?php echo $favicons_path; ?>/favicon-16x16.png" sizes="16x16">
    <link rel="icon" type="image/png" href="<?php echo $favicons_path; ?>/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="<?php echo $favicons_path; ?>/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="<?php echo $favicons_path; ?>/android-chrome-192x192.png" sizes="192x192">
    <?php
  }

  public static function Meta()
  {
    global $geoip_country_code;
    $favicons_path = get_bloginfo('template_url') . '/images/favicons';
    ?>
    <meta name="msvalidate.01"            content="EFC5E23CCFDAEE199AA369EDFF3201B1">
    <meta name="google-site-verification" content="wZ1M2SOQpdwMUVsTJeEADjbuNm8mWBkAVR9R_3Fz30Q">
    <meta name="COUNTRY"                  content="<?php echo $geoip_country_code; ?>">
    <meta name="propeller"                content="12fda498640901b80f36005a35bbd396">
    <meta name="theme-color"              content="#EC1C24">
    <meta name="msapplication-TileColor"  content="#EC1C24">
    <meta name="msapplication-TileImage"  content="<?php echo $favicons_path; ?>/mstile-144x144.png">
    <link rel="manifest"         href="<?php echo $favicons_path; ?>/manifest.json">
    <link rel="mask-icon"        href="<?php echo $favicons_path; ?>/safari-pinned-tab.svg"        color="#5bbad5">
    <link rel="apple-touch-icon" href="<?php echo $favicons_path; ?>/apple-touch-icon-57x57.png"   sizes="57x57">
    <link rel="apple-touch-icon" href="<?php echo $favicons_path; ?>/apple-touch-icon-60x60.png"   sizes="60x60">
    <link rel="apple-touch-icon" href="<?php echo $favicons_path; ?>/apple-touch-icon-72x72.png"   sizes="72x72">
    <link rel="apple-touch-icon" href="<?php echo $favicons_path; ?>/apple-touch-icon-76x76.png"   sizes="76x76">
    <link rel="apple-touch-icon" href="<?php echo $favicons_path; ?>/apple-touch-icon-114x114.png" sizes="114x114">
    <link rel="apple-touch-icon" href="<?php echo $favicons_path; ?>/apple-touch-icon-120x120.png" sizes="120x120">
    <link rel="apple-touch-icon" href="<?php echo $favicons_path; ?>/apple-touch-icon-144x144.png" sizes="144x144">
    <link rel="apple-touch-icon" href="<?php echo $favicons_path; ?>/apple-touch-icon-152x152.png" sizes="152x152">
    <link rel="apple-touch-icon" href="<?php echo $favicons_path; ?>/apple-touch-icon-180x180.png" sizes="180x180">
    <?php
  }
}
