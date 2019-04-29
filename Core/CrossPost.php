<?php
namespace TRD\Core;

class CrossPost
{

  const TAXONOMY_NAME = 'crosspost';

  public function __construct() {
    
  }

  public static function Register()
  {
    add_action( 'init', array( __CLASS__, 'register_taxonomy' ), 0 );
    add_action( 'save_post', array( __CLASS__, 'save_post' ), 10, 3 );
    add_action( 'admin_menu' , array( __CLASS__, 'remove_crosspost_box' ) );
    add_action( 'admin_notices', array( '\TRD\Core\ApiPost', 'admin_notices' ) );
    // if(WP_DEBUG) 
    add_filter( 'https_ssl_verify', '__return_false' );
    
  }

  public static function remove_crosspost_box()
  {
    // get post ID from url GET since $post global var is not set yet.
    $post_id = $_GET['post'];
    $crosspost = get_post_meta( $post_id, '_crosspost', true );
    // remove the crosspost if the post is already crosspost.
    if( $crosspost == '1'){
      remove_meta_box( self::TAXONOMY_NAME.'div' , 'post' , 'side' );
    }
  }

  final public static function save_post( $post_id, $post, $update )
  {
    // https://codex.wordpress.org/Post_Status
    // if( in_array( $post->post_status, array( 'auto-draft', 'inherit', 'trash', 'pending', 'private' ) ) return;
    if( in_array( $post->post_status, array( 'draft', 'publish', 'future' ) ) ){
      $crosspost = new ApiPost( $post, $update );
      $crosspost->save();
      return true;
    }
  }

  final public static function register_taxonomy()
  {

    $labels = array(
      'name'                       => _x( 'Crossposting', 'Taxonomy General Name', 'trd' ),
      'singular_name'              => _x( 'Crosspost', 'Taxonomy Singular Name', 'trd' ),
      'menu_name'                  => __( 'Crossposting', 'trd' ),
      'all_items'                  => __( 'All Regions', 'trd' ),
      'parent_item'                => __( 'Parent Region', 'trd' ),
      'parent_item_colon'          => __( 'Parent Region:', 'trd' ),
      'new_item_name'              => __( 'New Region Name', 'trd' ),
      'add_new_item'               => __( 'Add New Region', 'trd' ),
      'edit_item'                  => __( 'Edit Region', 'trd' ),
      'update_item'                => __( 'Update Region', 'trd' ),
      'view_item'                  => __( 'View Region', 'trd' ),
      'separate_items_with_commas' => __( 'Separate regions with commas', 'trd' ),
      'add_or_remove_items'        => __( 'Add or remove regions', 'trd' ),
      'choose_from_most_used'      => __( 'Choose from the most used regions', 'trd' ),
      'popular_items'              => __( 'Popular Regions', 'trd' ),
      'search_items'               => __( 'Search Regions', 'trd' ),
      'not_found'                  => __( 'Region Not Found', 'trd' ),
      'no_terms'                   => __( 'No regions', 'trd' ),
      'items_list'                 => __( 'Regions list', 'trd' ),
      'items_list_navigation'      => __( 'Regions list navigation', 'trd' ),
    );
    $args = array(
      'labels'                     => $labels,
      'hierarchical'               => true,
      'public'                     => true,
      'show_ui'                    => true,
      'show_admin_column'          => true,
      'show_in_nav_menus'          => false,
      'show_tagcloud'              => false,
      'query_var'                  => self::TAXONOMY_NAME,
      'rewrite'                    => false,
      'show_in_rest'               => true,
      'rest_base'                  => self::TAXONOMY_NAME,
    );
    register_taxonomy( self::TAXONOMY_NAME, array( 'post' ), $args );

    self::set_default_terms();
    self::api_endpoint();
  }

  public static function set_default_terms()
  {
    $terms = array(
      'chicago'  => 'Chicago',
      'la'       => 'Los Angeles',
      'miami'    => 'Miami',
      'national' => 'National',
      'ny'       => 'New York',
      'tristate' => 'Tri-State',
    );

    foreach ( $terms as $slug => $term ) {
      wp_insert_term( $term, self::TAXONOMY_NAME, array( 'slug' => $slug ) );
    } 
  }

  public static function api_endpoint()
  {
    // register to get crosspost using crosspost slug
    add_action( 'rest_api_init', function () {
      register_rest_route( 'wp/v2', '/'.self::TAXONOMY_NAME.'/(?P<slug>[a-z]+)', array(
        'methods' => 'GET',
        'callback' => array( __CLASS__, 'get_by_slug' )
      ) );
    } );

    // expos post meta fields
    \register_rest_field( array( 'post', 'attachment' ), 'meta', array(
      'get_callback' => array( __CLASS__, 'get_api_post_meta' ),
      'update_callback' => array( __CLASS__, 'update_api_post_meta'),
    ) );
  }

  /**
   * Get post meta for api
   * @return object of post meta in key and value format.
   */
  public static function get_api_post_meta( $obj )
  {
    return get_post_meta( $obj['id'] );
  }

  public static function update_api_post_meta( $meta, $post )
  {
    foreach ( $meta as $key => $values ) {
      foreach ($values as $value) {
        update_post_meta( $post->ID, $key, $value );
        // update the Custom Field field.
        if(in_array( $key, array( 'second_featured_image', 'headline_2' ) ) ) {
          update_field( $key, $value, $post->ID );
        };
      }
    }
  }

  /**
   * Get post by crosspost slug
   * @return array of sanitize post
   */
  public static function get_by_slug( \WP_REST_Request $request )
  {
    $args = array(
      'posts_per_page'   => 10,
      'orderby'          => 'date',
      'order'            => 'DESC',
      'post_type'        => 'post',
      'post_status'      => 'publish',
      'suppress_filters' => true,
      'tax_query'        => array(
        array(
          'taxonomy' => self::TAXONOMY_NAME,
          'field' => 'slug',
          'terms' => $request['slug']
        )
      )
    );
    return self::sanitize_posts( get_posts( $args ) );
  }

  public static function sanitize_posts( $posts )
  {
    for ( $i = 0, $n = count( $posts ); $i < $n; $i++ ) { 
      $post = $posts[ $i ];
      $tmp = array(
        'id'            => $post->ID,
        'post_title'    => get_the_title( $post->ID ),
        'post_excerpt'  => get_the_excerpt( $post->ID ),
        'post_image'    => get_the_post_thumbnail_url( $post, 'full' ),
        'post_link'     => get_permalink( $post->ID ),
        'post_date'     => $post->post_date,
        'post_date_gmt' => $post->post_date_gmt,
      );
      $posts[ $i ] = $tmp;
    }
    return $posts;
  }
}
