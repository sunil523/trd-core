<?php
namespace TRD\Widgets;

class Init
{

    public function __construct()
    {
        add_action( 'widgets_init', array( __CLASS__, 'register' ) );
    }

    public static function register()
    {
        self::register_areas();
        self::register_widgets();
    }

    public static function register_areas()
    {
        $areas = array(
            array(
                'id'            => 'trd_homepage_col_1',
                'name'          => 'TRD: Homepage - 1 Column',
                'before_widget' => '<div>',
                'after_widget'  => '</div>',
                'before_title'  => '',
                'after_title'   => '',
            ),
            array(
                'id'            => 'trd_sponsored_content',
                'name'          => 'TRD: Sponsored Content',
                'before_widget' => '<div>',
                'after_widget'  => '</div>',
                'before_title'  => '',
                'after_title'   => '',
            ),
        );

        foreach ( $areas as $area ) {
            register_sidebar( $area );
        }
    }

    public static function register_widgets()
    {
        if ( $handle = opendir( __DIR__ ) ) {
            while ( false !== ( $entry = readdir( $handle ) ) ) {
                if( in_array( $entry, array( '.', '..', 'Init.php' ) ) ) continue;
                $entry = str_replace( '.php', '', $entry );
                // register widget with namespace
                $widget = implode( '\\', array( __NAMESPACE__, $entry ) );
                register_widget( $widget );
            }
            closedir($handle);
        }
    }
}
