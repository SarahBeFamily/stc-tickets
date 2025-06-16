<?php
/*
 * ajax for store Booking object into user meta
 */
add_action ( 'wp_ajax_getBookingCart', "fun_getBookingCart" );
add_action ( 'wp_ajax_nopriv_getBookingCart', "fun_getBookingCart" );
function fun_getBookingCart (){
    $response = array(
        'message' => '',
        'status'  => false,
    );
    $addToCartObject        = $_POST['addToCart'];
    $subscription_list      = isset($_POST[ 'subscription_list' ]) ? $_POST[ 'subscription_list' ] : '';
    $orderId                = $_POST[ 'orderId' ];
    $user_id                = get_current_user_id();
    $get_user_meta_before   = get_user_meta( $user_id, 'addToCartObject' );
    $transaction_ids_before = !empty(get_user_meta( $user_id, 'transactionIds' )[0]) ? get_user_meta( $user_id, 'transactionIds' )[0] : array();
    $pcode                  = $_POST[ 'pcode' ];
    $show_date              = isset($_POST[ 'show_date' ]) ? $_POST[ 'show_date' ] : '';
    $final_cart_obj         = array ();
    $current_cart_obj       = array ();
    $subscription_cart_obj = array ();
    $subscription_cart_obj_final = array ();
    $transaction_ids_to_remove = array ();

    if(is_array($subscription_list) && !empty( $subscription_list ) && is_array($addToCartObject) && !empty( $addToCartObject )){
        foreach ( $addToCartObject as $addToCartObject_key => $addToCartObject_value ) {
            // $ticketName = $addToCartObject_key != '0' ? $addToCartObject_key : '';
            $first_addToCartObject = array_map( function ($addToCartObject_val){
                $addToCartObject_val['doBooking'] = 1;
                // MOD SARAH
                // Add tiket name and date to subscription object
                $addToCartObject_val['showDate'] = isset($_POST[ 'show_date' ]) ? $_POST[ 'show_date' ] : '';
                $addToCartObject_val['ticketName'] = isset($addToCartObject_key) ? $addToCartObject_key : '';

                return $addToCartObject_val;
            }, $addToCartObject_value );
            if( empty( $subscription_cart_obj[ $addToCartObject_key ] ) ) {
                $subscription_cart_obj[ $addToCartObject_key ] = $first_addToCartObject;
            }
        }
        $subscription_cart_obj_final = $subscription_cart_obj;
    }

    /**
     * if user has already added some tickets to cart
     * saved in user meta
     * then merge the new tickets with the old tickets
     * else add the new tickets to cart
     */
    if( ! empty( $get_user_meta_before ) && ! empty( $get_user_meta_before[ 0 ] ) ) {
//    if( ! empty( $get_user_meta_before ) && ! empty( $get_user_meta_before[ 0 ] ) && empty( $subscription_list ) ) {

        $final_cart_obj = $get_user_meta_before[ 0 ];

        foreach ( $get_user_meta_before[ 0 ] as $get_key => $get_value ) {
            if( isset($addToCartObject[ $get_key ]) ) {
                $addToCartZone = $addToCartObject[ $get_key ];

                foreach ( $get_value as $zone_key => $zone_value ) {
                    $zoneName             = $zone_value[ 'zoneName' ];
                    $zoneId               = $zone_value[ 'zoneId' ];
                    $reductions           = $zone_value[ 'reductions' ];
                    $final_zone_reduction = $reductions;

                    foreach ( $addToCartZone as $addToCartZone_key => $addToCartZone_value ) {
//                        echo "<pre>addToCartZone_value";
//                        print_r($addToCartZone_value);
//                        echo "</pre>";
                        if( $zoneName == $addToCartZone_value[ 'zoneName' ] ) {
//                            $addToCartZone_value[ 'doBooking' ] = 1;
//                          $temp_current_cart_obj = array_filter($final_cart_obj,function ($var) use ($zoneName){
//                                foreach($var as $var_key => $var_value){
//                                    if($var_value['zoneName'] == $zoneName){
//                                        return($var_value);
//                                    }
//                                }
//                            });
                            $addToCartZoneReductions = $addToCartZone_value[ 'reductions' ];

//                            $zone_array_filter = array_map( function ($zone_reductions) use ($reductions,$zoneId,$pcode,$transaction_ids_before,$transaction_ids_to_remove) {
//                                foreach ( $reductions as $reductions_key => $reductions_value ) {
//                                    if( $zone_reductions[ 'reductionName' ] == $reductions_value[ 'reductionName' ] ) {
//                                        $zone_reductions[ 'reductionQuantity' ] += $reductions_value[ 'reductionQuantity' ];
//                                    }
//                                    if( $zone_reductions[ 'reductionName' ] == $reductions_value[ 'reductionName' ] ) {
//                                        if(isset($reductions_value["seatId"])){
//                                            array_push( $transaction_ids_to_remove, $transaction_ids_before[$zoneId.$pcode]['transaction_id'] );
//                                        }
//                                        if( isset($zone_reductions["seatId"]) || isset($reductions_value["seatId"]) ) {
//                                            $zone_reductions["seatId"] = isset($zone_reductions["seatId"]) ? array_unique(array_merge($zone_reductions["seatId"],$reductions_value["seatId"])) : $reductions_value["seatId"];
//                                        }
//                                    }
//                                }
//                            return $zone_reductions;
//                            }, $addToCartZoneReductions );


                            foreach ( $addToCartZoneReductions as $zone_reductions_key => $zone_reductions ) {
                                foreach ( $reductions as $reductions_key => $reductions_value ) {
                                    if( $zone_reductions[ 'reductionName' ] == $reductions_value[ 'reductionName' ] ) {
                                        $zone_reductions[ 'reductionQuantity' ] += $reductions_value[ 'reductionQuantity' ];
                                    }
                                    if( $zone_reductions[ 'reductionName' ] == $reductions_value[ 'reductionName' ] ) {
                                        if(isset($reductions_value["seatId"])){
                                            array_push( $transaction_ids_to_remove, $transaction_ids_before[$zoneId.$pcode]['transaction_id'] );
                                        }
                                        if( isset($zone_reductions["seatId"]) || isset($reductions_value["seatId"]) ) {
                                            $zone_reductions["seatId"] = isset($zone_reductions["seatId"]) && is_array($zone_reductions["seatId"] && is_array($reductions_value["seatId"])) ? array_unique(array_merge($zone_reductions["seatId"],$reductions_value["seatId"])) : $reductions_value["seatId"];
                                        }
                                    }
                                }
                                $addToCartZoneReductions[$zone_reductions_key] = $zone_reductions;
                            }

                            $zone_array_filter = $addToCartZoneReductions;

                            foreach ( $reductions as $reductions_key => $reductions_value ) {
                                foreach ( $zone_array_filter as $zone_array_filter_key => $zone_array_filter_value ) {
                                    $zone_reduction_name_array = array_column( $final_zone_reduction, 'reductionName' );

                                    if( $reductions_value[ 'reductionName' ] == $zone_array_filter_value[ 'reductionName' ] ) {
                                        $final_zone_reduction[ $reductions_key ] = $zone_array_filter_value;
                                    } else {
                                        if( ! in_array( $zone_array_filter_value[ 'reductionName' ], $zone_reduction_name_array ) ) {
                                            array_push( $final_zone_reduction, $zone_array_filter_value );
                                        }
                                    }
                                }
                            }

                            foreach ( $final_cart_obj[ $get_key ] as $final_cart_obj_key => $final_cart_obj_value ) {
                                if( $final_cart_obj_value[ 'zoneName' ] == $addToCartZone_value[ 'zoneName' ] ) {
                                    $final_cart_obj_value[ 'reductions' ] = $final_zone_reduction;
                                    $final_cart_obj_value[ 'doBooking' ] = 1;
                                    // MOD SARAH
                                    $final_cart_obj_value[ 'showDate ' ] = $show_date;
                                    $current_cart_obj[ $get_key ][ $final_cart_obj_key ] = $final_cart_obj_value;
                                }
                                $final_cart_obj[ $get_key ][ $final_cart_obj_key ] = $final_cart_obj_value;
                            }
                        } else {
                            $zoneNameArray = array_column( $final_cart_obj[ $get_key ], 'zoneName' );
                            if( ! in_array( $addToCartZone_value[ 'zoneName' ], $zoneNameArray ) ) {
                                $addToCartZone_value[ 'doBooking' ] = 1;
                                $addToCartZone_value[ 'showDate' ] = $show_date;
                                array_push( $final_cart_obj[ $get_key ], $addToCartZone_value );
                            }
                        }
                    }
                }
            } else {
                foreach ( $addToCartObject as $addToCartObject_key => $addToCartObject_value ) {
                    $first_addToCartObject = array_map( function ($addToCartObject_val){
                        $addToCartObject_val['doBooking'] = 1;
                        return $addToCartObject_val;
                    }, $addToCartObject_value );
                    if( empty( $final_cart_obj[ $addToCartObject_key ] ) ) {
                        $final_cart_obj[ $addToCartObject_key ] = $first_addToCartObject;
                    }
                }
            }
        }
    } else {
        //
        foreach ( $addToCartObject as $addToCartObject_key => $addToCartObject_value ) {
            $first_addToCartObject = array_map( function ($addToCartObject_val){
                $addToCartObject_val['doBooking'] = 1;
                return $addToCartObject_val;
            }, $addToCartObject_value );
            if( empty( $final_cart_obj[ $addToCartObject_key ] ) ) {
                $final_cart_obj[ $addToCartObject_key ] = $first_addToCartObject;
            }
        }
        $final_cart_obj_before = $final_cart_obj;
    }

    //    echo "<pre>";
    //    print_r($final_cart_obj);
    //    echo "</pre>";
    //    if(!empty($current_cart_obj)){
    //        $addToCartObject = $current_cart_obj;
    //    }

    if(!empty($subscription_cart_obj_final)){
        $addToCartObject = $subscription_cart_obj_final;
    }else{
        $addToCartObject = $final_cart_obj;
    }

    //    echo "<pre>";
    //    print_r($get_user_meta_after);
    //    echo "</pre>";
    //    $bestseatapi = 'gfh';

    //    echo "<pre>";
    //    print_r($addToCartObject);
    //    print_r($transaction_ids_to_remove);
    //    echo "</pre>";
    //    die();

    if(!empty($transaction_ids_to_remove)){
        $transactions_str = "";
        foreach($transaction_ids_to_remove as $transaction_remove_key => $transaction_remove_val){
            $transactions_str .= "transactionCode[]=".$transaction_remove_val."&";
        }
        $transactions_str = rtrim($transactions_str,"&");
        // echo "<pre>";
        // print_r($transactions_str);
        // echo "</pre>";
        $curl_url = API_HOST . 'backend/backend.php?id=' . APIKEY . '&cmd=setExpiry&' . $transactions_str . '&timeout=-1000&preserveOnError=1';
        $empty_cart_cookie = tempnam ("/tmp", "CURLCOOKIE");

        $set_expiry_curl = curl_init();

        curl_setopt_array($set_expiry_curl, array(
          CURLOPT_URL => $curl_url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEJAR => $empty_cart_cookie,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $set_expiry_response = curl_exec ( $set_expiry_curl );
        if (curl_errno($set_expiry_curl)) {
            $error_msg = curl_error($set_expiry_curl);
            $response[ 'message' ] = $error_msg;
        }
        curl_close($set_expiry_curl);


        $set_expiry_xml         = isset($set_expiry_response) && !empty($set_expiry_response) ?  simplexml_load_string ( $set_expiry_response ) : '';
        $set_expiry_json        = isset($set_expiry_xml) && !empty($set_expiry_xml) ?  json_encode ( $set_expiry_xml ) : '';
        $set_expiry_response    = isset($set_expiry_json) && !empty($set_expiry_json) ?  json_decode ( $set_expiry_json, TRUE ) : array();
    }
    //    echo "<pre>";
    //    print_r($set_expiry_response);
    //    echo "</pre>";
    //    die();


    $transaction_ids  = array ();
    $transaction_list = array ();
    $final_seats      = array ();
    $booked_seats     = array ();
    $subs_seat_list   = get_user_meta( $user_id, 'subscriptionSeatList', true );
    $subs_seat_list   = !empty($subs_seat_list) ? $subs_seat_list : array ();


    $vcode            = $_POST[ 'vcode' ];
    // $pcode            = $_POST[ 'pcode' ];
    $regData          = $_POST[ 'regData' ];
    $manualSelection  = $_POST[ 'manualSelection' ];
    $subscription     = $_POST[ 'subscription' ];
    $current_user     = wp_get_current_user ();
    $user_firstname   = ! empty ( $current_user->user_firstname ) ? $current_user->user_firstname : $current_user->user_login;
    $user_lastname    = ! empty ( $current_user->user_lastname ) ? $current_user->user_lastname : $current_user->user_login;
    $user_email       = urlencode ( $current_user->user_email );
    $user_registered  = isset ( $current_user->user_registered ) ? urlencode ( $current_user->user_registered ) : '';
    $billing_phone    = '%2B' . get_user_meta ( $current_user->ID, 'billing_phone', true );
    $dob              = ! empty ( get_user_meta ( $current_user->ID, 'dob', true ) ) ? get_user_meta ( $current_user->ID, 'dob', true ) : '1970-01-01';
    $place_of_birth   = ! empty ( get_user_meta ( $current_user->ID, 'place_of_birth', true ) ) ? get_user_meta ( $current_user->ID, 'place_of_birth', true ) : 'rome';
    $user_ip          = ! empty ( get_user_meta ( $current_user->ID, 'user_ip', true ) ) ? get_user_meta ( $current_user->ID, 'user_ip', true ) : '103.215.158.90';

    // Create a DateTime object from the given date string
    $date = DateTime::createFromFormat('d/m/Y', $dob);

    // Check if the date was created successfully
    if ($date) {
        // Format the date to 'Y-m-d'
        $formattedDate = $date->format('Y-m-d');
        $final_dob = $formattedDate; // Output: 1989-04-22
    } else {
        $final_dob = $dob;
    }

    if($_SERVER['REMOTE_ADDR'] == '103.215.158.90') {
        $firstname        = $user_firstname;
        $lastname         = $user_lastname;
        $email            = $user_email;
        $birthdate        = $final_dob;
        $birthplace       = $place_of_birth;
        $telephone        = $billing_phone;
        $regId            = '1';
        $regIp            = $user_ip;
        $regDate          = $user_registered;
    }else {
        // for live users data
        $firstname        = $user_firstname;
        $lastname         = $user_lastname;
        $email            = $user_email;
        $birthdate        = $final_dob;
        $birthplace       = $place_of_birth;
        $telephone        = $billing_phone;
        $regId            = '1';
        $regIp            = $user_ip;
        $regDate          = $user_registered;
    }
    $idType           = 'OTP';
    if($subscription){
        $timeout          = '1200';
    }else{
        $timeout          = '600';
    }
    $loop_counter = 0;

    //    if(isset($_GET['print']) && $_GET['print'] == 1){
    //        echo "<pre>";
    //        print_r($addToCartObject);
    //        echo "</pre>";
    //    }

    foreach ( $addToCartObject as $addToCartObject_key => $addToCartObject_value ) {
        $ticketName = $addToCartObject_key;
        foreach ( $addToCartObject_value as $addToCartObject_k => $addToCartObject_v ) {
            $zoneId                              = $addToCartObject_v[ 'zoneId' ];
            $zoneName                            = $addToCartObject_v[ 'zoneName' ];
            $addToCartReductions                 = $addToCartObject_v[ 'reductions' ];
            $doBooking                           = $addToCartObject_v[ 'doBooking' ];
            if($doBooking == 1){
                $reductionTicket                       = '';
                $manualReductions                      = '';
                $zoneIds                               = '';
                $seatId                                = '';
                $transaction_list[ 'transaction_qty' ] = 0;
                $transaction_list[ 'seatId' ]          = array();
                foreach ( $addToCartReductions as $addToCartReductions_key => $addToCartReductions_value ) {
                    if (is_array($addToCartReductions_value)):
                        $transaction_list[ 'transaction_qty' ] = isset( $transaction_list[ 'transaction_qty' ] ) ? $transaction_list[ 'transaction_qty' ] + $addToCartReductions_value[ 'reductionQuantity' ] : $addToCartReductions_value[ 'reductionQuantity' ];
                        // $transaction_list[ 'seatId' ] = isset( $transaction_list[ 'seatId' ] ) ? $transaction_list[ 'seatId' ] + $addToCartReductions_value[ 'seatId' ] : $addToCartReductions_value[ 'seatId' ];
                        if($manualSelection == 'true'){
                            $seatids_tostring = is_array($addToCartReductions_value['seatId']) ? implode(",",$addToCartReductions_value['seatId']) : $addToCartReductions_value['seatId'];
                            $seatId .= rtrim($seatids_tostring, ',').',';
                            if(is_array($addToCartReductions_value['seatId'])){
                                for($i = 0; $i < count($addToCartReductions_value['seatId']); $i++){
                                    $manualReductions .= $addToCartReductions_value[ 'reductionId' ].',';
                                    $zoneIds .= $zoneId.',';
                                }
                            }else{
                                $manualReductions .= $addToCartReductions_value[ 'reductionId' ].',';
                                $zoneIds .= $zoneId.',';
                            }
                        }else{
                            $reductionTicket .= '&reduction[]=' . $addToCartReductions_value[ 'reductionId' ] . '&nTickets[]=' . $addToCartReductions_value[ 'reductionQuantity' ];
                        }
                    endif;
                }
                if($manualSelection == 'true'){
                        $seatId = rtrim($seatId, ',');
                        $manualReductions = rtrim($manualReductions,',');
                        $zoneIds = rtrim($zoneIds,',');
                    if( $regData == 1 ) {
                            $curl_url = API_HOST . 'backend/backend.php?cmd=manualRequest&id=' . APIKEY . '&timeout='.$timeout.'&vcode=' . trim($vcode) . '&pcode=' . trim($pcode) . '&stand=0&seats='.trim($seatId).'&reds='.trim($manualReductions).'&zone=' . trim($zoneIds) .'&firstname=' . urlencode(trim($firstname)) . '&lastname=' . urlencode(trim($lastname)) . '&email=' . trim($email) . '&birthdate=' . trim($birthdate) . '&birthplace=' . urlencode(trim($birthplace)) . '&telephone=' . trim($telephone) . '&'.'regId=' . trim($regId) . '&'.'regIp=' . trim($regIp) . '&'.'regDate=' . trim($regDate) . '&idType=' . trim($idType);
                    } else {
                            $curl_url = API_HOST . 'backend/backend.php?cmd=manualRequest&id=' . APIKEY . '&timeout='.$timeout.'&vcode=' . trim($vcode) . '&pcode=' . trim($pcode) . '&stand=0&seats='.trim($seatId).'&reds='.trim($manualReductions).'&zone=' . trim($zoneIds) . '&firstname=' . urlencode(trim($firstname)) . '&lastname=' . urlencode(trim($lastname)) . '&email=' . trim($email);
                    }
                }else{
                    if( $regData == 1 ) {
                        $curl_url = API_HOST . 'backend/backend.php?cmd=bestseat&id=' . APIKEY . '&timeout='.$timeout.'&vcode=' . trim($vcode) . '&pcode=' . trim($pcode) . '&zone=' . $zoneId . $reductionTicket . '&firstname=' . urlencode(trim($firstname)) . '&lastname=' . urlencode(trim($lastname)) . '&email=' . trim($email) . '&birthdate=' . trim($birthdate) . '&birthplace=' . urlencode(trim($birthplace)) . '&telephone=' . trim($telephone) . '&'.'regId=' . trim($regId) . '&'.'regIp=' . trim($regIp) . '&'.'regDate=' . trim($regDate) . '&idType=' . trim($idType);
                    } else {
                        $curl_url = API_HOST . 'backend/backend.php?cmd=bestseat&id=' . APIKEY . '&timeout='.$timeout.'&vcode=' . trim($vcode) . '&pcode=' . trim($pcode) . '&zone=' . $zoneId . $reductionTicket . '&firstname=' . urlencode(trim($firstname)) . '&lastname=' . urlencode(trim($lastname)) . '&email=' . trim($email);
                    }
                }
            //    echo "<pre>";
            //    print_r($curl_url);
            //    echo "</pre>";
            //    die();
                $book_cart_cookie = tempnam ("/tmp", "CURLCOOKIE");
                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => $curl_url,
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_COOKIEJAR => $book_cart_cookie,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'GET',
                ));

                $bestseat_response = curl_exec($curl);
            //    echo "<pre>";
            //    print_r($bestseat_response);
            //    echo "</pre>";
            //    echo "<pre>";
            //    print_r(curl_error($curl));
            //    echo "</pre>";
                if (curl_errno($curl)) {
                    $error_msg = curl_error($curl);
                    $response[ 'message' ] = $error_msg;
                }
                curl_close($curl);

                $xml                    = simplexml_load_string( $bestseat_response );
                $json                   = json_encode( $xml );
                $bestseatArr            = json_decode( $json, TRUE );
            //    echo "<pre>";
            //    print_r($addToCartReductions);
            //    echo "</pre>";
            //    echo "<pre>";
            //    print_r($bestseatArr);
            //    echo "</pre>";
                $transactions           = !empty( $bestseatArr[ 'transaction' ] ) ? $bestseatArr[ 'transaction' ] : '';
                if( ! empty( $transactions ) ) {
                    $transaction_list[ 'ticketName' ]         = $ticketName;
                    $transaction_list[ 'timestamp' ]          = (int) $bestseatArr[ '@attributes' ][ 'timet' ] + (int) $timeout;
                    $transaction_list[ 'transaction_id' ]     = $transactions[ '@attributes' ][ 'custref' ];
                    $transaction_list[ 'transaction_amount' ] = $transactions[ '@attributes' ][ 'amount' ];
                    $transaction_list[ 'regData' ]            = $regData;
                    $transaction_list[ 'zoneName' ]           = $zoneName;
                    $transaction_list[ 'zoneId' ]             = $zoneId;
                    $transaction_list[ 'pcode' ]              = $pcode;
                    $transaction_list[ 'vcode' ]              = $vcode;
                    $transaction_list[ 'seats' ]              = $addToCartReductions;
                    $transaction_list[ 'seatObject' ]         = $transactions[ 'seat' ];
                    $transaction_list[ 'subscription' ]       = $subscription;
                    $transaction_list[ 'showDate' ]           = $show_date;
                    if(!empty( $subscription_list )) {
                        $transaction_ids_before[$zoneId.$pcode]['subscription_seat'][] = $transaction_list;
                    }else{
                        $transaction_ids_before[$zoneId.$pcode] = $transaction_list;
                    }
                    if(!empty($transactions[ '@attributes' ][ 'custref' ])){
                        $final_cart_obj = $final_cart_obj;
                    }else{
                        $final_cart_obj = ! empty( $get_user_meta_before ) && ! empty( $get_user_meta_before[0] ) ? $get_user_meta_before[0] : array();
                    }
                }else{
                    $final_cart_obj = ! empty( $get_user_meta_before ) && ! empty( $get_user_meta_before[0] ) ? $get_user_meta_before[0] : array();
                    $response[ 'message' ] = $bestseatArr[ '@attributes' ]['errstring'];
                }
            }
            if( ! empty( $transactions ) && !empty($subscription_list) ) {
                $transcode     = $transactions[ '@attributes' ][ 'custref' ];
                $transaction_seatObject         = $transactions[ 'seat' ];
                $seat_ids = array();
                if(!empty($transaction_seatObject)){
                    if(array_key_first($transaction_seatObject) == 0){
                        foreach($transaction_seatObject as $seatObject_key => $seatObject_value){
                            $seat_id = $seatObject_value['@attributes']['id'];
                            $seat_ids[] = $seat_id;
                            $final_seats[] = $seat_id;
                        }
                    }else{
                        $seat_ids[] = $transaction_seatObject['@attributes']['id'];
                        $final_seats[] = $transaction_seatObject['@attributes']['id'];
                    }
                }
            //    $final_seats[] = $seat_ids;
            //    echo "<pre>";
            //    print_r($final_seats);
            //    echo "</pre>";
            //    echo "<pre>";
            //    print_r($subscription_list);
            //    echo "</pre>";
            //    $seat_html = '';
            //    if(count($seat_ids) > 1){
            //        foreach($seat_ids as $seat_key => $seat_value){
            //            $seat_html .= '&seatCode[]='.$seat_value;
            //        }
            //    }else{
            //        $seat_html .= '&seatCode='.$seat_ids[0];
            //    }
                if($loop_counter == 0){
                    $prev_count = count($seat_ids);
                    $start = 0;
                    $end = $prev_count;
                }else{
                    $start = $prev_count;
                    $end = $prev_count + count($seat_ids);
                    $prev_count = $prev_count + count($seat_ids);
                }
                for($i = $start; $i < $end; $i++){
                    $seat_html = '';
                    $oldRedId_html = '';
                    $newRedId_html = '';
                    $subsCode_html = '';
                    $seatCode = $final_seats[$i];
                    $oldRedId = $subscription_list[$i]['reductionId'];
                    $newRedId = $subscription_list[$i]['reductionId'];
                    $subsCode = $subscription_list[$i]['subscription'];
                //    if(count($seat_ids) > 1){
                //        $oldRedId_html .= '&oldRedId[]='.$subscription_list[$i]['reductionId'];
                //        $newRedId_html .= '&newRedId[]='.$subscription_list[$i]['reductionId'];
                //        $subsCode_html .= '&subsCode[]='.$subscription_list[$i]['subscription'];
                //    }else{
                //    echo "<pre>";
                //    print_r('i = '.$i.' - '.'$start = '.$start.' - '.'$end = '.$end.' - '.$final_seats[$i]);
                //    echo "</pre>";
                        $seat_html .= '&seatCode='.$seatCode;
                        $oldRedId_html .= '&oldRedId='.$oldRedId;
                        $newRedId_html .= '&newRedId='.$newRedId;
                        $subsCode_html .= '&subsCode='.$subsCode;
                //    }
                    $subscription_curl_url = API_HOST . 'backend/backend.php?id='.APIKEY.'&cmd=xmlChangeLock&tranCode='.$transcode.$seat_html.$oldRedId_html.$newRedId_html.$subsCode_html;
                    // echo "<pre>";
                    // print_r($subscription_curl_url);
                    // echo "</pre>";
                    $subs_cookie = tempnam ("/tmp", "CURLCOOKIE");
                    $subscription_curl = curl_init();

                    curl_setopt_array($subscription_curl, array(
                      CURLOPT_URL => $subscription_curl_url,
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => '',
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 0,
                      CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_COOKIEJAR => $subs_cookie,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => 'GET',
                    ));

                    $subscription_response = curl_exec($subscription_curl);
                    if (curl_errno($subscription_curl)) {
                        $error_msg = curl_error($subscription_curl);
                        $response[ 'message' ] = $error_msg;
                    }
                    curl_close($subscription_curl);

                    $subscription_xml     = simplexml_load_string ( $subscription_response );
                    $subscription_json    = json_encode ( $subscription_xml );
                    $subscription_tickets = json_decode ( $subscription_json, TRUE );
                    if(isset($subscription_tickets['@attributes']['errstring'])){
                        $response[ 'message' ] = $subscription_tickets['@attributes']['errstring'];
                        $response[ 'subscription' ] = true;
                    }
                    if(isset($subscription_tickets['transaction'])){
                        $booked_seats[] = $seatCode;
                        $subscription_tickets['transaction']['seat_id'] = $seatCode;
                        $subs_seat_list[$subsCode][] = $subscription_tickets['transaction'];
                    }
                //    echo "<pre>";
                //    print_r($subscription_tickets);
                //    echo "</pre>";
                }
            }
            $loop_counter++;
        }
    }
//    echo "<pre>";
//    print_r($transaction_ids_before);
//    echo "</pre>";
//    echo "<pre>";
//    print_r($final_cart_obj);
//    echo "</pre>";

    if(!empty($subs_seat_list)){
        $final_transaction_ids = array_map( function ($transaction_ids_val) use ($booked_seats){
            if(array_key_first($transaction_ids_val) == 'subscription_seat'){
                foreach($transaction_ids_val['subscription_seat'] as $subscription_seat_key => $subscription_seat_value){
                    $seatObject = $subscription_seat_value['seatObject'];
                    if(array_key_first($seatObject) == 0){
                        foreach($seatObject as $seatObject_key => $seatObject_value){
                            $seat_id = $seatObject_value['@attributes']['id'];
                            if (!in_array($seat_id, $booked_seats)){
                                unset($seatObject[$seatObject_key]);
                            }
                        }
                        $subscription_seat_value['seatObject'] = $seatObject;
                    }else{
                        $seat_id = $seatObject['@attributes']['id'];
                        if(!in_array($seat_id,$booked_seats)){
                            unset($subscription_seat_value['seatObject']);
                        }

                    }
                }
                if(empty($subscription_seat_value['seatObject'])){
                    unset($transaction_ids_val['subscription_seat'][$subscription_seat_key]);
                }else{
                    $transaction_ids_val['subscription_seat'][$subscription_seat_key] = $subscription_seat_value;
                }
            }
            return $transaction_ids_val;
        }, $transaction_ids_before );
    }
//    echo "<pre>";
//    print_r($transaction_ids_before);
//    echo "</pre>";
    $subs_seat_list_res = update_user_meta( $user_id, 'subscriptionSeatList', $subs_seat_list );
    $update_user_transaction = update_user_meta( $user_id, 'transactionIds', $transaction_ids_before );
    $transactionIds          = get_user_meta( $user_id, 'transactionIds' );
//    echo "<pre>";
//    print_r($transactionIds);
//    echo "</pre>";
//    echo "<pre>";
//    print_r($final_cart_obj);
//    echo "</pre>";
    foreach ( $final_cart_obj as $addToCartObject_key => $addToCartObject_value ) {
        $final_addToCartObject = array_map( function ($addToCartObject_val){
            $addToCartObject_val['doBooking'] = 0;
            return $addToCartObject_val;
        }, $addToCartObject_value );
        $final_cart_obj[$addToCartObject_key] = $final_addToCartObject;
    }
//    echo "<pre>";
//    print_r($final_cart_obj);
//    echo "</pre>";
    $update_user_meta = update_user_meta( $user_id, 'addToCartObject', $final_cart_obj );
    $get_user_meta    = get_user_meta( $user_id, 'addToCartObject' );
    if(!empty($orderId)){
        $update_user_meta = update_user_meta( $user_id, 'subscriptionOrderId', $orderId );
    }
//    echo "<pre>";
//    print_r($get_user_meta);
//    echo "</pre>";
    if( !empty($get_user_meta[0]) && empty($response[ 'message' ]) ) {
        $product_id          = ! empty( get_option( 'TICKET_WOOCOMMERCE_PRODUCT_ID' ) ) ? get_option( 'TICKET_WOOCOMMERCE_PRODUCT_ID' ) : get_option( 'wc_ticket_product' ); // product ID to add to cart
//        if(empty( $subscription_list )){
            WC()->cart->empty_cart();
            WC()->cart->add_to_cart( $product_id );
//        }
        $response[ 'message' ] = $get_user_meta;
        $response[ 'status' ]  = true;
//        echo "<pre>";
//        print_r($response);
//        echo "</pre>";
    }else if(empty($response[ 'message' ])){
        $response[ 'message' ] = __("Qualcosa è andato storto!!", "stc-tickets");
    }
    echo json_encode ( $response );
    wp_die ();
}
/*
 * ajax for checkout
 */
