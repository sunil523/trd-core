export default function ( options ){

  const defaults = {
    messageTimeout:  5000, // 5 seconds
    fixedEmbedTimer: 3000  // 3 seconds
  };

  let els;
  let fn;
  let handlers;
  let o = Object.assign( defaults, options );

  els = {
    $slideForm: $('.newsletter-slide'),
    $slideFormClose: $('.newsletter-slide-close-btn'),
    $form:        $('.newsletter-form'),
    $close:       $('#newsletter-form-close-btn'),
    $btn:         $('.newsletter-form').find('button[type=submit]'),
    $widget:      $('form[name=mc-embedded-subscribe-form], .newsletter .widget-embed-form'),
    $navlink:     $('.trd-nav-link[href*="/newsletter/"]'),
    $embed_close: $('.widget-embed-close'),
    $fixedWidget: $('.newsletter.widget-embed.widget-fixed'),
    $subscribeSubmit: false
  };

  fn = {
    init () {
      let timer = setInterval(() => {
        if (window.jQuery){
          fn.eventListener();
          clearInterval(timer);
        }
      }, 500);
    },

    registerEls () {
      els.$slideForm = $('.newsletter-slide');
      els.$slideFormClose = $('.newsletter-slide-close-btn');
      els.$form        = $('.newsletter-form');
      els.$close       = $('#newsletter-form-close-btn');
      els.$btn         = $('.newsletter-form').find('button[type=submit]');
      fn.eventListener();
    },

    eventListener () {
      els.$form.on( 'submit', handlers.save );
      els.$widget.on( 'submit', handlers.loadSlideForm );
      els.$navlink.on( 'click', handlers.loadSlideForm );
      els.$embed_close.on( 'click', handlers.fixedWidgetClose );
      els.$slideFormClose.on( 'click', handlers.close );
    },

    showSlideForm ( email ) {
      $( 'header.trd-header, .admin-bar' ).addClass( 'zdown' );
      if( $(document).width() < 782 ){
        $('html,body').addClass('overflow-off');
      }
      els.$slideForm.addClass( 'show' );
      els.$form.find( 'input[type=email]' ).val( email );
      setTimeout (() => {
        els.$form.on( 'submit', handlers.save );
        els.$form.on( 'reset', handlers.close );
      }, 1000);
    },

    trackEvent ( event, category, label ) {
      var dataObject = {
        'event': event,
        'category': category,
        'label': label
      };
      if(typeof dataLayer != 'undefined'){
        dataLayer.push(dataObject);
      }
    }
  };

  handlers = {
    save ( e ) {
      if( els.$subscribeSubmit ) return false;
      els.$subscribeSubmit = true; 
      els.$btn = els.$form.find('button[type=submit]');
      let data = {
        'action':   'newsletter_subscribe',
        'security': trd_ajax.security,
        'data':     {}
      };
      els.$form.find( 'input' ).each( function() {
        let val  = $(this).val();
        let name = $(this).attr('name');
        let type = $(this).attr('type');
        if( 'checkbox' === type ) {
          if( name !== 'subscribe_all') {
            data.data[ name ] = $(this).is(":checked");
          }
        } else if ( $.trim( val ) !== '' ) {
          data.data[ name ] = val;
        }
      });
      fn.trackEvent('newsletter_signup_form', 'subscribe', data.data['place']);
      els.$btn.text('Subscribing...').attr('disabled', 'disabled');
      jQuery.post( trd_ajax.url, data, function( response ) {
        els.$subscribeSubmit = false;
        els.$btn.text('Subscribe').removeAttr('disabled');
        $('.newsletter-form-button').hide();
        if( response.success ){
          $('.newsletter-form-success').show();
          fn.trackEvent('newsletter_signup_form', 'subscribe', 'success');
        } else {
          $('.newsletter-form-error').show();
          fn.trackEvent('newsletter_signup_form', 'subscribe', 'failed');
        }

        setTimeout(() => {
          $('.newsletter-form-success,.newsletter-form-error').hide();
          $('.newsletter-form-button').show();
        }, o.messageTimeout);
      });
      return false;
    },

    loadSlideForm (e) {
      e.preventDefault();
      let name = '';
      let email = '';
      let widget = $(this);
      let data = {
        'action':   'newsletter_slide_form',
        'security': trd_ajax.security
      };
      if( widget.prop('tagName') === 'FORM' ){
        name = widget.attr('name');
        email = widget.find('input[type=email], input[name=EMAIL]').val();
      } else if( widget.prop('tagName') === 'A' ) {
        name = widget.attr('class');
      } 
      if( $('.newsletter-form.page').length ){
        $('.newsletter-form.page').find('input[type=email]').val( email ).focus();
        $('html,body').animate({ scrollTop: 0 }, 500);
      }
      else if( els.$slideForm.length ){
        fn.showSlideForm( email );
      } else {
        jQuery.post( trd_ajax.url, data, function( response ) {
          if( response ){
            $('body').append( response );
            fn.showSlideForm( email );
            fn.registerEls();
          }
        });
      }
      fn.trackEvent( 'newsletter_sinup_open', e.type, name );
      return false;
    },

    close ( e ) {
      handlers.fixedWidgetClose( e );
      els.$slideForm.removeClass('show');
      $('html,body').removeClass('overflow-off');
      setTimeout(() => {
        $('header.trd-header, .admin-bar').removeClass('zdown');
      }, 500);
    },

    fixedWidgetClose ( e ) {
      $('.newsletter.widget-embed.widget-fixed').removeClass('show');
    },

    showFixedWidget ( e ) {
      $('.newsletter.widget-embed.widget-fixed').addClass('show');
    }
    
  };

  fn.init();
};