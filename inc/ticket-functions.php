<?php
/*
 * Cron Setup for retreiving location(shows)
 */

add_filter( 'cron_schedules', function ($schedules) {
    $schedules[ 'daily' ]              = array (
        'interval' => 86400,
        'display'  => __( 'Every Day', 'stc-tickets' )
    );
    $schedules[ 'every_three_minute' ] = array (
        'interval' => 180,
        'display'  => __( 'Every Three Minutes', 'stc-tickets' )
    );
    $schedules[ 'every_six_hours' ] = array (
        'interval' => 21600,
        'display'  => __( 'Every Six Hours', 'stc-tickets' )
    );
    $schedules[ 'every_two_hours' ] = array (
        'interval' => 7200,
        'display'  => __( 'Every Two Hours', 'stc-tickets' )
    );
    $schedules[ 'every_minute' ] = array (
        'interval' => 60,
        'display'  => __( 'Every Minute', 'stc-tickets' )
    );
    return $schedules;
} );
if( ! wp_next_scheduled( 'everyday_cron_for_retrieving_location_callback' ) ) {
    wp_schedule_event( strtotime( '05:10:00' ), 'daily', 'everyday_cron_for_retrieving_location_callback' );
}
if( ! wp_next_scheduled( 'everyThreeMinute_cron_for_retrieving_location_callback' ) ) {
    wp_schedule_event( time(), 'every_three_minute', 'everyThreeMinute_cron_for_retrieving_location_callback' );
}
// to retrieve spettacolo
if( ! wp_next_scheduled( 'vivaticket_cron_for_retrieving_spettacolo_callback' ) ) {
    wp_schedule_event( time(), 'every_two_hours', 'vivaticket_cron_for_retrieving_spettacolo_callback' );
}
// to delete old spettacolo post
if( ! wp_next_scheduled( 'vivaticket_cron_for_delete_old_spettacolo_callback' ) ) {
    wp_schedule_event( time(), 'every_six_hours', 'vivaticket_cron_for_delete_old_spettacolo_callback' );
}
if( ! wp_next_scheduled( 'everyMinute_cron_for_empty_cart_callback' ) ) {
    wp_schedule_event( time(), 'every_minute', 'everyMinute_cron_for_empty_cart_callback' );
}

