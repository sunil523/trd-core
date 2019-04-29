<?php
namespace TRD\Core\Mailchimp;

class Subscribe{

  private $args = null;

  const STATUS_SUBSCRIBED   = 'subscribed';
  const STATUS_UNSUBSCRIBED = 'unsubscribed';
  const STATUS_CLEANED      = 'cleaned';
  const STATUS_PENDING      = 'pending';

  private $status = array( 
    self::STATUS_SUBSCRIBED,
    self::STATUS_UNSUBSCRIBED,
    self::STATUS_CLEANED,
    self::STATUS_PENDING
  );

  public function __construct( $list_id, $data )
  {
    
    $this->data      = $data;
    $this->list_id   = $list_id;
    $this->member_id = md5( strtolower( $data[ 'email_address' ] ) );
  }

  public function subscribed()
  {

    $this->data[ 'status' ] = self::STATUS_SUBSCRIBED;

    $api = new Api();
    $url = sprintf( '/lists/%s/members/%s', $this->list_id, $this->member_id );
    $url = $api->get_url( $url );
    $result = $api->get_result( $url, Api::METHOD_PUT, $this->data );

    if( is_null( $result ) ) return false;
    else if( isset( $result['status'] ) ) return false;
    else 
    if($result['status'] != '200'){

    }
  }
}