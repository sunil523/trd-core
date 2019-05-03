<?php
namespace TRD\Core\WP;

class Nav_Walker extends \Walker
{

  function start_lvl( &$output, $depth = 0, $args = array() ) 
  {
    return $output;
  }

  function end_lvl( &$output, $depth = 0, $args = array() )
  {
    return $output;
  }
  /**
     * At the start of each element, output a <li> and <a> tag structure.
     * 
     * Note: Menu objects include url and title properties, so we will use those.
     */
    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
      $output .= sprintf( "\n<a href='%s'%s>%s</a>\n",
          $item->url,
          ( $item->object_id === get_the_ID() ) ? ' class="current"' : '',
          $item->title
      );
  }
}

?>