add_action ( 'wp_ajax_getCheckout', "fun_getCheckout" );
add_action ( 'wp_ajax_nopriv_getCheckout', "fun_getCheckout" );
function fun_getCheckout (){
    $totalPrice = isset($_POST['totalPrice']) ? $_POST['totalPrice'] : '';
    $totalQuantity = isset($_POST['totalQuantity']) ? $_POST['totalQuantity'] : '';
    $transaction_id = '';
    $amount = 0;
    $total_qty = 0;
    $response = array(
        'message' => '',
        'status'  => false,
    );
    $user_id = get_current_user_id();
    $transactions = get_user_meta($user_id,'transactionIds');
    $tranIp = '102.129.166.20';
    // $tranIp = $_SERVER['REMOTE_ADDR'];
    foreach($transactions as $transaction_key => $transaction_value){
        foreach($transaction_value as $transaction_k => $transaction_v){
            if( is_array( $transaction_v ) && array_key_first( $transaction_v ) == 'subscription_seat' ) {
                foreach($transaction_v['subscription_seat'] as $subscription_seat_k => $subscription_seat_v){
                    $regData = $subscription_seat_v['regData'];
                    $transaction_id .= '&transactionCode[]='.$subscription_seat_v['transaction_id'];
                    $amount = $amount + 0;
                    $total_qty = $total_qty + (int)$subscription_seat_v['transaction_qty'];
                }
            }else{
                $regData = $transaction_v['regData'];
                $transaction_id .= '&transactionCode[]='.$transaction_v['transaction_id'];
                $amount = $amount + (int)$transaction_v['transaction_amount'];
                $total_qty = $total_qty + (int)$transaction_v['transaction_qty'];
            }
        }
    }

    // $curl_url = API_HOST.'backend/backend.php?cmd=extXmlPrepareTransToPay&id='.APIKEY.$transaction_id.'&tNumSeats='.$total_qty.'&tAmount='.$amount.'&language=1&tranIp='.$tranIp.'&transaction_receipt_url[preprod]=https%3A%2F%2Fpreprod.teatrosancarlo.it%2Fthank-you%2F%3F&transaction_ser_notify[preprod]=https%3A%2F%2Fpreprod.teatrosancarlo.it%2Fthank-you%2F%3F';

    // Check if the user is in preprod and redirect thank you page to preprod
    $urlKey = '';
    if (strpos($_SERVER['HTTP_HOST'], 'preprod') !== false || strpos($_SERVER['HTTP_HOST'], '-dev') !== false) {
        $urlKey = '&urlKey=preprod';
    }

    $curl_url = API_HOST.'backend/backend.php?cmd=extXmlPrepareTransToPay&id='.APIKEY.$transaction_id.'&tNumSeats='.$total_qty.'&tAmount='.$amount.'&language=1&tranIp='.$tranIp.$urlKey;

    $ext_cookie = tempnam ("/tmp", "CURLCOOKIE");
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $curl_url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_COOKIEJAR => $ext_cookie,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Cookie: v1Locale=%7B%22Currency%22%3Anull%2C%22SuggestCountry%22%3Anull%2C%22Country%22%3A%22IT%22%2C%22Language%22%3A%22it-IT%22%7D'
      ),
    ));

    $transactionResponse = curl_exec($curl);

    curl_close($curl);
    $xml                = simplexml_load_string( $transactionResponse);
    $json               = json_encode( $xml );
    $transactionArr     = json_decode( $json, TRUE );

    $responseArr = array();
    $responseArr['paym_code'] = isset($transactionArr['paym_code']) ? $transactionArr['paym_code'] : '';
    $responseArr['redirecturl'] = isset($transactionArr['redirecturl']) ? $transactionArr['redirecturl'] : '';
    $errcode = $transactionArr['@attributes']['errcode'];

    // test
    $responseArr['regData'] = $regData;
    $responseArr['urlKey'] = $urlKey;

    if( ! $errcode ) {
        $response[ 'message' ] = $responseArr;
        $response[ 'status' ]  = true;
    } else {
        $response[ 'message' ] = $transactionArr;
        $response[ 'status' ]  = false;
    }
    echo json_encode ( $response );
    wp_die ();
}
/*
 * ajax for checkout
 */
