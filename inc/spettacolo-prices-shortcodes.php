<?php
/*
 * spettacoli prices
 */
add_shortcode( 'spettacolo_prices', 'stcTickets_spettacolo_prices_callback' );
function stcTickets_spettacolo_prices_callback() {
    ob_start();

    $vcode         = isset( $_GET[ 'vcode' ] ) ? $_GET[ 'vcode' ] : '';
    $pcode         = (int) (isset( $_GET[ 'pcode' ] ) ? $_GET[ 'pcode' ] : '');
    $regData       = (int) (isset( $_GET[ 'regData' ] ) ? $_GET[ 'regData' ] : '');
    $spe_id        = isset( $_GET[ 'postId' ] ) ? $_GET[ 'postId' ] : '';
    $selectionMode = (int) (isset( $_GET[ 'selectionMode' ] ) ? $_GET[ 'selectionMode' ] : 0);
    $barcode       = isset( $_GET[ 'barcode' ] ) ? $_GET[ 'barcode' ] : 0;

    $spe_permalink = get_post_permalink( $spe_id );
    $spt_location  = ! empty( get_post_meta( $spe_id, 'spt_location', true ) ) ? get_post_meta( $spe_id, 'spt_location', true ) : __('Teatro San Carlo - NAPOLI','stc-tickets');
    $spt_img       = get_the_post_thumbnail_url( $spe_id ) ? get_the_post_thumbnail_url( $spe_id ) : plugin_dir_url( __DIR__ ) . 'assets/img/emiliano_test.jpg';
    $prices_cookie = tempnam ("/tmp", "CURLCOOKIE");
    $curl = curl_init();

    curl_setopt_array( $curl, array (
        CURLOPT_URL            => API_HOST . 'backend/backend.php?cmd=prices&id=' . APIKEY . '&vcode=' . $vcode . '&pcode=' . $pcode,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_COOKIEJAR => $prices_cookie,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'GET',
    ) );

    $response = curl_exec( $curl );
    curl_close( $curl );

    $xml             = simplexml_load_string( $response, 'SimpleXMLElement', LIBXML_NOCDATA );
    $pricesJson      = json_encode( $xml );
    $pricesArr       = json_decode( $pricesJson, TRUE );

    // testing: print the array of prices
    if(isset($_GET['print']) && $_GET['print'] == 1) {
        echo "<pre>";
        print_r($pricesJson);
        print_r($pricesArr);
        echo "</pre>";
    }

    // if(isset($pricesArr['@attributes']['errcode']) && isset($pricesArr['@attributes']['errstring'])){
    //     echo "<pre>";
    //     print_r(API_HOST . 'backend/backend.php?cmd=prices&id=' . APIKEY . '&vcode=' . $vcode . '&pcode=' . $pcode);
    //     echo "</pre>";
    // }

    $title           = ! empty( $pricesArr[ 'title' ] ) ? $pricesArr[ 'title' ] : '';
    $info_date       = ! empty( $pricesArr[ 'date' ] ) ? $pricesArr[ 'date' ] : '';
    $info_curr_date  = ! empty( $info_date ) ? explode( "/", $info_date ) : '';

    $info_final_date = ! empty( $info_curr_date ) && count($info_curr_date) > 1 ? $info_curr_date[ 1 ] . '/' . $info_curr_date[ 0 ] . '/' . $info_curr_date[ 2 ] : '';

    $info_final_date = ! empty( $info_final_date ) ? change_date_time_in_italy( strtotime( $info_final_date ), 'EEEE dd MMMM y' ) : '';
    $info_time       = ! empty( $pricesArr[ 'time' ] ) ? $pricesArr[ 'time' ] : '';
    $subscription    = ! empty ( $pricesArr[ 'subscription' ] );
    if( $subscription ) {
        $subscription = "1";
    } else {
        $subscription = "0";
    }

    $jsonTitle = json_encode($title);
    $cleanedTitle = str_replace('\"', '&quot;', $jsonTitle);
    $cleanedTitle = str_replace("'", '&apos;', $cleanedTitle);

    // search and replace the title with the cleaned title
    $pricesJson = str_replace($jsonTitle, $cleanedTitle, $pricesJson);
    $globalJsPricing = $pricesJson;

    ?>
    <script type="text/javascript">
        var globalJsPricing = '<?php echo isset( $globalJsPricing ) ? $globalJsPricing : '';?>';
    </script>
    <?php
    //    echo "<pre>";
    //    print_r($map_curl);
    //    echo "</pre>";
    //    echo "<pre>";
    //    print_r(API_HOST . 'backend/backend.php?id=' . APIKEY . '&cmd=extGetMapData&vcode=' . $vcode . '&pcode=' . $pcode);
    //    echo "</pre>";
    // curl of extGetMapData to create seat map
    $map_curl        = curl_init();
    $map_cookie = tempnam ("/tmp", "CURLCOOKIE");

    curl_setopt_array( $map_curl, array (
        CURLOPT_URL            => API_HOST . 'backend/backend.php?id=' . APIKEY . '&cmd=extGetMapData&vcode=' . $vcode . '&pcode=' . $pcode,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'GET',
        CURLOPT_COOKIEJAR => $map_cookie,
        CURLOPT_HTTPHEADER     => array (
            'Authorization: Basic S2lkZWE6S2lkZWFzcmw='
        ),
    ) );

    $map_response = curl_exec( $map_curl );
    $err          = curl_error( $map_curl );
    curl_close( $map_curl );

    $xmlNode       = simplexml_load_string( $map_response );
    $arrayData     = xmlToArray( $xmlNode );
    $json_en       = json_encode( $arrayData );
    $extGetMapDataPricing = str_replace("'", "\'", $json_en);

    // Extract the title from the array
    $jsonTitle = isset($arrayData['reply']['performance']['@attribute']['title']) ? json_encode($arrayData['reply']['performance']['@attribute']['title']) : '';
    $cleanedTitle = str_replace('\"', '&quot;', $jsonTitle);
    $cleanedTitle = str_replace("\'", '&apos;', $cleanedTitle);

    // search and replace the title with the cleaned title
    $extGetMapDataPricing = str_replace($jsonTitle, $cleanedTitle, $extGetMapDataPricing);

    ?>
    <script type="text/javascript">
        var extGetMapDataPricing = '<?php echo isset( $extGetMapDataPricing ) ? $extGetMapDataPricing : ""; ?>';
    </script>
    <?php
    $extGetMapData = json_decode( $json_en, true );

    // Testing: print the array of extGetMapData
    // by adding ?print=2 to the URL
    if(isset($_GET['print']) && $_GET['print'] == 2) {
        echo "<pre>";
        print_r($extGetMapData);
        echo "</pre>";
    }

    if( isset( $extGetMapData[ 'reply' ][ 'performance' ] ) ) {
        $performance           = $extGetMapData[ 'reply' ][ 'performance' ];
        $map_seats             = isset($performance[ 'seats' ]) ? $performance[ 'seats' ] : array();
        $map_reductions        = $performance[ 'reductions' ]['reduction'];
        $roomLastUpdate        = $performance[ '@attribute' ][ 'roomLastUpdate' ];
        $map_zones             = isset($performance[ 'zones' ][ 'zone' ]) ? $performance[ 'zones' ][ 'zone' ] : array();
        $seatsLayout           = get_post_meta( $spe_id, 'map_room_data_xml', true );
        $room_last_update_time = get_post_meta( $spe_id, 'room_last_update_time' . $pcode, true );
        if( empty( $room_last_update_time ) ) {
            update_post_meta( $spe_id, 'room_last_update_time' . $pcode, $roomLastUpdate );
        }
        $roomName               = isset( $performance[ '@attribute' ][ 'roomName' ] ) ? $performance[ '@attribute' ][ 'roomName' ] : '';
        if( !empty( $roomName ) ) {
            update_post_meta( $spe_id, 'spt_location', $roomName );
            $spt_location = $roomName;
        }
        // save seats layout and romm last update time in plugin meta options
        $stc_tickets_map_room_data_xml = get_option( 'stc_tickets_map_room_data_xml' );
        $stc_tickets_room_last_update_time = get_option( 'stc_tickets_room_last_update_time' . $pcode );

        // se le opzioni non esistono, le creo
        if( empty( $stc_tickets_map_room_data_xml ) ) {
            add_option( 'stc_tickets_map_room_data_xml', $seatsLayout, '', 'no' );
        }
        if( empty( $stc_tickets_room_last_update_time ) ) {
            add_option( 'stc_tickets_room_last_update_time' . $pcode, $roomLastUpdate, '', 'no' );
        }

        $get_room_last_update_time = get_post_meta( $spe_id, 'room_last_update_time' . $pcode, true );
        if( empty( $seatsLayout ) || $roomLastUpdate > $get_room_last_update_time ) {
            $path              = API_HOST.'map_room_data/' . $vcode . '.xml';
            $xmlfile           = remote_file_get_contents( $path );

            if (!$xmlfile || $xmlfile === false) {
                // uso la mappa salvata nelle opzioni del plugin
                $seatsLayoutObject = get_option( 'stc_tickets_map_room_data_xml' );
            } else {
                $fileObject        = simplexml_load_string( $xmlfile );
                $jsonObject        = json_encode( $fileObject );
                $seatsLayoutObject = json_decode( $jsonObject, true );
            }

            // dd( $path, $xmlfile, $seatsLayoutObject );

            if ($xmlfile !== false) {
                update_post_meta( $spe_id, 'map_room_data_xml', $seatsLayoutObject );
                update_post_meta( $spe_id, 'room_last_update_time' . $pcode, $roomLastUpdate );

                update_option( 'stc_tickets_map_room_data_xml', $seatsLayoutObject );
                update_option( 'stc_tickets_room_last_update_time' . $pcode, $roomLastUpdate );
            }

            $seatsLayout               = $seatsLayoutObject;
        
        }
        $circles_arr         = array ();
        $seatsOfSector       = $seatsLayout[ 'room' ][ 'seats' ];
        $sector              = $seatsOfSector[ 'sector' ];
        $seatFromXml         = $sector[ 'seat' ];
        $svgHeight           = $seatsLayout[ 'room' ][ '@attributes' ][ 'height' ];
        $svgBgcolor          = $seatsLayout[ 'room' ][ '@attributes' ][ 'bgcolor' ];
        $svgLastUpdate       = $seatsLayout[ 'room' ][ '@attributes' ][ 'lastUpdate' ];
        $seatsLayoutTypes    = $seatsLayout[ 'room' ][ 'types' ][ 'type' ];
        $temp_map_seat_array = array ();

        // Testing: print the array of map_zones
        // by adding ?print=1 to the URL
        if(isset($_GET['print']) && $_GET['print'] == 3) {
            echo "<pre>";
            print_r($seatsLayout);
            // print_r($map_seats);
            echo "</pre>";
        }

        if (!empty($map_seats)) : // Mod sarah
        foreach ( $map_seats as $map_seats_key => $map_seats_value ) {
            if( key( $map_seats_value ) == '0' ) {
                foreach ( $map_seats_value as $map_seats_k => $map_seats_val ) {
                    $chunk_offset       = $map_seats_val[ '@attribute' ][ 'offset' ];
                    $zones              = $map_seats_val[ 'zones' ];
                    $width              = $zones[ '@attribute' ][ 'width' ];
                    $value              = $zones[ 'value' ];
                    $status             = $map_seats_val[ 'status' ];
                    $value_arr          = str_split( $value, $width );
                    $status_arr         = str_split( $status, 1 );
                    $chunk_offset_limit = (int) $chunk_offset + count( $value_arr );
                    $prices_arr         = array();
                    $prices_attr        = array();
                    $c                  = 0;
                    for ( $i = (int) $chunk_offset; $i < $chunk_offset_limit; $i ++ ) {
                        $index_value               = $value_arr[ $c ] < 10 ? substr( $value_arr[ $c ], -1 ) : $value_arr[ $c ];
                        $color                     = (key( $map_zones ) == '0') ? $map_zones[ $index_value ][ '@attribute' ][ 'color' ] : $map_zones[ '@attribute' ][ 'color' ];
                        $zone_id                   = (key( $map_zones ) == '0') ? $map_zones[ $index_value ][ '@attribute' ][ 'id' ] : $map_zones[ '@attribute' ][ 'id' ];
                        $zone_desc                   = (key( $map_zones ) == '0') ? $map_zones[ $index_value ][ '@attribute' ][ 'description' ] : $map_zones[ '@attribute' ][ 'description' ];
                        if(key( $map_zones ) == '0'){
                            if( array_key_first( $map_zones[ $index_value ]['price'] ) == '0' ) {
                                $prices_attr = array_column( $map_zones[ $index_value ]['price'], '@attribute' );
                                $prices_arr = array_column( $prices_attr, 'price' );
                            } else {
                                $prices_attr = array_column( $map_zones[ $index_value ]['price'], 'price' );
                                $prices_arr = $prices_attr;
                            }
                            $final_prices_arr = array_filter($prices_arr, function ($zone_price, $index) use($map_reductions){
                                if( array_key_first( $map_reductions ) == '0' ) {
                                    if($map_reductions[$index]['@attribute']['id'] > 0){
                                        return $zone_price;
                                    }
                                }else{
                                    if($map_reductions['@attribute']['id'] > 0){
                                        return $zone_price;
                                    }
                                }
                            },ARRAY_FILTER_USE_BOTH);
                            if(!empty($final_prices_arr)){
                                // $final_prices_arr = array_filter($final_prices_arr, function($v) { return $v > 0; });
                                $final_prices_arr = array_filter($final_prices_arr, fn($v) => $v !== null && $v !== '');
                                $min_final = min( $final_prices_arr );
                                $max_final = max( $final_prices_arr );
                                $min_price =  is_user_logged_in() ? $min_final : $max_final;
                            }
                        }else{
                            if( array_key_first( $map_zones['price'] ) == '0' ) {
                                $prices_attr = array_column( $map_zones['price'], '@attribute' );
                                $prices_arr = array_column( $prices_attr, 'price' );
                            } else {
                                $prices_attr = array_column( $map_zones['price'], 'price' );
                                $prices_arr = $prices_attr;
                            }
                            $final_prices_arr = array_filter($prices_arr, function ($zone_price, $index) use($map_reductions){
                                if( array_key_first( $map_reductions ) == '0' ) {
                                    if($map_reductions[$index]['@attribute']['id'] > 0){
                                        return $zone_price;
                                    }
                                }else{
                                    if($map_reductions['@attribute']['id'] > 0){
                                        return $zone_price;
                                    }
                                }
                            },ARRAY_FILTER_USE_BOTH);
                            if(!empty($final_prices_arr)){
                                $final_prices_arr = array_filter($final_prices_arr, function($v) { return $v > 0; });
                                $min_price =  is_user_logged_in() ? min( $final_prices_arr ) : max( $final_prices_arr );
                            }
                        }
                        $price  = ! empty ( $min_price ) ? ((int) $min_price / 100) : 0;
                        $temp_map_seat_array[ $i ] = array (
                            'value'   => $value_arr[ $c ],
                            'status'  => $status_arr[ $c ],
                            'color'   => $color,
                            'zone_id' => $zone_id,
                            'zone_desc' => $zone_desc,
                            'price' => empty($barcode) ? $price : '0.00'
                        );
                        $c ++;
                    }
                }
            } else {
                $chunk_offset = $map_seats_value[ '@attribute' ][ 'offset' ];
                $zones        = $map_seats_value[ 'zones' ];
                $width        = $zones[ '@attribute' ][ 'width' ];
                $value        = $zones[ 'value' ];
                $status       = $map_seats_value[ 'status' ];
                $value_arr    = str_split( $value, $width );
                $status_arr   = str_split( $status, 1 );
                for ( $i = (int) $chunk_offset; $i < count( $value_arr ); $i ++ ) {
                    $index_value               = $value_arr[ $i ] < 10 ? substr( $value_arr[ $i ], -1 ) : $value_arr[ $i ];
                    $color                     = (key( $map_zones ) == '0') ? $map_zones[ $index_value ][ '@attribute' ][ 'color' ] : $map_zones[ '@attribute' ][ 'color' ];
                    $zone_id                   = (key( $map_zones ) == '0') ? $map_zones[ $index_value ][ '@attribute' ][ 'id' ] : $map_zones[ '@attribute' ][ 'id' ];
                    $zone_desc                   = (key( $map_zones ) == '0') ? $map_zones[ $index_value ][ '@attribute' ][ 'description' ] : $map_zones[ '@attribute' ][ 'description' ];
                    $price                   = (key( $map_zones ) == '0') ? $map_zones[ $index_value ]['price'][ '@attribute' ][ 'price' ] : $map_zones['price'][ '@attribute' ][ 'price' ];
                    if(key( $map_zones ) == '0'){
                        if( array_key_first( $map_zones[ $index_value ]['price'] ) == '0' ) {
                            $prices_attr = array_column( $map_zones[ $index_value ]['price'], '@attribute' );
                            $prices_arr = array_column( $prices_attr, 'price' );
                        } else {
                            $prices_attr = array_column( $map_zones[ $index_value ]['price'], 'price' );
                            $prices_arr = $prices_attr;
                        }
                        $final_prices_arr = array_filter($prices_arr, function ($zone_price, $index) use($map_reductions){
                            if( array_key_first( $map_reductions ) == '0' ) {
                                if($map_reductions[$index]['@attribute']['id'] > 0){
                                    return $zone_price;
                                }
                            }else{
                                if($map_reductions['@attribute']['id'] > 0){
                                    return $zone_price;
                                }
                            }
                        },ARRAY_FILTER_USE_BOTH);
                        if(!empty($final_prices_arr)){
                            $final_prices_arr = array_filter($final_prices_arr, function($v) { return $v > 0; });
                            $min_price =  is_user_logged_in() ? min( $final_prices_arr ) : max( $final_prices_arr );
                        }
                    }else{
                        if( array_key_first( $map_zones['price'] ) == '0' ) {
                            $prices_attr = array_column( $map_zones['price'], '@attribute' );
                            $prices_arr = array_column( $prices_attr, 'price' );
                        } else {
                            $prices_attr = array_column( $map_zones['price'], 'price' );
                            $prices_arr = $prices_attr;
                        }
                        $final_prices_arr = array_filter($prices_arr, function ($zone_price, $index) use($map_reductions){
                            if( array_key_first( $map_reductions ) == '0' ) {
                                if($map_reductions[$index]['@attribute']['id'] > 0){
                                    return $zone_price;
                                }
                            }else{
                                if($map_reductions['@attribute']['id'] > 0){
                                    return $zone_price;
                                }
                            }
                        },ARRAY_FILTER_USE_BOTH);
                        if(!empty($final_prices_arr)){
                            $final_prices_arr = array_filter($final_prices_arr, function($v) { return $v > 0; });
                            $min_price =  is_user_logged_in() ? min( $final_prices_arr ) : max( $final_prices_arr );
                        }
                    }
                    $price  = ! empty ( $min_price ) ? ((int) $min_price / 100) : 0;
                    $temp_map_seat_array[ $i ] = array (
                        'value'   => $value_arr[ $i ],
                        'status'  => $status_arr[ $i ],
                        'color'   => $color,
                        'zone_id' => $zone_id,
                        'zone_desc' => $zone_desc,
                        'price' => empty($barcode) ? $price : '0.00'
                    );
                }
            }
        }
        endif;

        /**
         * Testing: print the current seat
         * by adding ?print=4 to the URL
         */
        if(isset($_GET['print']) && $_GET['print'] == 41) {
            echo 'oooooooooooooooooo';
            echo "<pre>";
            print_r($seatFromXml);
            echo "</pre>";
        }

        foreach ( $seatFromXml as $seatFromXml_key => $seatFromXml_value ) {
            $currentSeat = $seatFromXml_value;

            /**
             * Testing: print the current seat
             * by adding ?print=4 to the URL
             */
            if(isset($_GET['print']) && $_GET['print'] == 42) {
                echo 'aaaaaaaaaaaaaa';
                echo "<pre>";
                print_r($currentSeat);
                echo "</pre>";
            }

            $id          = $currentSeat[ '@attributes' ][ 'id' ];
            $x           = $currentSeat[ '@attributes' ][ 'x' ];
            $y           = $currentSeat[ '@attributes' ][ 'y' ];
            $contiguity  = $currentSeat[ '@attributes' ][ 'contiguity' ];
            $index       = $currentSeat[ '@attributes' ][ 'index' ];
            $type        = $currentSeat[ '@attributes' ][ 'type' ];
            $description = $currentSeat[ '@attributes' ][ 'description' ];
            if( key( $seatsLayoutTypes ) === 0 ) {
                foreach ( $seatsLayoutTypes as $seatsLayoutTypesKey => $seatsLayoutTypesValue ) {
                    if( $seatsLayoutTypesValue[ '@attributes' ][ 'id' ] == $currentSeat[ '@attributes' ][ 'type' ] ) {
                        $circle_width = $seatsLayoutTypesValue[ '@attributes' ][ 'width' ];
                    }
                }
            } else {
                $circle_width = $seatsLayoutTypes[ '@attributes' ][ 'width' ];
            }
            array_push( $circles_arr, array (
                'seat-id'          => $id,
                'seat-x'           => $x,
                'seat-y'           => $y,
                'seat-contiguity'  => $contiguity,
                'seat-index'       => $index,
                'seat-description' => $description,
                'index-value'      => isset( $temp_map_seat_array[ $seatFromXml_key ] ) ? $temp_map_seat_array[ $seatFromXml_key ][ 'value' ] : '',
                'index-status'     => isset( $temp_map_seat_array[ $seatFromXml_key ] ) ? $temp_map_seat_array[ $seatFromXml_key ][ 'status' ] : '',
                'data-color'       => isset( $temp_map_seat_array[ $seatFromXml_key ] ) ? $temp_map_seat_array[ $seatFromXml_key ][ 'color' ] : '',
                'circle-width'     => ($circle_width / 2),
                'zone-id'          => isset( $temp_map_seat_array[ $seatFromXml_key ] ) ? $temp_map_seat_array[ $seatFromXml_key ][ 'zone_id' ] : '',
                'zone-desc'        => isset( $temp_map_seat_array[ $seatFromXml_key ] ) ? $temp_map_seat_array[ $seatFromXml_key ][ 'zone_desc' ] : '',
                'price'            => isset( $temp_map_seat_array[ $seatFromXml_key ] ) ? $temp_map_seat_array[ $seatFromXml_key ][ 'price' ] : '',
            ) );


        }
    } else {
        $errstring = isset( $extGetMapData[ 'reply' ][ '@attribute' ][ 'errstring' ] ) ? $extGetMapData[ 'reply' ][ '@attribute' ][ 'errstring' ] : '';
    }
    /**
     * Testing: print the current seat
     * by adding ?print=4 to the URL
     */
    if(isset($_GET['print']) && $_GET['print'] == 4) {
        echo "<pre>";
        print_r($circles_arr);
        echo "</pre>";
    }
    ?>
<!--    <div class="tooltip">
        <div class="tooltip__content"></div>
    </div>-->
    <div class="spettacolo-prices-wrapper" data-vcode="<?php echo $vcode; ?>" data-pcode="<?php echo $pcode; ?>" data-regData="<?php echo $regData; ?>" data-subscription="<?php echo $subscription; ?>"  data-barcode="<?php echo $barcode; ?>">
        <div class="">
            <div class="showheader">
                <a href="<?php echo $spe_permalink; ?>" class="backtoshow"><?php _e('Torna alla scheda evento','stc-tickets'); ?></a>
                <div class="spettacolo-info-wrap">
                    <!-- MOD SARAH -->
                    <div class="spettacolo-info-inner-wrap">
                        <div class="spe-info-half-wrap">
                            <div class="spettacolo-info-img">
                                <img src="<?php echo $spt_img; ?>" alt="alt"/>
                            </div>
                        </div>
                        <div class="spe-info-half-wrap">
                            <div class="spettacolo-info-inner">
                                <div class="list-title">
                                    <?php if( ! empty( $title ) ) { ?>
                                        <h3><?php echo $title; ?></h3>
                                    <?php } ?>
                                </div>
                                <div class="list-location">
                                    <svg data-v-e1017caa="" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="location-dot" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="svg-inline--fa fa-location-dot fa-fw fa-sm"><path data-v-e1017caa="" fill="currentColor" d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 256c-35.3 0-64-28.7-64-64s28.7-64 64-64s64 28.7 64 64s-28.7 64-64 64z" class=""></path></svg>
                                    <p><?php echo $spt_location; ?></p>
                                </div>
                                <div class="list-date">
                                    <?php if( ! empty( $info_final_date ) ) { ?>
                                        <svg data-v-e1017caa="" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="calendar" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-calendar fa-fw fa-sm"><path data-v-e1017caa="" fill="currentColor" d="M96 32V64H48C21.5 64 0 85.5 0 112v48H448V112c0-26.5-21.5-48-48-48H352V32c0-17.7-14.3-32-32-32s-32 14.3-32 32V64H160V32c0-17.7-14.3-32-32-32S96 14.3 96 32zM448 192H0V464c0 26.5 21.5 48 48 48H400c26.5 0 48-21.5 48-48V192z" class=""></path></svg>
                                        <p><?php echo $info_final_date . ' ' . $info_time; ?></p>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if(!is_user_logged_in(  )) { ?>
                        <div class="spe-info-half-wrap banner-community" style="background: linear-gradient(-90deg, rgba(0,0,0,0.2) 0%, #000 50%), url(<?php echo get_stylesheet_directory_uri(  );?>/resources/images/banner-comm-bg.jpg) no-repeat right 60%;">
                            <div class="inner">
                                <p><?php _e('ARE YOU IN OUR COMMUNITY?
Log in and discover your reserved fees for this event.', 'stc-tickets');?></p>
                            </div>
                        </div>
                    <?php } ?>
                    <!-- FINE MOD SARAH -->
                </div>
            </div>
            <div class="spettacolo-prices-wrap">
                <div class="spe-half-wrap left_part">
                    <?php
                    /**
                     * Print 5 test
                     */
                    if(isset($_GET['print']) && $_GET['print'] == 5) {
                        echo "<pre>";
                        print_r($circles_arr);
                        echo "</pre>";
                    }
                    if( ! empty( $circles_arr ) ) {
                        if( isset($_GET[ 'selectionMode' ]) && $_GET[ 'selectionMode' ] == '1' ) {
                            ?>
                            <div class="controls custom-controls">
                                <button id="zoom-in"><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"/></svg></button>
                                <button id="zoom-out"><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z"/></svg></button>
                                <button id="reset"><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 576 512"><path d="M575.8 255.5c0 18-15 32.1-32 32.1h-32l.7 160.2c0 2.7-.2 5.4-.5 8.1V472c0 22.1-17.9 40-40 40H456c-1.1 0-2.2 0-3.3-.1c-1.4 .1-2.8 .1-4.2 .1H416 392c-22.1 0-40-17.9-40-40V448 384c0-17.7-14.3-32-32-32H256c-17.7 0-32 14.3-32 32v64 24c0 22.1-17.9 40-40 40H160 128.1c-1.5 0-3-.1-4.5-.2c-1.2 .1-2.4 .2-3.6 .2H104c-22.1 0-40-17.9-40-40V360c0-.9 0-1.9 .1-2.8V287.6H32c-18 0-32-14-32-32.1c0-9 3-17 10-24L266.4 8c7-7 15-8 22-8s15 2 21 7L564.8 231.5c8 7 12 15 11 24z"/></svg></button>
                            </div>
                        <?php } ?>
                        <div class="spettacolo-prices-img">
                            <svg id="svgSeatSvg" height="<?php echo $svgHeight; ?>" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" viewBox="0 0 1280 960" style="width: 100%;" draggable="false">
                                <g draggable="false">
                                    <image id="svgSeatImage" xlink:href="<?php echo API_HOST.'/map_room_data/' . $vcode . '_w.gif'; ?>" height="<?php echo $svgHeight; ?>" data-id="svg-back"></image>
                                    <rect id="svgSeatRect" width="1280" height="960" fill="transparent" x="0" y="0" data-id="svg-layer"></rect>
                                    <?php
                                    foreach ( $circles_arr as $circles_arr_key => $circles_arr_value ) {
                                        $seat_ind_status = $circles_arr_value[ 'index-status' ];
                                        $clickable       = (($seat_ind_status == 0 || empty( $seat_ind_status )) && !empty($circles_arr_value[ 'zone-id' ])) ? 1 : 0;
                                        $circle_color    = (($seat_ind_status == 0 || empty( $seat_ind_status )) && !empty($circles_arr_value[ 'zone-id' ])) ? $circles_arr_value[ 'data-color' ] : '#000';
                                        $circle_hover    = (($seat_ind_status == 0 || empty( $seat_ind_status )) && !empty($circles_arr_value[ 'zone-id' ])) ? 'cursor: pointer' : 'cursor: default';
                                        $circle_class    = (($seat_ind_status == 0 || empty( $seat_ind_status )) && !empty($circles_arr_value[ 'zone-id' ])) ? 'active' : '';
                                        ?>
                                            <circle title="<?php echo $circles_arr_value[ 'seat-description' ]; ?>" class="<?php echo $circle_class; ?>" id="<?php echo 'seat_' . $circles_arr_value[ 'seat-index' ]; ?>" r="<?php echo $circles_arr_value[ 'circle-width' ]; ?>" cx="<?php echo $circles_arr_value[ 'seat-x' ]; ?>" cy="<?php echo $circles_arr_value[ 'seat-y' ]; ?>" cxd="<?php echo $circles_arr_value[ 'seat-x' ]; ?>" cyd="<?php echo $circles_arr_value[ 'seat-y' ]; ?>" fill="<?php echo $circle_color; ?>" data-index="<?php echo $circles_arr_value[ 'seat-index' ]; ?>" data-id="<?php echo $circles_arr_value[ 'seat-id' ]; ?>" data-zone-id="<?php echo $circles_arr_value[ 'zone-id' ]; ?>" data-seat-desc="<?php echo $circles_arr_value[ 'seat-description' ]; ?>" data-zone-desc="<?php echo $circles_arr_value[ 'zone-desc' ]; ?>" data-price="<?php echo $circles_arr_value[ 'price' ]; ?>" data-color="<?php echo $circle_color; ?>" data-status="<?php echo $clickable; ?>" style="<?php echo $circle_hover; ?>;"></circle>
                                        <?php
                                    }
                                    ?>
                                    <!-- <rect id="svgSeatRectTooltip" width="300" height="60" fill="#404040" x="1002" y="850" rx="10" ry="10"></rect>
                                        <text id="svgSeatTextTooltip" font-family="arial" font-size="25" size="25" family="arial" x="1012" y="880" fill="white">I love SVG!</text>-->
                                </g>
                            </svg>
                            <div class="tooltip" <?php echo $selectionMode == 0 ? 'style="display:none;"' : '' ?>>
                                <div class="tooltip-title"></div>
                                <div class="tooltip-subtitle"></div>
                                <div class="tooltip-price-wrap">
                                    <div class="tooltip-price-title"></div>
                                    <div class="tooltip-price"></div>
                                </div>
                            </div>
                        </div>
                        <?php
                    } else {
                        if( ! empty( $errstring ) ) echo $errstring;
                    }
                    ?>
                </div>
                <div class="spe-half-wrap">
                    <div class="spettacolo-prices-inner">
                        <?php
                        if($subscription){
                        ?>
                            <ul class="tab">
                                <li class="tab-links active" data-target-tab="price-table"><a href="#"><?php _e('SELEZIONA I TUOI BIGLIETTI','stc-tickets'); ?></a></li>
                            </ul>
                        <?php
                        }else{
                        ?>
                            <ul class="tab">
                                <li class="tab-links <?php echo $selectionMode == 0 ? 'active' : '' ?>" data-target-tab="price-table"><a href="#"><?php _e('Miglior posto','stc-tickets'); ?></a></li>
                                <li class="tab-links <?php echo $selectionMode == 1 ? 'active' : '' ?>" data-target-tab="selected-seats-table"><a href="#"><?php _e('Selezione manuale','stc-tickets'); ?></a></li>
                            </ul>
                        <?php } ?>
                        <div class="list-table-wrap">
                            <div class="selected-seats-table tab-content">
                                <p class="seat-selection-warning"><?php _e('Clicca sulla mappa per selezionare il tuo posto','stc-tickets');?></p>
                            </div>
                            <div class="price-table tab-content">
                                <?php

                                /**
                                 * Testing: print the array of prices
                                 * by adding ?print=5 to the URL
                                 */
                                if(isset($_GET['print']) && $_GET['print'] == '5'){
                                    echo "<pre>";
                                    print_r($pricesArr);
                                    echo "</pre>";
                                }

                                if(!empty($pricesArr[ 'macrozone' ])){
                                    $reduction_maxbuy = 0;
                                    $macrozone = $pricesArr[ 'macrozone' ];
                                    $macrozoneArray = array();

                                    if( isset( $macrozone[ '@attributes' ] ) ) {
                                        $macrozoneArray[] = $macrozone;
                                    } else {
                                        foreach ( $macrozone as $macrozone_key => $macrozone_value ) {
                                            $macrozoneArray[] = $macrozone_value;
                                        }
                                    }

                                    /**
                                     * Testing: print the array of macrozone
                                     * by adding ?print=5 to the URL
                                     */
                                    if(isset($_GET['print']) && $_GET['print'] == '5'){
                                        echo "<pre>";
                                        print_r($macrozoneArray);
                                        echo "</pre>";
                                    }

                                    if(!empty($macrozoneArray)){
                                        foreach ( $macrozoneArray as $macrozoneArray_key => $macrozoneArray_value ) {
                                            $description = $macrozoneArray_value[ 'description' ];
                                            $zone        = $macrozoneArray_value[ 'zone' ];
                                            $reduction   = $zone[ 'reduction' ];
                                            $reductionArr = array();
                                            if( isset( $reduction[ '@attributes' ] ) ) {
                                                $reductionArr[] = $reduction;
                                            } else {
                                                foreach ( $reduction as $reduction_key => $reduction_value ) {
                                                    $reductionArr[] = $reduction_value;
                                                }
                                            }

                                            /**
                                             * Testing: print the array of reduction
                                             * by adding ?print=6 to the URL
                                             */
                                            if(isset($_GET['print']) && $_GET['print'] == '6'){
                                                echo "<pre>";
                                                print_r($reductionArr);
                                                echo "</pre>";
                                            }

                                            $pricesArr = array_column( $reductionArr, 'price' );
                                            $min_reduction_price = !empty($pricesArr) ? (min( $pricesArr ) / 100) : 0;
                                            $show_list_row = false;
                                            ob_start();


                                            if( ! empty( $reductionArr ) ) {

                                                foreach ( $reductionArr as $reductionArr_key => $reductionArr_value ) {

                                                    if( is_user_logged_in() ) {
                                                        $reduction_flag = false;
                                                        $reduction_desc   = $reductionArr_value[ 'description' ];
                                                        $reduction_price  = ((int) $reductionArr_value[ 'price' ] / 100);
                                                        $reduction_maxbuy = $reductionArr_value[ 'maxbuy' ];
                                                        $reduction_id     = $reductionArr_value[ '@attributes' ][ 'id' ];
                                                        $reduction_flag = true;

                                                        if(!empty($barcode)){
                                                            $reduction_price = '0.00';
                                                        }
                                                        if($reductionArr_value[ 'maxbuy' ] > 0 && $reduction_flag){
                                                            $show_list_row = true;
                                                        ?>
                                                            <div class="list-row des1 n<?php echo $reductionArr_key ?>" data-reductionId="<?php echo $reduction_id ?>">
                                                                <div class="row-title">
                                                                    <p><?php echo $reduction_desc; ?></p>
                                                                </div>
                                                                <div class="priceqty">
                                                                    <div class="row-price" data-price="<?php echo $reduction_price; ?>">
                                                                        <p><?php echo $reduction_price . ' &euro;'; ?></p>
                                                                    </div>
                                                                    <div class="cart-qty-counter">
                                                                        <div class="button-counter">
                                                                            <button class="cart-qty-minus" type="button" value="-">-</button>
                                                                            <!--<input type="text" name="qty" class="qty" maxlength="12" value="0" class="input-text qty" />-->
                                                                            <input type="text" name="qty" class="qty" data-max="<?php echo $reduction_maxbuy; ?>" value="0" class="input-text qty" disabled="disabled"/>
                                                                            <button class="cart-qty-plus" type="button" value="+">+</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php
                                                        }
                                                    } else if( ! is_user_logged_in() && strpos($reductionArr_value[ 'description' ], 'COMMUNITY' ) == false ) {
                                                        $reduction_desc   = $reductionArr_value[ 'description' ];
                                                        $reduction_price  = ((int) $reductionArr_value[ 'price' ] / 100);
                                                        $reduction_maxbuy = $reductionArr_value[ 'maxbuy' ];
                                                        $reduction_id     = $reductionArr_value[ '@attributes' ][ 'id' ];
                                                        if(!empty($barcode)){
                                                            $reduction_price = '0.00';
                                                        }
                                                        if($reduction_maxbuy > 0){
                                                            $show_list_row = true;
                                                        ?>
                                                            <div class="list-row des2" data-reductionId="<?php echo $reduction_id ?>">
                                                                <div class="row-title">
                                                                    <p><?php echo $reduction_desc; ?></p>
                                                                </div>
                                                                <div class="priceqty">
                                                                    <div class="row-price" data-price="<?php echo $reduction_price; ?>">
                                                                        <p><?php echo $reduction_price . ' &euro;'; ?></p>
                                                                    </div>
                                                                    <div class="cart-qty-counter">
                                                                        <div class="button-counter">
                                                                            <button class="cart-qty-minus" type="button" value="-">-</button>
                                                                            <!--<input type="text" name="qty" class="qty" maxlength="12" value="0" class="input-text qty" />-->
                                                                            <input type="text" name="qty" class="qty" data-max="<?php echo $reduction_maxbuy; ?>" value="0" class="input-text qty" disabled="disabled"/>
                                                                            <button class="cart-qty-plus" type="button" value="+">+</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php
                                                        }
                                                    }
                                                    ?>
                                                    <?php
                                                }
                                            }
                                            // $reductionArr = [];
                                            $list_row_html = ob_get_clean ();
                                            if($show_list_row){
                                            ?>
                                                <div class="table-row" data-zoneId="<?php echo $zone[ '@attributes' ][ 'id' ]; ?>" data-color="<?php echo $zone[ '@attributes' ][ 'color' ]; ?>">
                                                    <div class="title-wrap">
                                                        <div class="title">
                                                            <?php if( ! empty( $description ) ) { ?>
                                                                <span data-color="<?php echo $zone[ '@attributes' ][ 'color' ]; ?>" style="background-color:<?php echo $zone[ '@attributes' ][ 'color' ]; ?>"></span>
                                                                <h2><?php echo $description; ?></h2>
                                                            <?php } ?>
                                                        </div>
                                                        <div class="zone-pricing">
                                                            <?php if( ! empty( $min_reduction_price ) ) { ?>
                                                                <p><?php echo __('A partire da','stc-tickets')." " . $min_reduction_price . ' &euro;'; ?></p>
                                                            <?php } ?>
                                                        </div>
                                                        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="collapsable-chevron text-muted ml-auto svg-inline--fa fa-chevron-right fa-fw"><path fill="currentColor" d="M342.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L274.7 256 105.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" class=""></path></svg>
                                                    </div>
                                                    <?php echo $list_row_html; ?>
                                                </div>
                                            <?php
                                            }
                                        }
                                    }else{
                                        ?>
                                            <p><?php _e('Al momento i posti non sono disponibili','stc-tickets'); ?></p>
                                        <?php
                                    }
                                    $macrozoneArray = [];
                                }
                                ?>
                            </div>
                        </div>
                        <div class="total-values-wrap" style="display:none;">
                            <div class="total-qty-count total-values">
                                <span class="total-qty-label"><?php echo __( 'Quantit&agrave;','stc-tickets' ).": "; ?></span>&nbsp;
                                <span class="total-qty-value" data-count="0"> 0</span>
                            </div>
                            <div class="total-price-count total-values">
                                <span class="total-price-label"><?php echo __( 'Prezzo totale','stc-tickets' ).": "; ?></span>&nbsp;
                                <span class="total-price-value" data-count="0"> 0 <?php echo ' &euro;'; ?></span>
                            </div>
                            <div class="cart-buy-btn-wrap">
                                <?php
                                if( is_user_logged_in() ) {
                                    $current_user       = wp_get_current_user();
                                    $current_user_id    = $current_user->ID;
                                    $current_user_email = $current_user->user_email;
                                    $user_billing_phone = get_user_meta( $current_user_id, 'billing_phone', true );
                                    $place_of_birth = get_user_meta( $current_user_id, 'place_of_birth', true );
                                    $dob = get_user_meta( $current_user_id, 'dob', true );

                                    if( ! empty( $user_billing_phone ) && ! empty( $place_of_birth ) && ! empty( $dob ) && empty($barcode)) {
                                        // Ho tutto i dati del profilo e procedo
                                        $button_class = 'cart-buy-btn';
                                        $profile_status = 'complete';
                                        $data_src = '';
                                    } else if(empty( $user_billing_phone )) {
                                        // Non ho il telefono, devo chiedere di aggiornarlo
                                        $button_class = 'cart-buy-btn';
                                        $profile_status = 'incomplete';
                                        $data_src = '#edit-fancybox-form';
                                    } else if(!empty($barcode)) {
                                        // Ho il telefono ma ho un barcode, quindi sono in fase di acquisto di un abbonamento
                                        $button_class = 'subscription-buy-btn';
                                        $profile_status = 'complete';
                                        $data_src = '#subscription-fancybox-wrap';

                                    } else if(empty( $place_of_birth ) || empty( $dob )) {
                                        // Ho il telefono ma non ho i dati di nascita
                                        $button_class = 'cart-buy-btn';
                                        $profile_status = 'incomplete';
                                        $data_src = '#edit-fancybox-form';
                                    }

                                } else { // Non sono loggato
                                    $button_class = 'cart-buy-btn';
                                    $data_src = '#login-fancybox-form';
                                    $profile_status = 'incomplete';
                                }

                                echo '<button class="'.$button_class.' buy-btn" data-profile-status="'.$profile_status.'" data-src="'.$data_src.'">' . __('PROSEGUI','stc-tickets') . '</button>';
                                ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php // EX fancybox form for update profile
if (is_user_logged_in(  )) {
    if(empty( $user_billing_phone )) {
        $disabled_email = !empty( $current_user_email ) ? 'style="pointer-events:none;"' : '';
        ?>

    <div id="edit-fancybox-form" class="ffancybox-wrapper">
        <button class="chiudi-box" aria-label="<?php _e( 'Chiudi finestra', 'stc-tickets' ); ?>">⨯</button>
        <div class="fancyfox-content">

        <h2><?php esc_html_e( 'Update Profile', 'stc-tickets' ); ?></h2>

        <form class="woocommerce-form woocommerce-form-update-user update-user" method="post">

            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="reg_email<?php echo FORM_FIELD_CHARS; ?>"><?php esc_html_e( 'Email address', 'stc-tickets' ); ?>&nbsp;<span class="required">*</span></label>
                <input type="email" autocomplete="nope" class="woocommerce-Input woocommerce-Input--text input-text" name="reg_email<?php echo FORM_FIELD_CHARS; ?>" id="reg_email<?php echo FORM_FIELD_CHARS; ?>" autocomplete="email" value="<?php echo ! empty( $current_user_email ) ? $current_user_email : ''; ?>" <?php echo $disabled_email; ?> />
            </p>
            <p class="upd-phone-msg"><?php  _e('Enter your mobile phone number in the field below to receive a SMS with a 6-digit OTP code to confirm your submission','stc-tickets');?></p>
            <p class="form-row form-row-wide vivaticket-original-field">
                <label for="reg_billing_phone"><?php _e( 'Telefono *', 'stc-tickets' ); ?></label>
                <select class="input-text" name="country_code" id="reg_country_code">
                    <?php echo do_shortcode( '[get_country_code_options]' ); ?>
                </select>
                <input type="text" autocomplete="nope" class="input-text" name="billing_phone" id="reg_billing_phone" value="<?php isset($_POST[ 'billing_phone' ]) ? esc_attr_e( $_POST[ 'billing_phone' ] ) : ''; ?>" />
            </p>
            <p class="form-row form-row-wide">
                <label for="reg_billing_phone<?php echo FORM_FIELD_CHARS; ?>"><?php _e( 'Telefono *', 'stc-tickets' ); ?></label>
                <select class="input-text" name="country_code<?php echo FORM_FIELD_CHARS; ?>" id="reg_country_code<?php echo FORM_FIELD_CHARS; ?>">
                    <?php echo do_shortcode( '[get_country_code_options]' ); ?>
                </select>
                <input type="text" autocomplete="nope" class="input-text" name="billing_phone<?php echo FORM_FIELD_CHARS; ?>" id="reg_billing_phone<?php echo FORM_FIELD_CHARS; ?>" value="<?php isset($_POST[ 'billing_phone' . FORM_FIELD_CHARS ])? esc_attr_e( $_POST[ 'billing_phone' . FORM_FIELD_CHARS ] ) : ''; ?>" />
                <span id="phoneNumberError" style="color:red; display:none;"><?php esc_html_e( 'Invalid phone number', 'stc-tickets' ); ?></span>
            </p>
            <p id="otpAttemptsError" style="color:red; display:none;"><?php esc_html_e( "Hai già richiesto l' OTP, attendi 15 minuti prima di riprovare", 'stc-tickets' ); ?></p>
            <!-- Turnstile captcha -->
            <div id="ts-container" class="cf-turnstile" data-sitekey="<?php echo TS_CAPTCHA_DEV_SITE_KEY; ?>"></div>
            <p class="upd-phone-msg otp-msg" style="display:none;"><?php  _e('Enter the OTP code to verify your account and complete your purchase.','stc-tickets');?></p>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide otp-box" style="display:none;">
                <label for="registerotp"><?php esc_html_e( 'OTP', 'stc-tickets' ); ?>&nbsp;<span class="required">*</span></label>
                <input class="woocommerce-Input woocommerce-Input--text input-text" autocomplete="nope" type="text" name="registerotp" id="registerotp" autocomplete="OTP" />
            </p>
            <p class="woocommerce-form-row form-row">
                <?php // Check if user has test email
                /* DISABILITATO
                    if ($current_user_email == TEST_EMAIL) {
                        <button class="woocommerce-Button woocommerce-button update_phone_otp otp-verified-disabled button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : ''  ); ?> woocommerce-form-update__submit" name="update_phone_otp" value="<?php esc_attr_e( 'Update', 'stc-tickets' ); ?>"><?php esc_html_e( 'Update', 'stc-tickets' ); ?></button>

                    // if test email save phone number without verify and OTP
                    } else { */
                    ?>
                <button type="submit" class="woocommerce-Button otp-generate woocommerce-button update_phone_otp button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : ''  ); ?>" name="update_phone_otp" value="otp"><?php esc_html_e( 'Invia OTP', 'stc-tickets' ); ?></button>
                <button class="woocommerce-Button woocommerce-button button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : ''  ); ?> woocommerce-form-update__submit" name="update_billing_phone" value="update" hidden="hidden" style="display: none;"><?php esc_html_e( 'Update', 'stc-tickets' ); ?></button>
            </p>
        </form>
        <?php if( empty( $place_of_birth ) || empty( $dob ) ) { ?>
            <div class="cta-wrap cta-with-phone"  style="display:none">
                <p><?php esc_html_e( 'Thank you, your telephone is now verified.', 'stc-tickets' ); ?></p>
                <p><?php echo __( 'Your profile however is still not complete, please', 'stc-tickets' ). ' ' .'<a href="'.site_url()."/mio-account/?edit-account".'" target="_blank">'.__( "click here", "stc-tickets" ).'</a>'. ' ' .__( 'to complete it in order to buy the tickets', 'stc-tickets' ); ?></p>
            </div>
        <?php } ?>
        </div>
    </div>

    <?php //
    } else if(!empty($barcode)) { // Se ho il barcode, sono in fase di acquisto di un abbonamento ?>
    <div id="subscription-fancybox-wrap" class="ffancybox-wrapper">
        <button class="chiudi-box" aria-label="<?php _e( 'Chiudi finestra', 'stc-tickets' ); ?>">⨯</button>
        <div class="fancyfox-content">
            <h2><?php esc_html_e( 'Seleziona Spettacoli', 'stc-tickets' ); ?></h2>
            <div id="replace-me">
                <p><?php _e('Hello, this is the content to be replaced inside FancyBox!','stc-tickets'); ?></p>
            </div>
            <button class="cart-buy-btn buy-btn" data-profile-status="complete"><?php _e('Passo Successivo','stc-tickets'); ?></button>
        </div>
    </div>

    <?php } else if( empty( $place_of_birth ) || empty( $dob ) ) {
        // Non ho il profilo completo
        // retrieve site url in current language
        $site_url = site_url();
        if ( get_locale() !== 'it_IT' ) {
            $site_url = $site_url . '/en/';
        }
    ?>
    <div id="edit-fancybox-form" class="ffancybox-wrapper">
        <button class="chiudi-box" aria-label="<?php _e( 'Chiudi finestra', 'stc-tickets' ); ?>">⨯</button>
        <div class="fancyfox-content">
            <h2><?php esc_html_e( 'Update Profile', 'stc-tickets' ); ?></h2>
            <div class="cta-wrap">
                <p><?php echo __( 'Your profile however is still not complete, please', 'stc-tickets' ). ' ' .'<a href="'.$site_url."/mio-account/?edit-account".'" target="_blank">'.__( "click here", "stc-tickets" ).'</a>'. ' ' .__( 'to complete it in order to buy the tickets', 'stc-tickets' ); ?></p>
            </div>
        </div>
    </div>
    <?php
    }

} else { // login
?>

<div id="login-fancybox-form" class="ffancybox-wrapper">
    <button class="chiudi-box" aria-label="<?php _e( 'Chiudi finestra', 'stc-tickets' ); ?>">⨯</button>
    <div class="fancyfox-content">
        <?php do_action( 'woocommerce_before_customer_login_form' ); ?>

        <?php if( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>

            <div class="u-columns col2-set" id="customer_login">

                <div class="u-column1 col-1">

                <?php endif; ?>

                <h2><?php esc_html_e( 'Login', 'stc-tickets' ); ?></h2>

                <form class="woocommerce-form woocommerce-form-login login" method="post">

                    <?php do_action( 'woocommerce_login_form_start' ); ?>

                    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                        <label for="username"><?php esc_html_e( 'Username or email address', 'stc-tickets' ); ?>&nbsp;<span class="required">*</span></label>
                        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST[ 'username' ] ) ) ? esc_attr( wp_unslash( $_POST[ 'username' ] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine   ?>
                    </p>
                    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide form-login-password">
                        <label for="password"><?php esc_html_e( 'Password', 'stc-tickets' ); ?>&nbsp;<span class="required">*</span></label>
                        <input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" /><i onclick="showPsw()" class="bf-icon icon-hide-password toggle-password"></i> <?php // added by sarah ?>
                    </p>

                    <?php do_action( 'woocommerce_login_form' ); ?>
                    <div id="ts-container" class="cf-turnstile" data-sitekey="<?php echo TS_CAPTCHA_DEV_SITE_KEY; ?>"></div>
                    <p class="form-row">
                        <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
                            <input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'stc-tickets' ); ?></span>
                        </label>
                        <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
                        <button type="submit" class="woocommerce-button button woocommerce-form-login__submit<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : ''  ); ?>" name="login" value="<?php esc_attr_e( 'Log in', 'stc-tickets' ); ?>"><?php esc_html_e( 'Log in', 'stc-tickets' ); ?></button>
                    </p>
                    <p class="woocommerce-LostPassword lost_password tt">
                        <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" id="custom-lost-password-link"><?php esc_html_e( 'Lost your password?', 'stc-tickets' ); ?></a>
                    </p>

                    <?php // added by sarah ?>
                    <p>
                        <?php
                        echo sprintf( __( 'Non hai un account? <a href="%s">Registrati</a>', 'stc-tickets' ),
                                home_url( '/user-registration' )
                        );
                        ?>
                    </p>

                    <?php do_action( 'woocommerce_login_form_end' ); ?>

                </form>

                <?php // added by sarah ?>
                <script>
                    function showPsw() {
                        var x = document.getElementById("password");
                        if (x.type === "password") {
                            x.type = "text";
                        } else {
                            x.type = "password";
                        }
                    }
                </script>

                <?php if( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>

                </div>

                <div class="u-column2 col-2">

                    <a href="/user-registration" target="_blank"><?php echo __('Register Now','stc-tickets').'!!'; ?></a>

                </div>

            </div>
        <?php endif; ?>

        <?php do_action( 'woocommerce_after_customer_login_form' ); ?>
    </div>
</div>
<?php
}
    $html = ob_get_clean();
    return $html;
}

function my_js_variables() {
    ?>
    <script type="text/javascript">
        var globalJsPricing = '<?php echo isset( $globalJsPricing ) ? $globalJsPricing : ""; ?>';
        var extGetMapDataPricing = '<?php echo isset( $extGetMapDataPricing ) ? $extGetMapDataPricing : ""; ?>';
    </script><?php
}
add_action( 'wp_head', 'my_js_variables' );
