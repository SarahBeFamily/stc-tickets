<?php
/*
 * developer test
 */
add_shortcode ( 'developer_test', 'stcTickets_developer_test_callback' );

function stcTickets_developer_test_callback() {
    ob_start ();
//    echo "<pre>";
//    print_r(get_user_meta( get_current_user_id()));
//    echo "</pre>";
//    $cart       = WC()->cart->cart_contents;
//    echo "<pre>";
//    print_r($cart);
//    echo "</pre>";
    
//    foreach ( $cart as $cart_item_key => $cart_item ) {
//        $selected_seat_price = $cart_item[ 'selected_seat_price' ][ 0 ];
//    }
    
//    $order_array = array(11142);
//    if(!empty($order_array)){
//        foreach($order_array as $order_key => $order_value){
//            $current_order_id = $order_value;
//            $current_order = wc_get_order($current_order_id);
//            if(!empty($current_order)){
//
//            $transactionIds = $current_order->get_meta( 'transactionIds' );
//
//            if(!empty($transactionIds)){
//                foreach($transactionIds as $transactionIds_key => $transactionIds_value){
//                    $seatObject = $transactionIds_value['seatObject'];
//                    if(is_array($seatObject) && !empty($seatObject) && array_key_first( $seatObject ) == 0){
//                        foreach($seatObject as $trans_seat_key => $trans_seat_value){
//                            $current_seat_price = (int) $trans_seat_value['price'];
//                            $transaction_final_price        = $transaction_final_price + ((int) $current_seat_price / 100);
//                        }
//                    }else{
//                        $seat_price = (int) $seatObject['price'];
//                        $transaction_final_price        = $transaction_final_price + ((int) $seat_price / 100);
//                    }
//                }
//            }
//
//            // Add product
//            $product_id          = ! empty( get_option( 'TICKET_WOOCOMMERCE_PRODUCT_ID' ) ) ? get_option( 'TICKET_WOOCOMMERCE_PRODUCT_ID' ) : get_option( 'wc_ticket_product' ); // product ID to add to cart
//
//            $items = $current_order->get_items();
//            if(!empty($items)){
//                foreach ( $items as $item ) {
//                    $deletable_product = wc_get_product( $item['product_id'] );
//
//                    if ( $deletable_product->get_id() == $product_id ) {
//                        $current_order->remove_item( $item->get_id() );
//                    }
//                }
//            }
//
//            $product = wc_get_product( $product_id ); // Replace with your product ID
//            if ( $product ) {
//                $current_order->add_product( $product, 1, [
//                    'subtotal'     => $transaction_final_price, // e.g. 32.95
//                    'total'        => $transaction_final_price, // e.g. 32.95
//                ] );
//            }        
//
//            // Calculate totals        
//            $current_order->calculate_totals();
//
//            // Save the order
//            $order_id = $current_order->save();
//
//            echo "<pre>";
//            print_r($order_id);
//            echo "</pre>";
//            }else{
//                echo "<pre>";
//                print_r("order not found.");
//                echo "</pre>";
//            }
//        }
//    }
    
/*
 * 
 * start of add order from live to preprod
 */
    
//    $json_data = '{
//    "id": 10641,
//    "parent_id": 0,
//    "status": "completed",
//    "currency": "EUR",
//    "version": "8.8.5",
//    "prices_include_tax": false,
//    "date_created": {
//        "date": "2024-06-29 14:38:11.000000",
//        "timezone_type": 3,
//        "timezone": "Europe/Rome"
//    },
//    "date_modified": {
//        "date": "2024-06-29 14:38:12.000000",
//        "timezone_type": 3,
//        "timezone": "Europe/Rome"
//    },
//    "discount_total": "0",
//    "discount_tax": "0",
//    "shipping_total": "0",
//    "shipping_tax": "0",
//    "cart_tax": "0",
//    "total": "110.00",
//    "total_tax": "0",
//    "customer_id": 38049,
//    "order_key": "wc_order_A78bai5pcELCP",
//    "billing": {
//        "first_name": "Mueller",
//        "last_name": "Christian",
//        "company": "",
//        "address_1": "",
//        "address_2": "",
//        "city": "",
//        "state": "",
//        "postcode": "",
//        "country": "",
//        "email": "drchristianmueller@kabelmail.de",
//        "phone": "1715333533"
//    },
//    "shipping": {
//        "first_name": "",
//        "last_name": "",
//        "company": "",
//        "address_1": "",
//        "address_2": "",
//        "city": "",
//        "state": "",
//        "postcode": "",
//        "country": "",
//        "phone": ""
//    },
//    "payment_method": "online",
//    "payment_method_title": "",
//    "transaction_id": "",
//    "customer_ip_address": "104.28.45.22",
//    "customer_user_agent": "Mozilla/5.0 (iPhone; CPU iPhone OS 17_5_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Mobile/15E148 Safari/604.1",
//    "created_via": "checkout",
//    "customer_note": "",
//    "date_completed": {
//        "date": "2024-06-29 14:38:12.000000",
//        "timezone_type": 3,
//        "timezone": "Europe/Rome"
//    },
//    "date_paid": {
//        "date": "2024-06-29 14:38:11.000000",
//        "timezone_type": 3,
//        "timezone": "Europe/Rome"
//    },
//    "cart_hash": "06156577d5a202a1db4d9d672da0a665",
//    "order_stock_reduced": true,
//    "download_permissions_granted": true,
//    "new_order_email_sent": false,
//    "recorded_sales": true,
//    "recorded_coupon_usage_counts": true,
//    "number": "10641",
//    "meta_data": [
//        {
//            "id": 416801,
//            "key": "articolo_in_evidenza",
//            "value": ""
//        },
//        {
//            "id": 416826,
//            "key": "is_vat_exempt",
//            "value": "no"
//        },
//        {
//            "id": 416827,
//            "key": "_mc4wp_optin",
//            "value": ""
//        },
//        {
//            "id": 416839,
//            "key": "confirmedOrderObject",
//            "value": {
//                "LA TRAVIATA": [
//                    {
//                        "zoneName": "PALCHI CENT. III/IV ORD. PARAPETTO",
//                        "zoneId": "125313",
//                        "seats": [
//                            {
//                                "reductionName": "INTERO",
//                                "reductionId": "30741",
//                                "reductionQuantity": "2",
//                                "reductionPrice": "55"
//                            }
//                        ]
//                    }
//                ]
//            }
//        },
//        {
//            "id": 416840,
//            "key": "transactionIds",
//            "value": {
//                "12531310703910": {
//                    "transaction_qty": 2,
//                    "ticketName": "LA TRAVIATA",
//                    "timestamp": 1719665061,
//                    "transaction_id": "TLITE0120814417957",
//                    "transaction_amount": "7957",
//                    "regData": "1",
//                    "zoneName": "PALCHI CENT. III/IV ORD. PARAPETTO",
//                    "zoneId": "125313",
//                    "pcode": "10703910",
//                    "vcode": "vt0001012",
//                    "seats": [
//                        {
//                            "reductionName": "INTERO",
//                            "reductionId": "30741",
//                            "reductionQuantity": "2",
//                            "reductionPrice": "55"
//                        }
//                    ],
//                    "seatObject": [
//                        {
//                            "@attributes": {
//                                "id": "339846375",
//                                "scode": "17807359"
//                            },
//                            "description": "PALCHI CENT. III/IV ORD. PARAPETTO PALCO Ord. IV nr. 7 Posto 2",
//                            "price": "5500",
//                            "presale": "0",
//                            "commission": "275",
//                            "iva": "60",
//                            "barcode": "1SE0UEUGG2",
//                            "zone": "125313",
//                            "reduction": {
//                                "@attributes": {
//                                    "id": "30741"
//                                },
//                                "description": "INTERO"
//                            }
//                        },
//                        {
//                            "@attributes": {
//                                "id": "339846376",
//                                "scode": "17807358"
//                            },
//                            "description": "POSTI ASCOLTO PALCO Ord. IV nr. 7 Posto 5",
//                            "price": "2000",
//                            "presale": "0",
//                            "commission": "100",
//                            "iva": "22",
//                            "barcode": "1TFLNDU8IB",
//                            "zone": "125317",
//                            "reduction": {
//                                "@attributes": {
//                                    "id": "30741"
//                                },
//                                "description": "INTERO"
//                            }
//                        }
//                    ],
//                    "subscription": "0"
//                }
//            }
//        },
//        {
//            "id": 416841,
//            "key": "orderTransactionCodeArr",
//            "value": [
//                "TLITE0120814417957"
//            ]
//        },
//        {
//            "id": 416842,
//            "key": "booked_subs_seats",
//            "value": []
//        },
//        {
//            "id": 416843,
//            "key": "subscriptionOrderId",
//            "value": ""
//        },
//        {
//            "id": 442807,
//            "key": "wf_order_exported_status",
//            "value": "1"
//        }
//    ],
//    "line_items": {
//        "1574": {
//            "legacy_values": null,
//            "legacy_cart_item_key": null,
//            "legacy_package_key": null
//        }
//    },
//    "tax_lines": [],
//    "shipping_lines": [],
//    "fee_lines": [],
//    "coupon_lines": []
//}';
//
//$order_data = json_decode($json_data, true);
//
////$args = array(
////    'order_id' => $order_data['id'],
////);
//// Create a new order
////$order = wc_create_order($args);
//$order = wc_create_order();
//
//// Set customer and billing information
//$order->set_customer_id( $order_data['customer_id'] );
//$order->set_currency( $order_data['currency'] );
//
//// Set billing address
//$billing_address = array(
//    'first_name' => $order_data['billing']['first_name'],
//    'last_name'  => $order_data['billing']['last_name'],
//    'email'      => $order_data['billing']['email'],
//    'phone'      => $order_data['billing']['phone'],
//    'address_1'  => $order_data['billing']['address_1'],
//    'address_2'  => $order_data['billing']['address_2'],
//    'city'       => $order_data['billing']['city'],
//    'state'      => $order_data['billing']['state'],
//    'postcode'   => $order_data['billing']['postcode'],
//    'country'    => $order_data['billing']['country']
//);
//$order->set_billing_address( $billing_address );
//
//// Set shipping address if available
//if ( ! empty( $order_data['shipping']['first_name'] ) ) {
//    $shipping_address = array(
//        'first_name' => $order_data['shipping']['first_name'],
//        'last_name'  => $order_data['shipping']['last_name'],
//        'address_1'  => $order_data['shipping']['address_1'],
//        'address_2'  => $order_data['shipping']['address_2'],
//        'city'       => $order_data['shipping']['city'],
//        'state'      => $order_data['shipping']['state'],
//        'postcode'   => $order_data['shipping']['postcode'],
//        'country'    => $order_data['shipping']['country']
//    );
//    $order->set_shipping_address( $shipping_address );
//}
//
//// Set other order details
//
//$order->set_payment_method( $order_data['payment_method'] );
//$order->set_payment_method_title( $order_data['payment_method_title'] );
//$order->set_created_via( $order_data['created_via'] );
//
//// Add product
//    $product_id          = ! empty( get_option( 'TICKET_WOOCOMMERCE_PRODUCT_ID' ) ) ? get_option( 'TICKET_WOOCOMMERCE_PRODUCT_ID' ) : get_option( 'wc_ticket_product' ); // product ID to add to cart
//    $product = wc_get_product( $product_id ); // Replace with your product ID
//    if ( $product ) {
//        $order->add_product( $product, 1, [
//            'subtotal'     => $order_data["total"], // e.g. 32.95
//            'total'        => $order_data["total"], // e.g. 32.95
//        ] );
//    }
//
//// Add meta data
//foreach ( $order_data['meta_data'] as $meta ) {
//    $order->update_meta_data( $meta['key'], $meta['value'] );
//}
//
//// Calculate totals
//$order->calculate_totals();
//
//// Save the order
//$order_id = $order->save();
//
//// Print or use the data as needed
//print_r($order_id);
    
    
/*
 * 
 * end of add order from live to preprod
 */

//    $order = wc_get_order( 11138 );
//    echo "<pre>";
//    print_r($order);
//    echo "</pre>";
//    $xml_sub_cookie = tempnam ("/tmp", "CURLCOOKIE");
//        $curl = curl_init();
//
//        curl_setopt_array($curl, array(
//          CURLOPT_URL => 'https://www.teatrosancarlo.it/wp-json/vivaticket/v1/getOrder/?id=11138',
//          CURLOPT_RETURNTRANSFER => true,
//          CURLOPT_ENCODING => '',
//          CURLOPT_MAXREDIRS => 10,
//          CURLOPT_TIMEOUT => 0,
//          CURLOPT_FOLLOWLOCATION => true,
//          CURLOPT_COOKIEJAR => $xml_sub_cookie,
//          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//          CURLOPT_CUSTOMREQUEST => 'GET',
//        ));
//
//        $response = curl_exec($curl);
//
//        curl_close($curl);
//        
//        echo "<pre>";
//        print_r($response);
//        echo "</pre>";

    ?>
        <?php
//    if ( function_exists( 'icl_get_home_url' ) ) {
//        $default_language_url = rtrim( apply_filters( 'wpml_home_url', icl_get_home_url(), ICL_LANGUAGE_CODE ), '/' );
//        $base_directory = dirname( __DIR__ );
//        echo "<pre>";
//        print_r($base_directory);
//        echo "</pre>";
//        $default_language_url_with_base = $base_directory . $default_language_url;
//        echo $default_language_url_with_base;
//    } else {
//        echo 'WPML is not installed.';
//    }

//    die();
//    $user = get_user_by('id', $user_id); // Get the user object by user ID
//    $password = '5435WWWccc1'; // The password entered by the user

//    if (wp_check_password($password, $user->user_pass, $user->ID)) {
//        // Password is correct
//        echo "Password is correct!";
//    } else {
//        // Password is incorrect
//        echo "Password is incorrect!";
//    }

    
//    wp_set_password( "testuser2205@mailinator.com", 36515 );

//    update_user_meta('3', 'billing_phone', '');
//    $users = get_users(array( 'fields' => array( 'ID' ) ));
//    $get_billing_phone = '9876543210';
//    global $wpdb;
//    $custom_field_key = 'billing_phone'; // Replace 'your_custom_field_key' with your actual custom field key
//$get_billing_phone = '9023728513'; // Replace '9875643210' with the specific value you want to check
//$query = $wpdb->prepare("
//        SELECT DISTINCT user_id
//        FROM $wpdb->usermeta
//        WHERE meta_key = %s
//        AND meta_value = %s
//    ", 'billing_phone', $get_billing_phone);
//    $results = $wpdb->get_results($query);
//    echo "<pre>";
//    print_r($results);
//    echo "</pre>";
// SQL query to fetch all values for the custom field where the value matches the specific value
//$query = $wpdb->prepare("
//    SELECT DISTINCT user_id
//    FROM $wpdb->usermeta
//    WHERE meta_key = %s
//    AND meta_value = %s
//", $custom_field_key, $specific_value);
//
//// Execute the query
//$results = $wpdb->get_results($query);
    // Execute the query
    // billing_phone
//    echo "<pre>";
//    print_r($results);
//    echo "</pre>";
//    foreach($users as $users_key => $users_value){
//        $user_id = $users_value->id;
//        $this_billing_phone  = get_user_meta ($user_id, 'billing_phone' );
////        if($get_billing_phone == $this_billing_phone){
////            $billing_phone_exist = true;
////        }else{
////            $billing_phone_exist = false;
////        }
//    }
//    $user_name = "Sergio";
//    $user_email = "sergio.desiato@facebook.com";
//    $customer = get_user_by ( 'email', $user_email );
//    echo "<pre>";
//    print_r($customer);
//    echo "</pre>";
//        if( !empty($customer)) {
////                            $user_ids[] = $customer->ID;
//            echo "<pre>if";
//            print_r($customer);
//            echo "</pre>";
//        } else {
//            $password = wp_generate_password();
//            $user_id = wp_create_user( $user_name, $password, $user_email );
//            echo "<pre>else";
//            print_r($user_email);
//            echo "</pre>";
//            echo "<pre>";
//            print_r($user_id);
//            echo "</pre>";
//            if ( ! is_wp_error( $user_id ) ) {
//                echo "<pre>else if";
//                print_r($user_id);
//                echo "</pre>";
//                // User created successfully
////                                $user_ids[] = $user_id;
////                                update_user_meta( $user_id, 'first_name', $user_name );
////                                update_user_meta( $user_id, 'last_name', $user_surname );
////                                update_user_meta( $user_id, 'dob', $user_dob );
////                                update_user_meta( $user_id, 'user_data', $user_data );
//            } else {
//                // Error creating user
//                $error_msg = $user_id->get_error_message();
//                echo "<pre>else else";
//                print_r($error_msg);
//                echo "</pre>";
//            }
//        }
//    $organization_csv_file = plugin_dir_url( __DIR__ ).'csv/db_unificati_sancarlo.csv';
//    
//    if (($handle = fopen($organization_csv_file, "r")) !== FALSE) {
//      $c = 0;
//        while(($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
//            if($c != 0){
//            
//                $filteredData = array_filter($data, function ($key) {
//                    return $key != '' || $key != null;
//                });
//
//                if(!empty($filteredData)){
//                 // Get the data from CSV columns (adjust the array keys according to your CSV)
//                    echo "<pre>";
//                    print_r($data);
//                    echo "</pre>";
////                    $post_title = $data[0];
////                    $post_category = $data[1];
////                    $address1 = $data[2];
////                    $address2 = $data[3];
////
////                      // Create post array
////                    $post_data = array(
////                      'post_title'   => $post_title,
////                      'post_content' => $notes,
////                      'post_status'  => 'publish',
////                      'post_type'    => 'product-organization', // Change to your custom post type if applicable
////                    );
////
////                    // Insert the post into the database
////                   $post_id = wp_insert_post($post_data);
////
////                    update_field('business_category_section_tag_line',  $president, $post_id); 
//                }
//            }
//          $c++;
//        }
//          
//    fclose($handle);
//  }
//    $user_id = get_current_user_id();
//    
//    $get_user_meta                            = get_user_meta( $user_id, 'addToCartObject' );
//    $transactionIds                           = get_user_meta( $user_id, 'transactionIds' );
//    $subscriptionSeatList                     = get_user_meta( $user_id, 'subscriptionSeatList', true );
//    $subscriptionOrderId                      = get_user_meta( $user_id, 'subscriptionOrderId', true );
//    
//    echo "<pre>";
//    print_r($get_user_meta);
//    echo "</pre>";
//    echo "<pre>";
//    print_r($transactionIds);
//    echo "</pre>";
//    echo "<pre>";
//    print_r($subscriptionSeatList);
//    echo "</pre>";
//    echo "<pre>";
//    print_r($subscriptionOrderId);
//    echo "</pre>";
//    $order = wc_get_order( 7020 );
//    
//    $order->update_meta_data( 'subscriptionOrderId', array() );
//    
//    $order->save();
//    
//    echo "<pre>";
//    print_r($order);
//    echo "</pre>";
//    if($orders){
//        foreach ( $orders as $order ) {
//            $order_id          = $order->get_id();
//            $order_date        = $order->get_date_created()->format( 'M j, Y' );
//            $order_total       = wc_price( $order->get_total() );
//            $transactionIds    = get_post_meta( $order->get_id(), 'transactionIds', true );
//            $subscription_flag = false;
//
////            if( ! empty( $transactionIds ) ) {
////                foreach ( $transactionIds as $transactionIds_key => $transactionIds_value ) {
////                    if( ! $subscription_flag ) {
////                        if( $transactionIds_value[ 'subscription' ] == '1' ) {
////                            $seatObject = $transactionIds_value[ 'seatObject' ];
////                            $barcode = $seatObject[ 'barcode' ];
////
////                            $curl = curl_init();
////
////                            curl_setopt_array( $curl, array (
////                                CURLOPT_URL            => API_HOST . 'backend/backend.php?id=SANCARLO&cmd=xmlSubscriptionData&barcode=' . $barcode,
////                                CURLOPT_RETURNTRANSFER => true,
////                                CURLOPT_ENCODING       => '',
////                                CURLOPT_MAXREDIRS      => 10,
////                                CURLOPT_TIMEOUT        => 0,
////                                CURLOPT_FOLLOWLOCATION => true,
////                                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
////                                CURLOPT_CUSTOMREQUEST  => 'GET',
////                            ) );
////
////                            $response = curl_exec( $curl );
////                            if( curl_errno( $curl ) ) {
////                                $error_msg = curl_error( $curl );
////                            }
////                            curl_close( $curl );
////                            $xml              = simplexml_load_string( $response, 'SimpleXMLElement', LIBXML_NOCDATA );
////                            $subscriptionJson = json_encode( $xml );
////                            $subscriptionArr  = json_decode( $subscriptionJson, TRUE );
////
////                            if(!empty($subscriptionArr)){
////                                if(isset($subscriptionArr['@attributes']['errcode']) && $subscriptionArr['@attributes']['errcode'] == "-1"){
////                                    $errstring = $subscriptionArr['@attributes']['errstring'];
////                                    echo "<pre>";
////                                    print_r($errstring);
////                                    echo "</pre>";
////                                    $subscription_flag = false;
////                                }else{
////                                    $subscription_flag = true;
////                                }
////                            }else{
////                                    $subscription_flag = true;
////                            }
////                        }
////                    }
////                }
////            }
////            if( $subscription_flag ) {
////                echo "<pre>";
////                print_r("yes");
////                echo "</pre>";
////            }
//        }
//    }

    /*  user integration script from API to wordpress user.

      $privacy_curl = curl_init();

      curl_setopt_array($privacy_curl, array(
      CURLOPT_URL => API_HOST.'backend/backend.php?id=' . APIKEY . '&cmd=tliteQueryData&qtype=GetPrivacyClause',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
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
      $get_customer_curl = curl_init ();

      curl_setopt_array ( $get_customer_curl, array (
      CURLOPT_URL            => API_HOST.'backend/backend.php?id=' . APIKEY . '&cmd=tliteQueryData&qtype=GetCustomer&sdate=20240201000000&edate=20240210000000',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING       => '',
      CURLOPT_MAXREDIRS      => 10,
      CURLOPT_TIMEOUT        => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST  => 'GET',
      ) );

      $get_customer_curl_response = curl_exec ( $get_customer_curl );

      curl_close ( $get_customer_curl );
      //    $customersResponse = json_decode ( $curl_response );
      $response_xml           = simplexml_load_string ( $get_customer_curl_response );
      $response_json          = json_encode ( $response_xml );
      $customersData          = json_decode ( $response_json, TRUE );

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
      $cusemail = strtolower($cusemail);
      if( ! empty ( $cusemail ) ) {
      $customer = get_user_by ( 'email', $cusemail );
      if( !empty($customer)) {
      $user_ids[] = $customer->ID;
      //                        echo "<pre>";
      //                        print_r($customer);
      //                        echo "</pre>";
      } else {
      $password = wp_generate_password();
      $user_id = wp_create_user( 'username', $password, 'user@example.com' );
      if ( ! is_wp_error( $user_id ) ) {
      // User created successfully
      $user_ids[] = $user_id;
      update_user_meta( $user_id, 'first_name', $customers_value['@attributes']['cusfname'] );
      update_user_meta( $user_id, 'last_name', $customers_value['@attributes']['cuslname'] );
      update_user_meta( $user_id, 'user_address', $customers_value['@attributes']['cusadd1'] );
      update_user_meta( $user_id, 'billing_phone', isset($customers_value['@attributes']['cusmobile']) ? $customers_value['@attributes']['cusmobile'] : $customers_value['@attributes']['custel'] );
      //                            echo "<pre>";
      //                            print_r($customers_value);
      //                            echo "</pre>";
      } else {
      // Error creating user
      $error_msg = $user_id->get_error_message();
      }
      }
      }
      }
      }
      if(!empty($user_ids)){
      echo "<pre>";
      print_r($user_ids);
      echo "</pre>";
      }
      }

     */

//       $response      = array (
//           'status'  => 0,
//           'message' => 'Error While register user',
//           'data'    => $args//array()
//       );
//       $auth_code     = $args[ 'auth_code' ];
//       $user_name     = $args[ 'user_name' ];
//       $user_email    = $args[ 'user_email' ];
//       $user_password = $args[ 'user_password' ];
//       $user_phone    = $args[ 'billing_phone' ];
//       $user_login    = $user_email;
//
//       if ( ! empty( $auth_code ) ) {
//           if ( $auth_code == "5c555c18946eb" ) {
//               $response[ 'status' ]  = 1;
//               $response[ 'message' ] = "Authcode Is Valid";
//               if ( ! empty( $user_email ) ) {
//                   $user = get_user_by( 'email', $user_email );
//                   if ( $user !== false ) {
//                       $response[ 'status' ]  = 1;
//                       $response[ 'message' ] = "User Already Registered";
//                       $response[ 'data' ]    = array (
//                           'id' => $user->ID,
//                       );
//                   } else {
//                       $new_user_id = wp_create_user( $user_login, $user_password, $user_email );
//                       if ( ! is_wp_error( $new_user_id ) ) {
//                           update_user_meta( $new_user_id, 'nickname', $user_name );
//                           update_user_meta( $new_user_id, 'billing_phone', $user_phone );
//                           update_user_meta( $new_user_id, 'billing_first_name', $user_name );
//                           update_user_meta( $new_user_id, 'first_name', $user_name );
//                           $user_responce                      = array ();
//   // Fetch the WP_User object of our user.
//                           $user_obj                           = new WP_User( $new_user_id );
//   // Replace the current role with 'editor' role
//                           $user_obj->set_role( 'customer' );
//                           $user_id                            = $user_obj->data->ID;
//                           $user_responce[ 'user_id' ]         = $user_obj->data->ID;
//                           $user_responce[ 'user_login' ]      = $user_obj->data->user_login;
//                           $user_responce[ 'user_nicename' ]   = $user_name;
//                           $user_responce[ 'user_email' ]      = $user_obj->data->user_email;
//                           $user_responce[ 'user_phone' ]      = get_user_meta( $user_id, 'billing_phone', true );
//                           $user_responce[ 'user_registered' ] = $user_obj->data->user_registered;
//                           $user_responce[ 'display_name' ]    = $user_obj->data->display_name;
//                           $user_responce[ 'roles' ]           = $user_obj->roles;
//                           $response[ 'status' ]               = 1;
//                           $response[ 'message' ]              = "User Successfully Register";
//                           $response[ 'data' ]                 = array (
//                               'id'  => $new_user_id,
//                               'obj' => $user_responce,
//                           );
//                       } else {
//                           $response[ 'status' ]  = 0;
//                           $response[ 'message' ] = "Registration Error :: " . strip_tags( $new_user_id->get_error_message() );
//                       }
//                   }
//               } else {
//                   $response[ 'status' ]  = 0;
//                   $response[ 'message' ] = "Email Is Require";
//               }
//           } else {
//               $response[ 'status' ]  = 0;
//               $response[ 'message' ] = "Authcode Is Invalid";
//           }
//       } else {
//           $response[ 'status' ]  = 0;
//           $response[ 'message' ] = "Authcode Is Require";
//       }
//       echo json_encode( $response );
//       exit();
    // Get the list of active languages and their URLs
//    $languages = icl_get_languages('skip_missing=0&orderby=code');
//
//    if (is_array($languages) && !empty($languages)) {
//        // Get the current page URL with parameters
//        $current_url = add_query_arg($_GET, home_url($_SERVER['REQUEST_URI']));
//
//        // Construct the language switcher dropdown
//        echo '<select id="language-switcher">';
//        foreach ($languages as $language) {
//            // Get the URL for the language
//            $url = $language['url'];
//
//            // Append the parameters from the current page URL to the language URL
//            $url_with_params = add_query_arg($_GET, $url);
//
//            // Output the option tag for the language
//            printf(
//                '<option value="%s"%s>%s</option>',
//                esc_url($url_with_params),
//                selected($language['active'], 1, false),
//                esc_html($language['translated_name'])
//            );
//        }
//        echo '</select>';
//    }
//    $cart       = WC()->cart->cart_contents;
//    $user_id = get_current_user_id();
//    $transactionIds = get_user_meta( $user_id, 'transactionIds', true );
//    $addToCartObject = get_user_meta( $user_id, 'addToCartObject', true );
//    $subscriptionSeatList = get_user_meta( $user_id, 'subscriptionSeatList', true );
////    $delete_tran_array = array("TLITE0117207030433","TLITE0117207031171");
////    $ticket_title = "BACH/HAYDN/RIES";
//    echo "<pre>";
//    print_r($transactionIds);
//    echo "</pre>";
//    echo "<pre>";
//    print_r($addToCartObject);
//    echo "</pre>";
//    echo "<pre>";
//    print_r($subscriptionSeatList);
//    echo "</pre>";
//    $final_transaction_ids = array_filter( $transactionIds, function ($transaction) use ($delete_tran_array) {
//                            if( ! in_array($transaction[ 'transaction_id' ],$delete_tran_array)) {
//                                return($transaction);
//                            }
//                        } );
//    if( ! empty( $cart ) ) {
//        foreach ( $cart as $cart_item_key => $cart_item ) {
//            unset($addToCartObject[$ticket_title]);
//            $final_addToCartObject = $addToCartObject;
//            if( ! empty( $final_addToCartObject ) ) {
//                foreach ( $final_addToCartObject as $meta_key => $meta_value ) {
//                    if( ! empty( $meta_value ) ) {
//                        foreach ( $meta_value as $meta_k => $meta_v ) {
//                            $reductions = $meta_v[ 'reductions' ];
//                            if( ! empty( $reductions ) ) {
//                                foreach ( $reductions as $reductions_key => $reductions_value ) {
//                                    $reductionQuantity = $reductions_value[ 'reductionQuantity' ];
//                                    $reductionPrice    = $reductions_value[ 'reductionPrice' ];
//                                    $totalPrice        = $totalPrice + ((int) $reductionPrice * (int) $reductionQuantity);
//                                    $totalQty          = $totalQty + (int) $reductionQuantity;
//                                }
//                            }
//                        }
//                    }
//                }
//            }
//            update_user_meta( $user_id, 'addToCartObject', $final_addToCartObject );
//            update_user_meta( $user_id, 'transactionIds', $final_transaction_ids );
//            if(!empty($final_transaction_ids)){
//                $cart_item[ 'data' ]->set_price( $totalPrice );
//                $cart_item[ 'selected_seat_price' ][ 0 ]    = $final_addToCartObject;
//                $cart_item[ 'transaction_ids' ][ 0 ]    = $final_transaction_ids;
//            }else{
//                WC()->cart->empty_cart();
//                update_user_meta( $user_id, 'addToCartObject', array () );
//                update_user_meta( $user_id, 'transactionIds', array () );
//                update_user_meta( $user_id, 'subscriptionSeatList', array () );
//            }
//            echo "<pre>";
//            print_r($cart_item);
//            echo "</pre>";
////            WC()->cart->cart_contents[ $cart_item_key ] = $cart_item;
////            WC()->cart->calculate_totals();
//        }
//    }
//    echo "<pre>";
//    print_r($final_transaction_ids);
//    echo "</pre>";
//    $current_order = wc_get_order( 6314 );
//    $subscription_order_id = $current_order->get_meta( 'subscriptionOrderId' );
//    echo "<pre>";
//    print_r($subscription_order_id);
//    echo "</pre>";
//    echo "<pre>";
//    print_r($current_order);
//    echo "</pre>";
//    update_user_meta( $user_id, 'subscriptionSeatList', array() );
//    $subscriptionSeatList = get_user_meta( $user_id, 'subscriptionSeatList', true );
//    
//    echo "<pre>";
//    print_r($subscriptionSeatList);
//    echo "</pre>";
//    
//    $subscription_order = wc_get_order(6314);
//    $booked_subs_seats = $subscription_order->get_meta( 'booked_subs_seats', true, 'view' );
//    $curr_transactionIds = $subscription_order->get_meta( 'transactionIds', true, 'view' );
//    $orderTransactionCodeArr = $subscription_order->get_meta( 'orderTransactionCodeArr', true, 'view' );
//    $orderTransactionCodeArr[] = "TLITE0116983325620";
//    $curr_transactionIds['125318327881558'] = $curr_transactionIds['12531810703881'];
//    $curr_transactionIds['125318327881558']['subscription_seat'][0]['ticketName'] = "La Danza Francese da Serge Lifar a Roland Petit";
//    $curr_transactionIds['125318327881558']['subscription_seat'][0]['zoneName'] = "BALCONATA V e VI";
//    $curr_transactionIds['125318327881558']['subscription_seat'][0]['subscription'] = 0;
//    $curr_transactionIds['125318327881558']['subscription_seat'][0]['transaction_id'] = "TLITE0116983325620";
//    $subscription_order->update_meta_data( 'orderTransactionCodeArr', $orderTransactionCodeArr, true );
//    $subscription_order->save();
//    echo "<pre>";
//    print_r($booked_subs_seats);
//    echo "</pre>";
//    echo "<pre>";
//    print_r($curr_transactionIds);
//    echo "</pre>";
//    echo "<pre>";
//    print_r($orderTransactionCodeArr);
//    echo "</pre>";
//    echo "<pre>";
//    print_r($user_id);
//    echo "</pre>";
//    $current_cart = WC()->cart->get_cart();
//    foreach($current_cart as $current_cart_key => $current_cart_value){
//        $booked_subs_seats = $current_cart_value['booked_subs_seats'];
//        $transaction_ids = $current_cart_value['transaction_ids'];
//    }
////    $subscription_order_id = $current_order->get_meta('subscription_order_id');
//    
//    $current_order = wc_get_order(6165);
//    echo "<pre>";
//    print_r($current_order);
//    echo "</pre>";
//
//    $prev_booked_subs_seats = $subscription_order->get_meta('booked_subs_seats');
//    $prev_transaction_ids = $subscription_order->get_meta('transactionIds');
//    $curr_booked_subs_seats = $current_order->get_meta('booked_subs_seats',true,'view');
//    $curr_transaction_ids = $current_order->get_meta('transactionIds',true,'view');
//    echo "<pre>";
//    print_r($prev_booked_subs_seats);
//    echo "</pre>";
//    echo "<pre>";
//    print_r($prev_transaction_ids);
//    echo "</pre>";
//    echo "<pre>";
//    print_r($curr_booked_subs_seats);
//    echo "</pre>";
//    echo "<pre>";
//    print_r($curr_transaction_ids);
//    echo "</pre>";
//    $final_transaction_ids = array();
//    $final_booked_subs_seats = array();
//    
//    $final_transaction_ids = recursiveArrayMerge($prev_transaction_ids, $curr_transaction_ids);
//    if(!empty($prev_booked_subs_seats)){
//        $final_booked_subs_seats = recursiveArrayMerge($prev_booked_subs_seats, $curr_booked_subs_seats);
//    }else{
//        $final_booked_subs_seats = $curr_booked_subs_seats;
//    }
//    echo "<pre>";
//    print_r($final_transaction_ids);
//    echo "</pre>";
//    echo "<pre>";
//    print_r($final_booked_subs_seats);
//    echo "</pre>";
//    $stcApiUrl = API_HOST.'backend/backend.php?cmd=titleInfoList&id='.APIKEY;
//
//    $curl = curl_init();
//
//    curl_setopt_array( $curl, array (
//        CURLOPT_URL            => $stcApiUrl,
//        CURLOPT_RETURNTRANSFER => true,
//        CURLOPT_ENCODING       => '',
//        CURLOPT_MAXREDIRS      => 10,
//        CURLOPT_TIMEOUT        => 0,
//        CURLOPT_FOLLOWLOCATION => true,
//        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
//        CURLOPT_CUSTOMREQUEST  => 'GET',
//        CURLOPT_HTTPHEADER     => array (
//            'Cookie: v1Locale=%7B%22Currency%22%3Anull%2C%22SuggestCountry%22%3Anull%2C%22Country%22%3A%22IT%22%2C%22Language%22%3A%22it-IT%22%7D'
//        ),
//    ) );
//
//    $response = curl_exec( $curl );
//
//    curl_close( $curl );
//    $xml           = simplexml_load_string( $response );
//    $json          = json_encode( $xml );
//    $vivaticketArr = json_decode( $json, TRUE );
//    $vivaVenue     = $vivaticketArr[ 'tit_info_venue' ];
////spettacolo
//    if( ! empty( $vivaVenue ) ) {
//        if( array_key_first( $vivaVenue ) == '0' ) {
//            $vivaVenue_new = $vivaVenue;
//        } else {
//            $vivaVenue_new = array($vivaVenue);            
//        }
//        foreach ( $vivaVenue_new as $key => $value ) {
//            $title_info = $value[ 'tit_info_title' ];
//            $vCode      = $value[ '@attributes' ][ 'vcode' ];
//            if( array_key_first( $title_info ) == '0' ) {
//                foreach ( $title_info as $ti_key => $ti_value ) {
//                    $title_infoAtts = $ti_value[ '@attributes' ];
//                    $id_show        = $title_infoAtts[ 'id_show' ];
//                    $tit_info_perform = $ti_value[ 'tit_info_perform' ];
//                    $args           = array (
//                        'post_type'      => 'spettacolo',
//                        'status'         => 'publish',
//                        'posts_per_page' => 1,
//                        'meta_query'     => array (
//                            'relation' => 'AND',
//                            array (
//                                'key'     => 'spt_vcode',
//                                'compare' => '=',
//                                'value'   => $vCode
//                            ),
//                            array (
//                                'key'     => 'spt_id_show',
//                                'compare' => '=',
//                                'value'   => $id_show
//                            ),
//                        )
//                    );
//                    $query          = new WP_Query( $args );
//                    if( $query->have_posts() ) {
//                        while ( $query->have_posts() ) {
//                            $query->the_post();
//                            $spt_post_id = get_the_ID();
//                        }
//                        wp_reset_query();
//                        wp_reset_postdata();
////                        echo "<pre>";
////                        print_r( 'If1: ' );
////                        echo "</pre>";
//                    } else {
//                        $org_cpt     = array (
//                            'post_type'   => 'spettacolo',
//                            'post_status' => 'publish',
//                            'post_title'  => $title_infoAtts[ 'name' ]
//                        );
////                        $spt_post_id = wp_insert_post( $org_cpt );
//                    }
////                    echo "<pre>";
////                    print_r( $spt_post_id . ' : ' . $title_infoAtts[ 'name' ] );
////                    echo "</pre>";
////                    update_post_meta( $spt_post_id, 'spt_vcode', $vCode );
////                    update_post_meta( $spt_post_id, 'spt_id_show', $id_show );
////                    update_post_meta( $spt_post_id, 'spt_name', $title_infoAtts[ 'name' ] );
////                    update_post_meta( $spt_post_id, 'spt_location', '' );
////                    update_post_meta( $spt_post_id, 'spt_startDate', $title_infoAtts[ 'primaData' ] );
////                    update_post_meta( $spt_post_id, 'spt_endDate', $title_infoAtts[ 'ultimaData' ] );
////                    update_post_meta( $spt_post_id, 'spt_numPerf', $title_infoAtts[ 'numPerf' ] );
////                    update_post_meta( $spt_post_id, 'spt_tit_info_title', $ti_value );
//                    $subscripton_show = 0;                    
//                    if( array_key_first( $tit_info_perform ) == '0' ) {
//                        foreach ( $tit_info_perform as $tip_key => $tip_value ) {
//                            if($tip_value['@attributes']['abb'] == '1') {
//                                $subscripton_show = 1;
//                                break;
//                            }
//                        }
//                    } else {
//                        if($tit_info_perform['@attributes']['abb'] == '1') {
//                            $subscripton_show = 1;
//                        }
//                    }
//                    if($subscripton_show == '1') {
//                        echo "<pre>";
//                        print_r($ti_value);
//                        echo "</pre>";
//                    }
////                    update_post_meta( $spt_post_id, 'spt_subscription_show', $subscripton_show );
//                }
//            } else {
//                $title_infoAtts = $value[ 'tit_info_title' ][ '@attributes' ];
//                $tit_info_perform = $value[ 'tit_info_title' ][ 'tit_info_perform' ]; 
//                $id_show        = $title_infoAtts[ 'id_show' ];
//                $args           = array (
//                    'post_type'      => 'spettacolo',
//                    'status'         => 'publish',
//                    'posts_per_page' => 1,
//                    'meta_query'     => array (
//                        'relation' => 'AND',
//                        array (
//                            'key'     => 'spt_vcode',
//                            'compare' => '=',
//                            'value'   => $vCode
//                        ),
//                        array (
//                            'key'     => 'spt_id_show',
//                            'compare' => '=',
//                            'value'   => $id_show
//                        ),
//                    )
//                );
//                $query          = new WP_Query( $args );
//                if( $query->have_posts() ) {
//                    while ( $query->have_posts() ) {
//                        $query->the_post();
//                        $spt_post_id = get_the_ID();
////                $vCode   = get_post_meta( $org_cpt_id, 'code', true );
//                    }
//                    wp_reset_query();
//                    wp_reset_postdata();
////                    echo "<pre>";
////                    print_r( 'If2: ' );
////                    echo "</pre>";
//                } else {
//                    $org_cpt     = array (
//                        'post_type'   => 'spettacolo',
//                        'post_status' => 'publish',
//                        'post_title'  => $title_infoAtts[ 'name' ]
//                    );
////                    $spt_post_id = wp_insert_post( $org_cpt );
//                }
////                echo "<pre>";
////                print_r( $spt_post_id . ' : ' . $title_infoAtts[ 'name' ] );
////                echo "</pre>";
////                update_post_meta( $spt_post_id, 'spt_vcode', $vCode );
////                update_post_meta( $spt_post_id, 'spt_id_show', $id_show );
////                update_post_meta( $spt_post_id, 'spt_name', $title_infoAtts[ 'name' ] );
////                update_post_meta( $spt_post_id, 'spt_location', '' );
////                update_post_meta( $spt_post_id, 'spt_startDate', $title_infoAtts[ 'primaData' ] );
////                update_post_meta( $spt_post_id, 'spt_endDate', $title_infoAtts[ 'ultimaData' ] );
////                update_post_meta( $spt_post_id, 'spt_numPerf', $title_infoAtts[ 'numPerf' ] );
////                update_post_meta( $spt_post_id, 'spt_tit_info_title', $title_info );
//                $subscripton_show = 0;                    
//                if( array_key_first( $tit_info_perform ) == '0' ) {
//                    foreach ( $tit_info_perform as $tip_key => $tip_value ) {
//                        if($tip_value['@attributes']['abb'] == '1') {
//                            $subscripton_show = 1;
//                            break;
//                        }
//                    }
//                } else {
//                    if($tit_info_perform['@attributes']['abb'] == '1') {
//                        $subscripton_show = 1;
//                    }
//                }
////                update_post_meta( $spt_post_id, 'spt_subscription_show', $subscripton_show );
//            }
//        }
//    }
    $html = ob_get_clean ();
    return $html;
}
