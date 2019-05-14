function init_piano() {
  window.tp.push(["init", function(){
    if( $('.trd-nav-account-login, .trd-nav-account-logout') != null ) {
      if( window.tp.pianoId.isUserValid() ) {
        $('.trd-header.trd-nav').addClass('trd-nav-loggedin');
        $('.trd-nav-account-login').addClass('hide');
        $('.trd-nav-account-logout').removeClass('hide');
        $('.trd-nav-account-logout a[href*="/#logout"]').on('click', function( event ){
          event.preventDefault();
          window.tp.pianoId.logout();
          window.location.reload();
        });
      } else {
        $('.trd-nav-account-logout').addClass('hide');
        $('.trd-nav-account-login').removeClass('hide');
        $('.trd-nav-account-login a[href*="/#login"]').on('click', function( event ){
          event.preventDefault();
          window.tp.pianoId.show({
            screen:      'login',
            displayMode: 'modal',
            loggedIn:    function () {
              window.location.reload();
            }
          });
          return false;
        });
      }
    }
  }]);
}
function init_trd_nav() {
  $('.trd-nav-menu-btn').on('click', function() {
    if($(this).hasClass('close')){
      $('.trd-nav-secondary').removeClass('show');
      $('html,body').removeClass('overflow-off');
    }else{
      $('.trd-nav-secondary').addClass('show');
      $('html,body').addClass('overflow-off');
    }
  });
  $('.trd-nav-search-btn').on('click', () => {
    $('.trd-nav-search').toggleClass('show');
  });
  $('.trd-nav-mid-left').on('click', () => {
    let scroll = nav_scroll_left();
    $('.trd-nav-regions').animate({
      scrollLeft: scroll - 100
    }, 1000);
  });
  $('.trd-nav-mid-right').on('click', () => {
    let scroll = nav_scroll_left();
    $('.trd-nav-regions').animate({
      scrollLeft: scroll + 100
    }, 1000);
  });
  $('.trd-nav-regions').on('scroll', () => {
    nav_scroll_left();
  });

  function nav_scroll_left(){
    let lscroll = $('.trd-nav-regions').scrollLeft();
    let rscroll = get_total_nav_item_width();
    let width   = window.nav_regions_width;
    if( lscroll > 0){
      $('.trd-nav-mid').addClass('show-left');
    }else{
      $('.trd-nav-mid').removeClass('show-left');
    }
    if( rscroll <= (width + lscroll) ){
      $('.trd-nav-mid').removeClass('show-right');
    }else{
      $('.trd-nav-mid').addClass('show-right');
    }
    return lscroll;
  }

  function get_total_nav_item_width(){
    if(!window.nav_item_width){
      window.nav_regions_width = $('.trd-nav-regions').width();
      window.nav_item_width = 0;
      $('.trd-nav-regions .trd-nav-item').each(function() {
        window.nav_item_width += $(this).width();
      });
    }
    return window.nav_item_width;
  }
}
let piano_timer = setInterval(() => {
  if ( typeof window.tp === 'object' ){
    init_piano();
    clearInterval( piano_timer );
  }
}, 500);

let nav_timer = setInterval(() => {
  if ( typeof window.jQuery === 'function' ){
    init_trd_nav();
    clearInterval( nav_timer );
  }
}, 500);