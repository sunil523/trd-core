export default function ( options ){

  const defaults = {
    messageTimeout:  5000, // 5 seconds
    fixedEmbedTimer: 3000, // 3 seconds
    navPosition: $('.trd-nav').offset().top,
    prevScrollpos: 130
  };

  let els;
  let fn;
  let handlers;
  let o = Object.assign( defaults, options );

  fn = {
    init() {
      let piano_timer = setInterval(() => {
        if ( typeof window.tp === 'object' ){
          fn.piano();
          clearInterval( piano_timer );
        }
      }, 500);
      
      let nav_timer = setInterval(() => {
        if ( typeof window.jQuery === 'function' ){
          fn.trd_nav();
          clearInterval( nav_timer );
        }
      }, 500);
      fn.eventListener();
    },

    eventListener () {
      $(window).on('scroll', handlers.navFixed );
    },

    
    piano() {
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
    },

    trd_nav() {
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
        let scroll = fn.nav_scroll_left();
        $('.trd-nav-regions').animate({
          scrollLeft: scroll - 100
        }, 1000);
      });
      $('.trd-nav-mid-right').on('click', () => {
        let scroll = fn.nav_scroll_left();
        $('.trd-nav-regions').animate({
          scrollLeft: scroll + 100
        }, 1000);
      });
      $('.trd-nav-regions').on('scroll', () => {
        fn.nav_scroll_left();
      });
    },

    nav_scroll_left(){
      let lscroll = $('.trd-nav-regions').scrollLeft();
      let rscroll = fn.get_total_nav_item_width();
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
    },

    get_total_nav_item_width(){
      if(!window.nav_item_width){
        window.nav_regions_width = $('.trd-nav-regions').width();
        window.nav_item_width = 0;
        $('.trd-nav-regions .trd-nav-item').each(function() {
          window.nav_item_width += $(this).width();
        });
      }
      return window.nav_item_width;
    }
  };

  handlers = {
    navFixed ( e ) {
      let width = $(document).width();
      let scrollTop = $(window).scrollTop();
      if( scrollTop > o.navPosition ) {
        $('body').addClass('is-sticky');
      } else {
        $('body').removeClass('is-sticky');
      }
      console.log(o.prevScrollpos, scrollTop, o.navPosition, o.prevScrollpos < scrollTop, scrollTop >= o.navPosition);
      if (o.prevScrollpos < scrollTop && scrollTop >= o.navPosition) {
        $("body").addClass('is-move-out');
        o.prevScrollpos = scrollTop;
      } else {
        $("body").removeClass('is-move-out');
      }
    }
  };

  fn.init();
};