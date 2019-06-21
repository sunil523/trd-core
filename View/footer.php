<footer class="trd-footer">
  <div class="trd-footer-container">
    <div class="row">
    <?php
      wp_nav_menu( array(
        'menu_class' => 'trd-nav-regions',
        'container'  => '',
        'items_wrap' => '<section id="%1$s" class="%2$s">%3$s</section>',
        'walker'     => new \TRD\Core\WP\Nav_Walker(),
        'location'   => 'footer_primary',
      ) );
    ?>
    </div>
  </div>
</footer>