add_action ( 'wp_ajax_emptyCart', "fun_emptyCart" );
add_action ( 'wp_ajax_nopriv_emptyCart', "fun_emptyCart" );
function fun_emptyCart (){
    $response = array(
        'message' => '',
        'status'  => false,
    );
    $user_id = get_current_user_id();
    $transactionIds          = get_user_meta( $user_id, 'transactionIds' , true );
    $transactions_str = "";
    // Set total quantity in order to manage errors in cart
    $total_qty = isset($_POST['totalQuantity']) ? $_POST['totalQuantity'] : 1;

    if($total_qty > 0) :
    if(!empty($transactionIds)){
        foreach($transactionIds as $transactionIds_key => $transactionIds_value){
            $transactions_str .= "transactionCode[]=".$transactionIds_value['transaction_id']."&";
        }
        $transactions_str = rtrim($transactions_str,"&");
        $curl_url = API_HOST . 'backend/backend.php?id=' . APIKEY . '&cmd=setExpiry&' . $transactions_str . '&timeout=-1000&preserveOnError=1';
        $empty_cart_cookie = tempnam ("/tmp", "CURLCOOKIE");

        $set_expiry_curl = curl_init();

        curl_setopt_array($set_expiry_curl, array(
          CURLOPT_URL => $curl_url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEJAR => $empty_cart_cookie,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $set_expiry_response = curl_exec ( $set_expiry_curl );
        $err      = curl_error ( $set_expiry_curl );
        curl_close($set_expiry_curl);

        $xml         = simplexml_load_string ( $set_expiry_response );
        $json        = json_encode ( $xml );
        $set_expiry_response = json_decode ( $json, TRUE );
        $response[ 'message' ] = $set_expiry_response;
        $response[ 'status' ]  = true;
    }
    endif;

    // MOD SARAH
    // Empty cart with woo commerce
    WC()->cart->empty_cart();

    update_user_meta($user_id,'addToCartObject',array());
    update_user_meta($user_id,'transactionIds',array());
    update_user_meta( $user_id, 'subscriptionSeatList', array () );
    update_user_meta( $user_id, 'subscriptionOrderId', array () );

    echo json_encode ( $response );
    wp_die ();
}
/*
 * ajax for checkout
 */
add_action ( 'wp_ajax_getUserLogin', "fun_getUserLogin" );
add_action ( 'wp_ajax_nopriv_getUserLogin', "fun_getUserLogin" );
function fun_getUserLogin (){
    session_start();
    global $wpdb;
    $response = array(
        'message' => '',
        'status'  => false,
    );
    // $user = 'Kidea';
    // $password = 'TSC@2023';
    $otpMatched          = false;
    $otpCreated          = false;
    $err                 = '';
    $username            = !empty($_POST[ 'username' ]) ? $_POST[ 'username' ] : '';
    $password            = !empty($_POST[ 'password' ]) ? $_POST[ 'password' ] : '';
    $get_billing_phone   = $_POST[ 'billing_phone' ];
    $country_code        = ltrim( $_POST[ 'country_code' ], "+" );
    // add country code in phone(currently restricted to italy(+39) only)
    $billing_phone       = (int) $country_code . $get_billing_phone;
    $email               = isset($_POST[ 'email' ]) ? $_POST[ 'email' ] : '';
    $generate_otp_now    = isset($_POST[ 'generate_otp_now' ]) ? $_POST[ 'generate_otp_now' ] : false;
    $registerOtp         = isset( $_POST[ 'registerotp' ] ) ? (int) $_POST[ 'registerotp' ] : '';
    $query = $wpdb->prepare("
        SELECT DISTINCT user_id
        FROM $wpdb->usermeta
        WHERE meta_key = %s
        AND meta_value = %s
    ", 'billing_phone', $get_billing_phone);
    $results = $wpdb->get_results($query);
    //    $billing_phone_exist = false;
    //    $users = get_users(array( 'fields' => array( 'ID' ) ));
    //    foreach($users as $users_key => $users_value){
    //        $user_id = $users_value->id;
    //        $this_billing_phone  = get_user_meta ($user_id, 'billing_phone' );
    //        if(in_array($get_billing_phone,$this_billing_phone)){
    //            $billing_phone_exist = true;
    //        }else{
    //            $billing_phone_exist = false;
    //        }
    //    }
    $curl_response = array();
    if( $generate_otp_now ) {

        // Check turnstile recaptcha
        $turnstile_response = isset($_POST['turnstile_response']) ? $_POST['turnstile_response'] : '';
        if (empty($turnstile_response)) {
            $response['error'] = __('Please complete the reCAPTCHA verification.', 'stc-tickets');
            echo json_encode($response);
            wp_die();
        }
        $turnstile_response = sanitize_text_field($_POST['cf-turnstile-response']);
        $response = wp_remote_post('https://challenges.cloudflare.com/turnstile/v0/siteverify', array(
            'body' => array(
                'secret' => TS_CAPTCHA_DEV_SECRET_KEY,
                'response' => $turnstile_response,
                'remoteip' => $_SERVER['REMOTE_ADDR'],
            ),
        ));
        
        $response = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($response['success']) && $response['success'] === false) {
            $response[ 'error' ] = __('reCAPTCHA verification failed.', 'stc-tickets');
            wp_send_json_error($response);
            wp_die();
        } elseif (!isset($response['success']) || $response['success'] !== true) {
            $response[ 'error' ] = __('reCAPTCHA verification failed.', 'stc-tickets');
            wp_send_json_error($response);
            wp_die();
        } else {
            // reCAPTCHA verification passed
            // $response['otp_next'] = 'manda OPT';
            // echo json_encode($response);
            // wp_die();
        }
        

        if(email_exists($email)){
            $response[ 'error' ]            = "Email già esistente!!";
            echo json_encode ( $response );
            wp_die ();
        }else if(username_exists($username)){
            $response[ 'error' ]            = "Il nome utente esiste già!!";
            echo json_encode ( $response );
            wp_die ();
        }

        if(empty($results)) {
            $genratedOtp        = random_int( 100000, 999999 );
            $_SESSION[ "$email" ] = $genratedOtp;
            if($country_code == '39'){
                $message = __("Benvenuto nella Community del Teatro di San Carlo! Questo è il codice OTP","stc-tickets"). " " . $genratedOtp . " " . __("per completare la registrazione","stc-tickets");
            }else{
                $message = __("Welcome in the Community of Teatro di San Carlo! This is the OTP code","stc-tickets"). " " . $genratedOtp . " " . __("to register","stc-tickets");
            }
            $curl_body          = array (
                "default"  => [
                    "from" => "Sancarlo",
                    "text" => $message
                ],
                "specific" => [
                    [ "to" => "$billing_phone" ]
                ]
            );
            $curl_body     = json_encode ( $curl_body );
            $ulogin_cookie = tempnam ("/tmp", "CURLCOOKIE");
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://httpsmpp3.rdcom.it:2500/v1/sms/send',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_COOKIEJAR => $ulogin_cookie,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>$curl_body,
              CURLOPT_HTTPHEADER => array(
                'Authorization: Basic S2lkZWE6I2diVFNDJjY=',
                'Content-Type: application/json'
              ),
            ));

            $curl_response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            $generate_otp_now = false;
            $otpCreated       = true;
        } else {
            $err = __('Phone Number is already exist!!','stc-tickets');
        }
    }
    if(!empty($registerOtp)){
        $genratedOtp = $_SESSION["$email"];
        if($registerOtp === $genratedOtp){
            $otpMatched = true;
        }else{
            $err = __('OTP is not valid!!','stc-tickets');
        }
    }
    if( ! empty( $get_billing_phone ) && ! empty( $email ) ) {
        $response[ 'message' ]          = array ( $billing_phone, $otpMatched, $curl_response, $country_code );
        $response[ 'otpMatched' ]       = $otpMatched;
        $response[ 'otpCreated' ]       = $otpCreated;
//        $response[ 'otp' ]              = $genratedOtp;
        $response[ 'generate_otp_now' ] = $generate_otp_now;
        $response[ 'status' ]           = true;
        $response[ 'error' ]            = $err;
    }
    echo json_encode ( $response );
    wp_die ();
}

