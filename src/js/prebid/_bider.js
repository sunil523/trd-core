import PREBID_BIDER_CH from './bider/_ch';
import PREBID_BIDER_LA from './bider/_la';
import PREBID_BIDER_NA from './bider/_na';
import PREBID_BIDER_NY from './bider/_ny';
import PREBID_BIDER_SF from './bider/_sf';
import PREBID_BIDER_TS from './bider/_ts';

export const PREBID_BIDERS = {
  "trd-chicago":  PREBID_BIDER_CH,
  "trd-la":       PREBID_BIDER_LA,
  "trd-national": PREBID_BIDER_NA,
  "trd-ny":       PREBID_BIDER_NY,
  "trd-miami":    PREBID_BIDER_SF,
  "trd-tristate": PREBID_BIDER_TS
};