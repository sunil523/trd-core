'use strict';
console.info('TRD Core Loaded');
import Navigation from './_navigation';
import Newsletter from './_newsletter';

window.trd               = window.trd || {};
window.trd.fn            = window.trd.fn || {};
window.trd.fn.nav        = Navigation;
window.trd.fn.newsletter = Newsletter;

window.trd.nav        = new Navigation();
window.trd.newsletter = new Newsletter();
