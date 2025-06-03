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
    define( 'TEST_EMAIL', '' );
}

if( !defined( 'CAPTCHA_SITE_KEY' ) ) {
    define( 'CAPTCHA_SITE_KEY', '6LedGCcpAAAAAOGFFUqTQMl7ieQiSHh7ggKrXNnL' );
}

if( !defined( 'CAPTCHA_SECRET_KEY' ) ) {
    define( 'CAPTCHA_SECRET_KEY', '6LedGCcpAAAAAPEy0a3prut2kH-NSRFUErDfsss2' );
}

if( !defined( 'TS_CAPTCHA_DEV_SITE_KEY' ) ) {
    // Check is it is a development environment
    // If so, use the development site key
    // Otherwise, use the production site key
    if( defined( 'WP_ENV' ) && WP_ENV === 'development' ) {
        define( 'TS_CAPTCHA_DEV_SITE_KEY', '0x4AAAAAABfNHlEGDn5l4ZK_' );
    } else {
        define( 'TS_CAPTCHA_DEV_SITE_KEY', '0x4AAAAAABfWjKypWQPNbIoR' );
    }
}

if( !defined( 'TS_CAPTCHA_DEV_SECRET_KEY' ) ) {
    // Check is it is a development environment
    // If so, use the development secret key
    // Otherwise, use the production secret key
    if( defined( 'WP_ENV' ) && WP_ENV === 'development' ) {
        define( 'TS_CAPTCHA_DEV_SECRET_KEY', '0x4AAAAAABfNHqqbSGfSXfSoUc0lKqMGK7Y' );
    } else {
        define( 'TS_CAPTCHA_DEV_SECRET_KEY', '0x4AAAAAABfWjMSfI-Babch5uG-_I55IYm4' );
    }
}
