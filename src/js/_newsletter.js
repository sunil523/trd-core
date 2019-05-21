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
    $widgetSlide: $('.newsletter-slide'),
    $form:        $('.newsletter-form'),
    $close:       $('#newsletter-form-close-btn'),
    $btn:         $('.newsletter-form').find('button[type=submit]'),
    $subAll:     $('#newsletter_subscribe_all'),
    $widget:      $('form[name=mc-embedded-subscribe-form], .newsletter .widget-embed-form'),
    $navlink:     $('.trd-nav-link[href*="/newsletter/"]'),
    $embed_close: $('.widget-embed-close'),
    $fixedWidget: $('.newsletter.widget-embed.widget-fixed')
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

    eventListener () {
      els.$form.on( 'submit', handlers.save );
      els.$widget.on( 'submit', handlers.loadWidgetForm );
      els.$navlink.on( 'click', handlers.loadWidgetForm );
      els.$embed_close.on( 'click', handlers.closeFixedWidget );
      els.$subAll.on( 'change', handlers.subscribeAll );
    },

    registerEls () {
      els.$widgetSlide = $('.newsletter-widget');
      els.$form        = $('.newsletter-form');
      els.$close       = $('#newsletter-form-close-btn');
      els.$btn         = $('.newsletter-form').find('button[type=submit]');
      els.$subAll      = $('#newsletter_subscribe_all');
    },

    fixedEmbed () {
      if( els.$fixedWidget.length ){
        setTimeout(() => {
          handlers.showFixedWidget();
        }, o.fixedEmbedTimer);
      }
    },

    showWidgetForm ( email ) {
      els.$subAll.prop( 'checked', true ).trigger( 'change' );
      $( 'header.trd-header, .admin-bar' ).addClass( 'zdown' );
      $('html,body').addClass('overflow-off');
      els.$widgetSlide.addClass( 'show' );
      els.$form.find( 'input[type=email]' ).val( email );
      setTimeout (() => {
        els.$form.on( 'submit', handlers.save );
        els.$form.on( 'reset', handlers.close );
      }, 1000);
    }
  };

  handlers = {
    save ( e ) {
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
      els.$btn.text('Subscribing...').attr('disabled', 'disabled');
      jQuery.post( trd_ajax.url, data, function( response ) {
        els.$btn.text('Subscribe').removeAttr('disabled');
        $('.newsletter-form-button').hide();
        if( response.success ){
          $('.newsletter-form-success').show();
        } else {
          $('.newsletter-form-error').show();
        }

        setTimeout(() => {
          $('.newsletter-form-success,.newsletter-form-error').hide();
          $('.newsletter-form-button').show();
        }, o.messageTimeout);
      });
      return false;
    },

    loadWidgetForm (e) {
      e.preventDefault();
      let data = {
        'action':   'newsletter_widget_form',
        'security': trd_ajax.security
      };
      let widget = $(this);
      let email = widget.find('input[type=email], input[name=EMAIL]').val();
      if( $('.newsletter-form.page').length ){
        $('.newsletter-form.page').find('input[type=email]').val( email ).focus();
        $('html,body').animate({ scrollTop: 0 }, 500);
      }
      else if( els.$widgetSlide.length ){
        fn.showWidgetForm( email );
      } else {
        jQuery.post( trd_ajax.url, data, function( response ) {
          if( response ){
            $('body').append( response );
            fn.showWidgetForm( email );
            fn.registerEls();
          }
        });
      }
      return false;
    },

    close ( e ) {
      els.$widgetSlide.removeClass('show');
      els.$subAll.prop('checked', true).trigger('change');
      $('html,body').removeClass('overflow-off');
      setTimeout(() => {
        $('header.trd-header, .admin-bar').removeClass('zdown');
      }, 500);
    },

    closeFixedWidget ( e ) {
      $('.newsletter.widget-embed.widget-fixed').removeClass('show');
    },

    showFixedWidget ( e ) {
      $('.newsletter.widget-embed.widget-fixed').addClass('show');
    },

    subscribeAll ( e ) {
      let checked = $(this).prop("checked");
      els.$form.find('.checkboxes input[type=checkbox]').prop('checked', checked);
    }
  };

  fn.init();
};