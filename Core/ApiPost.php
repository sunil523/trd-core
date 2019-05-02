<?php
namespace TRD\Core;

class ApiPost extends ApiConfig
{

  private $api             = null;
  private $post            = null;
  private $update          = null;
  private $crosspost       = null;
  private $crosspost_field = null;
  private $default_status  = 'draft';

  public function __construct( $post, $update=false )
  {
    parent::__construct();
    $this->post      = $post;
    $this->update    = $update;
    $this->crosspost = array();
    $this->update_crosspost_field();
  }

  /**
   * 
   */
  public function save()
  {
    $crossposts = get_the_terms( $this->post, CrossPost::TAXONOMY_NAME );
    if( is_wp_error( $crossposts ) || empty( $crossposts ) ) return;
    // save which crosspost was created and which crosspost didn't get created.
    $result = array( 'success' => array(), 'error' => array() );
    // set the commen fields
    $this->set_fields();
    foreach ($crossposts as $crosspost) {
      $this->slug = $crosspost->slug;
      $api_args = $this->apis[ $this->slug ];
      if( empty( $api_args ) ) continue;
      // set API params
      $this->api = (OBJECT) $api_args;
      $key = '_crosspost_id_'.$this->slug;
      // set the username and password if they are not set
      if(!isset($this->api->user)) $this->api->user = $this->api_user;
      if(!isset($this->api->pass)) $this->api->pass = $this->api_pass;

      // get saved crosspost ID
      $crosspost_id = get_post_meta( $this->post->ID, $key, true );
      // change the url when it's update and we have crosspost ID
      if( $this->update && !empty( $crosspost_id ) ) {
        $url = sprintf("%s/posts/%s", $this->api->root, $crosspost_id );
        unset($this->crosspost['status']);
      } else {
        $url = sprintf("%s/posts", $this->api->root );
        $this->crosspost['status'] = $this->default_status;
      }
      // set the API Header
      $this->api->headers = array(
        'Authorization' => 'Basic ' . base64_encode( $this->api->user . ':' . $this->api->pass ),
      );
      // Update the fields
      $this->update_fields();
      // Save the post to APIs
      $response = wp_remote_post( $url, array( 'headers' => $this->api->headers, 'body' => $this->crosspost ) );
      // request was success so save the result
      if( !is_wp_error($response) && in_array($response[ 'response' ][ 'code' ], array( 200, 201 ) ) ){
        $result[ 'success' ][] = $this->slug;
        $body = wp_remote_retrieve_body( $response );
        if( !is_wp_error( $body ) ){
          $body = json_decode( $body );
          // save the corsspost id in the current post 
          update_post_meta( $this->post->ID, $key, $body->id );
        }
      } else {
        $result[ 'error' ][] = $this->slug;
      }
    }
    update_option('trd_apipost_notices', $result);
    return true;
  }

  public static function admin_notices()
  {
    $notices = get_option('trd_apipost_notices');
    delete_option('trd_apipost_notices');
    if(empty($notices)) return;
    foreach ($notices as $key => $values) {
      if( empty($values) ) continue;
      if( $key=='success' ){
        $message = sprintf( 'Crosspost created under <strong>%s</strong>.', implode(', ', $values) );
      }
      else if( $key=='error' ){
        $message = sprintf( 'Can not create crosspost under <strong>%s</strong>.', implode(', ', $values) );
      }
      if( !empty($message) ){
        ?>
        <div class="notice notice-<?php echo $key; ?> is-dismissible">
            <p><?php echo $message; ?></p>
        </div>
        <?php
      }
    }
  }

  /**
   * set the crosspost field to current website.
   */
  private function update_crosspost_field()
  {
    $site_url = site_url();
    if( $site_url == $this->site_root){
      $this->crosspost_field = $this->apis[ 'ny' ][ 'crosspost_field' ];
    }else{
      $site = trim( str_replace( $this->site_root, '', $site_url ), "/" );
      if( array_key_exists( $site, $this->api ) ){
        $this->crosspost_field = $this->apis[ $site ][ 'crosspost_field' ];
      } else {
        $this->crosspost_field = null;
      }
    }
  }

  /**
   * 
   */
  private function set_fields()
  {
    $fields = array(
      'title' => get_the_title( $this->post ),
      'status' => $this->default_status,
      'content' => $this->post->post_content,
      'meta' => array(
        $this->crosspost_field       => array( 1 ),
        '_crosspost'                 => array( 1 ), // to hide the crosspost taxonomy box on crosspost.
        '_links_to'                  => array( get_the_permalink( $this->post->ID ) ),
        '_links_to_target'           => array( "_blank" ),
        'Dock-News'                  => get_post_meta( $this->post->ID, 'Dock-News' ),
        'amp_exclude'                => get_post_meta( $this->post->ID, 'amp_exclude' ),
        'twitterCardType'            => get_post_meta( $this->post->ID, 'twitterCardType' ),
        '_bitly_permalink'           => get_post_meta( $this->post->ID, '_bitly_permalink' ),
        '_bitly_shorturl'            => get_post_meta( $this->post->ID, '_bitly_shorturl' ),
        '_aioseop_title'             => get_post_meta( $this->post->ID, '_aioseop_title' ),
        '_aioseop_description'       => get_post_meta( $this->post->ID, '_aioseop_description' ),
        '_aioseop_keywords'          => get_post_meta( $this->post->ID, '_aioseop_keywords' ),
        '_aioseop_custom_link'       => array( get_the_permalink( $this->post->ID ) ),
        '_aioseop_noindex'           => get_post_meta( $this->post->ID, '_aioseop_noindex' ),
        '_aioseop_nofollow'          => get_post_meta( $this->post->ID, '_aioseop_nofollow' ),
        '_aioseop_disable'           => get_post_meta( $this->post->ID, '_aioseop_disable' ),
        '_aioseop_disable_analytics' => get_post_meta( $this->post->ID, '_aioseop_disable_analytics' ),
      ),
      'author' => $this->post->post_author,
      'comment_status' => $this->post->comment_status,
      'ping_status' => $this->post->ping_status,
    );
    $this->crosspost = array_merge( $this->crosspost, $fields );
  }

