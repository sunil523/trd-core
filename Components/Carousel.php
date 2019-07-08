<?php
namespace TRD\Components;

class Carousel
{
  
  function __construct( $classes = array(), $attrs = array() )
  {
    $this->classes = array_merge( array('trd-carousel'), $classes );
    $this->attrs   = $attrs;
  }

  public function display()
  {
    if( has_tag('trd-video') ) array_push( $this->classes, 'trd-video' );
    $attrs = '';
    foreach ($this->attrs as $key => $value) {
      if( in_array( $key, array( 'class', 'href' ) ) ) continue;
      $attrs .= sprintf( ' %s="%s"', $key, $value );
    }
    ?>
    <a class="<?php echo implode( ' ', $this->classes ); ?>" href="<?php the_permalink(); ?>"<?php echo $attrs; ?>>
      <div class="trd-article-image">
        <span class="trd-article-image-wrap"><?php echo $this->get_image_tag( get_post_thumbnail_id(), 'blogroll' ); ?></span>
      </div>
      <div class="trd-article-body">
        <h4 class="trd-article-title"><?php echo $this->get_title(); ?></h4>
        <div class="trd-article-meta"><?php 
          $meta = array();
          if( $author = $this->get_author() ) array_push( $meta, $author );
          array_push( $meta, get_the_time('F d, Y h:iA') );
          echo implode( ' | ', $meta );
        ?></div>
        <div class="trd-article-meta"><?php echo $this->get_sources(); ?></div>
        <p class="trd-article-excerpt"><?php echo $this->get_excerpt(); ?></p>
      </div>
    </a>
    <?php
  }
}