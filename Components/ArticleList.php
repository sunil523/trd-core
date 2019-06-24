<?php
namespace TRD\Components;

class ArticleList extends Article{

  public function __construct()
  {
    $this->template_name = 'article-list';
    parent::__construct();
  }

  public function display()
  {
    ?>
    <a class="trd-article trd-article-list<?php echo has_tag('trd-video') ? 'trd-video' : ''; ?>" href="<?php the_permalink(); ?>">
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
?>