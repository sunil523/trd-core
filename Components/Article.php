<?php
namespace TRD\Components;

abstract class Article{

  protected $template_name;
  protected $placeholder_image_url;
  
  public function __construct()
  {
    $this->post_id = get_the_ID();
    $this->placeholder_image_url = esc_url( TRD_CORE_URL.'assets/images/placeholder.svg' );
  }

  abstract public function display();

  public function get_title( $post_id = null )
  {
    if( empty( $post_id ) ) $post_id = $this->post_id;
    $title = get_post_meta( $post_id, 'Alternative_title', true );
    if( empty( $title ) ) $title = get_the_title();
    return $title;
  }

  public function get_excerpt( $post_id = null )
  {
    if( empty( $post_id ) ) $post_id = $this->post_id;
    $excrept_lenght = 35;
    $title_lenght   = strlen( $this->get_title( $post_id ) );
    if ($title_lenght <= 43)       $excrept = rm_excerpt( $excrept_lenght,      null, false );
    else if ($title_lenght <= 90)  $excrept = rm_excerpt( $excrept_lenght - 15, null, false );
    else if ($title_lenght <= 160) $excrept = rm_excerpt( $excrept_lenght - 10, null, false );
    return $excrept;
  }

  public function get_image_tag( $image_id, $size )
  {
    $image = $this->get_image( $image_id, $size );
    return $image['tag'];
  }

  public function get_author( $post_id = null, $prefix = 'by' )
  {
    if( empty( $post_id ) ) $post_id = $this->post_id;
    $author = get_post_meta( $post_id , 'author', true );
    if ( !empty($author) && $author !== 'House' ){
      return sprintf( '%s %s', $prefix, $author );
    }
    return '';
  }

  public function get_sources( $post_id = null )
  {
    if( empty( $post_id ) ) $post_id = $this->post_id;
    $sources = array(
      'A3_weekend_edition' => array(
        'color-class' => 'trd-color',
        'label'       => 'WEEKEND EDITION'
      ),
      'A3_trd_ny' => array(
        'color-class' => 'trd-color',
        'label'       => 'New York'
      ),
      'A3_trd_miami' => array(
        'color-class' => 'trd-miami-color',
        'label'       => 'MIAMI'
      ),
      'A3_trd_chicago' => array(
        'color-class' => 'trd-chicago-color',
        'label'       => 'CHICAGO'
      ),
      'A3_weekend_la' => array(
        'color-class' => 'trd-la-color',
        'label'       => 'LOS ANGELES'
      ),
      'A3_trd_national' => array(
        'color-class' => 'trd-national-color',
        'label'       => 'NATIONAL'
      ),
      'A3_tri_state' => array(
        'color-class' => 'trd-tristate-color',
        'label'       => 'TRI-STATE'
      ),
      'A3_trd_archive' => array(
        'color-class' => 'trd-color',
        'label'       => 'ARCHIVE'
      ),
      'A3_trd_issue' => array(
        'color-class' => 'trd-color',
        'label'       => 'ISSUE'
      ),
      'A3_trd_subscriber' => array(
        'color-class' => 'trd-color',
        'label'       => 'SUBSCRIBER'
      ),
    );
    $output = array();
    foreach ($sources as $key => $settings) {
      $value = get_post_meta( $post_id, $key, true );
      if( !empty( $value ) ){
        $source = sprintf( '<strong>T<span class="%s">R</span>D</strong> %s', $settings['color-class'], $settings['label'] );
        array_push( $output, $source );
      }
    }
    return implode( ' ', $output );
  }

  private function get_image( $image_id, $size )
	{
		// get the image size
		$image = wp_get_attachment_image_src( $image_id, $size, false );
		if( ! empty( $image ) ) {
			$image = array(
				'url'    => $image[0],
				'width'  => $image[1],
				'height' => $image[2],
				'alt'    => get_post_meta( $image_id, '_wp_attachment_image_alt', true ),
			);
			$image_caption = wp_get_attachment_caption( $image_id );
			return $this->image_setup( $image, $image_caption );
		}
		// get original image
		$image_url = wp_get_attachment_url( $image_id );
		if( ! empty( $image_url ) ) {
			$image_caption = wp_get_attachment_caption( $image_id );
			$image = array(
				'url'    => $image_url,
				'alt'    => get_post_meta( $image_id, '_wp_attachment_image_alt', true ),
				'width'  => 690,
				'height' => 430,
			);
			return $this->image_setup( $image, $image_caption );
		}
		// get placeholder image
		return $this->image_setup();		
	}

	private function image_setup( $image = array(), $caption = '' )
	{
		$default = array(
			'url'    => $this->placeholder_image_url,
			'alt'    => 'Placeholder image',
			'width'  => 690,
			'height' => 430,
		);
		$image = array_merge( $default, $image );
		return array(
			'caption' => $caption,
			'tag'     => sprintf(
				'<img class="lazyload" src="%s" data-src="%s" alt="%s" width="%s" height="%s" />', 
				$this->placeholder_image_url,
				esc_url( $image['url'] ),
				esc_attr( $image['alt'] ),
				$image['width'],
				'auto'
			)
		);
	}
}
?>