<?php
namespace TRD\Core\WP;

class Nav_Walker extends \Walker_Nav_Menu
{

  function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 )
  {
    if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
      $t = '';
      $n = '';
    } else {
      $t = "\t";
      $n = "\n";
    }
    $indent = ( $depth ) ? str_repeat( $t, $depth ) : '';
    $class_names = array('nav-item');
    if( in_array('menu-item-has-children', $item->classes) ) array_push($class_names, 'sub');
    $item->current ? array_push($class_names, 'current') : '';
    $class_names = $class_names ? ' class="' . esc_attr( implode(" ", $class_names) ) . '"' : '';
    $output .= $indent . '<div' . $class_names . '>';

    $atts                 = array();
    $atts['title']        = ! empty( $item->attr_title ) ? $item->attr_title : '';
    $atts['target']       = ! empty( $item->target ) ? $item->target : '';
    $atts['rel']          = ! empty( $item->xfn ) ? $item->xfn : '';
    $atts['href']         = ! empty( $item->url ) ? $item->url : '';
    $atts['aria-current'] = $item->current ? 'page' : '';
    $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
    $attributes = '';
    foreach ( $atts as $attr => $value ) {
        if ( ! empty( $value ) ) {
            $value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
            $attributes .= ' ' . $attr . '="' . $value . '"';
        }
    }
    $title = apply_filters( 'the_title', $item->title, $item->ID );
    $title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

    $href = trim(str_replace(site_url(), '', $item->url), '/');
    $item_output  = $args->before;
    $item_output .= ( !empty($href) && $href != '#' ) ? '<a' . $attributes . '>' : '<span>';
    $item_output .= $args->link_before . $title . $args->link_after;
    $item_output .= ( !empty($href) && $href != '#' ) ? '</a>' : '</span>';
    $item_output .= $args->after;
    $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
  }

  function end_el( &$output, $item, $depth = 0, $args = array() )
  {
    if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
      $t = '';
      $n = '';
    } else {
      $t = "\t";
      $n = "\n";
    }
    $output .= '</div>';
  }

  function start_lvl( &$output, $depth = 0, $args = array() ) 
  {
    if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
      $t = '';
      $n = '';
  } else {
      $t = "\t";
      $n = "\n";
  }
  $indent = str_repeat( $t, $depth );

  // Default class.
  $classes = array( 'nav-sub' );
  $class_names = join( ' ', apply_filters( 'nav_menu_submenu_css_class', $classes, $args, $depth ) );
  $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

  $output .= "{$n}{$indent}<nav$class_names>{$n}";
  }

  function end_lvl( &$output, $depth = 0, $args = array() )
  {
    if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
      $t = '';
      $n = '';
    } else {
      $t = "\t";
      $n = "\n";
    }
    $indent  = str_repeat( $t, $depth );
    $output .= "$indent</nav>{$n}";
  }
}

?>