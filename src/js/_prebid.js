import PREBID_BIDDERS    from './prebid/_bidder';
import PREBID_POSITIONS from './prebid/_positions';

export default function( options ){
  
  const defaults = {
    networkCode:      1015965,
    topLevel:         "trd-ny",
    s1:               "hp",
    pid:              "homepage",
    pgtype:           "landing",
    category:         null,
    tags:             null,
    breakpoint:       "mobile",
    slotName:         null,
    test:             false,
    adsStart:         (new Date()).getTime(),
    PREBID_TIMEOUT:   1000,
    FAILSAFE_TIMEOUT: 3000,
    adUnits:          [],
    _adUnits:         [],
    debug:            false
  };

  let fn;
  let o = Object.assign( defaults, options );
  o.slotName = "/" + o.networkCode + "/" + o.topLevel + "/" + o.s1;


  fn = {
    init () {
      fn.updateTest();
      fn.updateBreakpointWidth();
      fn.updateAdUnits();
      window.googletag = window.googletag || {};
      window.googletag.cmd = window.googletag.cmd || {};
      window.pbjs = window.pbjs || {};
      window.pbjs.que = window.pbjs.que || {};
    },

    run () {
      fn.queUnits();
      fn.queGoogleTags();
    },

    updateTest() {
      let CurrentURL = new URLSearchParams(window.location.search.slice(1));
      o.test = CurrentURL.get('test');
    },

    getScreenWidth () {
      return window.screen.width || window.innerWidth || window.document.documentElement.clientWidth || Math.min(window.innerWidth, window.document.documentElement.clientWidth) || window.innerWidth || window.document.documentElement.clientWidth || window.document.getElementsByTagName('body')[0].clientWidth;
    },

    /** update the breakpoint var base on getScreenWidth function */
    updateBreakpointWidth () {
      let width = fn.getScreenWidth();
      // default is mobile
      o.breakpoint = "mobile";
      // change to tablet
      if( width >= 768 ) {
        o.breakpoint = "tablet";
      } else if( width >= 1024) {
        o.breakpoint = "desktop";
      }
    },

    updateAdUnits () {
      o._adUnits = [];
      let positions = PREBID_POSITIONS;
      let bidders   = PREBID_BIDDERS;
      console.log(o);
      /** Loop over positions */
      for ( const position in positions ) {
        if(o.adUnits.includes( position ) ) console.log( position + 'found in adunit' );
        if(positions.hasOwnProperty( position ) ) console.log( position + 'found postion' );
        if ( o.adUnits.includes( position ) && positions.hasOwnProperty( position ) ) {
          // get position element
          const element = positions[ position ];
          // setup ad unit element
          let adUnit = {
            code: element.divId,
            mediaTypes: {
              banner: {
                sizes: element[ o.breakpoint ]
              }
            },
          bids: bidders[ o.topLevel ][ positions ]
          };
          // push the ad unit element to master ad units;
          o._adUnits.push( adUnit );
        }
      }
    },

    initAdServer() {
      if (window.pbjs.initAdserverSet) return;
      window.googletag.cmd.push( () => {
        window.pbjs.que.push( () => {
          window.pbjs.setTargetingForGPTAsync();
          window.googletag.pubads().refresh();
        });
      });
      window.pbjs.initAdserverSet = true;
    },

    queUnits() {
      window.pbjs.que.push(() => {

        if( o.debug ) {
          window.pbjs.setConfig({
            debug: true,
            cache: {
              url: false
            },
          });
        }
        console.log(o._adUnits);
        window.pbjs.addAdUnits(o._adUnits);
        window.pbjs.requestBids({
          timeout:         o.PREBID_TIMEOUT,
          bidsBackHandler: ( bidResponses ) => {
            fn.initAdserver();
          }
        });
      });
      // in case PBJS doesn't load
      setTimeout(fn.initAdserver, o.FAILSAFE_TIMEOUT);
    },

    queGoogleTags() {
      window.googletag.cmd.push(() => {
        let positions = PREBID_POSITIONS;
        let i = 0;
        for ( const position in positions ) {
          if ( positions.hasOwnProperty( position ) ) {
            i++;
            // get position element
            const element = positions[ position ];
            if(element[ o.breakpoint ] === 'OutOfPage' ){
              window.googletag
                .defineOutOfPageSlot( o.slotName, element.divId )
                .addService( window.googletag.pubads() )
                .setTargeting( "pos",  i )
              ;
            } else {
              window.googletag
                .defineSlot( o.slotName, element[ o.breakpoint ], element.divId )
                .addService( window.googletag.pubads() )
                .setTargeting( "pos", i )
              ;
            }
          }
        }
      });
    },

    initGoogleTags() {
      window.googletag.cmd.push(() => {
        window.googletag.pubads().setTargeting("s1",         o.s1);
        window.googletag.pubads().setTargeting("pid",        o.pid);
        window.googletag.pubads().setTargeting("pgtype",     o.pgtype);
        window.googletag.pubads().setTargeting("category",   o.category);
        window.googletag.pubads().setTargeting("tags",       o.tags);
        window.googletag.pubads().setTargeting("breakpoint", o.breakpoint);
        window.googletag.pubads().setTargeting("test",       o.test);
        // Init DFP
        window.googletag.pubads().disableInitialLoad();
        window.googletag.pubads().enableSingleRequest();
        window.googletag.pubads().collapseEmptyDivs();
        window.googletag.enableServices();
      });
    }
  };

  this.run = fn.run;

  fn.init();
};