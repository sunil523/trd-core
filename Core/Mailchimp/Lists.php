<?php
namespace TRD\Core\Mailchimp;

class Lists
{
  
  private $list_id = null;
  
  function __construct( $list_id, $email='' )
  {
    $this->list_id = $list_id;
    $this->member_id = empty( $email ) ? '' : md5( strtolower( $email ) );
  }

  public function get_list_regions()
  {
    $regions = $this->get_regions();
    $regions = $this->sanatize_regions( $regions );
    return $regions;
  }

  public function get_list_interests()
  {
    $interests = $this->get_interests();
    $interests = $this->sanatize_interests( $interests );
    return $interests;
  }

  public function get_frequency()
  {
    return array(
      array(
        'id' => 'daily',
        'name' => 'Daily'
      ),
      array(
        'id' => 'weekly',
        'name' => 'Weekly'
      )
    );
  }

  private function get_regions()
  {
    $cats_url = sprintf( "/lists/%s/interest-categories/", $this->list_id );
    $cats = $this->call_api( $cats_url, array( 'count' => 20, 'type' => 'checkboxes' ) );
    $categories = array();
    // we have categories
    if( $cats && isset( $cats['total_items'] ) && $cats['total_items'] > 0 ){
      $cats = $cats['categories'];
      // go over each cats to get all interests
      foreach ( $cats as $cat ) {
        // if( $cat['type'] == 'hidden' ) continue;
        array_push( $categories, array(
          'category_id'   => $cat['id'],
          'category_name' => $cat['title']
        ) );
      }
    }
    return $categories;
  }

  private function get_interests()
  {
    $cats_url = sprintf( "/lists/%s/interest-categories/", $this->list_id );
    $cats = $this->call_api( $cats_url, array( 'count' => 20, 'type' => 'checkboxes' ) );
    $interests = array();
    // we have categories
    if( $cats && isset( $cats['total_items'] ) && $cats['total_items'] > 0 ){
      $cats = $cats['categories'];
      // go over each cats to get all interests
      foreach ( $cats as $cat ) {
        $ints = $this->call_api( sprintf( "%s/%s/interests/", $cats_url, $cat['id'] ) );
        // we have interests
        if( $ints && isset( $ints['total_items'] ) && $ints['total_items'] > 0 ){
          $ints = $ints['interests'];
          // go over each ints and push data to main interests array
          foreach ( $ints as $int ) {
            array_push( $interests, array(
              'interest_id'   => $int['id'],
              'interest_name' => $int['name'],
              'category_id'   => $cat['id'],
              'category_name' => $cat['title']
            ) );
          }
        }
      }
    }
    return $interests;
  }

  private function sanatize_regions( $data )
  {
    if( count( $data ) == 0 ) return null;
    $regions = array();
    foreach ($data as $cat) {
      $region = array(
        'id'   => $cat['category_id'],
        'name' => $this->sanatize_category_name( $cat['category_name'] )
      );
      array_push( $regions, $region );
    }
    return $regions;
  }

  private function sanatize_interests( $data )
  {
    if( count( $data ) == 0 ) return null;
    $interests = array(
      'daily'  => array(),
      'weekly' => array(),
      'trdata' => array(),
    );
    foreach ($data as $int) {
      $interest = array(
        'id'   => $int['interest_id'],
        'name' => $this->sanatize_category_name( $int['category_name'] )
      );
      if( strpos( strtolower( $int['interest_name'] ), 'daily' ) === 0 ) {
        array_push( $interests['daily'],  $interest );
      } else if( strpos( strtolower( $int['interest_name'] ), 'weekly' ) === 0 ) {
        array_push( $interests['weekly'], $interest );
      } else if( strpos( strtolower( $int['category_name'] ), 'trdata' ) === 0 ){
        list($name, $often) = explode(' - ', $int['interest_name'] );
        $interest['name'] = trim( $name );
        array_push( $interests['trdata'], $interest );
      }
    }
    return $interests;
  }

  private function sanatize_category_name( $cat )
  {
    $cat = trim( str_replace( 
      array('Real Estate News', 'Weekly Roundup'), 
      '', 
      $cat 
    ) );
    return $cat;
  }

  private function call_api( $url, $data = array() )
  {
    
    $api    = new Api();
    $api->set_api( $api->get_url( $url ), Api::METHOD_GET, $data );
    $result = $api->call_api();
    if($result){
      return $api->get_response();
    }else{
      return false;
    }
  }
}
