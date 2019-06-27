import PREBID_BIDDER_CH from './bidder/_ch';
import PREBID_BIDDER_LA from './bidder/_la';
import PREBID_BIDDER_NA from './bidder/_na';
import PREBID_BIDDER_NY from './bidder/_ny';
import PREBID_BIDDER_SF from './bidder/_sf';
import PREBID_BIDDER_TS from './bidder/_ts';

export const PREBID_BIDDERS = {
  "trd-chicago":  PREBID_BIDDER_CH,
  "trd-la":       PREBID_BIDDER_LA,
  "trd-national": PREBID_BIDDER_NA,
  "trd-ny":       PREBID_BIDDER_NY,
  "trd-miami":    PREBID_BIDDER_SF,
  "trd-tristate": PREBID_BIDDER_TS
};