<?php
namespace TRD\Core\Mailchimp;

class Api{

  private $api_dc   = ''; // API Data Center
  private $api_user = ''; // API Username
  private $api_key  = ''; // API Key
  private $api_url  = '';

  const METHOD_GET    = 'GET';
  const METHOD_POST   = 'POST';
  const METHOD_PATCH  = 'PATCH';
  const METHOD_PUT    = 'PUT';
  const METHOD_DELETE = 'DELETE';

  function __construct(){
    // read the key from server env file.
    $this->api_url  = getenv("MC_API_URL");
    $this->api_user = getenv("MC_API_USER");
    $this->api_key = getenv("MC_API_KEY");
  }

  /**
   * get the api url
   */
  final public function get_url( $path )
  {
    $url = str_replace( '<dc>', $this->get_dc(), $this->api_url );
    return $url.$path;
  }

  /**
   * get the api data center.
   */
  final protected function get_dc()
  {
   if( empty( $this->api_dc ) ){
     list($key, $dc) = explode('-', $this->api_key);
     $this->api_dc = $dc;
   }
   return $this->api_dc;
  }

  public function set_api( $url, $method, $data = array() )
  {
    $this->_url    = $url;
    $this->_method = $method;
    $this->_data   = $data;
  }

  public function call_api()
  {
    // init the curl
    $curl = curl_init();
    // set the data base on the method
    switch ($this->_method){
      case "PATCH":
      case "PUT":
      case "POST":
        curl_setopt($curl, CURLOPT_POSTFIELDS, json( $this->_data ) );
        break;
      case "GET":
      default:
        if( !empty($this->_data) ) $this->_url = sprintf("%s?%s", $this->_url, http_build_query($this->_data));
    }
    // setup the header
    $headers = array(
      "authorization: Basic ".base64_encode($this->api_user.":".$this->api_key),
      "cache-control: no-cache",
      "content-type: application/json"
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    // setup other paralemters
    curl_setopt($curl, CURLOPT_URL, $this->_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->_method);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    // get the result and error
    $result = curl_exec( $curl );
    $error  = curl_error( $curl );
    // close the connection
    curl_close( $curl );
    // problem calling API
    if( $error ) {
      $this->error = null;
      return false;
    }
    // conver the json response to php array
    $result = json_decode( $result, true );
    // the result has error response
    if( isset( $result['status'] ) ){
      $this->error = $result;
      return false;
    }
    // API call was success
    $this->response = $result;
    return true;
  }

  public function get_error()
  {
    return $this->error;
    // return new Error( $this->error );
  }
  /**
   * get the result from Mailchimp API.
   */
  final public function get_response()
  {
    return $this->response;
    // return new Response( $this->response );
  }
}