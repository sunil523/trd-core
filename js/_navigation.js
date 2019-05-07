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
  $('.nav-menu-btn').on('click', function(){
    console.log($(this).hasClass('close'));
    if($(this).hasClass('close')){
      $('.nav-secondry').removeClass('show');
      $('html,body').removeClass('overflow-off');
    }else{
      $('.nav-secondry').addClass('show');
      $('html,body').addClass('overflow-off');
    }
  });
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