// Hook into that action that'll fire every day
add_action( 'everyday_cron_for_retrieving_location_callback', 'every_day_event_func' );
function every_day_event_func() {
    update_option( 'start_cron_ticket', '1' );
}
// Hook into that action that'll fire every day
add_action( 'everyMinute_cron_for_empty_cart_callback', 'everyMinute_empty_cart_func' );
function everyMinute_empty_cart_func() {
    $user_id = get_current_user_id();
    $get_user_meta = get_user_meta($user_id,'addToCartObject');
    $transactionIds = get_user_meta($user_id,'transactionIds');
    $current_timestamp = strtotime("now");
    foreach($transactionIds[0] as $transactionIds_key => $transactionIds_value){
        if($current_timestamp > $transactionIds_value['timestamp']){
            $ticketName = $transactionIds_value['ticketName'];
            $zoneName = $transactionIds_value['zoneName'];
            $zoneId = $transactionIds_value['zoneId'];
            $seats = $transactionIds_value['seats'];
            foreach($get_user_meta[0] as $get_user_key => $get_user_value ){
                if($get_user_key == $ticketName){
                    foreach($get_user_value as $get_user_k => $get_user_v){
                        if($get_user_v['zoneName'] == $zoneName && $get_user_v['zoneId'] == $zoneId){
                            $reductions = $get_user_v['reductions'];
                            foreach($seats as $seats_key => $seats_value){
                                foreach($reductions as $reductions_key => $reductions_value){
                                    if($seats_value['reductionName'] == $reductions_value['reductionName'] && $seats_value['reductionId'] == $reductions_value['reductionId']){
//                                        $reductions_value['reductionQuantity'] = (int)$reductions_value['reductionQuantity'] - (int)$seats_value['reductionQuantity'];
                                        $get_user_meta[0][$get_user_key][$get_user_k]['reductions'][$reductions_key]['reductionQuantity'] = ((int)$get_user_meta[0][$get_user_key][$get_user_k]['reductions'][$reductions_key]['reductionQuantity'] - (int)$seats_value['reductionQuantity']) > 0 ? ((int)$get_user_meta[0][$get_user_key][$get_user_k]['reductions'][$reductions_key]['reductionQuantity'] - (int)$seats_value['reductionQuantity']) : 0;
                                        if($get_user_meta[0][$get_user_key][$get_user_k]['reductions'][$reductions_key]['reductionQuantity'] == 0){
                                            unset($get_user_meta[0][$get_user_key][$get_user_k]['reductions'][$reductions_key]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    $update_user_meta = update_user_meta($user_id,'addToCartObject',$get_user_meta[0]);
    $get_user_meta_after = get_user_meta($user_id,'addToCartObject');
    // echo "<pre>";
    // print_r($get_user_meta_after);
    // echo "</pre>";
}
// Hook into that action that'll fire every three minutes
add_action( 'everyThreeMinute_cron_for_retrieving_location_callback', 'every_three_min_event_func' );
function every_three_min_event_func() {
    $start_cron = get_option( 'start_cron_ticket' );
    if( $start_cron == '1' ) {

        $apiUrl = API_HOST.'/backend/backend.php?cmd=orgInfoList&id='.APIKEY;
        $cron_cookie = tempnam ("/tmp", "CURLCOOKIE");
        $curl = curl_init();

        curl_setopt_array( $curl, array (
            CURLOPT_URL            => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEJAR => $cron_cookie,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => array (
                'Cookie: v1Locale=%7B%22Currency%22%3Anull%2C%22SuggestCountry%22%3Anull%2C%22Country%22%3A%22IT%22%2C%22Language%22%3A%22it-IT%22%7D'
            ),
        ) );

        $response = curl_exec( $curl );

        curl_close( $curl );
        $xml           = simplexml_load_string( $response );
        $json          = json_encode( $xml );
        $vivaticketArr = json_decode( $json, TRUE );

        $cur_page       = get_option( 'ticket_cur_page' );
        $cur_page       = ! empty( $cur_page ) ? $cur_page : 1;
        $posts_per_page = 40;
        $start          = ($cur_page - 1) * $posts_per_page;
        $end            = ($cur_page) * $posts_per_page;
        $vivaticketArrPages = (count( $vivaticketArr[ 'org_info' ]) / $posts_per_page);
//        echo $cur_page . ' : ' . $posts_per_page . ' : ' . $start . ' : ' . $end;
        //location
        if( ! empty( $vivaticketArr ) && ! empty( $vivaticketArr[ 'org_info' ] ) ) {
            if( is_array( $vivaticketArr[ 'org_info' ] ) ) {
                if( $cur_page <= $vivaticketArrPages ) {
                    foreach ( $vivaticketArr[ 'org_info' ] as $key => $value ) {
                        if( $key >= $start && $key < $end ) {
                            $org_cpt_data      = array (
                                'id'        => $value[ '@attributes' ][ 'id' ],
                                'nome'      => $value[ 'nome' ],
                                'indirizzo' => $value[ 'indirizzo' ],
                                'comune'    => $value[ 'comune' ],
                                'cap'       => $value[ 'cap' ],
                                'provincia' => $value[ 'provincia' ],
                                'telefono'  => $value[ 'telefono' ],
                                'tea_info'  => $value[ 'tea_info' ],
                            );
                            $org_cpt_data_json = json_encode( $org_cpt_data );
                            $spe_name          = $value[ 'nome' ] . ' ' . $value[ '@attributes' ][ 'id' ];
                            $args              = array (
                                'post_type'      => 'location',
                                'status'         => 'publish',
                                'posts_per_page' => 1,
                                'meta_query'     => array (
                                    array (
                                        'key'     => 'location_name',
                                        'compare' => '=',
                                        'value'   => $spe_name
                                    )
                                )
                            );
                            $query             = new WP_Query( $args );
                            if( $query->have_posts() ) {
                                while ( $query->have_posts() ) {
                                    $query->the_post();
                                    $org_cpt_id = get_the_ID();
                                    $spe_name   = get_post_meta( $org_cpt_id, 'location_name', true );
                                }
                                wp_reset_query();
                                wp_reset_postdata();
                            } else {
                                $org_cpt    = array (
                                    'post_type'   => 'location',
                                    'post_status' => 'publish',
                                    'post_title'  => $value[ 'nome' ]
                                );
                                $org_cpt_id = wp_insert_post( $org_cpt );
                            }
                            if( $org_cpt_id ) {
                                update_post_meta( $org_cpt_id, 'location_data', $org_cpt_data_json );
                                update_post_meta( $org_cpt_id, 'location_name', $spe_name );
                            }
                        } else {
                            if( $key == $end ) {
                                $new_cur_page = $cur_page + 1;
                                update_option( 'ticket_cur_page', $new_cur_page );
                                break;
                            }
                        }
                    }
                    if( $cur_page == $vivaticketArrPages ) {
                        update_option( 'start_cron_ticket', '0' );
                        update_option( 'ticket_cur_page', '1' );
                    }
                } else {
                    update_option( 'start_cron_ticket', '0' );
                    update_option( 'ticket_cur_page', '1' );
                }
            }
        }
    }
}


/*
 * retrieving spettacolo
 */
add_action( 'vivaticket_cron_for_retrieving_spettacolo_callback', 'stcticket_spettacolo_add_update_callback' );
function stcticket_spettacolo_add_update_callback() {
    $stcApiUrl = API_HOST.'backend/backend.php?cmd=titleInfoList&id='.APIKEY;
    $cron_cookie = tempnam ("/tmp", "CURLCOOKIE");

    $curl = curl_init();

    curl_setopt_array( $curl, array (
        CURLOPT_URL            => $stcApiUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_COOKIEJAR => $cron_cookie,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'GET',
        CURLOPT_HTTPHEADER     => array (
            'Cookie: v1Locale=%7B%22Currency%22%3Anull%2C%22SuggestCountry%22%3Anull%2C%22Country%22%3A%22IT%22%2C%22Language%22%3A%22it-IT%22%7D'
        ),
    ) );

    $response = curl_exec( $curl );

    curl_close( $curl );
    $xml           = simplexml_load_string( $response );
    $json          = json_encode( $xml );
    $vivaticketArr = json_decode( $json, TRUE );
    $vivaVenue     = $vivaticketArr[ 'tit_info_venue' ];
//spettacolo
    if( ! empty( $vivaVenue ) ) {
        if( array_key_first( $vivaVenue ) == '0' ) {
            $vivaVenue_new = $vivaVenue;
        } else {
            $vivaVenue_new = array($vivaVenue);            
        }
        foreach ( $vivaVenue_new as $key => $value ) {
            $title_info = $value[ 'tit_info_title' ];
            $vCode      = $value[ '@attributes' ][ 'vcode' ];
            if( array_key_first( $title_info ) == '0' ) {
                foreach ( $title_info as $ti_key => $ti_value ) {
                    $title_infoAtts = $ti_value[ '@attributes' ];
                    $id_show        = $title_infoAtts[ 'id_show' ];
                    $tit_info_perform = $ti_value[ 'tit_info_perform' ];
                    $args           = array (
                        'post_type'      => 'spettacolo',
                        'status'         => 'publish',
                        'posts_per_page' => 1,
                        'meta_query'     => array (
                            'relation' => 'AND',
                            array (
                                'key'     => 'spt_vcode',
                                'compare' => '=',
                                'value'   => $vCode
                            ),
                            array (
                                'key'     => 'spt_id_show',
                                'compare' => '=',
                                'value'   => $id_show
                            ),
                        )
                    );
                    $query          = new WP_Query( $args );
                    if( $query->have_posts() ) {
                        while ( $query->have_posts() ) {
                            $query->the_post();
                            $spt_post_id = get_the_ID();
                        }
                        wp_reset_query();
                        wp_reset_postdata();
//                        echo "<pre>";
//                        print_r( 'If1: ' );
//                        echo "</pre>";
                    } else {
                        $org_cpt     = array (
                            'post_type'   => 'spettacolo',
                            'post_status' => 'publish',
                            'post_title'  => $title_infoAtts[ 'name' ]
                        );
                        $spt_post_id = wp_insert_post( $org_cpt );
                    }

                    $spt_numPerf = isset( $title_infoAtts[ 'numPerf' ] ) ? $title_infoAtts[ 'numPerf' ] : 0;

                    update_post_meta( $spt_post_id, 'spt_vcode', $vCode );
                    update_post_meta( $spt_post_id, 'spt_id_show', $id_show );
                    update_post_meta( $spt_post_id, 'spt_name', $title_infoAtts[ 'name' ] );
                    update_post_meta( $spt_post_id, 'spt_location', '' );
                    update_post_meta( $spt_post_id, 'spt_startDate', $title_infoAtts[ 'primaData' ] );
                    update_post_meta( $spt_post_id, 'spt_endDate', $title_infoAtts[ 'ultimaData' ] );
                    update_post_meta( $spt_post_id, 'spt_numPerf', $spt_numPerf );
                    update_post_meta( $spt_post_id, 'spt_tit_info_title', $ti_value );
                    $subscripton_show = 0;                    
                    if( array_key_first( $tit_info_perform ) == '0' ) {
                        foreach ( $tit_info_perform as $tip_key => $tip_value ) {
                            if($tip_value['@attributes']['abb'] == '1') {
                                if(!empty($tip_value['@attributes']['dataInizioVendita'])) {
                                    $dataInizioVendita = $tip_value['@attributes']['dataInizioVendita'];
                                    update_post_meta( $spt_post_id, 'spt_startDate', $dataInizioVendita );
                                }
                                if(!empty($tip_value['@attributes']['dataFineVendita'])) {
                                    $dataFineVendita = $tip_value['@attributes']['dataFineVendita'];
                                    update_post_meta( $spt_post_id, 'spt_endDate', $dataFineVendita );
                                }
                                $subscripton_show = 1;
                                break;
                            }
                        }
                    } else {
                        if($tit_info_perform['@attributes']['abb'] == '1') {
                            if(!empty($tit_info_perform['@attributes']['dataInizioVendita'])) {
                                $dataInizioVendita = $tit_info_perform['@attributes']['dataInizioVendita'];
                                update_post_meta( $spt_post_id, 'spt_startDate', $dataInizioVendita );
                            }
                            if(!empty($tit_info_perform['@attributes']['dataFineVendita'])) {
                                $dataFineVendita = $tit_info_perform['@attributes']['dataFineVendita'];
                                update_post_meta( $spt_post_id, 'spt_endDate', $dataFineVendita );
                            }
                            $subscripton_show = 1;
                        }
                    }
                    update_post_meta( $spt_post_id, 'spt_subscription_show', $subscripton_show );
                }
            } else {
                $title_infoAtts = $value[ 'tit_info_title' ][ '@attributes' ];
                $tit_info_perform = $value[ 'tit_info_title' ][ 'tit_info_perform' ]; 
                $id_show        = $title_infoAtts[ 'id_show' ];
                $args           = array (
                    'post_type'      => 'spettacolo',
                    'status'         => 'publish',
                    'posts_per_page' => 1,
                    'meta_query'     => array (
                        'relation' => 'AND',
                        array (
                            'key'     => 'spt_vcode',
                            'compare' => '=',
                            'value'   => $vCode
                        ),
                        array (
                            'key'     => 'spt_id_show',
                            'compare' => '=',
                            'value'   => $id_show
                        ),
                    )
                );
                $query          = new WP_Query( $args );
                if( $query->have_posts() ) {
                    while ( $query->have_posts() ) {
                        $query->the_post();
                        $spt_post_id = get_the_ID();
//                $vCode   = get_post_meta( $org_cpt_id, 'code', true );
                    }
                    wp_reset_query();
                    wp_reset_postdata();
//                    echo "<pre>";
//                    print_r( 'If2: ' );
//                    echo "</pre>";
                } else {
                    $org_cpt     = array (
                        'post_type'   => 'spettacolo',
                        'post_status' => 'publish',
                        'post_title'  => $title_infoAtts[ 'name' ]
                    );
                    $spt_post_id = wp_insert_post( $org_cpt );
                }
//                echo "<pre>";
//                print_r( $spt_post_id . ' : ' . $title_infoAtts[ 'name' ] );
//                echo "</pre>";
                update_post_meta( $spt_post_id, 'spt_vcode', $vCode );
                update_post_meta( $spt_post_id, 'spt_id_show', $id_show );
                update_post_meta( $spt_post_id, 'spt_name', $title_infoAtts[ 'name' ] );
                update_post_meta( $spt_post_id, 'spt_location', '' );
                update_post_meta( $spt_post_id, 'spt_startDate', $title_infoAtts[ 'primaData' ] );
                update_post_meta( $spt_post_id, 'spt_endDate', $title_infoAtts[ 'ultimaData' ] );
                update_post_meta( $spt_post_id, 'spt_numPerf', $title_infoAtts[ 'numPerf' ] );
                update_post_meta( $spt_post_id, 'spt_tit_info_title', $title_info );
                $subscripton_show = 0;                    
                if( array_key_first( $tit_info_perform ) == '0' ) {
                    foreach ( $tit_info_perform as $tip_key => $tip_value ) {
                        if($tip_value['@attributes']['abb'] == '1') {
                            if(!empty($tip_value['@attributes']['dataInizioVendita'])) {
                                $dataInizioVendita = $tip_value['@attributes']['dataInizioVendita'];
                                update_post_meta( $spt_post_id, 'spt_startDate', $dataInizioVendita );
                            }
                            if(!empty($tip_value['@attributes']['dataFineVendita'])) {
                                $dataFineVendita = $tip_value['@attributes']['dataFineVendita'];
                                update_post_meta( $spt_post_id, 'spt_endDate', $dataFineVendita );
                            }
                            $subscripton_show = 1;
                            break;
                        }
                    }
                } else {
                    if($tit_info_perform['@attributes']['abb'] == '1') {
                        if(!empty($tit_info_perform['@attributes']['dataInizioVendita'])) {
                            $dataInizioVendita = $tit_info_perform['@attributes']['dataInizioVendita'];
                            update_post_meta( $spt_post_id, 'spt_startDate', $dataInizioVendita );
                        }
                        if(!empty($tit_info_perform['@attributes']['dataFineVendita'])) {
                            $dataFineVendita = $tit_info_perform['@attributes']['dataFineVendita'];
                            update_post_meta( $spt_post_id, 'spt_endDate', $dataFineVendita );
                        }
                        $subscripton_show = 1;
                    }
                }
                update_post_meta( $spt_post_id, 'spt_subscription_show', $subscripton_show );
            }
        }
    }
}

/**
 * Shortcode for getting spettacolo
 * @param type $atts
 * @return string
 */
function stcticket_get_vivaticket_shows_from_api_sc( ) {
    stcticket_spettacolo_add_update_callback();
}
add_shortcode( 'get_vt_shows', 'stcticket_get_vivaticket_shows_from_api_sc' );


/*
 * delete old spettacolo
 */

//add_action( 'vivaticket_cron_for_delete_old_spettacolo_callback', 'stcticket_spettacolo_delete_old_post_callback' );
function stcticket_spettacolo_delete_old_post_callback() {
    $expired = false;
    $args = array(
        'post_type' => 'spettacolo',
        'posts_per_page' => -1,
    );
    $query = new WP_Query( $args );
    if( $query->have_posts () ) {
        while ( $query->have_posts () ) {
            $query->the_post ();
            $spe_id             = get_the_ID ();
            $spe_title          = get_the_title ();
            $spe_end_date       = get_post_meta ( $spe_id, 'spt_endDate', true );
            $new_spe_end_date   = date_create(str_replace("/","-",$spe_end_date));
            $current_date       = date_create("now");
            if($new_spe_end_date < $current_date){
                $expired = true;
            }else{
                $expired = false;
            }
            if($expired){
                wp_delete_post($spe_id, true );                
            }
        }   
    }
}

//add_filter( 'single_template', 'override_single_template' );
//function override_single_template( $single_template ){
//    global $post;
//
//    $file = dirname(__FILE__) .'/templates/single-'. $post->post_type .'.php';
//
//    if( file_exists( $file ) ) $single_template = $file;
//
//    return $single_template;
//}

/*
 * define template and select it for perticular page inside plugin
 */
function template_chooser($template){
    global $post;
    $plugindir = dirname(__FILE__);

    $post_type = isset($post->post_type) ? $post->post_type : 'spettacolo';

    if( $post_type == 'spettacolo' ){
        $singleTemp = $plugindir . '/../templates/single-spettacolo.php';
        return $singleTemp;
    }
    return $template;   
}
//add_filter('template_include', 'template_chooser');
