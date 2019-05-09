<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
<header class="trd-header nav">
  <div class="nav-primary">
    <div class="container">
      <div class="nav-mobile">
        <div class="nav-menu-btn"><i class="fa fas fa-bars"></i></div>
      </div>
      <div class="nav-left">
        <a class="nav-logo" href="/"><img src="<?php echo \TRD\Core\Navigation::Logo(); ?>"></a>
      </div>
      <div class="nav-mid show-right">
        <div class="nav-mid-left mobile"><i class="fa fas fa-chevron-left"></i></div>
        <?php
          wp_nav_menu( array(
            'menu'       => 'nav-regions',
            'menu_class' => 'nav-regions',
            'container'  => '',
            'items_wrap' => '<nav id="%1$s" class="%2$s">%3$s</nav>',
            'walker'     => new \TRD\Core\WP\Nav_Walker(),
            'location'   => 'header_primary',
          ) );
        ?>
        <div class="nav-mid-right mobile"><i class="fa fas fa-chevron-right"></i></div>
      </div>
      <div class="nav-right">
        <div class="nav-search">
          <div class="nav-search-btn"><i class="fa fa-search"></i></div>
          <form class="nav-search-form" autocomplete="off">
            <input class="nav-search-input" type="search" id="nav-search-input" name="s" autocomplete="off" placeholder="search" autofocus>
          </form>
        </div>
        <?php
          wp_nav_menu( array(
            'menu'       => 'my-account-login',
            'menu_class' => 'nav-account nav-account-login',
            'container'  => '',
            'items_wrap' => '<nav id="%1$s" class="%2$s">%3$s</nav>',
            'walker'     => new \TRD\Core\WP\Nav_Walker(),
            'location'   => 'header_account_login',
          ) );
          wp_nav_menu( array(
            'menu'       => 'my-account-logout',
            'menu_class' => 'nav-account nav-account-logout hide',
            'container'  => '',
            'items_wrap' => '<nav id="%1$s" class="%2$s">%3$s</nav>',
            'walker'     => new \TRD\Core\WP\Nav_Walker(),
            'location'   => 'header_account_logout',
          ) );
        ?>
      </div>
    </div>
  </div>
  <div class="nav-secondary">
    <div class="container">
      <div class="nav-mobile">
        <div class="nav-menu-btn close"><i class="fa fas fa-close"></i></div>
      </div>
      <div class="nav-left">
        <a class="nav-logo" href="/"><img src="<?php echo \TRD\Core\Navigation::Logo(); ?>"></a>
      </div>
      <div class="nav-mid">
        <?php
        wp_nav_menu( array(
          'menu'       => 'my-account-login',
          'menu_class' => 'nav-account nav-account-login mobile ',
          'container'  => '',
          'items_wrap' => '<nav id="%1$s" class="%2$s">%3$s</nav>',
          'walker'     => new \TRD\Core\WP\Nav_Walker(),
          'location'   => 'header_account_login',
        ) );
        wp_nav_menu( array(
          'menu'       => 'my-account-logout',
          'menu_class' => 'nav-account nav-account-logout mobile hide',
          'container'  => '',
          'items_wrap' => '<nav id="%1$s" class="%2$s">%3$s</nav>',
          'walker'     => new \TRD\Core\WP\Nav_Walker(),
          'location'   => 'header_account_logout',
        ) );
        wp_nav_menu( array(
          'menu'       => 'nav-sections',
          'menu_class' => 'nav-sections',
          'container'  => '',
          'items_wrap' => '<nav id="%1$s" class="%2$s">%3$s</nav>',
          'walker'     => new \TRD\Core\WP\Nav_Walker(),
          'location'   => 'header_secondary',
        ) );
      ?></div>
      <div class="nav-right"><?php
          wp_nav_menu( array(
            'menu'       => 'nav-social',
            'menu_class' => 'nav-social',
            'container'  => '',
            'items_wrap' => '<nav id="%1$s" class="%2$s">%3$s</nav>',
            'walker'     => new \TRD\Core\WP\Nav_Walker(),
            'location'   => 'header_social',
          ) );
      ?></div>
    </div>
  </div>
</header>