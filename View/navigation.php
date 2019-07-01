<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
<header id="trd-header" class="trd-header trd-nav">
  <div class="trd-nav-primary">
    <div class="trd-nav-container">
      <div class="trd-nav-mobile">
        <div class="trd-nav-menu-btn"><i class="fa fas fa-bars"></i></div>
      </div>
      <div class="trd-nav-left">
        <a class="trd-nav-logo" href="<?php echo site_url(); ?>"><img src="<?php echo \TRD\Core\Navigation::Logo(); ?>"></a>
      </div>
      <div class="trd-nav-mid show-right">
        <div class="trd-nav-mid-left mobile"><i class="fa fas fa-chevron-left"></i></div>
        <?php
          wp_nav_menu( array(
            'menu'       => 'nav-regions',
            'menu_class' => 'trd-nav-regions',
            'container'  => '',
            'items_wrap' => '<nav id="%1$s" class="%2$s">%3$s</nav>',
            'walker'     => new \TRD\Core\WP\Nav_Walker(),
            'location'   => 'header_primary',
          ) );
        ?>
        <div class="trd-nav-mid-right mobile"><i class="fa fas fa-chevron-right"></i></div>
      </div>
      <div class="trd-nav-right">
        <div class="trd-nav-search">
          <div class="trd-nav-search-btn"><i class="fa fa-search"></i></div>
          <form class="trd-nav-search-form" autocomplete="off" method="get" action="<?php echo \TRD\Core\Navigation::SearchLink('/google-search/'); ?>">
            <input class="trd-nav-search-input" type="search" id="nav-search-input" name="q" autocomplete="off" placeholder="search" autofocus>
          </form>
        </div>
        <?php
          wp_nav_menu( array(
            'menu'       => 'my-account-login',
            'menu_class' => 'trd-nav-account trd-nav-account-login',
            'container'  => '',
            'items_wrap' => '<nav id="%1$s" class="%2$s">%3$s</nav>',
            'walker'     => new \TRD\Core\WP\Nav_Walker(),
            'location'   => 'header_account_login',
          ) );
          wp_nav_menu( array(
            'menu'       => 'my-account-logout',
            'menu_class' => 'trd-nav-account trd-nav-account-logout hide',
            'container'  => '',
            'items_wrap' => '<nav id="%1$s" class="%2$s">%3$s</nav>',
            'walker'     => new \TRD\Core\WP\Nav_Walker(),
            'location'   => 'header_account_logout',
          ) );
        ?>
      </div>
    </div>
  </div>
  <div class="trd-nav-secondary">
    <div class="trd-nav-container">
      <div class="trd-nav-mobile">
        <div class="trd-nav-menu-btn close"><i class="fa fas fa-close fa-times"></i></div>
      </div>
      <div class="trd-nav-left">
        <a class="trd-nav-logo" href="<?php echo site_url(); ?>"><img src="<?php echo \TRD\Core\Navigation::Logo(); ?>"></a>
      </div>
      <div class="trd-nav-mid">
        <?php
        wp_nav_menu( array(
          'menu'       => 'my-account-login',
          'menu_class' => 'trd-nav-account trd-nav-account-login mobile ',
          'container'  => '',
          'items_wrap' => '<nav id="%1$s" class="%2$s">%3$s</nav>',
          'walker'     => new \TRD\Core\WP\Nav_Walker(),
          'location'   => 'header_account_login',
        ) );
        wp_nav_menu( array(
          'menu'       => 'my-account-logout',
          'menu_class' => 'trd-nav-account trd-nav-account-logout mobile hide',
          'container'  => '',
          'items_wrap' => '<nav id="%1$s" class="%2$s">%3$s</nav>',
          'walker'     => new \TRD\Core\WP\Nav_Walker(),
          'location'   => 'header_account_logout',
        ) );
        wp_nav_menu( array(
          'menu'       => 'nav-sections',
          'menu_class' => 'trd-nav-sections',
          'container'  => '',
          'items_wrap' => '<nav id="%1$s" class="%2$s">%3$s</nav>',
          'walker'     => new \TRD\Core\WP\Nav_Walker(),
          'location'   => 'header_secondary',
        ) );
      ?></div>
      <div class="trd-nav-right"><?php
          wp_nav_menu( array(
            'menu'       => 'nav-social',
            'menu_class' => 'trd-nav-social',
            'container'  => '',
            'items_wrap' => '<nav id="%1$s" class="%2$s">%3$s</nav>',
            'walker'     => new \TRD\Core\WP\Nav_Walker(),
            'location'   => 'header_social',
          ) );
      ?></div>
    </div>
  </div>
</header>