  /**
   * 
   */
  private function update_fields()
  {
    $thumbnail_id          = $this->get_media_id( get_post_meta( $this->post->ID, '_thumbnail_id', true ) );
    $second_featured_image = $this->get_media_id( get_post_meta( $this->post->ID, 'second_featured_image', true ) );
    $fields = array(
      'categories' => $this->get_taxonomy_terms( 'category', 'categories', array('cross-post') ),
      'tags' => $this->get_taxonomy_terms( 'post_tag', 'tags' ),
    );
    // set meta fields
    $metas = array(
      'second_featured_image'  => array( $second_featured_image ),
      '_thumbnail_id'          => array( $thumbnail_id ),
    );
    $this->crosspost['meta'] = array_merge( $this->crosspost['meta'], $metas );
    $this->crosspost = array_merge( $this->crosspost, $fields );
  }

  /**
   * 
   */
  private function get_taxonomy_terms( $taxonomy, $rest_slug, $include_terms=array() )
  {
    // get categories slug
    $terms = get_the_terms( $this->post->ID, $taxonomy );
    if( empty( $terms ) ) return null;
    $slugs = $include_terms;
    foreach ($terms as $term) {
      array_push( $slugs, $term->slug );
    }
    // send the slug over to remote site
    $url      = sprintf( '%s/%s?slug=[%s]', $this->api->root, $rest_slug, implode(',', $slugs) );
    $response = wp_remote_get( $url );
    if( is_wp_error($response) ) return;
    // return null if response code is not 200
    if( $response['response']['code'] !== 200) return null;
    // get the id from the result terms
    $terms      = json_decode( $response['body'] );
    $categories = array();
    foreach ($terms as $term) {
      array_push( $categories, $term->id );
    }
    return $categories;
  }

  /**
   * 
   */
  public function get_media_id( $image_id )
  {
    // check if this image has crosspot ids
    $image = get_post( $image_id );
    $metas = get_post_meta( $image->ID );
    $key = '_crosspost_id_'.$this->slug;
    if( array_key_exists( $key, $metas ) ){
      return $metas[ $key ][0];
    }
    // upload the image and update the fields
    $upload_id = $this->get_upload_image_id( $image_id );
    if( empty( $upload_id ) ) return null;
    // set the fields
    $fields = array(
      'status' => 'publish',
      'title' => $image->post_title,
      'author' => $image->post_author,
      'comment_status' => $image->comment_status,
      'ping_status' => $image->ping_status,
      'alt_text' => $image->alt_text,
      'description' => $image->post_content,
      'excerpt' => $image->post_excerpt,
      'meta' => array(
        '_crosspost' => array( 1 )
      ),
    );

    $url = sprintf( '%s/media/%s', $this->api->root, $upload_id );
    $response = wp_remote_post( $url, array( 'headers' => $this->api->headers, 'body' => $fields ) );
    if( is_wp_error($response) ) return null;
    if( in_array( $response[ 'response' ][ 'code' ], array( 200, 201 ) ) ){
      $body = wp_remote_retrieve_body( $response );
      if( !is_wp_error( $body ) ){
        $body = json_decode( $body );
        // save the corsspost id in the current post 
        update_post_meta( $image->ID, $key, $body->id );
        return $body->id;
      }
    }
    return null;
  }

  /**
   * 
   */
  private function get_upload_image_id( $image_id )
  {
    $image_file = get_attached_file( $image_id );
    $filename   = basename( $image_file );
    $file_data  = file_get_contents( $image_file );

    $url     = sprintf( '%s/media', $this->api->root );
    $headers = array(
      'Content-Disposition' => 'attachment; filename="'.$filename.'"',
      'content-type'        => 'application/binary',
      'accept'              => 'application/json',
    );
    $headers  = array_merge( $this->api->headers, $headers );
    $response = wp_remote_post( $url, array( 'headers' => $headers, 'body' => $file_data ) );
    if( is_wp_error($response) ) return null;
    if( in_array($response[ 'response' ][ 'code' ], array( 200, 201 ) ) ){
      $body = wp_remote_retrieve_body( $response );
      if( !is_wp_error( $body ) ){
        $body = json_decode( $body );
        return $body->id;
      }
    }
    return null;
  }
}
