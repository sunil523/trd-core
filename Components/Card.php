<?php
namespace TRD\Components;

class Card
{
  
  function __construct( $classes = array(), $attrs = array() )
  {
    $this->classes = array_merge( array('trd-card'), $classes );
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
      <div class="trd-card-image">
        <span class="trd-card-image-wrap"><?php echo Image::GET_TAG( get_post_thumbnail_id(), 'medium' ); ?></span>
      </div>
      <div class="trd-card-body">
        <h4 class="trd-card-title"><?php echo $this->get_title(); ?></h4>
      </div>
    </a>
    <?php
  }
}