/*
 * ajax for email validation in registration
 */
add_action ( 'wp_ajax_checkUserEmail', "fun_checkUserEmail" );
add_action ( 'wp_ajax_nopriv_checkUserEmail', "fun_checkUserEmail" );
function fun_checkUserEmail (){
    global $wpdb;
    $response = array(
        'status'  => false,
        'error' => '',
    );
    $username            = !empty($_POST[ 'username' ]) ? $_POST[ 'username' ] : '';
    $email               = $_POST[ 'email' ];

    if(email_exists($email)){
        $response[ 'error' ]            = __("Email already exists!!","stc-tickets");
    }else if(username_exists($username)){
        $response[ 'error' ]            = __("The username already exists!!","stc-tickets");
    }else{
        $response[ 'status' ]            = true;
    }

    echo json_encode ( $response );
    wp_die ();
}

/*
 * ajax for update phone from my account
 */
add_action ( 'wp_ajax_UpdateUserPhone', "fun_UpdateUserPhone" );
add_action ( 'wp_ajax_nopriv_UpdateUserPhone', "fun_UpdateUserPhone" );
function fun_UpdateUserPhone (){
    session_start();
    global $wpdb;
    $response = array(
        'message' => '',
        'status'  => false,
    );
 //    $user = 'Kidea';
 //    $password = 'TSC@2023';
    $otpMatched          = false;
    $otpCreated          = false;
    $err                 = '';
    $get_billing_phone   = $_POST[ 'billing_phone' ];
    $country_code        = ltrim( $_POST[ 'country_code' ], "+" );
    // add country code in phone(currently restricted to italy(+39) only)
    $billing_phone       = (int) $country_code . $get_billing_phone;
    $email               = isset($_POST[ 'email' ]) ? $_POST[ 'email' ] : '';
    $generate_otp_now    = isset($_POST[ 'generate_otp_now' ]) ? $_POST[ 'generate_otp_now' ] : false;
    $registerOtp         = isset( $_POST[ 'registerotp' ] ) ? (int) $_POST[ 'registerotp' ] : '';
    // $registerOtp         = $email === TEST_EMAIL ? "TEST_OTP" : $registerOtp;
    $turnstile_response  = isset($_POST['tsc_verify']) ? $_POST['tsc_verify'] : '';
    
    // Verify nonce
    if ( ! wp_verify_nonce( $_POST['nonce'], 'otp_nonce' ) ) {
        $err = __('Nonce verification failed.', 'stc-tickets');
        wp_send_json_error($err);
        wp_die();
    }
    $current_user = wp_get_current_user();
    $current_user_id =  $current_user->ID;
    $query = $wpdb->prepare("
        SELECT DISTINCT user_id
        FROM $wpdb->usermeta
        WHERE meta_key = %s
        AND meta_value = %s
    ", 'billing_phone', $get_billing_phone);
    $results = $wpdb->get_results($query);
    //    $billing_phone_exist = false;
    //    $users = get_users(array( 'fields' => array( 'ID' ) ));
    //    foreach($users as $users_key => $users_value){
    //        $user_id = $users_value->id;
    //        $this_billing_phone  = get_user_meta ($user_id, 'billing_phone', true );
    ////        if(in_array($get_billing_phone,$this_billing_phone)){
    ////        if($get_billing_phone == $this_billing_phone){
    ////            $billing_phone_exist = true;
    ////        }else{
    ////            $billing_phone_exist = false;
    ////        }
    //    }
    $curl_response = array();
    if( $generate_otp_now ) {
        // Check turnstile reecaptcha
        if (empty($turnstile_response)) {
            $response['error'] = __("Please check on the reCAPTCHA box.",'stc-tickets');
            echo json_encode ( $response );
            wp_die ();
        } else {
            $response = wp_remote_post('https://challenges.cloudflare.com/turnstile/v0/siteverify', array(
                'body' => array(
                    'secret' => TS_CAPTCHA_DEV_SECRET_KEY,
                    'response' => $turnstile_response,
                    'remoteip' => $_SERVER['REMOTE_ADDR'],
                ),
            ));

            // Check reCAPTCHA response
            $response = json_decode($response['body'], true);
            
            if (isset($response['success']) && $response['success'] === false) {
                $response['error'] = __('reCAPTCHA verification failed.', 'stc-tickets');
                echo json_encode ( $response );
                wp_die ();

            } elseif (!isset($response['success']) || $response['success'] !== true) {
                $response['error'] = __('reCAPTCHA verification failed.', 'stc-tickets');
                echo json_encode ( $response );
                wp_die();
            } else {
                // reCAPTCHA verification passed
                // $response['otp_next'] = 'manda OPT';
                // echo json_encode($response);
                // wp_die();
            }
        }
        
        
        if(empty($results)) {
            $genratedOtp        = random_int( 100000, 999999 );
            $_SESSION[ "$email" ] = $genratedOtp;
            if($country_code == '39'){
                $message = __("Benvenuto nella Community del Teatro di San Carlo! Questo è il codice OTP","stc-tickets"). " " . $genratedOtp . " " . __("per completare la registrazione","stc-tickets");
            }else{
                $message = __("Welcome in the Community of Teatro di San Carlo! This is the OTP code","stc-tickets"). " " . $genratedOtp . " " . __("to register","stc-tickets");
            }
            $curl_body          = array (
                "default"  => [
                    "from" => "Sancarlo",
                    "text" => $message
                ],
                "specific" => [
                    [ "to" => "$billing_phone" ]
                ]
            );

            $curl_body     = json_encode ( $curl_body );
            $gen_otp_cookie = tempnam ("/tmp", "CURLCOOKIE");
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://httpsmpp3.rdcom.it:2500/v1/sms/send',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_COOKIEJAR => $gen_otp_cookie,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>$curl_body,
              CURLOPT_HTTPHEADER => array(
                'Authorization: Basic S2lkZWE6I2diVFNDJjY=',
                'Content-Type: application/json'
              ),
            ));

            $curl_response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            $generate_otp_now = false;
            $otpCreated       = true;
        } else {
            $err = __('Phone Number is already exist!!','stc-tickets');
        }
    }
    $phone_updated = '';
    if(!empty($registerOtp)){
        $genratedOtp = $_SESSION["$email"];
        // Check for email test
        //  DISABILITATO
        // if ( $email == TEST_EMAIL ) {
        //     $genratedOtp = "TEST_OTP";
        //     $otpCreated = "TEST_OTP";
        // }
        
        if($registerOtp === $genratedOtp){
            $otpMatched = true;
            if(isset($_POST[ 'country_code' ])) {
                update_user_meta( $current_user_id, 'country_code', $_POST[ 'country_code' ] );
            }
            if(isset($_POST[ 'billing_phone' ])) {
                $phone_updated = update_user_meta( $current_user_id, 'billing_phone', $_POST[ 'billing_phone' ] );
            }
        }else{
            $err = __('OTP is not valid!!','stc-tickets');
        }
    }
    if( ! empty( $get_billing_phone ) && ! empty( $email ) ) {
        $response[ 'message' ]          = array ( $billing_phone, $otpMatched, $curl_response, $country_code );
        $response[ 'otpMatched' ]       = $otpMatched;
        $response[ 'otpCreated' ]       = $otpCreated;
        $response[ 'phone_updated' ]    = $phone_updated;
        $response[ 'generate_otp_now' ] = $generate_otp_now;
        $response[ 'status' ]           = true;
        $response[ 'error' ]            = $err;

        // if ($email === TEST_EMAIL) {
        //     $response[ 'email' ]        = $email;
        //     $response[ 'registerOtp' ]  = $registerOtp;
        //     $response[ 'genratedOtp' ]  = $genratedOtp;
        // }
        
    }
    echo json_encode ( $response );
    wp_die ();
}

