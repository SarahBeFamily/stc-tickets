<?php
/*
 * ajax for store Booking object into user meta
 */
if( !defined( 'APIKEY' ) ) {
    define( 'APIKEY', 'SANCARLO' );
//    define( 'APIKEY', 'APITEST' );
}
if( !defined( 'API_HOST' ) ) {
//    define( 'API_HOST', 'https://service-test.vivaticket.it/' );
    define( 'API_HOST', 'https://service.vivaticket.it/' );
}
if( !defined( 'FORM_FIELD_CHARS' ) ) {
    define( 'FORM_FIELD_CHARS', 'gishdgrd' );
}

if ( !defined( 'TEST_EMAIL' ) ) {
    define( 'TEST_EMAIL', 'cyber.soc@nectlc.com' );
}

if( !defined( 'CAPTCHA_SECRET_KEY' ) ) {
    define( 'CAPTCHA_SECRET_KEY', '6LedGCcpAAAAAPEy0a3prut2kH-NSRFUErDfsss2' );
}

if( !defined( 'CAPTCHA_SITE_KEY' ) ) {
    define( 'CAPTCHA_SITE_KEY', '6LedGCcpAAAAAOGFFUqTQMl7ieQiSHh7ggKrXNnL' );
}
//if( !defined( 'WC_CART_SLUG' ) ) {
//    define( 'WC_CART_SLUG', basename(wc_get_cart_url()) );
//}
//if( !defined( 'WC_CHECKOUT_SLUG' ) ) {
//    define( 'WC_CHECKOUT_SLUG', basename(wc_get_checkout_url()) );
//}