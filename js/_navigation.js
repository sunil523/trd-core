function init_piano() {
  window.tp.push(["init", function(){
    if( $('.nav-account-login, .nav-account-logout') != null ) {
      if( window.tp.pianoId.isUserValid() ) {
        $('.trd-header.nav').addClass('nav-logined');
        $('.nav-account-login').addClass('hide');
        $('.nav-account-logout').removeClass('hide');
        $('.nav-account-logout a[href*="/#logout"]').on('click', function( event ){
          event.preventDefault();
          window.tp.pianoId.logout();
          window.location.reload();
        });
      } else {
        $('.nav-account-logout').addClass('hide');
        $('.nav-account-login').removeClass('hide');
        $('.nav-account-login a[href*="/#login"]').on('click', function( event ){
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
  $('.nav-menu-btn').on('click', function() {
    if($(this).hasClass('close')){
      $('.nav-secondary').removeClass('show');
      $('html,body').removeClass('overflow-off');
    }else{
      $('.nav-secondary').addClass('show');
      $('html,body').addClass('overflow-off');
    }
  });
  $('.nav-search-btn').on('click', () => {
    $('.nav-search').toggleClass('show');
  });
  $('.nav-mid-left').on('click', () => {
    let scroll = nav_scroll_left();
    $('.nav-regions').animate({
      scrollLeft: scroll - 100
    }, 1000);
  });
  $('.nav-mid-right').on('click', () => {
    let scroll = nav_scroll_left();
    $('.nav-regions').animate({
      scrollLeft: scroll + 100
    }, 1000);
  });
  $('.nav-regions').on('scroll', () => {
    nav_scroll_left();
  });

  function nav_scroll_left(){
    let lscroll = $('.nav-regions').scrollLeft();
    let rscroll = get_total_nav_item_width();
    let width   = window.nav_regions_width;
    if( lscroll > 0){
      $('.nav-mid').addClass('show-left');
    }else{
      $('.nav-mid').removeClass('show-left');
    }
    if( rscroll <= (width + lscroll) ){
      $('.nav-mid').removeClass('show-right');
    }else{
      $('.nav-mid').addClass('show-right');
    }
    return lscroll;
  }

  function get_total_nav_item_width(){
    if(!window.nav_item_width){
      window.nav_regions_width = $('.nav-regions').width();
      window.nav_item_width = 0;
      $('.nav-regions .nav-item').each(function() {
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