/*
 * ajax for checkout
 */
add_action( 'wp_ajax_UpdateUserProfile', "fun_UpdateUserProfile" );
add_action( 'wp_ajax_nopriv_UpdateUserProfile', "fun_UpdateUserProfile" );
function fun_UpdateUserProfile() {
    $response            = array (
        'message' => '',
        'status'  => false,
    );
    $user_id             = get_current_user_id();
    $get_billing_phone   = $_POST[ 'billing_phone' ];
    $first_name   = $_POST[ 'first_name' ];
    $last_name   = $_POST[ 'last_name' ];
    $place_of_birth      = !empty($_POST[ 'place_of_birth' ]) ? $_POST[ 'place_of_birth' ] : '';
    $dob                 = !empty($_POST[ 'dob' ]) ? $_POST[ 'dob' ] : '';
    $country_code        = ltrim( $_POST[ 'country_code' ], "+" );
    // add country code in phone(currently restricted to italy(+39) only)
    $billing_phone       = (int) $country_code . $get_billing_phone;
    $email               = $_POST[ 'email' ];
    $update_res = update_user_meta( $user_id, 'country_code', $country_code );
    $update_res = update_user_meta( $user_id, 'billing_phone', $get_billing_phone );
    $update_res = update_user_meta($user_id,'dob',$dob);
    $update_res = update_user_meta($user_id,'place_of_birth',$place_of_birth);
    $update_res = update_user_meta($user_id,'first_name',$first_name);
    $update_res = update_user_meta($user_id,'last_name',$last_name);
    $update_res = update_user_meta($user_id,'user_ip',$_SERVER['REMOTE_ADDR']);
    if( ! empty( $get_billing_phone ) && ! empty( $email ) ) {
        $response[ 'message' ]          = array ( $billing_phone, $email );
        $response[ 'status' ]           = true;
    }
    echo json_encode( $response );
    wp_die();
}
/*
 * get customers ajax function
 */
