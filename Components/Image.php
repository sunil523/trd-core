<?php
namespace TRD\Components;

class Image
{

  final public static function GET_TAG( $image_id, $size )
  {
    $image = self::GET_IMAGE( $image_id, $size );
    return $image['tag'];
  }

  final public static function GET_IMAGE( $image_id, $size )
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
			return self::SETUP( $image, $image_caption );
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
			return self::SETUP( $image, $image_caption );
		}
		// get placeholder image
		return self::SETUP();		
	}

	final public static function SETUP( $image = array(), $caption = '' )
	{
		$default = array(
			'url'    => self::placeholder_image_url,
			'alt'    => 'Placeholder image',
			'width'  => 690,
			'height' => 430,
		);
		$image = array_merge( $default, $image );
		return array(
			'caption' => $caption,
			'tag'     => sprintf(
				'<img class="lazyload" src="%s" data-src="%s" alt="%s" width="%s" height="%s" />', 
				self::placeholder_image_url,
				esc_url( $image['url'] ),
				esc_attr( $image['alt'] ),
				$image['width'],
				'auto'
			)
		);
	}
}
?>