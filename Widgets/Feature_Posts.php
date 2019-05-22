<?php
namespace TRD\Widgets;

class Feature_Posts extends \WP_Widget {

    private $posts      = array(); // cache the posts we query
    private $count      = 5;       // total post for the widget
    private $limit      = 100;     // total post to get from query
    private $newsletter = true;   // display newsletter form with this widget

	/**
     * class constructor
     */
     public function __construct()
    {
        parent::__construct(
            'trd_weidgets_feature_posts',
            'TRD: Feature Posts',
            array( 
                'description' => 'Feature posts on the page.',
            )
        );

        $this->posts = $this->get_posts();
    }
    
    /**
	 * output the widget content on the front-end
     */
     public function widget( $args, $instance )
    {
        if( empty( $instance ) || empty( $instance[ 'post_ids' ] ) ) return;
        $cover_story = array_shift( $instance[ 'post_ids' ] );
        $top_stories = $instance['post_ids'];
        $this->newsletter = $instance['newsletter'];
        // Display
        echo '<div class="top-stories">';
            $this->display_cover_story( $cover_story );
            $this->display_top_stories( $top_stories );
        echo '</div>';
    }

    /**
     * output the option form field in admin Widgets screen
     */
    public function form( $instance )
    {
        $post_ids = ! empty( $instance['post_ids'] ) ? $instance['post_ids'] : array();
        for ( $i=0; $i<$this->count; $i++ ) { 
            ?>
            <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'post_ids_'.$i ) ); ?>">
            <?php esc_attr_e( 'Post #'.($i+1), 'trd' ); ?>
            </label> 
            
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_ids'.$i ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_ids' ) ); ?>[]" required>
                <?php $this->build_options( $this->posts, $post_ids[ $i ] ); ?>
            </select>
            </p>
            <?php
        }
        ?>
        <!-- <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'newsletter' ) ); ?>">
                <input class="widefat" type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'newsletter' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'newsletter' ) ); ?>" <?php echo $instance['newsletter'] ? 'checked' : ''; ?>>
                <?php esc_attr_e( 'Display newsletter form?', 'trd' ); ?>
            </label>
        </p> -->
        <?php
    }

	/**
     * Update the value for the widget
     */
    public function update( $new_instance, $old_instance )
    {
        $instance = array();
        $instance['post_ids']   = empty( $new_instance['post_ids'] ) ? array() : $new_instance['post_ids'];
        $instance['newsletter'] = empty( $new_instance['newsletter'] ) ? false : true;
        return $instance;
    }

    /**
     * PRIVATE METHODS
     */

    /**
     * Get posts for users to select from
     * @return array  of WP_Post Objects
     */
    private function get_posts()
    {
        $args = array(
            'posts_per_page'   => $this->limit,
            'orderby'          => 'date',
            'order'            => 'DESC',
            'post_type'        => 'post',
            'post_status'      => 'publish',
            'suppress_filters' => true,
            // 'date_query' => array(
            //     array(
            //         'before'    => date( 'Y-m-d', strtotime('-2 days') ),
            //         'after'     => date('Y-m-d'),
            //         'inclusive' => true,
            //     ),
            // ),
        );
        return get_posts( $args );
    }

    /**
     * Build option tag for select tags
     * @return void
     */
    private function build_options( $options, $value = '' )
    {
        if ( empty( $options ) ) echo '<option value="" disabled>--no post found--</option>';
        echo '<option value="" disabled>--Select a Post--</option>';
        foreach ( $options as $post ) {
            $selected = ( $value == $post->ID ) ? 'selected' : '';
            echo sprintf('<option value="%s" %s>%s</option>', $post->ID, $selected, $post->post_title);
        }
    }

    /**
     * Display cover story for given post
     * @return void
     */
    private function display_cover_story( $post_id )
    {
        $post = get_post( $post_id );
        if( is_wp_error( $post ) ) return;
        // setup_postdata( $post );
        // Display
        $link  = get_the_permalink( $post->ID );
        $title = get_the_title( $post->ID );
        $f_title = get_post_meta( $post->ID, 'Featured Image Headline', true );
        $image = get_field('second_featured_image', $post->ID ); // custom fields plugins
        if( empty( $f_title) ) $f_title = $title;
        if( !empty( $image ) )
        {
            if(!is_array($image)) $image = array('url' => wp_get_attachment_url($image));
            // setup the image tag
            $image['tag'] = sprintf( '<img class="lazyload" src="%s" alt="">', $image['url'] );
            // lazyload image if layload function exists
            $image['tag'] = function_exists( 'prepare_images_for_lazyload' ) ? prepare_images_for_lazyload( $image['tag'] ) : $image['tag'];
        }else{
            $image = array(
                'tag' => '<img class="lazyload" src="https://therealdeal.com/wp-content/uploads/2014/07/trd-related-200x143.jpg" alt="" width="690">',
                'caption' => ''
            );
        }
        ?>
            <div class="cover-story">
                <div class="meta">
                    <h3 class="top-slider-title">
                        <?php echo sprintf( '<a href="%s" title="%s">%s</a>', $link, $title, $f_title ); ?>
                    </h3>
                </div>
                <div class="image"><?php
                    // display image
                    echo sprintf( '<a href="%s">%s</a>', $link, $image['tag'] );
                    // display caption
                    echo sprintf('<div class="cover-story-caption"><em>%s</em></div>', $image['caption']);
                ?></div>
            </div>
        <?php
        // wp_reset_postdata();
    }

    /**
     * Display top stories next to cover story
     * @return void
     */
    private function display_top_stories( $post_ids )
    {
        ?>
        <div class="top-story-side">
            <table>
                <?php
                    foreach ( $post_ids as $post_id ) {
                        $this->display_top_story( $post_id );
                    }
                ?>
            </table>
            <?php signup_form('homepage_feature_posts'); ?>
        </div>
        <?php
    }

    /**
     * Display top story for given post
     * @return void
     */
    private function display_top_story( $post_id )
    {
        $post = get_post( $post_id );
        if( is_wp_error( $post ) ) return;
        // Display
        $link     = get_the_permalink( $post->ID );
        $title    = get_the_title( $post->ID );
        $image    = get_post_images( $post->ID , 140, 99, 1 );
        $headline = get_post_meta( $post->ID, 'Featured Image Headline', true );
        $f_title  = !empty( $headline ) ? $headline : wp_trim_words( $title , 9, '...' );
        if( empty( $image ) ) $image = '<img class="lazyload" src="https://therealdeal.com/wp-content/uploads/2014/07/trd-related-200x143.jpg" alt="" width="140">';
        ?>
        <tr>
            <td class="top-side-featured">
                <div class="image top-side-featured-image"><?php
                    echo sprintf( '<a href="%s">%s</a>', $link, $image );
                ?></div>
                <h3><?php
                    echo sprintf( '<a style="text-align:left;" href="%s" title="%s">%s</a>', $link, $title, $f_title );
                ?></h3>
            </td>
        </tr>
        <?php
    }
}

?>