add_action ( 'wp_ajax_nopriv_getCustomers', 'get_customers_list_fun' );
add_action ( 'wp_ajax_getCustomers', 'get_customers_list_fun' );

function get_customers_list_fun() {
    $response = array (
        'message' => '',
        'statue'  => false,
    );
    $gatStartDate = isset($_POST['startDate']) ? $_POST['startDate'] : '' ;
    $gatStartDate = str_replace('/', '-', $gatStartDate);
    $getEndDate = isset($_POST['endDate']) ? $_POST['endDate'] : '' ;
    $getEndDate = str_replace('/', '-', $getEndDate);
    $tempStartDate = date("YmdHis", strtotime($gatStartDate));
    $tempEndDate = date("YmdHis", strtotime($getEndDate));
    $privacy_cookie = tempnam ("/tmp", "CURLCOOKIE");
    $privacy_curl = curl_init();

    curl_setopt_array($privacy_curl, array(
      CURLOPT_URL => API_HOST.'backend/backend.php?id=' . APIKEY . '&cmd=tliteQueryData&qtype=GetPrivacyClause',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_COOKIEJAR => $privacy_cookie,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $privacy_response = curl_exec($privacy_curl);

    curl_close($privacy_curl);
    $privacy_xml           = simplexml_load_string ( $privacy_response );
    $privacy_json          = json_encode ( $privacy_xml );
    $privacyArray          = json_decode ( $privacy_json, TRUE );
    $privacyData = array();
    foreach($privacyArray['rows']['row'] as $privacyData_key => $privacyData_val){
        $privacyData[$privacyData_val['@attributes']['prvclid']] = array(
            'body' => $privacyData_val['@attributes']['prvclbody'],
            'description' => $privacyData_val['@attributes']['prvcldescription']
        );
    }
    $get_cus_cookie = tempnam ("/tmp", "CURLCOOKIE");
    $get_customer_curl = curl_init ();

    curl_setopt_array ( $get_customer_curl, array (
        CURLOPT_URL            => API_HOST.'backend/backend.php?id=' . APIKEY . '&cmd=tliteQueryData&qtype=GetCustomer&sdate=' . $tempStartDate . '&edate=' . $tempEndDate,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_COOKIEJAR => $get_cus_cookie,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'GET',
    ) );

    $get_customer_curl_response = curl_exec ( $get_customer_curl );

    curl_close ( $get_customer_curl );
 //    $customersResponse = json_decode ( $curl_response );
    $response_xml           = simplexml_load_string ( $get_customer_curl_response );
    $response_json          = json_encode ( $response_xml );
    $customersData          = json_decode ( $response_json, TRUE );

    if( $get_customer_curl_response ) {
        $response[ 'message' ] = array ( 'customersData' => $customersData, 'privacyData' => $privacyData);
        $response[ 'statue' ]  = true;
    }
    echo json_encode ( $response );
    wp_die ();
}
/*
 * import customers ajax function
 */
add_action ( 'wp_ajax_nopriv_importCustomers', 'import_customers_list_fun' );
add_action ( 'wp_ajax_importCustomers', 'import_customers_list_fun' );

function import_customers_list_fun() {
    $response = array (
        'message' => '',
        'statue'  => false,
    );
    $gatStartDate = isset($_POST['startDate']) ? $_POST['startDate'] : '' ;
    $gatStartDate = str_replace('/', '-', $gatStartDate);
    $getEndDate = isset($_POST['endDate']) ? $_POST['endDate'] : '' ;
    $getEndDate = str_replace('/', '-', $getEndDate);
    $tempStartDate = date("YmdHis", strtotime($gatStartDate));
    $tempEndDate = date("YmdHis", strtotime($getEndDate));
    $privacy_cookie = tempnam ("/tmp", "CURLCOOKIE");
    $privacy_curl = curl_init();

    curl_setopt_array($privacy_curl, array(
      CURLOPT_URL => API_HOST.'backend/backend.php?id=' . APIKEY . '&cmd=tliteQueryData&qtype=GetPrivacyClause',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_COOKIEJAR => $privacy_cookie,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $privacy_response = curl_exec($privacy_curl);

    curl_close($privacy_curl);
    $privacy_xml           = simplexml_load_string ( $privacy_response );
    $privacy_json          = json_encode ( $privacy_xml );
    $privacyArray          = json_decode ( $privacy_json, TRUE );
    $privacyData = array();
    foreach($privacyArray['rows']['row'] as $privacyData_key => $privacyData_val){
        $privacyData[$privacyData_val['@attributes']['prvclid']] = array(
            'body' => $privacyData_val['@attributes']['prvclbody'],
            'description' => $privacyData_val['@attributes']['prvcldescription']
        );
    }
    $get_customer_cookie = tempnam ("/tmp", "CURLCOOKIE");
    $get_customer_curl = curl_init ();

    curl_setopt_array ( $get_customer_curl, array (
        CURLOPT_URL            => API_HOST.'backend/backend.php?id=' . APIKEY . '&cmd=tliteQueryData&qtype=GetCustomer&sdate=' . $tempStartDate . '&edate=' . $tempEndDate,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_COOKIEJAR => $get_customer_cookie,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'GET',
    ) );

    $get_customer_curl_response = curl_exec ( $get_customer_curl );

    curl_close ( $get_customer_curl );
    if(curl_error( $get_customer_curl )){
        $err                   = curl_error ( $get_customer_curl );
        $response[ 'message' ] = $err;
    }
 //    $customersResponse = json_decode ( $curl_response );
    $response_xml           = isset($get_customer_curl_response) ? simplexml_load_string ( $get_customer_curl_response ) : '';
    $response_json          = json_encode ( $response_xml );
    $customersData          = json_decode ( $response_json, TRUE );

    /*
    * user integration script from API to wordpress user.
    */

    if(!empty($customersData)) {
    //        echo "<pre>";
    //        print_r($customersData['rows']['row']);
    //        echo "</pre>";

        $customers = $customersData['rows']['row'];
        $user_ids = array();
        if(!empty($customers)){
            foreach($customers as $customers_key => $customers_value){
                $cusemail = $customers_value['@attributes']['cusemail'];
                $cuslname = $customers_value['@attributes']['cuslname'];
                $cusfname = $customers_value['@attributes']['cusfname'];
                $cusemail = strtolower($cusemail);
                if( ! empty ( $cusemail ) ) {
                    $customer = get_user_by ( 'email', $cusemail );
                    if( !empty($customer)) {
                        $user_ids[] = $customer->ID;
    //   echo "<pre>";
    //   print_r($customer);
    //   echo "</pre>";
                    } else {
                        $password = wp_generate_password();
                        $user_id = wp_create_user( $cusfname, $password, $cusemail );
                        if ( ! is_wp_error( $user_id ) ) {
                            // User created successfully
                            $user_ids[] = $user_id;
                            update_user_meta( $user_id, 'first_name', $customers_value['@attributes']['cusfname'] );
                            update_user_meta( $user_id, 'last_name', $customers_value['@attributes']['cuslname'] );
                            update_user_meta( $user_id, 'user_address', $customers_value['@attributes']['cusadd1'] );
                            update_user_meta( $user_id, 'billing_phone', isset($customers_value['@attributes']['cusmobile']) ? $customers_value['@attributes']['cusmobile'] : $customers_value['@attributes']['custel'] );
    //     echo "<pre>";
    //     print_r($customers_value);
    //     echo "</pre>";
                        } else {
                            // Error creating user
                            $error_msg = $user_id->get_error_message();
                        }
                    }
                }
            }
        }
        if(!empty($user_ids)){
        //  echo "<pre>";
        //  print_r($user_ids);
        //  echo "</pre>";
            $response[ 'user_ids' ]  = $user_ids;
            $response[ 'statue' ]  = true;
        }
    }

    if( $get_customer_curl_response ) {
        $response[ 'message' ] = array ( 'customersData' => $customersData, 'privacyData' => $privacyData);
        $response[ 'statue' ]  = true;
    }
    echo json_encode ( $response );
    wp_die ();
}

/*
 * print order ajax function
 * MOD SARAH
 * take trace of the number of prints and results of the API
 */
add_action ( 'wp_ajax_nopriv_printOrder', 'print_order_fun' );
add_action ( 'wp_ajax_printOrder', 'print_order_fun' );

function print_order_fun() {

    // Get the current user data
    $current_user = wp_get_current_user();
    $user_email = $current_user->user_email;
    $birthdate = get_user_meta( $current_user->ID, 'dob', true );

    // Get or create the meta for number of prints
    $print_count = get_user_meta( $current_user->ID, 'print_count', true );
    if ( empty( $print_count ) ) {
        $print_count = 0;
    }
    $print_count++;
    update_user_meta( $current_user->ID, 'print_count', $print_count );

    $trcode = $_POST['transactionCode'];

    $pdfApiUrl = API_HOST.'backend/backend.php?id=' . APIKEY . '&cmd=printAtHome&trcode='.$trcode.'&xml=0';

    $response = array (
        'message' => '',
        'status'  => false,
        'user_email' => $user_email,
        'pdf_url' => $pdfApiUrl,
    );

    /** MOD SARAH **/
    $api_response = remote_file_get_contents($pdfApiUrl);

    //Check if apiData starts with $amp;lt; which is a special character
    // $clean_apiData = $apiData.indexOf('&amp;lt;') == -1 ? '&amp;lt;'.$apiData : $apiData;
    // <!-- simplexml_load_string(): Entity: line 1: parser error : Start tag expected, '&amp;lt;' not found (500 Internal Server Error) -->
    // Clean the API response to remove any unwanted characters
    if (strpos($api_response, '&amp;lt;') !== false) {
        $api_response = str_replace('&amp;lt;', '<', $api_response);
    }
    if (strpos($api_response, '&amp;gt;') !== false) {
        $api_response = str_replace('&amp;gt;', '>', $api_response);
    }
    // Decode the XML response
    $apiData = simplexml_load_string($api_response);

    $errmsg = '';

    // Check if ApiData has error messages
    $apiData = json_decode(json_encode($apiData), true);
    if(isset($apiData['@attributes']) && !empty($apiData['@attributes'])){
        if(isset($apiData['@attributes']['errstring']) && !empty($apiData['@attributes']['errstring'])){
            // Decode special characters
            $errmsg = html_entity_decode($apiData['@attributes']['errstring']);
        }
    }

    $response[ 'errmsg' ] = $errmsg;
    // $response[ 'ApiResponse' ] = $api_response;
    // $response[ 'ApiData' ] = $apiData;

    // Check if the response is successful and the content type is PDF
    if ($api_response !== false && $errmsg == '') {
        $fileName = dirname(__dir__) .'/pdf/order_'.$trcode.'.pdf';

        // Set headers for PDF download
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');

    //    $pdfDecoded = base64_decode($response);
        file_put_contents($fileName, $api_response);
    // Output the PDF content directly to the user
    //    echo $response;
    //    readfile($fileName);
        $response[ 'message' ] =  get_site_url().'/wp-content/plugins/stc-tickets/pdf/order_'.$trcode.'.pdf';
        $response[ 'status' ]  = true;

        // Check order status and update the order status
        $orderID = $_SERVER['HTTP_REFERER'];
        $orderID = explode('view-order/', $orderID);
        $orderID = $orderID[1];
        $order = wc_get_order( $orderID );
        if ( !empty($order) ) {
            $order->update_status( 'completed' );
        }
    } else if($errmsg != '') {
        $response[ 'message' ] = $errmsg;
        $response[ 'status' ]  = false;
    } else {
        $response[ 'message' ] = __('Failed to get the PDF from the API.','stc-tickets');
        $response[ 'status' ]  = false;
    }

    // Create a log file with the number of prints
    // Creo un file di log con il numero di stampe
    $logFile = dirname(__dir__) .'/print_logs/print_log_'.date('Y-m').'.txt';
    // If the file exist but exceeds 100mb, create another file
    // Se il file esiste ma supera i 100mb, crea un altro file
    if (file_exists($logFile) && filesize($logFile) > 100000000) {
        $logFile = dirname(__dir__) .'/print_logs/print_log_'.date('Y-m-d').'.txt';
    } else if (!file_exists($logFile)) {
        $log = fopen($logFile, 'w');
        fclose($log);
    }
    // Write the number of prints to the log file
    // Scrivi il numero di stampe nel file di log
    $log = fopen($logFile, 'a');
    fwrite($log, '['.date('d/m/Y H:i:s').'] User: '.$user_email.' - Birthdate: '.$birthdate.' - Prints: '.$print_count.' - Result: '.$response['message'].PHP_EOL);
    fclose($log);

    echo json_encode ( $response );

    wp_die ();
}

/*
 * get barcodes ajax function
 */
add_action ( 'wp_ajax_nopriv_getbarcodes', 'get_barcodes_fun' );
add_action ( 'wp_ajax_getbarcodes', 'get_barcodes_fun' );

function get_barcodes_fun() {
    $response = array (
        'message' => '',
        'status'  => false,
    );

    $barcodes = array();
    $cartObject = WC()->cart->get_cart();

    if(!empty($cartObject)){
        foreach ( $cartObject as $cart_item_key => $cart_item ) {
            $transaction_ids = $cart_item['transaction_ids'];
            if(!empty($transaction_ids)){
                foreach($transaction_ids as $transaction_key => $transaction_value){
                    foreach($transaction_value as $transaction_k => $transaction_v){
                        if(isset($transaction_v['seatObject']) && $transaction_v['subscription'] == '1'){
                            if( array_key_first( $transaction_v['seatObject'] ) == '0' ) {
                                foreach($transaction_v['seatObject'] as $seatObject_k => $seatObject_v){
                                    $barcodes[] = $seatObject_v['barcode'];
                                }
                            } else {
                                $barcodes[] = $transaction_v['seatObject']['barcode'];
                            }
                        }
                    }
                }
            }else{
                $response[ 'message' ] = __("cart is empty",'stc-tickets');
            }
        }
    }else{
        $response[ 'message' ] = __("cart is empty",'stc-tickets');
    }

    if(!empty($cartObject) && !empty($barcodes)){
        $response[ 'message' ] = $barcodes;
        $response[ 'status' ]  = true;
    }

    echo json_encode ( $response );
    wp_die ();
}

/*
 * delete cart tickets ajax function
 */
add_action ( 'wp_ajax_nopriv_deleteTickets', 'delete_cart_tickets_fun' );
add_action ( 'wp_ajax_deleteTickets', 'delete_cart_tickets_fun' );

function delete_cart_tickets_fun() {
    $response = array (
        'message' => '',
        'status'  => false,
    );
    global $woocommerce;
    $cart       = WC()->cart->cart_contents;
    $totalPrice = 0;
    $totalQty   = 0;
    $delete_transaction_ids = !empty($_POST['delete_transaction_ids']) ? $_POST['delete_transaction_ids'] : array() ;
    $ticket_title = !empty($_POST['ticket_title']) ? $_POST['ticket_title'] : "" ;
    $user_id = get_current_user_id();
    $transactionIds = get_user_meta( $user_id, 'transactionIds', true );
    $addToCartObject = get_user_meta( $user_id, 'addToCartObject', true );
    $transactions_str = "";
    if(!empty($delete_transaction_ids)){
        foreach ( $delete_transaction_ids as $transactionIds_key => $transactionIds_value ) {
            $transactions_str .= "transactionCode[]=" . $transactionIds_value . "&";
        }
        $transactions_str = rtrim( $transactions_str, "&" );
        $curl_url         = API_HOST . 'backend/backend.php?id=' . APIKEY . '&cmd=setExpiry&' . $transactions_str . '&timeout=-1000&preserveOnError=1';
        $exp_cookie = tempnam ("/tmp", "CURLCOOKIE");

        $set_expiry_curl = curl_init();

        curl_setopt_array( $set_expiry_curl, array (
            CURLOPT_URL            => $curl_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEJAR => $exp_cookie,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
        ) );

        $set_expiry_response = curl_exec( $set_expiry_curl );
        $err                 = curl_error( $set_expiry_curl );
        curl_close( $set_expiry_curl );

        $xml                 = simplexml_load_string( $set_expiry_response );
        $json                = json_encode( $xml );
        $set_expiry_response = json_decode( $json, TRUE );

        $final_transaction_ids = array_filter( $transactionIds, function ($transaction) use ($delete_transaction_ids) {
                                    if( is_array( $transaction ) && array_key_first( $transaction ) == 'subscription_seat' ) {
                                        foreach ( $transaction[ 'subscription_seat' ] as $subscription_seat_key => $subscription_seat_value ) {
                                            if( in_array($subscription_seat_value[ 'transaction_id' ],$delete_transaction_ids)) {
                                                unset($transaction[ 'subscription_seat' ][$subscription_seat_key]);
                                            }
                                        }
                                        if(!empty($transaction[ 'subscription_seat' ])){
                                            return($transaction);
                                        }
                                    }else{
                                        if( ! in_array($transaction[ 'transaction_id' ],$delete_transaction_ids)) {
                                            return($transaction);
                                        }
                                    }
                                } );

        if( ! empty( $cart ) ) {
            foreach ( $cart as $cart_item_key => $cart_item ) {
//                $final_addToCartObject = $cart_item[ 'selected_seat_price' ][ 0 ];
//                $transaction_ids     = $cart_item[ 'transaction_ids' ][ 0 ];
                unset($addToCartObject[$ticket_title]);
                $final_addToCartObject = $addToCartObject;
                if( ! empty( $final_addToCartObject ) ) {
                    foreach ( $final_addToCartObject as $meta_key => $meta_value ) {
                        if( ! empty( $meta_value ) ) {
                            foreach ( $meta_value as $meta_k => $meta_v ) {
                                $reductions = $meta_v[ 'reductions' ];
                                if( ! empty( $reductions ) ) {
                                    foreach ( $reductions as $reductions_key => $reductions_value ) {
                                        $reductionQuantity = $reductions_value[ 'reductionQuantity' ];
                                        $reductionPrice    = $reductions_value[ 'reductionPrice' ];
                                        $totalPrice        = $totalPrice + ((int) $reductionPrice * (int) $reductionQuantity);
                                        $totalQty          = $totalQty + (int) $reductionQuantity;
                                    }
                                }
                            }
                        }
                    }
                }
                if(!empty($final_transaction_ids)){
                    $cart_item[ 'data' ]->set_price( $totalPrice );
                    $cart_item[ 'selected_seat_price' ][ 0 ]    = $final_addToCartObject;
                    $cart_item[ 'transaction_ids' ][ 0 ]    = $final_transaction_ids;
                    WC()->cart->cart_contents[ $cart_item_key ] = $cart_item;
                    WC()->cart->calculate_totals();
                    update_user_meta( $user_id, 'addToCartObject', $final_addToCartObject );
                    update_user_meta( $user_id, 'transactionIds', $final_transaction_ids );
                }else{
                    WC()->cart->empty_cart();
                    update_user_meta( $user_id, 'addToCartObject', array () );
                    update_user_meta( $user_id, 'transactionIds', array () );
                    update_user_meta( $user_id, 'subscriptionSeatList', array () );
                    update_user_meta( $user_id, 'subscriptionOrderId', array () );
                }
            }
        }
    }else{
        $response[ 'message' ] = __("cart is empty",'stc-tickets');
    }

    WC()->cart->set_session();

    if(!empty($delete_transaction_ids)){
        $response[ 'message' ] = $delete_transaction_ids;
        $response[ 'set_expiry' ] = $set_expiry_response;
        $response[ 'status' ]  = true;
    }

    echo json_encode ( $response );
    wp_die ();
}

/*
 * check subscription barcode ajax function
 */
add_action ( 'wp_ajax_nopriv_subscriptionCheck', 'subscription_check_fun' );
add_action ( 'wp_ajax_subscriptionCheck', 'subscription_check_fun' );

function subscription_check_fun() {
    $response = array (
        'message' => '',
        'status'  => false,
    );
    $barcode = !empty($_POST['barcode']) ? $_POST['barcode'] : "" ;

    if(!empty($barcode)){
        $sub_check_cookie = tempnam ("/tmp", "CURLCOOKIE");

        $curl = curl_init();

        curl_setopt_array( $curl, array (
            CURLOPT_URL            => API_HOST . 'backend/backend.php?id=SANCARLO&cmd=xmlSubscriptionData&barcode=' . $barcode,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEJAR      => $sub_check_cookie,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
        ) );

        $subscription_response = curl_exec( $curl );
        if( curl_errno( $curl ) ) {
            $error_msg = curl_error( $curl );
        }
        curl_close( $curl );
        $xml              = simplexml_load_string( $subscription_response, 'SimpleXMLElement', LIBXML_NOCDATA );
        $subscriptionJson = json_encode( $xml );
        $subscriptionArr  = json_decode( $subscriptionJson, TRUE );

    }else{
        $response[ 'message' ] = __("Barcode is empty",'stc-tickets');
    }

    if(isset($subscriptionArr['@attributes']['errcode']) && isset($subscriptionArr['@attributes']['errstring'])){
        $response[ 'message' ] = $subscriptionArr['@attributes']['errstring'];
    }
    if(!empty($error_msg)){
        $response[ 'message' ] = $error_msg;
    }

    if(!empty($subscriptionArr) && empty($response[ 'message' ]) ){
        $response[ 'message' ] = $subscriptionArr;
        $response[ 'status' ]  = true;
    }

    echo json_encode ( $response );
    wp_die ();
}

/*
 * check recaptcha ajax function
 */
add_action ( 'wp_ajax_nopriv_checkRecaptcha', 'checkRecaptcha_fun' );
add_action ( 'wp_ajax_checkRecaptcha', 'checkRecaptcha_fun' );

function checkRecaptcha_fun() {
    $response = array (
        'message' => '',
        'status'  => false,
    );
    $recaptcha = !empty($_POST['recaptcha']) ? $_POST['recaptcha'] : "" ;

    // reCAPTCHA validation
    if(!empty($recaptcha)) {

        // Google secret API
        // $secretAPIkey = CAPTCHA_SECRET_KEY;

        // reCAPTCHA response verification
        // $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secretAPIkey.'&response='.$recaptcha);
        $turnstile_response = sanitize_text_field($recaptcha);
        $response = wp_remote_post('https://challenges.cloudflare.com/turnstile/v0/siteverify', array(
            'body' => array(
                'secret' => TS_CAPTCHA_DEV_SECRET_KEY,
                'response' => $turnstile_response,
                'remoteip' => $_SERVER['REMOTE_ADDR'],
            ),
        ));
        // Check reCAPTCHA response
        $response = json_decode($response['body'], true);
        
        if (isset($response['success']) && $response['success'] === false) {
            $response[ 'message' ] = __("Robot verification failed, please try again.",'stc-tickets');

        } elseif (!isset($response['success']) || $response['success'] !== true) {
            $response[ 'message' ] = __("Robot verification failed, please try again.",'stc-tickets');
        } else {
            // reCAPTCHA verification passed
            $response[ 'message' ] = __("reCAPTCHA is verified.",'stc-tickets');
            $response[ 'status' ] = true;
        }

        // Decode JSON data
        // $res = json_decode($verifyResponse);
        //     if($res->success){

        //         $response[ 'message' ] = __("reCAPTCHA is verified.",'stc-tickets');
        //         $response[ 'status' ] = true;
        //     } else {
        //         $response[ 'message' ] = __("Robot verification failed, please try again.",'stc-tickets');
        //     }
    } else{
        $response[ 'message' ] = __("Please check on the reCAPTCHA box.",'stc-tickets');
    }

    echo json_encode ( $response );
    wp_die ();
}

/**
 * Create a preorder on payment button click
 * MOD SARAH
 * 
 * @return void
 */
function createPreorder_fun() {
    $preorder_nonce = $_POST['preorder_nonce'];

    if ( ! wp_verify_nonce( $preorder_nonce, 'preorder_nonce' ) ) {
        wp_send_json_error( 'Invalid nonce' );
    }

    $order_id                   = '';
    $user_id                    = get_current_user_id();

    if( $user_id == 'undefined' || $user_id == 0 ) {
        $response = array (
            'message' => 'User not logged in',
            'status'  => false,
        );
        echo json_encode( $response );
        wp_die();
    }

    $transactionCode            = isset( $_POST[ 'transactionCode' ] ) ? $_POST[ 'transactionCode' ] : '';
    $addToCartObject            = get_user_meta( $user_id, 'addToCartObject', true );
    $transactionIds             = get_user_meta( $user_id, 'transactionIds', true );
    $transactionCodeArr         = explode( ",", $transactionCode );
    $confirmed_order            = array ();
    $confirmed_order_arr        = array ();
    $confirmed_order_new        = array ();
    $final_confirmed_order_arr  = array ();
    $subscriptionOrderId        = get_user_meta ( $user_id, 'subscriptionOrderId',true );

    $get_confirmed_preorder_arr    = array ();

    $current_user      = wp_get_current_user();
    $user_firstname    = $current_user->user_firstname;
    $user_lastname     = $current_user->user_lastname;
    $user_email        = $current_user->user_email;
    $billing_address_1 = get_user_meta( $current_user->ID, 'billing_address_1', true );
    $billing_address_2 = get_user_meta( $current_user->ID, 'billing_address_2', true );
    $billing_city      = get_user_meta( $current_user->ID, 'billing_city', true );
    $billing_postcode  = get_user_meta( $current_user->ID, 'billing_postcode', true );
    $billing_country   = get_user_meta( $current_user->ID, 'billing_country', true );
    $billing_state     = get_user_meta( $current_user->ID, 'billing_state', true );
    $billing_phone     = get_user_meta( $current_user->ID, 'billing_phone', true );

    global $woocommerce;
    $address = array (
        'first_name' => $user_firstname,
        'last_name'  => $user_lastname,
        'email'      => $user_email,
        'phone'      => $billing_phone,
        'address_1'  => $billing_address_1,
        'address_2'  => $billing_address_2,
        'city'       => $billing_city,
        'state'      => $billing_state,
        'postcode'   => $billing_postcode,
        'country'    => $billing_country
    );
    $get_current_user = wp_get_current_user();
    $current_user_email = $get_current_user->user_email;
    $current_user_id = $get_current_user->ID;
    $cart = WC()->cart;

    if( ! empty( $cart ) && ! empty( $addToCartObject ) ) {
        $get_confirmed_order_arr = array ();
        $get_final_confirmed_order_arr = array ();
        $finalTransactionIds = array ();

        if (!$subscriptionOrderId) {
            // Crate a new order
            $checkout = WC()->checkout();
            $order_id = $checkout->create_order( array('customer_id'=>$current_user_id,'billing_email'=>$current_user_email,'payment_method' => 'online') );
            $order = wc_get_order( $order_id );
            if ( ! $order ) return false;

            $order = wc_get_order( $order->get_id() );
            $order->set_customer_id( $current_user_id );
            $order->set_billing_email( $current_user_email );
            $order->set_payment_method( 'online' );
            $order_id = $order->get_id();

            update_post_meta( $order_id, '_customer_user', get_current_user_id() );
            $order->set_address( $address, 'billing' );
            $order->calculate_totals();

            if( ! empty( $transactionCodeArr ) || ! empty( $confirmedOrderObjectBefore ) ) {
                if( ! empty( $transactionIds ) ) {
                    $confirmed_order = $transactionIds;
                    foreach ( $confirmed_order as $confirmed_order_key => $confirmed_order_value ) {
                        if( is_array( $confirmed_order_value ) && array_key_first( $confirmed_order_value ) == 'subscription_seat' ) {
                            foreach($confirmed_order_value['subscription_seat'] as $subscription_seat_k => $subscription_seat_v){
                                $zone_arr                             = array ();
                                $ticketName                           = $subscription_seat_v[ 'ticketName' ];
                                $showDate                             = $subscription_seat_v[ 'showDate' ];
                                $zoneName                             = $subscription_seat_v[ 'zoneName' ];
                                $zoneId                               = $subscription_seat_v[ 'zoneId' ];
                                $seats                                = $subscription_seat_v[ 'seats' ];
                                $zone_arr[ 'zoneName' ]               = $zoneName;
                                $zone_arr[ 'zoneId' ]                 = $zoneId;
                                $zone_arr[ 'seats' ]                  = $seats;
                                $confirmed_order_arr[ $ticketName ][] = $zone_arr;
                            }
                        }else{
                            $zone_arr                             = array ();
                            $ticketName                           = $confirmed_order_value[ 'ticketName' ];
                            $showDate                             = $confirmed_order_value[ 'showDate' ];
                            $zoneName                             = $confirmed_order_value[ 'zoneName' ];
                            $zoneId                               = $confirmed_order_value[ 'zoneId' ];
                            $seats                                = $confirmed_order_value[ 'seats' ];
                            $zone_arr[ 'zoneName' ]               = $zoneName;
                            $zone_arr[ 'zoneId' ]                 = $zoneId;
                            $zone_arr[ 'seats' ]                  = $seats;
                            $confirmed_order_arr[ $ticketName ][] = $zone_arr;
                        }
                    }
                }
                if( ! empty( $confirmedOrderObjectBefore ) ) {
                    $final_confirmed_order_arr = array_merge_recursive( $confirmedOrderObjectBefore, $confirmed_order_arr );
                }
                $get_confirmed_order_arr[$order_id]       = get_confirmed_order_arr_fun( $confirmed_order_arr );
                $get_final_confirmed_order_arr[$order_id] = get_confirmed_order_arr_fun( $final_confirmed_order_arr );
            }
            if( ! empty( $beforeTransactionIds ) ) {
                $get_filtered_transaction = array_filter( $beforeTransactionIds, function ($var) use ($transactionCodeArr) {
                    if( in_array( $var, $transactionCodeArr ) ) {
                        return($var);
                    }
                } );
            } else {
                $get_filtered_transaction = '';
            }

            if( ! empty( $get_filtered_transaction ) ) {
                update_user_meta( $user_id, 'finalConfirmedOrder', $get_final_confirmed_order_arr );
                $finalTransactionIds = array_merge( $beforeTransactionIds, $transactionCodeArr );
                update_user_meta( $user_id, 'finalTransactionIds', $finalTransactionIds );
            } else {
                update_user_meta( $user_id, 'finalConfirmedOrder', $get_confirmed_order_arr );
                update_user_meta( $user_id, 'finalTransactionIds', $transactionCodeArr );
            }

            update_user_meta( $user_id, 'preOrder', $get_confirmed_order_arr );
            $order->add_meta_data( 'preOrderObject', $get_confirmed_order_arr, true );
            $order->add_meta_data( 'transactionIds', $transactionIds, true );
            $order->add_meta_data( 'orderTransactionCodeArr', $transactionCodeArr, true );
            $order->save();
            $order_id = $order->get_id();

            $current_order = $order;

            // Get data from the current order
            $transactionIds = $current_order->get_meta( 'transactionIds', true, 'view' ) !== null ? $current_order->get_meta( 'transactionIds', true, 'view' ) : array();
            // Merge order data for log
            $order_array = array();
            $order_array[$order_id]['transactionIds'] = $transactionIds;
            $order_array[$order_id]['orderdata'] = $current_order;

            $jsonOrderArray = json_encode($order_array);

            $log_message = "current pre order : " . $current_order;
            error_log( $log_message, 3, WP_CONTENT_DIR . '/pre_order_detail.log' );
        }

    }
    $preOrderObject = get_user_meta( $user_id, 'preOrder', true );

    // Send json ajax response
    $response = array (
        'order_id' => $order_id,
        'status'  => true,
    );

    wp_send_json_success( $response );
}
add_action( 'wp_ajax_createPreorder', 'createPreorder_fun' );
add_action( 'wp_ajax_nopriv_createPreorder', 'createPreorder_fun' );
