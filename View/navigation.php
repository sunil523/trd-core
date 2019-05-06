<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
<header class="trd-header nav">
  <div class="nav-primary">
    <div class="container">
      <div class="nav-left">
        <a class="nav-logo" href="/"><img src="<?php echo TRD_CORE_URL.'trd-logo.svg'; ?>"></a>
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
      </div>
      <div class="nav-right">
        <form class="nav-search-form" autocomplete="off">
          <input class="nav-search" type="search" id="nav-search" name="s" autocomplete="off">
          <button type="submit"><i class="fa fa-search"></i></button>
          <label class="nav-search-label" for="nav-search">
            <i class="fa fa-search"></i>
          </label>
        </form>
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
            'menu'       => 'my-account-login',
            'menu_class' => 'nav-account nav-account-logout',
            'container'  => '',
            'items_wrap' => '<nav id="%1$s" class="%2$s">%3$s</nav>',
            'walker'     => new \TRD\Core\WP\Nav_Walker(),
            'location'   => 'header_account_login',
          ) );
        ?>
      </div>
    </div>
  </div>
  <div class="nav-secondry">
    <div class="container">
      <div class="nav-left"><?php
        wp_nav_menu( array(
          'menu'       => 'nav-sections',
          'menu_class' => 'nav-sections',
          'container'  => '',
          'items_wrap' => '<nav id="%1$s" class="%2$s">%3$s</nav>',
          'walker'     => new \TRD\Core\WP\Nav_Walker(),
          'location'   => 'header_secondry',
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