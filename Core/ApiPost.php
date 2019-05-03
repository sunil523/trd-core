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
  private $result          = array('success' => array(), 'error' => array(), 'info' => array(), 'warning' => array());

  public function __construct( $post, $update=false )
  {
    parent::__construct();
    $this->post      = $post;
    $this->update    = $update;
    $this->crosspost = array();
  }

  /**
   * 
   */
  public function save()
  {
    $crossposts = get_the_terms( $this->post, CrossPost::TAXONOMY_NAME );
    if( is_wp_error( $crossposts ) || empty( $crossposts ) ) return;
    // Crosspost to different citys
    foreach ($crossposts as $crosspost) {
      $this->cp( $crosspost );
    }
    update_option('trd_apipost_notices', $this->result);
    return true;
  }

  public function cp( $crosspost )
  {
    $this->slug = $crosspost->slug;
    $key = '_crosspost_id_'.$this->slug;
    $api_args = $this->apis[ $this->slug ];
    if( empty( $api_args ) ) return false;
    // set API params
    $this->api = (OBJECT) $api_args;
    // set the API Header
    $this->api->headers = array(
      'authorization' => 'Basic ' . base64_encode( $this->api_user . ':' . $this->api_pass ),
    );
    // set the fields
    $this->update_crosspost_field();
    $this->set_fields();
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
    // Save the post to APIs
    $response = wp_remote_post( $url, array( 'headers' => $this->api->headers, 'body' => $this->crosspost ) );
    $body     = wp_remote_retrieve_body( $response );
    $code     = wp_remote_retrieve_response_code( $response );
    // request was success so save the result
    if( !is_wp_error( $response ) && !is_wp_error( $body ) && in_array( $code, array( 200, 201 ) ) ){
      $this->result[ 'success' ][] = $this->slug;
      $body = json_decode( $response['body'] );
      // save the corsspost id in the current post 
      update_post_meta( $this->post->ID, $key, $body->id );
      $this->result[ 'info' ][] = sprintf('<p><strong>%s</strong>: <a href="%s/wp-admin/post.php?post=%s&action=edit">Edit Crosspost</p>', $this->slug, site_url($this->api->base), $body->id );
    } else {
      $error = is_wp_error( $body ) ? $body : $response;
      $this->result[ 'error' ][] = $this->slug;
      $this->result[ 'warning' ][] = sprintf('<p><strong>%s</strong>: [%s] %s - %s (%s)</p>', $this->slug, $code, $error->get_error_code(), $error->get_error_message(), $url );
    }
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
      else{
        $message = implode('', $values );
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
    // get the image id
    $thumbnail_id = $this->get_media_id( get_post_meta( $this->post->ID, '_thumbnail_id', true ) );
    $sf_image_id  = $this->get_media_id( get_post_meta( $this->post->ID, 'second_featured_image', true ) );

    $fields = array(
      'title' => get_the_title( $this->post ),
      'status' => $this->default_status,
      'content' => $this->post->post_content,
      'author' => $this->post->post_author,
      'comment_status' => $this->post->comment_status,
      'ping_status' => $this->post->ping_status,
      'categories' => $this->get_taxonomy_terms( 'category', 'categories', array('cross-post') ),
      'tags' => $this->get_taxonomy_terms( 'post_tag', 'tags' ),
      'meta' => array()
    );
    // copy the curret post meta
    $fields['meta'] = get_post_meta( $this->post->ID );
    // only set the city crosspost when national and tristate fields are not set
    if( !isset( $fields['meta']['A3_trd_national'] ) && !isset( $fields['meta']['A3_tri_state'] ) ){
      $fields['meta'][ $this->crosspost_field ] = array( 1 );
    }
    // update other meta fields
    $fields['meta']['_crosspost']            = array( 1 ); // to hide the crosspost taxonomy box on crosspost.
    $fields['meta']['_links_to']             = array( get_the_permalink( $this->post->ID ) );
    $fields['meta']['_links_to_target']      = array( "_blank" );
    $fields['meta']['second_featured_image'] = array( $sf_image_id );
    $fields['meta']['_thumbnail_id']         = array( $thumbnail_id );
    $fields['meta']['_aioseop_custom_link']  = array( get_the_permalink( $this->post->ID ) );
    // unset the other meta fields
    unset($fields['meta']['_edit_lock']);
    unset($fields['meta']['_edit_last']);
    unset($fields['meta']['_encloseme']);
    unset($fields['meta']['_second_featured_image']);
    unset($fields['meta']['_headline_2']);
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
    $body     = wp_remote_retrieve_body( $response );
    $code     = wp_remote_retrieve_response_code( $response );
    if( !is_wp_error( $response ) && !is_wp_error( $body ) && in_array( $code, array( 200, 201 ) ) ){
      $body = json_decode( $body );
      // save the corsspost id in the current post 
      update_post_meta( $image->ID, $key, $body->id );
      return $body->id;
    }else{
      $error = is_wp_error( $body ) ? $body : $response;
      $this->result[ 'warning' ][] = sprintf( '<p><strong>%s</strong>: [%s] %s - %s</p>', $this->slug, $code, $error->get_error_code(), $error->get_error_message() );
      return null;
    }
  }

  /**
   * 
   */
  private function get_upload_image_id( $image_id )
  {
    $image_file = get_attached_file( $image_id );
    $f          = @fopen( $image_file, 'rb');
    $fsize      = filesize($image_file);
    $file_data  = fread( $f, $fsize );
    fclose($f);
    
    $url  = sprintf( '%s/media', $this->api->root );
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_SSL_VERIFYHOST => 0,
      CURLOPT_SSL_VERIFYPEER => 0,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_HTTPHEADER => array(
        "authorization: Basic ".base64_encode( $this->api_user.':'.$this->api_pass ),
        "cache-control: no-cache",
        "content-disposition: attachment; filename=crosspost-".date('Ymdhis').".".pathinfo($image_file, PATHINFO_EXTENSION),
        "content-type: ".mime_content_type($image_file),
      ),
      CURLOPT_POSTFIELDS => $file_data,
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    $body = json_decode( $response );
    if ($err) {
      $this->result[ 'warning' ][] = sprintf('<p><strong>%s</strong>: [%s] %s - %s</p>', $this->slug, $body->data->status, $body->code, $body->message);
      return null;
    } else {
      $this->result[ 'info' ][] = sprintf('<p><strong>%s</strong>: %s is uploaded. <a href="%s/wp-admin/upload.php?item=%s" target="_blank">Media ID: %s</a></p>', $this->slug, basename($image_file), site_url($this->api->base), $body->id, $body->id);
      return $body->id;
    }
  }
}
