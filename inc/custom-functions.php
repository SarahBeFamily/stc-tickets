<?php
/*
 * Add Custom FIeld on Product Page
 */
//$user_id = get_current_user_id ();
add_filter( 'auto_update_plugin', '__return_false' );
add_filter( 'auto_update_theme', '__return_false' );

/**
 * Display custom data on cart and checkout page.
 */
add_filter( 'woocommerce_get_item_data', 'get_item_data', 25, 2 );
function get_item_data($other_data, $cart_item) {
    ob_start();
    $user_id                = get_current_user_id();
    $selected_seats         = $cart_item [ 'selected_seat_price' ];
    $transaction_ids        = $cart_item [ 'transaction_ids' ];
    $subs_seat_list         = $cart_item [ 'booked_subs_seats' ];
    $subs_order_id          = isset($cart_item[ 'subscription_order_id' ]) ? $cart_item[ 'subscription_order_id' ] : array();
    $abbonamento            = false;
    $abbonamento_with_sub   = false;
    $subclass               = !empty($subs_order_id) ? 'sub-'.$subs_order_id : '';

    // test
    if( isset($_GET['print']) && $_GET[ 'print' ] == 1 ) {
        echo "<pre>";
        print_r($cart_item);
        echo "</pre>";
        // echo "<pre>";
        // print_r($transaction_ids);
        // echo "</pre>";
        echo "<pre>";
        print_r($selected_seats);
        echo"</pre>";
        // echo "<pre>";
        // print_r($subs_seat_list);
        // echo "</pre>";
    }

    $tickets_array = array ();
    foreach ( $transaction_ids[ 0 ] as $transaction_ids_key => $transaction_ids_value ) {
        $zoneId = $transaction_ids_key;
        if( ! isset( $transaction_ids_value[ 'subscription_seat' ] ) ) {
            $ticketName          = isset($transaction_ids_value[ 'ticketName' ]) ?  $transaction_ids_value[ 'ticketName' ] : '';
            $zoneName            = isset($transaction_ids_value[ 'zoneName' ]) ?  $transaction_ids_value[ 'zoneName' ] : '';
            $seatObject          = isset($transaction_ids_value[ 'seatObject' ]) ?  $transaction_ids_value[ 'seatObject' ] : '';
            $subscription        = isset($transaction_ids_value[ 'subscription' ]) ?  $transaction_ids_value[ 'subscription' ] : '';
            $transaction_id      = isset($transaction_ids_value[ 'transaction_id' ]) ?  $transaction_ids_value[ 'transaction_id' ] : '';
            $showData            = isset($transaction_ids_value['showDate']) ? $transaction_ids_value['showDate'] : '';
            if($subscription){
                $abbonamento     = true;
            }
            
            $tickets_array[ $ticketName ][] = array (
                'zoneId'            => $transaction_ids_key,
                'zoneName'          => $zoneName,
                'seatObject'        => $seatObject,
                'subscription'      => $subscription,
                'transaction_id'    => $transaction_id,
                'showDate'          => isset($selected_seats[0][$ticketName][0]['showDate']) ? $selected_seats[0][$ticketName][0]['showDate'] : $showData,
            );
        } else if( isset( $transaction_ids_value[ 'subscription_seat' ] ) && ! empty( $subs_seat_list ) ) {
            // dd($transaction_ids_value[ 'subscription_seat' ]);
            foreach ( $transaction_ids_value[ 'subscription_seat' ] as $subscription_seat_key => $subscription_seat_value ) {
                if(!$abbonamento){
                    $ticketName = $subscription_seat_value[ 'ticketName' ];
                }
                if($abbonamento){
                    $abbonamento_with_sub = true;
                }
                $zoneName       = $subscription_seat_value[ 'zoneName' ];
                $seatObject     = $subscription_seat_value[ 'seatObject' ];
                $seatObject[ 'showName' ] = $subscription_seat_value[ 'ticketName' ];
                $seatObject[ 'showDate' ] = isset($subscription_seat_value['showDate']) ? $subscription_seat_value['showDate'] : '';
                $subscription   = $subscription_seat_value[ 'subscription' ];
                $transaction_id = $subscription_seat_value[ 'transaction_id' ];

                $tickets_array[ $ticketName ][] = array (
                    'zoneId'             => $transaction_ids_key,
                    'zoneName'           => $zoneName,
                    'seatObject'         => $seatObject,
                    'subscription'       => $subscription,
                    'under_subscription' => true,
                    'transaction_id'     => $transaction_id,
                    'showDate'           => isset($selected_seats[0][$ticketName][0]['showDate']) ? $selected_seats[0][$ticketName][0]['showDate'] : $showData,
                );
            }
        }
    }
    //    if(empty($tickets_array) && !empty($subs_seat_list)){
    //        $subscription_order_id = $cart_item [ 'subscription_order_id' ];
    //    }
    ?>
    <div class="spettacolo-cart-wrapper wc-spettacolo-cart-wrapper cart-class <?php echo $subclass;?>">
        <div class="container">
            <div class="spettacolo-cart-inner">
                <div class="spettacolo-tickets">
                    <?php
                    
                    if( ! empty( $tickets_array ) ) {                        
                        $counter = 1;
                        foreach ( $tickets_array as $tickets_array_key => $tickets_array_value ) {
                            $extra_class = $counter < count( $tickets_array ) ? 'border-bot' : '';
                            $ticketName  = $tickets_array_key;
                            $showData = isset($tickets_array_value[0]['showDate']) ? $tickets_array_value[0]['showDate'] : '';
                            $counter ++;
                            if (isset($_GET['print']) && $_GET[ 'print' ] == 2) {
                                echo "<pre>";
                                print_r($tickets_array_value);
                                echo "</pre>";
                            }
                            ?>
                            <div class="ticket-datails-wrap <?php echo $extra_class; ?>">
                                <div class="ticket-title-wrap">
                                    <div class="ticket-title" data-name="<?php echo $ticketName; ?>">
                                        <h2 class="<?php echo ($abbonamento) ? 'abbonamento' : 'spettacolo'; ?>"><?php echo $ticketName; ?></h2>
                                        <?php /** MOD SARAH **/ // Display the show date ?>
                                        <div class="data"><?php echo $showData;?></div>
                                    </div>
                                    <div class="ticket-delete">
                                        <img src="<?php echo plugin_dir_url( __DIR__ ) . 'assets/img/delete_icon.svg'; ?>">
                                    </div>
                                </div>
                                <?php
                                if( ! empty( $tickets_array_value ) ) {
                                    foreach ( $tickets_array_value as $tickets_array_value_k => $tickets_array_value_v ) {
                                        $zoneName           = isset($tickets_array_value_v[ 'zoneName' ]) ? $tickets_array_value_v[ 'zoneName' ] : '';
                                        $zoneId             = isset($tickets_array_value_v[ 'zoneId' ]) ? $tickets_array_value_v[ 'zoneId' ] : '';
                                        $seatObject         = isset($tickets_array_value_v[ 'seatObject' ]) ? $tickets_array_value_v[ 'seatObject' ] : '';
                                        $subscription       = isset($tickets_array_value_v[ 'subscription' ]) ? $tickets_array_value_v[ 'subscription' ] : '';
                                        $under_subscription = isset($tickets_array_value_v[ 'under_subscription' ]) ? $tickets_array_value_v[ 'under_subscription' ] : '';
                                        $transaction_id     = isset($tickets_array_value_v[ 'transaction_id' ]) ? $tickets_array_value_v[ 'transaction_id' ] : '';
                                        $seatObject_new     = array(); // MOD SARAH

                                        if(!$abbonamento_with_sub || ($abbonamento_with_sub && !$under_subscription)){ ?>
                                        <div class="ticket-zone" data-transaction-id="<?php echo $transaction_id; ?>">
                                            <div class="zone-title" data-zoneId="<?php echo $zoneId; ?>">
                                                <h4><?php echo $zoneName; ?></h4>
                                            </div>
                                            <?php
                                            if( ! empty( $seatObject ) ) {
                                                if( array_key_first( $seatObject ) == '0' ) {
                                                    $seatObject_new = $seatObject;
                                                } else {
                                                    $seatObject_new = array ( $seatObject );
                                                }
                                            }
                                            if (isset($_GET['print']) && $_GET[ 'print' ] == 2) {
                                                echo "<pre>";
                                                print_r($seatObject_new);
                                                echo "</pre>";
                                            }

                                            if(is_array($seatObject_new) && !empty($seatObject_new)) :
                                            foreach ( $seatObject_new as $seatObject_key => $seatObject_value ) {
                                                if (isset($_GET['print']) && $_GET[ 'print' ] == 2) {
                                                    echo "<pre>";
                                                    print_r($seatObject_value);
                                                    echo "</pre>";
                                                }

                                                $seat_desc  = $seatObject_value[ 'description' ];
                                                $seat_price = $seatObject_value[ 'price' ];
                                                $barcode = "";
                                                $currrent_subs_seats = array();
                                                $seat_price = ! empty( $seat_price ) ? (float) $seat_price / 100 : 0;
                                                if( $under_subscription ) {
                                                    $seat_price = 0;
                                                }
                                                $seat_reduction    = $seatObject_value[ 'reduction' ];
                                                $reduction_id      = $seat_reduction[ '@attributes' ][ 'id' ];
                                                $reduction_name    = $seat_reduction[ 'description' ];
                                                $reductionQuantity = 1;
                                                if( $subscription == 1 ) {
                                                    $barcode = $seatObject_value[ 'barcode' ];
                                                }
                                                if( ! empty( $subs_seat_list ) ) {
                                                    if( array_key_first($subs_seat_list) == '0' ) {
                                                        $currrent_subs_seats = $subs_seat_list[ 0 ][ $barcode ];
                                                    }else{
                                                        // If the key is not 0 then loop through the array to get the correct key
                                                        foreach($subs_seat_list as $subs_seat_list_key => $subs_seat_list_value){
                                                            if($subs_seat_list_key == $barcode){
                                                                $currrent_subs_seats = $subs_seat_list_value;
                                                            }
                                                        }
                                                    }
                                                }
                                                ?>
                                                <div class="zone-reductions">
                                                    <div class="seat-title" data-reductionId="<?php echo $reduction_id; ?>">
                                                        <p><?php echo preg_replace( '/' . preg_quote( $zoneName, '/' ) . '/', '', $seat_desc, 1 ); ?></p>
                                                    </div>
                                                    
                                                    <div class="tipoticketeprezzo">
                                                        <div class="reduction-title-wrap">
                                                            <div class="reduction-title">
                                                                <p><?php echo $reduction_name; ?></p>
                                                            </div>
                                                            <div class="reduction-qty">
                                                                <p><?php echo " X " . $reductionQuantity; ?></p>
                                                            </div>
                                                        </div>
                                                        <div class="reduction-price">
                                                            <p><?php echo " " . $seat_price . " &euro;"; ?></p>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    if( $subscription == 1 ) {
                                                        ?>
                                                        <div class="barcode" data-barcode="<?php echo $barcode; ?>">
                                                            <p><?php echo $barcode; ?></p>
                                                        </div>
                                                        <div class="go-to-subscription">
                                                            <a class="go-to-subscription-btn" href="<?php echo (is_plugin_active('sitepress-multilingual-cms/sitepress.php') ? rtrim(apply_filters( 'wpml_home_url', get_option( 'home' ) ), '/') : get_site_url()) . "/subscription/?barcode=" . $barcode; ?>"><?php echo _e( 'Seleziona Spettacoli', 'stc-tickets' ); ?></a>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if( ! empty( $currrent_subs_seats ) ) { ?>
                                                        <div class="subscription-seats">
                                                            <?php
                                                            // dd($currrent_subs_seats);
                                                            foreach ( $currrent_subs_seats as $currrent_subs_key => $currrent_subs_value ) {
                                                                if( isset( $currrent_subs_value[ 'seat' ] ) ) {
                                                                    $seat_title = $currrent_subs_value[ 'seat' ][ 'description' ];
                                                                    $custref = $currrent_subs_value['@attributes']['custref'];
                                                                    $ticketName = '';
                                                                    ?>
                                                                    <p data-transaction-id="<?php echo $custref; ?>">
                                                                        <span class="showTitle"><?php echo $ticketName; ?></span> &nbsp;
                                                                        <?php echo $seat_title; ?>
                                                                        <span class="data"><?php echo $showData; ?></span>
                                                                    </p>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <?php
                                            }
                                            endif;
                                            ?>
                                        </div>
                                        <?php
                                    }
                                    }
                                }
                                ?>
                            </div>    
                            <?php
                        }
                    }

                    // Check if there is a subscription order
                    // and if user is adding shows to the subscription with barcode
                    if (is_array($subs_seat_list) && !empty($subs_seat_list)) {
                        foreach ($subs_seat_list as $subs_seat_list_key => $subs_seat_list_value) {
                            $barcode = $subs_seat_list_key;
                            $currrent_subs_seats = $subs_seat_list_value;
                            $ticketName = '';
                            // Check barcode in order to get the remaining seats
                            $sub_cookie = tempnam ("/tmp", "CURLCOOKIE");
                            $curl = curl_init();

                            curl_setopt_array( $curl, array (
                                CURLOPT_URL            => API_HOST . 'backend/backend.php?id=SANCARLO&cmd=xmlSubscriptionData&barcode=' . $barcode,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING       => '',
                                CURLOPT_MAXREDIRS      => 10,
                                CURLOPT_TIMEOUT        => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_COOKIEJAR => $sub_cookie,
                                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST  => 'GET',
                            ) );

                            $response = curl_exec( $curl );
                            if( curl_errno( $curl ) ) {
                                $error_msg = curl_error( $curl );
                            }
                            curl_close( $curl );
                            $xml                    = simplexml_load_string( $response, 'SimpleXMLElement', LIBXML_NOCDATA );
                            $subscriptionJson       = json_encode( $xml );
                            $subscriptionArr        = json_decode( $subscriptionJson, TRUE );
                            $accruals               = 0;
                            $totalaccruals          = 0;
                            $remainingaccruals      = 0;
                            $title_subscription     = '';

                            if( ! empty( $subscriptionArr && isset($subscriptionArr[ 'subscriptiondata' ])) ) {
                                $accruals           = $subscriptionArr[ 'subscriptiondata' ][ 'accruals' ];
                                $title_subscription = $subscriptionArr[ 'subscriptiondata' ][ '@attributes' ][ 'title' ];
                                $totalaccruals      = $subscriptionArr[ 'subscriptiondata' ][ '@attributes' ][ 'numaccruals' ];
                                $remainingaccruals  = $subscriptionArr[ 'subscriptiondata' ][ '@attributes' ][ 'remainingaccruals' ];
                            }

                            // if the remainingaccruals is > 0 then the user can add more shows
                            if($remainingaccruals > 0) {
                            ?>
                            <div class="ticket-datails-wrap-button">
                                
                                <p><?php echo sprintf(_n('You still have %s seat available', 'You still have %s seats available', (int)$remainingaccruals, 'stc-tickets'), (int)$remainingaccruals); ?></p>
                                <a class="button alt bf-btn primary-btn" href="<?php echo (is_plugin_active('sitepress-multilingual-cms/sitepress.php') ? rtrim(apply_filters( 'wpml_home_url', get_option( 'home' ) ), '/') : get_site_url()) . "/subscription/?barcode=" . $barcode; ?>"><?php echo sprintf(__( 'Seleziona Spettacoli per %s', 'stc-tickets' ), $title_subscription); ?></a>
                            </div>
                        <?php
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    // test
    if( isset($_GET['print']) && $_GET[ 'print' ] == 1 ) {
        echo "Ticket array:<br><pre>".print_r($tickets_array)."</pre>";
    }
    
    $html = ob_get_clean();
//    return $html;
//    if ( isset( $cart_item [ 'custom_data' ] ) ) {
//        $custom_data = $cart_item [ 'custom_data' ];

    $other_data[] = array (
        'name'    => ($abbonamento) ? __("Abbonamento",'stc-tickets')  : __("Spettacolo",'stc-tickets'),
        'display' => $html
    );
//    }
    return $other_data;
}
/**
 * Add data to cart item
 */
add_filter( 'woocommerce_add_cart_item_data', 'add_cart_item_data', 99999, 2 );
function add_cart_item_data($cart_item_meta, $product_id) {

    $user_id                                  = get_current_user_id();
    $get_user_meta                            = get_user_meta( $user_id, 'addToCartObject' );
    $transactionIds                           = get_user_meta( $user_id, 'transactionIds' );
    $subscriptionSeatList                     = get_user_meta( $user_id, 'subscriptionSeatList', true );
    $subscriptionOrderId                      = get_user_meta( $user_id, 'subscriptionOrderId', true );
    $cart_item_meta [ 'selected_seat_price' ] = $get_user_meta;
    $cart_item_meta [ 'booked_subs_seats' ]   = $subscriptionSeatList;
    $cart_item_meta [ 'transaction_ids' ]     = $transactionIds;
    if( ! empty( $subscriptionOrderId ) ) {
        $cart_item_meta [ 'subscription_order_id' ] = $subscriptionOrderId;
    }

    return $cart_item_meta;
}
/**
 * Add order item meta.
 */
add_action( 'woocommerce_add_order_item_meta', 'add_order_item_meta', 10, 2 );
function add_order_item_meta($item_id, $values) {

    if( isset( $values [ 'selected_seat_price' ] ) ) {

//        $custom_data = $values [ 'custom_data' ];
        wc_add_order_item_meta( $item_id, 'Prezzo del posto scelto', $values [ 'selected_seat_price' ] );
    }
}
/*
 * set price for item in cart
 */
add_action( 'woocommerce_before_calculate_totals', 'alter_price_cart', 9999 );
function alter_price_cart($cart) {
    $totalPrice = 0;
    $totalQty   = 0;
    foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
        $selected_seats = $cart_item[ 'selected_seat_price' ];
        if( ! empty( $selected_seats[ 0 ] ) ) {
            foreach ( $selected_seats[ 0 ] as $meta_key => $meta_value ) {
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
        $transaction_ids     = $cart_item[ 'transaction_ids' ][ 0 ];
        $transaction_final_price = 0;
        // dd($transaction_ids);
        if( ! empty( $transaction_ids ) ) {
            foreach ( $transaction_ids as $trans_key => $trans_value ) {
                if(is_array($trans_value) && !empty($trans_value)){

                /** MOD SARAH -- error on add subscription selection to cart **/
                    // Check if seatObject is set
                    if(isset($trans_value['seatObject'])) {
                        // Check if the seatObject is an array and not empty and the key is 0
                        if(is_array($trans_value['seatObject']) && !empty($trans_value['seatObject']) && array_key_first( $trans_value['seatObject'] ) == 0){
                        // if(is_array($trans_value['seatObject']) && !empty($trans_value['seatObject'])){
                            // dd($trans_value['seatObject']);
                            foreach($trans_value['seatObject'] as $trans_seat_key => $trans_seat_value){
                                $current_seat_price = (int) $trans_seat_value['price'];
                                $transaction_final_price = $transaction_final_price + ((int) $current_seat_price / 100);
                            }
                        } else {
                            $seat_price = (int) $trans_value['seatObject']['price'];
                            $transaction_final_price = $transaction_final_price + ((int) $seat_price / 100);
                        }
                    }

                    // Check if subscription_seat is set
                    if(isset($trans_value['subscription_seat'])) {
                        foreach($trans_value['subscription_seat'] as $sub_seat_key => $sub_seat_value){
                            // $current_seat_price = (int) $sub_seat_value['seatObject']['price'];
                            $current_seat_price = (int) $sub_seat_value['seats'][0]['reductionPrice'];
                            $transaction_final_price = $transaction_final_price + ((int) $current_seat_price / 100);
                            // dd($trans_value['subscription_seat']);
                        }
                    }
                }
            }
        }

        if(!empty($transaction_final_price) && $transaction_final_price != 0 && $transaction_final_price != $totalPrice){
            $totalPrice = $transaction_final_price;
        }
        // $total_price   = $custom_data[ 'product_del_price_' . $user_id ];
        if( ! empty( $totalPrice ) && $totalPrice != 0 ) {
            $cart_item[ 'data' ]->set_price( $totalPrice );
        }
    }
}
// ADD THE INFORMATION AS META DATA SO THAT IT CAN BE SEEN AS PART OF THE ORDER
//add_action('woocommerce_add_order_item_meta','add_and_update_values_to_order_item_meta', 1, 3 );
//function add_and_update_values_to_order_item_meta( $item_id, $item_values, $item_key ) {
//    echo "<pre>";
//    print_r($item_id);
//    print_r($item_values);
//    print_r($item_key);
//    echo "</pre>";
//    // Getting your custom product ID value from order item meta
//    $custom_value = wc_get_order_item_meta( $item_id, 'custom_field_key', true );
//
//    // Here you update the attribute value set in your simple product
//    wc_update_order_item_meta( $item_id, 'pa_your_attribute', $custom_value );
//    
//}
//add_action( 'woocommerce_thankyou', 'woocommerce_redirectcustom');
//function woocommerce_redirectcustom( $order_id ){
//    echo "<pre>";
//    print_r($order );
//    echo "</pre>";
//    die();
//    $order = wc_get_order( $order_id );
//    $url = 'https://www.google.com/';
//    if ( ! $order->has_status( 'failed' ) ) {
////        wp_redirect( $url , 302 );
//        header('Location: ' . $url);
//        exit;
//    }
//}

add_filter( 'woocommerce_return_to_shop_redirect', 'woocommerce_change_return_shop_url_fun' );
function woocommerce_change_return_shop_url_fun() {
    return (is_plugin_active('sitepress-multilingual-cms/sitepress.php') ? rtrim(apply_filters( 'wpml_home_url', get_option( 'home' ) ), '/') : get_site_url()) . '/spettacoli/';
}

/**
 * Display custom data on order details page in user account area.
 */
add_action( 'woocommerce_order_details_after_order_table_items', 'woocommerce_order_details_fun' );
function woocommerce_order_details_fun($order) {
    ob_start();
    $selected_seats         = get_post_meta( $order->get_id(), 'confirmedOrderObject', true );
    $transaction_ids        = get_post_meta( $order->get_id(), 'transactionIds', true );
    $subs_seat_list         = get_post_meta( $order->get_id(), 'booked_subs_seats', true );
    $subscription_order_id  = get_post_meta( $order->get_id(), 'subscriptionOrderId', true );
    $abbonamento            = false;
    $abbonamento_with_sub   = false;
    $abbonamento_barcode    = false;

    $tickets_array = array ();

    if (is_array($transaction_ids) && !empty($transaction_ids)) :
    foreach ( $transaction_ids as $transaction_ids_key => $transaction_ids_value ) {
        $zoneId = $transaction_ids_key;
        if( ! isset( $transaction_ids_value[ 'subscription_seat' ] ) ) {
            $ticketName     = isset($transaction_ids_value[ 'ticketName' ]) ?  $transaction_ids_value[ 'ticketName' ] : '';
            $zoneName       = isset($transaction_ids_value[ 'zoneName' ]) ?  $transaction_ids_value[ 'zoneName' ] : '';
            $seatObject     = isset($transaction_ids_value[ 'seatObject' ]) ?  $transaction_ids_value[ 'seatObject' ] : array();
            $subscription   = isset($transaction_ids_value[ 'subscription' ]) ?  $transaction_ids_value[ 'subscription' ] : '0';
            $transaction_id = isset($transaction_ids_value[ 'transaction_id' ]) ?  $transaction_ids_value[ 'transaction_id' ] : '';
            $showData       = isset($transaction_ids_value['showDate']) ? $transaction_ids_value['showDate'] : '';
            if($subscription){
                $abbonamento = true;
            }

            $tickets_array[ $ticketName ][] = array (
                'zoneId'       => $transaction_ids_key,
                'zoneName'     => $zoneName,
                'seatObject'   => $seatObject,
                'subscription' => $subscription,
                'transaction_id' => $transaction_id,
                'showDate'     => $showData,
            );
        } else if( isset( $transaction_ids_value[ 'subscription_seat' ] ) && ! empty( $subs_seat_list ) ) {
            foreach ( $transaction_ids_value[ 'subscription_seat' ] as $subscription_seat_key => $subscription_seat_value ) {
                // Barcode is the key of the sub_seat_list array
                $barcode = array_key_first($subs_seat_list);
                // check if this order has been made with a subscription barcode
                if (empty($subscription_order_id)) {
                    $abbonamento_barcode = true;
                }

                if(!$abbonamento || $abbonamento_barcode){
                    $ticketName = $subscription_seat_value[ 'ticketName' ];
                }
                if($abbonamento){
                    $abbonamento_with_sub = true;
                }
                $zoneName       = isset($subscription_seat_value[ 'zoneName' ]) ? $subscription_seat_value[ 'zoneName' ] : '';
                $seatObject     = isset($subscription_seat_value[ 'seatObject' ]) ? $subscription_seat_value[ 'seatObject' ] : array();
                $subscription   = isset($subscription_seat_value[ 'subscription' ]) ? $subscription_seat_value[ 'subscription' ] : '';
                $transaction_id = isset($subscription_seat_value[ 'transaction_id' ]) ? $subscription_seat_value[ 'transaction_id' ] : '';
                $showDate       = isset($subscription_seat_value['showDate']) ? $subscription_seat_value['showDate'] : '';
                $seatObject_new = array(); // MOD SARAH

                // Add subscription ticket name to the seatObject
                if (!empty($seatObject)) {
                    $seatObject_new = $seatObject;
                    $seatObject_new['ticketName'] = $subscription_seat_value[ 'ticketName' ];
                }
                $tickets_array[ $ticketName ][] = array (
                    'zoneId'             => $transaction_ids_key,
                    'zoneName'           => $zoneName,
                    'seatObject'         => !empty($seatObject_new) ? $seatObject_new : $seatObject,
                    'subscription'       => $subscription,
                    'under_subscription' => $abbonamento_with_sub,
                    'outher_subscription' => $abbonamento_barcode,
                    'abbonamento'        => $abbonamento_barcode ? $barcode : '',
                    'transaction_id'     => $transaction_id,
                    'showDate'           => $showDate,
                );
            }
        }
    }
    endif;
    ?>
    <div class="spettacolo-cart-wrapper wc-spettacolo-cart-wrapper ticket-order-table">
        <div class="container">
            <div class="spettacolo-cart-inner">
                <div class="spettacolo-tickets">
                    <?php
                    if( ! empty( $tickets_array ) ) {
                        $counter = 1;
                        foreach ( $tickets_array as $tickets_array_key => $tickets_array_value ) {
                            $extra_class = $counter < count( $tickets_array ) ? 'border-bot' : '';
                            $ticketName  = $tickets_array_key;
                            $showData = is_array($tickets_array_value) && !empty($tickets_array_value) && isset($tickets_array_value[0]['showDate']) ? $tickets_array_value[0]['showDate'] : '';
                            $counter ++;
                            ?>
                            <div class="ticket-datails-wrap <?php echo $extra_class; ?>">
                                <div class="ticket-title" data-name="<?php echo $ticketName; ?>">
                                    <h2 class="<?php echo ($abbonamento) ? 'abbonamento'  : 'spettacolo'; ?>"><?php echo $ticketName; ?></h2>
                                    <?php /** MOD SARAH **/ // Display the show date ?>
                                    <div class="data"><?php echo $showData;?></div>
                                </div>
                                <?php
                                if( ! empty( $tickets_array_value ) ) {
                                    
                                    foreach ( $tickets_array_value as $tickets_array_value_k => $tickets_array_value_v ) {
                                        $zoneName           = isset($tickets_array_value_v[ 'zoneName' ]) ? $tickets_array_value_v[ 'zoneName' ] : '';
                                        $zoneId             = isset($tickets_array_value_v[ 'zoneId' ]) ? $tickets_array_value_v[ 'zoneId' ] : '';
                                        $seatObject         = isset($tickets_array_value_v[ 'seatObject' ]) ? $tickets_array_value_v[ 'seatObject' ] : '';
                                        $subscription       = isset($tickets_array_value_v[ 'subscription' ]) ? $tickets_array_value_v[ 'subscription' ] : '';
                                        $under_subscription = isset($tickets_array_value_v[ 'under_subscription' ]) ? $tickets_array_value_v[ 'under_subscription' ] : '';
                                        $totalaccruals      = 0;

                                        // if(!$abbonamento_with_sub || ($abbonamento_with_sub && !$under_subscription)){
                                        if ($subscription == '' || $under_subscription == '') {
                                            ?>
                                            <div class="ticket-zone">
                                                <div class="zone-title" data-zoneId="<?php echo $zoneId; ?>">
                                                    <h4><?php echo $zoneName; ?></h4>
                                                    <p></p>
                                                </div>
                                                <?php
                                                if( ! empty( $seatObject ) ) {
                                                    if( array_key_first( $seatObject ) == '0' ) {
                                                        $seatObject_new = $seatObject;
                                                    } else {
                                                        $seatObject_new = array ( $seatObject );
                                                    }
                                                }
                                                if(!empty($seatObject_new) && is_array($seatObject_new)) {
                                                foreach ( $seatObject_new as $seatObject_key => $seatObject_value ) {
                                                    // dd($seatObject_value);
                                                    $seat_desc  = isset($seatObject_value[ 'description' ]) ? $seatObject_value[ 'description' ] : '';
                                                    $seat_price = isset($seatObject_value[ 'price' ]) ? $seatObject_value[ 'price' ] : '';
                                                    $seat_price = ! empty( $seat_price ) ? (float) $seat_price / 100 : 0;
                                                    if( $under_subscription ) {
                                                        $seat_price = 0;
                                                    }
                                                    $seat_reduction    = isset($seatObject_value[ 'reduction' ]) ? $seatObject_value[ 'reduction' ] : array();
                                                    $reduction_id      = isset($seat_reduction[ '@attributes' ][ 'id' ]) ? $seat_reduction[ '@attributes' ][ 'id' ] : '';
                                                    $reduction_name    = isset($seat_reduction[ 'description' ]) ? $seat_reduction[ 'description' ] : '';
                                                    $reductionQuantity = 1;
                                                    if( $subscription == 1 ) {
                                                        $barcode = isset($seatObject_value[ 'barcode' ]) ? $seatObject_value[ 'barcode' ] : '';
                                                    }
                                                    if( ! empty( $subs_seat_list ) ) {
                                                        if( array_key_first( $subs_seat_list ) == 0 ) {
                                                            $currrent_subs_seats = $subs_seat_list[ 0 ][ $barcode ];
                                                        } else {
                                                            $currrent_subs_seats = $subs_seat_list[ $barcode ];
                                                        }
                                                    }
                                                    ?>
                                                    <div class="zone-reductions">
                                                        <?php
                                                        if( $subscription != 1 ) {
                                                            ?>
                                                            <div class="seat-title" data-reductionId="<?php echo $reduction_id; ?>">
                                                                <p><?php echo preg_replace( '/' . preg_quote( $zoneName, '/' ) . '/', '', $seat_desc, 1 ); ?></p>
                                                            </div>
                                                            <?php
                                                        }
                                                        ?>
                                                        <div class="tipoticketeprezzo">
                                                            <div class="reduction-title-wrap">
                                                                <div class="reduction-title">
                                                                    <p><?php echo $reduction_name; ?></p>
                                                                </div>
                                                                <div class="reduction-qty">
                                                                    <p><?php echo " " . $reductionQuantity; ?></p>
                                                                </div>
                                                            </div>
                                                            <div class="reduction-price">
                                                                <p><?php echo " " . $seat_price . " &euro;"; ?></p>
                                                            </div>
                                                        </div>
                                                        <?php
                                                        if( $subscription == 1 || $abbonamento_barcode ) {
                                                            // test
                                                            // $accruals      = array(
                                                            //     'accrual' => array(
                                                            //         0 => array(
                                                            //             '@attributes' => array(
                                                            //                 'title' => 'test',
                                                            //                 'status' => 30
                                                            //             )
                                                            //         ),
                                                            //         // 1 => array(
                                                            //         //     '@attributes' => array(
                                                            //         //         'title' => 'test2',
                                                            //         //         'status' => 30
                                                            //         //     )
                                                            //         // )
                                                            //     ),
                                                            // );
                                                            // $totalaccruals = 2;
                                                            // $subscriptionArr = array();

                                                            // if(!isset($_COOKIE["remainingSeats"])) {
                                                            //     $_COOKIE["remainingSeats"];
                                                            // }

                                                            $sub_cookie = tempnam ("/tmp", "CURLCOOKIE");
                                                            $curl = curl_init();

                                                            curl_setopt_array( $curl, array (
                                                                CURLOPT_URL            => API_HOST . 'backend/backend.php?id=SANCARLO&cmd=xmlSubscriptionData&barcode=' . $barcode,
                                                                CURLOPT_RETURNTRANSFER => true,
                                                                CURLOPT_ENCODING       => '',
                                                                CURLOPT_MAXREDIRS      => 10,
                                                                CURLOPT_TIMEOUT        => 0,
                                                                CURLOPT_FOLLOWLOCATION => true,
                                                                CURLOPT_COOKIEJAR => $sub_cookie,
                                                                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                                                                CURLOPT_CUSTOMREQUEST  => 'GET',
                                                            ) );

                                                            $response = curl_exec( $curl );
                                                            if( curl_errno( $curl ) ) {
                                                                $error_msg = curl_error( $curl );
                                                            }
                                                            curl_close( $curl );
                                                            $xml              = simplexml_load_string( $response, 'SimpleXMLElement', LIBXML_NOCDATA );
                                                            $subscriptionJson = json_encode( $xml );
                                                            $subscriptionArr  = json_decode( $subscriptionJson, TRUE );
                                                            $accruals         = array();
                                                            $totalaccruals    = 0;
                                                            $remainingaccruals = 0;
                                                            $seat_count        = 0;

                                                            if( ! empty( $subscriptionArr ) && ! isset($subscriptionArr['@attributes']['errcode']) ) {
                                                                $accruals      = $subscriptionArr[ 'subscriptiondata' ][ 'accruals' ];
                                                                $totalaccruals = (int)$subscriptionArr[ 'subscriptiondata' ][ '@attributes' ][ 'numaccruals' ];
                                                            }

                                                            if( isset($subscriptionArr['@attributes']['errcode']) && $subscriptionArr['@attributes']['errcode'] == '-1' ){
                                                                echo $subscriptionArr['@attributes']['errstring'];
                                                            } else {
                                                                // Show the barcode here only if the order is a subscription and not a subscription with barcode
                                                                if (!$abbonamento_barcode) {
                                                                ?>
                                                                <div class="barcode" data-barcode="<?php echo $barcode; ?>">
                                                                    <p><?php echo __('Codice abbonamento','stc-tickets').": " . $barcode; ?></p>
                                                                </div>
                                                            <?php }
                                                            }

                                                            if( !isset($subscriptionArr['@attributes']['errcode'])){
                                                                if( ! empty( $accruals ) ) {
                                                                    //test
                                                                    if(isset($_GET['print']) && $_GET['print'] == '1') {
                                                                        // echo "<pre>";
                                                                        // print_r($subscriptionArr);
                                                                        // echo "</pre>";
                                                                        echo "<pre>";
                                                                        print_r($accruals);
                                                                        echo "</pre>";
                                                                    }
                                                                    if( array_key_first( $accruals[ 'accrual' ] ) == '0' ) {
                                                                        $accruals_count = array_filter( $accruals[ 'accrual' ], function ($var) {
                                                                            if( $var[ '@attributes' ][ 'status' ] == 30 ) {
                                                                                return($var);
                                                                            }
                                                                        } );
                                                                        $seat_count = count( $accruals_count );
                                                                    } else {
                                                                        $seat_count = 1;
                                                                    }

                                                                    // Show the button and count here only if the order isn't a subscription with barcode
                                                                    if (!$abbonamento_barcode) {
                                                                    if ($totalaccruals - $seat_count > 0) {
                                                                        echo '<p>'.sprintf(_n('Hai ancora %s posto disponibile', 'Hai ancora %s posti disponibili', $totalaccruals - $seat_count, 'stc-tickets'), $totalaccruals - $seat_count) . '</p>';
                                                                        // echo '<div class="go-to-subscription">';
                                                                        ?>
                                                                            <a class="go-to-subscription-btn" href="<?php echo (is_plugin_active('sitepress-multilingual-cms/sitepress.php') ? rtrim(apply_filters( 'wpml_home_url', get_option( 'home' ) ), '/') : get_site_url()) . "/subscription/?barcode=" . $barcode . "&order_id=" . $order->get_id(); ?>"><?php _e('Seleziona Spettacoli','stc-tickets'); ?></a>
                                                                        <?php
                                                                        // echo '</div>';
                                                                    } 
                                                                    
                                                                    echo '<p>'.sprintf(_n('Hai selezionato %s spettacolo dei %s disponibili', 'Hai selezionato %s spettacoli dei %s disponibili', $seat_count, 'stc-tickets'), $seat_count, $totalaccruals) .'</p>';

                                                                    }
                                                                    // Show subscription seats only if the order is a subscription
                                                                    // don't show the subscription seats if user is adding shows to the subscription with barcode
                                                                    if( $abbonamento && !$abbonamento_barcode ) { ?>
                                                                    <div class="subscription-seats">
                                                                        <?php
                                                                        if( array_key_first( $accruals[ 'accrual' ] ) == '0' ) {
                                                                            foreach ( $accruals[ 'accrual' ] as $accrual_key => $accrual_value ) {
                                                                                $seat_title  = $accrual_value[ '@attributes' ][ 'title' ];
                                                                                $seat_status = $accrual_value[ '@attributes' ][ 'status' ];
                                                                                if( $seat_status == 30 ) {
                                                                                    echo "<p>" . $seat_title . "</p>";
                                                                                }
                                                                            }
                                                                        } else {
                                                                            $seat_title  = $accruals[ 'accrual' ][ '@attributes' ][ 'title' ];
                                                                            $seat_status = $accruals[ 'accrual' ][ '@attributes' ][ 'status' ];
                                                                            if( $seat_status == 30 ) {
                                                                                echo "<p>" . $seat_title . "</p>";
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                <?php } // end if abbonamento barcode
                                                                
                                                                } else if( ! empty( $currrent_subs_seats ) && !$abbonamento_barcode ) {
                                                                    /*<p><?php _e('hai selezionato','stc-tickets'); ?> <?php echo count( $currrent_subs_seats ); ?> <?php _e('spettacolo dei','stc-tickets'); ?> <?php echo $totalaccruals; ?> <?php _e('disponibili','stc-tickets'); ?></p>*/
                                                                    ?>
                                                                    <p><?php echo sprintf(_n('Hai selezionato %s spettacolo dei %s disponibili', 'Hai selezionato %s spettacoli dei %s disponibili', count( $currrent_subs_seats ), 'stc-tickets'), count( $currrent_subs_seats ), $totalaccruals); ?></p>
                                                                    <div class="subscription-seats">
                                                                        <?php
                                                                        foreach ( $currrent_subs_seats as $currrent_subs_key => $currrent_subs_value ) {
                                                                            if( isset( $currrent_subs_value[ 'seat' ] ) ) {
                                                                                $seat_title = $currrent_subs_value[ 'seat' ][ 'description' ];
                                                                                echo "<p>" . $seat_title . "</p>";
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                <?php } else {
                                                                    if( $subscription == 1 ) {
                                                                        echo "<p>" . sprintf(_n('Hai selezionato %s spettacolo dei %s disponibili', 'Hai selezionato %s spettacoli dei %s disponibili', $seat_count, 'stc-tickets'), $seat_count, $totalaccruals) . "</p>";
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <?php
                                                } // end foreach seatObject
                                                }
                                                ?>
                                            </div>
                                            <?php
                                            // Show the barcode here only if the order is a subscription with barcode
                                            if ($abbonamento_barcode) {
                                                $accruals_count = array();
                                                // Check again how many seats the user has selected
                                                $sub_cookie = tempnam ("/tmp", "CURLCOOKIE");
                                                $curl = curl_init();

                                                curl_setopt_array( $curl, array (
                                                    CURLOPT_URL            => API_HOST . 'backend/backend.php?id=SANCARLO&cmd=xmlSubscriptionData&barcode=' . $barcode,
                                                    CURLOPT_RETURNTRANSFER => true,
                                                    CURLOPT_ENCODING       => '',
                                                    CURLOPT_MAXREDIRS      => 10,
                                                    CURLOPT_TIMEOUT        => 0,
                                                    CURLOPT_FOLLOWLOCATION => true,
                                                    CURLOPT_COOKIEJAR => $sub_cookie,
                                                    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                                                    CURLOPT_CUSTOMREQUEST  => 'GET',
                                                ) );

                                                $response = curl_exec( $curl );
                                                if( curl_errno( $curl ) ) {
                                                    $error_msg = curl_error( $curl );
                                                }
                                                curl_close( $curl );
                                                $xml              = simplexml_load_string( $response, 'SimpleXMLElement', LIBXML_NOCDATA );
                                                $subscriptionJson = json_encode( $xml );
                                                $subscriptionArr  = json_decode( $subscriptionJson, TRUE );
                                                $accruals         = array();
                                                $totalaccruals    = 0;
                                                $remainingaccruals = 0;
                                                $seat_count        = 0;

                                                if( ! empty( $subscriptionArr ) && ! isset($subscriptionArr['@attributes']['errcode']) ) {
                                                    $accruals      = $subscriptionArr[ 'subscriptiondata' ][ 'accruals' ];
                                                    $totalaccruals = (int)$subscriptionArr[ 'subscriptiondata' ][ '@attributes' ][ 'numaccruals' ];
                                                }

                                                if( isset($subscriptionArr['@attributes']['errcode']) && $subscriptionArr['@attributes']['errcode'] == '-1' ){
                                                    echo $subscriptionArr['@attributes']['errstring'];
                                                }

                                                if( !isset($subscriptionArr['@attributes']['errcode'])){
                                                    if( ! empty( $accruals ) ) {
                        
                                                        if( array_key_first( $accruals[ 'accrual' ] ) == '0' ) {
                                                            $accruals_count = array_filter( $accruals[ 'accrual' ], function ($var) {
                                                                if( $var[ '@attributes' ][ 'status' ] == 30 ) {
                                                                    return($var);
                                                                }
                                                            } );
                                                            $seat_count = count( $accruals_count );
                                                        } else {
                                                            $accruals_count = $accruals;
                                                            $seat_count = 1;
                                                        }
                                                    }
                                                }
                                                ?>
                                                <div class="barcode" data-barcode="<?php echo $barcode; ?>">
                                                    <p><?php echo __('Codice abbonamento','stc-tickets').": " . $barcode; ?></p>
                                                </div>
                                            <?php 
                                                // Check if totalaccruals is > 0 then the user can add more shows
                                                if( is_array($accruals_count) && $totalaccruals - count( $accruals_count ) > 0 && $abbonamento_barcode ) {
                                                    ?>
                                                    <p><?php echo sprintf(_n('Hai selezionato %s spettacolo dei %s disponibili', 'Hai selezionato %s spettacoli dei %s disponibili', count( $accruals_count ), 'stc-tickets'), count( $accruals_count ), $totalaccruals); ?></p>
                                                    
                                                    <div class="go-to-subscription">
                                                        <a class="go-to-subscription-btn" href="<?php echo (is_plugin_active('sitepress-multilingual-cms/sitepress.php') ? rtrim(apply_filters( 'wpml_home_url', get_option( 'home' ) ), '/') : get_site_url()) . "/subscription/?barcode=" . $barcode . "&order_id=" . $order->get_id(); ?>"><?php _e('Seleziona Spettacoli','stc-tickets'); ?></a>
                                                    </div>
                                                    <?php
                                                }
                                            } else {
                                                // Check if totalaccruals is > 0 then the user can add more shows
                                                $sub_seats_new = isset($currrent_subs_seats) ? $currrent_subs_seats : array();
                                                if( is_array($sub_seats_new) && $totalaccruals - count( $sub_seats_new ) > 0 && $abbonamento_barcode ) {
                                                    ?>
                                                    <p><?php echo sprintf(_n('Hai selezionato %s spettacolo dei %s disponibili', 'Hai selezionato %s spettacoli dei %s disponibili', count( $sub_seats_new ), 'stc-tickets'), count( $sub_seats_new ), $totalaccruals); ?></p>
                                                    
                                                    <div class="go-to-subscription">
                                                        <a class="go-to-subscription-btn" href="<?php echo (is_plugin_active('sitepress-multilingual-cms/sitepress.php') ? rtrim(apply_filters( 'wpml_home_url', get_option( 'home' ) ), '/') : get_site_url()) . "/subscription/?barcode=" . $barcode . "&order_id=" . $order->get_id(); ?>"><?php _e('Seleziona Spettacoli','stc-tickets'); ?></a>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                        } else {
                                            // If the order is a subscription 
                                            ?>
                                            <div class="subscription-seats">
                                                <?php
                                                // dd($seatObject);
                                                // foreach ( $seatObject as $seatObject_key => $seatObject_value ) {
                                                //     $seat_title = isset($seatObject_value[ 'description' ]) ? $seatObject_value[ 'description' ] : '';
                                                //     echo "<p>" . $seat_title . "</p>";
                                                // }
                                                
                                                foreach ($tickets_array_value_v as $ticket) {
                                                    $ticketName = isset($ticket['ticketName']) ? $ticket['ticketName'] : '';
                                                    $showData = isset($ticket['showDate']) ? ' ('.$ticket['showDate'].')' : '';
                                                    $seat_desc = isset($ticket[ 'description' ]) ? $ticket[ 'description' ] : '';
                                                    $seat_title = $ticketName.$showData.'<br>'.$seat_desc;
                                                    if ($ticketName !== '' && $seat_desc !== '') {
                                                        echo "<p>" . $seat_title . "</p>";
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </div>    
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    $contact_method = ob_get_clean();
    if( $contact_method ) {
        ?>
        <tr>
            <th scope="row"><?php _e('Biglietti ordinati','stc-tickets'); ?>:</th>
            <td><?php echo $contact_method; ?></td>
        </tr>
        <?php
    }
}
/*
  function remove_woocommerce_order_details_table_head() {

  if (is_order_received_page()) {
  remove_action('woocommerce_before_order_table', 'woocommerce_order_details_table', 10);
  add_action('woocommerce_order_details_after_order_table', 'custom_order_details_table');
  }
  }

  function custom_order_details_table() {
  // Get the order
  $order = wc_get_order($GLOBALS['wp']->query_vars['order-received']);

  // Output the order details table without the thead
  ?>
  <table class="woocommerce-table woocommerce-table--order-details shop_table order_details">
  <tbody>
  <?php
  foreach ($order->get_items() as $item_id => $item) :
  $product = $item->get_product();
  ?>
  <tr class="woocommerce-table__line-item order_item">
  <td class="woocommerce-table__product-name product-name">
  <?php echo $product->get_name(); ?>
  </td>
  <!-- Add other columns as needed -->
  </tr>
  <?php endforeach; ?>
  </tbody>
  </table>
  <?php
  }

  add_action('woocommerce_order_details_after_order_table', 'custom_order_details_table', 20);
  add_action('init', 'remove_woocommerce_order_details_table_head');
 */

//add_filter( 'woocommerce_order_item_display_meta_key', 'change_order_item_meta_title', 20, 3 );
//
///**
// * Changing a meta title
// * @param  string        $key  The meta key
// * @param  WC_Meta_Data  $meta The meta object
// * @param  WC_Order_Item $item The order item object
// * @return string        The title
// */
//function change_order_item_meta_title( $key, $meta, $item ) {
//    echo "<pre>";
//    print_r($key);
//    print_r($meta);
//    print_r($item);
//    echo "</pre>";
//    // By using $meta-key we are sure we have the correct one.
//    if ( 'confirmedOrderObject' == $meta->key ) { $key = 'SOMETHING'; }
//     
//    return $key;
//}
//
//add_filter( 'woocommerce_order_item_display_meta_value', 'change_order_item_meta_value', 20, 3 );
//
///**
// * Changing a meta value
// * @param  string        $value  The meta value
// * @param  WC_Meta_Data  $meta   The meta object
// * @param  WC_Order_Item $item   The order item object
// * @return string        The title
// */
//function change_order_item_meta_value( $value, $meta, $item ) {
//    
//    echo "<pre>";
//    print_r($key);
//    print_r($meta);
//    print_r($item);
//    echo "</pre>";
//    // By using $meta-key we are sure we have the correct one.
//    if ( 'confirmedOrderObject' == $meta->key ) { $value = 'SOMETHING'; }
//     
//    return $value;
//}
// Save custom order item meta
//add_action( 'woocommerce_checkout_create_order_line_item', 'save_custom_order_item_meta', 10, 4 );
//function save_custom_order_item_meta( $item, $cart_item_key, $values, $order ) {
//    if ( isset($values['file']) && ! empty($values['file']) ) {
//        // Save it in an array to hide meta data from admin order items
//        $item->add_meta_data('file', array( $values['file'] ) );
//    }
//}

/**
 * Get custom order item meta and display a linked download button
 * Note for testing: add ?print=1 to the URL to see the output
 * 
 * @param int $item_id
 * @param object $item
 * @param object $product
 * @return void
 */
add_action( 'woocommerce_after_order_itemmeta', 'display_admin_order_item_custom_button', 10, 3 );
function display_admin_order_item_custom_button($item_id, $item, $product) {
    // Only "line" items and backend order pages
    global $woocommerce;
    $order_id              = $item->get_order_id ();
    $order                 = wc_get_order ( $order_id );
    $confirmedOrderObject  = $order->get_meta ( 'confirmedOrderObject', true, 'view' ); // Get order item meta data (array)
    $transaction_ids       = $order->get_meta ( 'transactionIds', true, 'view' ); // Get order item meta data (array)
    $subs_seat_list        = $order->get_meta ( 'booked_subs_seats', true, 'view' ); // Get order item meta data (array)
    $subscription_order_id = $order->get_meta ( 'subscriptionOrderId', true, 'view' ); // Get order item meta data (array)
    $tickets_array         = array ();
    $abbonamento           = false;
    $abbonamento_with_sub  = false;
    $abbonamento_barcode   = false;
    
    /** MoD sarah */
    if( is_array( $transaction_ids ) && ! empty( $transaction_ids ) ): //Check if $trasaction_id is an array and not empty
        foreach ( $transaction_ids as $transaction_ids_key => $transaction_ids_value ) {
            $zoneId = $transaction_ids_key;
            if( ! isset( $transaction_ids_value[ 'subscription_seat' ] ) ) {
                
                $ticketName     = isset($transaction_ids_value[ 'ticketName' ]) ?  $transaction_ids_value[ 'ticketName' ] : '';
                $zoneName       = isset($transaction_ids_value[ 'zoneName' ]) ?  $transaction_ids_value[ 'zoneName' ] : '';
                $seatObject     = isset($transaction_ids_value[ 'seatObject' ]) ?  $transaction_ids_value[ 'seatObject' ] : array();
                $subscription   = isset($transaction_ids_value[ 'subscription' ]) ?  $transaction_ids_value[ 'subscription' ] : '0';
                $transaction_id = isset($transaction_ids_value[ 'transaction_id' ]) ?  $transaction_ids_value[ 'transaction_id' ] : '';
                $showData       = isset($transaction_ids_value['showDate']) ? $transaction_ids_value['showDate'] : 'prova';

                $tickets_array[ $ticketName ][] = array (
                    'zoneId'       => $transaction_ids_key,
                    'zoneName'     => $zoneName,
                    'seatObject'   => $seatObject,
                    'subscription' => $subscription,
                    'transaction_id' => $transaction_id,
                    'showDate'     => $showData,
                );

                if($subscription == "1"){
                    $abbonamento     = true;
                }
            } else if( isset( $transaction_ids_value[ 'subscription_seat' ] ) && ! empty( $subs_seat_list ) ) {
                foreach ( $transaction_ids_value[ 'subscription_seat' ] as $subscription_seat_key => $subscription_seat_value ) {
                    // Barcode is the key of the sub_seat_list array
                    $barcode = array_key_first($subs_seat_list);
                    // check if this order has been made with a subscription barcode
                    if (empty($subscription_order_id)) {
                        $abbonamento_barcode = true;
                    }

                    if(!$abbonamento || $abbonamento_barcode){
                        $ticketName = $subscription_seat_value[ 'ticketName' ];
                    }

                    if($abbonamento){
                        $abbonamento_with_sub     = true;
                    }
                    $ticketName     = $subscription_seat_value[ 'ticketName' ];
                    $zoneName       = $subscription_seat_value[ 'zoneName' ];
                    $seatObject     = $subscription_seat_value[ 'seatObject' ];
                    $subscription   = $subscription_seat_value[ 'subscription' ];
                    $transaction_id = $subscription_seat_value[ 'transaction_id' ];
                    $showDate       = isset($subscription_seat_value['showDate']) ? $subscription_seat_value['showDate'] : '';
                    $seatObject_new = array(); // MOD SARAH
                    
                    $tickets_array[ $ticketName ][] = array (
                        'zoneId'             => $transaction_ids_key,
                        'zoneName'           => $zoneName,
                        'seatObject'         => $seatObject,
                        'subscription'       => $subscription,
                        'under_subscription' => true,
                        'outher_subscription' => $abbonamento_barcode,
                        'abbonamento'        => $abbonamento_barcode ? $barcode : '',
                        'transaction_id'     => $transaction_id,
                        'showDate'           => $showDate,
                    );
                }
            }
        }
    endif;
    ?>
    <div class="spettacolo-cart-wrapper wc-spettacolo-cart-wrapper ticket-order-table">
        <div class="container">
            <div class="spettacolo-cart-inner">
                <div class="spettacolo-tickets">
                    <?php
                    if( ! empty( $tickets_array ) ) {
                        $counter = 1;
                        foreach ( $tickets_array as $tickets_array_key => $tickets_array_value ) {
                            $extra_class = $counter < count( $tickets_array ) ? 'border-bot' : '';
                            $ticketName  = $tickets_array_key;
                            $showData = is_array($tickets_array_value) && !empty($tickets_array_value) && isset($tickets_array_value[0]['showDate']) ? $tickets_array_value[0]['showDate'] : "";
                            $counter ++;
                            $barcode = '';
                            ?>
                            <div class="ticket-datails-wrap <?php echo $extra_class; ?>">
                                <div class="ticket-title" data-name="<?php echo $ticketName; ?>">
                                    <h2 class="<?php echo ($abbonamento) ? "abbonamento" : "spettacolo"; ?>"><?php echo $ticketName; ?></h2>
                                    <div class="data"><?php echo $showData;?></div>
                                </div>
                                <?php
                                if( ! empty( $tickets_array_value ) ) {
                                    foreach ( $tickets_array_value as $tickets_array_value_k => $tickets_array_value_v ) {
                                        $zoneName           = isset($tickets_array_value_v[ 'zoneName' ]) ? $tickets_array_value_v[ 'zoneName' ] : '';
                                        $zoneId             = isset($tickets_array_value_v[ 'zoneId' ]) ? $tickets_array_value_v[ 'zoneId' ] : '';
                                        $seatObject         = isset($tickets_array_value_v[ 'seatObject' ]) ? $tickets_array_value_v[ 'seatObject' ] : array();
                                        $subscription       = isset($tickets_array_value_v[ 'subscription' ]) ? $tickets_array_value_v[ 'subscription' ] : '';
                                        $under_subscription = isset($tickets_array_value_v[ 'under_subscription' ]) ? $tickets_array_value_v[ 'under_subscription' ] : '';

                                        if(!$abbonamento_with_sub || ($abbonamento_with_sub && !$under_subscription)){
                                            ?>
                                            <div class="ticket-zone">
                                                <div class="zone-title" data-zoneId="<?php echo $zoneId; ?>">
                                                    <h4><?php echo $zoneName; ?></h4>
                                                </div>
                                                <?php
                                                if( ! empty( $seatObject ) ) {
                                                    if( array_key_first( $seatObject ) == '0' ) {
                                                        $seatObject_new = $seatObject;
                                                    } else {
                                                        $seatObject_new = array ( $seatObject );
                                                    }
                                                }
                                                foreach ( $seatObject_new as $seatObject_key => $seatObject_value ) {
                                                    $seat_desc  = $seatObject_value[ 'description' ];
                                                    $seat_price = $seatObject_value[ 'price' ];
                                                    $seat_price = ! empty( $seat_price ) ? (float) $seat_price / 100 : 0;
                                                    if( $under_subscription ) {
                                                        $seat_price = 0;
                                                    }
                                                    $seat_reduction    = $seatObject_value[ 'reduction' ];
                                                    $reduction_id      = $seat_reduction[ '@attributes' ][ 'id' ];
                                                    $reduction_name    = $seat_reduction[ 'description' ];
                                                    $reductionQuantity = 1;
                                                    if( $subscription == 1 ) {
                                                        $barcode = $seatObject_value[ 'barcode' ];
                                                    }
                                                    if( ! empty( $subs_seat_list ) ) {
                                                        if( array_key_first( $subs_seat_list ) == 0 ) {
                                                            $currrent_subs_seats = isset($subs_seat_list[ 0 ][ $barcode ]) ? $subs_seat_list[ 0 ][ $barcode ] : array();
                                                        } else {
                                                            $currrent_subs_seats = isset($subs_seat_list[ $barcode ]) ? $subs_seat_list[ $barcode ] : array();
                                                        }
                                                    }
                                                    ?>
                                                    <div class="zone-reductions">
                                                        <?php
                                                        if( $subscription != 1 ) {
                                                            ?>
                                                            <div class="seat-title" data-reductionId="<?php echo $reduction_id; ?>">
                                                                <p><?php echo preg_replace( '/' . preg_quote( $zoneName, '/' ) . '/', '', $seat_desc, 1 ); ?></p>
                                                            </div>
                                                            <?php
                                                        }
                                                        ?>
                                                        <div class="tipoticketeprezzo">
                                                            <div class="reduction-title-wrap">
                                                                <div class="reduction-title">
                                                                    <p><?php echo $reduction_name; ?></p>
                                                                </div>
                                                                <div class="reduction-qty">
                                                                    <p><?php echo " " . $reductionQuantity; ?></p>
                                                                </div>
                                                            </div>
                                                            <div class="reduction-price">
                                                                <p><?php echo " " . $seat_price . " &euro;"; ?></p>
                                                            </div>
                                                        </div>
                                                        <?php
                                                        if( $barcode !== '' || $subscription == 1 ) {
                                                            $sub_cookie = tempnam ("/tmp", "CURLCOOKIE");
                                                            $curl = curl_init();

                                                            curl_setopt_array( $curl, array (
                                                                CURLOPT_URL            => API_HOST . 'backend/backend.php?id=SANCARLO&cmd=xmlSubscriptionData&barcode=' . $barcode,
                                                                CURLOPT_RETURNTRANSFER => true,
                                                                CURLOPT_ENCODING       => '',
                                                                CURLOPT_MAXREDIRS      => 10,
                                                                CURLOPT_TIMEOUT        => 0,
                                                                CURLOPT_FOLLOWLOCATION => true,
                                                                CURLOPT_COOKIEJAR => $sub_cookie,
                                                                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                                                                CURLOPT_CUSTOMREQUEST  => 'GET',
                                                            ) );

                                                            $response = curl_exec( $curl );
                                                            if( curl_errno( $curl ) ) {
                                                                $error_msg = curl_error( $curl );
                                                            }
                                                            curl_close( $curl );
                                                            $xml                = simplexml_load_string( $response, 'SimpleXMLElement', LIBXML_NOCDATA );
                                                            $subscriptionJson   = json_encode( $xml );
                                                            $subscriptionArr    = json_decode( $subscriptionJson, TRUE );
                                                            $accruals           = array();
                                                            $totalaccruals      = 0;
                                                            $remainingaccruals  = 0;
                                                            
                                                            // test
                                                            if(isset($_GET['print']) && $_GET['print'] == '1') {
                                                                echo "<pre>";
                                                                print_r($subscriptionArr);
                                                                echo "</pre>";
                                                            }

                                                            if( ! empty( $subscriptionArr ) && isset($subscriptionArr['subscriptiondata']) ) {
                                                                $accruals      = $subscriptionArr[ 'subscriptiondata' ][ 'accruals' ];
                                                                $totalaccruals = $subscriptionArr[ 'subscriptiondata' ][ '@attributes' ][ 'numaccruals' ];
                                                            }
                                                            $seat_count = 0;
                                                            if( isset($subscriptionArr['@attributes']['errcode']) && $subscriptionArr['@attributes']['errcode'] == '-1' ){
                                                                echo $subscriptionArr['@attributes']['errstring'];
                                                            }else{
                                                            ?>
                                                                <div class="barcode" data-barcode="<?php echo $barcode; ?>">
                                                                    <p><?php echo __('Codice abbonamento','stc-tickets').": " . $barcode; ?></p>
                                                                </div>
                                                            <?php } } ?>
                                                        <?php
                                                        if( !isset($subscriptionArr['@attributes']['errcode'])){
                                                            if( ! empty( $accruals ) ) {

                                                                // test
                                                                if(isset($_GET['print']) && $_GET['print'] == '1') {
                                                                    // echo "<pre>";
                                                                    //     print_r($subscriptionArr);
                                                                    // echo "</pre>";
                                                                    echo "<pre>";
                                                                        print_r($accruals);
                                                                    echo "</pre>";
                                                                }

                                                                if( array_key_first( $accruals[ 'accrual' ] ) == '0' ) {
                                                                    $accruals_count = array_filter( $accruals[ 'accrual' ], function ($var) {
                                                                        if( $var[ '@attributes' ][ 'status' ] == 30 ) {
                                                                            return($var);
                                                                        }
                                                                    } );
                                                                    $seat_count = count( $accruals_count );
                                                                } else {
                                                                    $seat_count = 1;
                                                                }
                                                                echo sprintf(_n('Hai selezionato %s spettacolo dei %s disponibili', 'Hai selezionato %s spettacoli dei %s disponibili', $seat_count, 'stc-tickets'), $seat_count, $totalaccruals);
                                                                ?>
                                                                <div class="subscription-seats">
                                                                    <?php
                                                                    if( array_key_first( $accruals[ 'accrual' ] ) == '0' ) {
                                                                        foreach ( $accruals[ 'accrual' ] as $accrual_key => $accrual_value ) {
                                                                            $seat_title  = $accrual_value[ '@attributes' ][ 'title' ];
                                                                            $seat_status = $accrual_value[ '@attributes' ][ 'status' ];
                                                                            if( $seat_status == 30 ) {
                                                                                echo "<p>" . $seat_title . "</p>";
                                                                            }
                                                                        }
                                                                    } else {
                                                                        $seat_title  = $accruals[ 'accrual' ][ '@attributes' ][ 'title' ];
                                                                        $seat_status = $accruals[ 'accrual' ][ '@attributes' ][ 'status' ];
                                                                        if( $seat_status == 30 ) {
                                                                            echo "<p>" . $seat_title . "</p>";
                                                                        }
                                                                    }
                                                                    ?>
                                                                </div>
                                                            <?php } else if( ! empty( $currrent_subs_seats ) ) {
                                                                ?>
                                                                <div class="subscription-seats">
                                                                    <?php
                                                                    foreach ( $currrent_subs_seats as $currrent_subs_key => $currrent_subs_value ) {
                                                                        if( isset( $currrent_subs_value[ 'seat' ] ) ) {
                                                                            $seat_title = $currrent_subs_value[ 'seat' ][ 'description' ];
                                                                            echo "<p>" . $seat_title . "</p>";
                                                                        }
                                                                    }
                                                                    ?>
                                                                </div>
                                                            <?php } else {
                                                                if( $subscription == 1 ) {
                                                                    echo sprintf(_n('Hai selezionato %s spettacolo dei %s disponibili', 'Hai selezionato %s spettacoli dei %s disponibili', $seat_count, 'stc-tickets'), $seat_count, $totalaccruals);
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </div>    
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}
/*
 * Show Phone Number field at registration Page
 */
function wooc_extra_register_fields() {
    $country_code  = (isset( $_POST[ 'country_code' ] ) && ! empty( $_POST[ 'country_code' ] )) ? $_POST[ 'country_code' ] : "+39";
    $billing_phone = (isset( $_POST[ 'billing_phone' ] ) && ! empty( $_POST[ 'billing_phone' ] )) ? $_POST[ 'billing_phone' ] : '';
    $dob           = (isset( $_POST[ 'dob' ] ) && ! empty( $_POST[ 'dob' ] )) ? $_POST[ 'dob' ] : '';
    $pob           = (isset( $_POST[ 'place_of_birth' ] ) && ! empty( $_POST[ 'place_of_birth' ] )) ? $_POST[ 'place_of_birth' ] : '';
    $first_name           = (isset( $_POST[ 'first_name' ] ) && ! empty( $_POST[ 'first_name' ] )) ? $_POST[ 'first_name' ] : '';
    $last_name           = (isset( $_POST[ 'last_name' ] ) && ! empty( $_POST[ 'last_name' ] )) ? $_POST[ 'last_name' ] : '';
    ?>
    <p class="form-row form-row-wide">
        <label for="reg_fname"><?php _e( 'Nome *', 'stc-tickets' ); ?></label>
        <input type="text" class="input-text" name="first_name" id="reg_fname" placeholder="<?php esc_html_e( 'Nome', 'stc-tickets' ); ?>" value="<?php esc_attr_e( $first_name ); ?>" />
    </p>
    <p class="form-row form-row-wide">
        <label for="reg_lname"><?php _e( 'Cognome *', 'stc-tickets' ); ?></label>
        <input type="text" class="input-text" name="last_name" id="reg_lname" placeholder="<?php esc_html_e( 'Cognome', 'stc-tickets' ); ?>" value="<?php esc_attr_e( $last_name ); ?>" />
    </p>
    <p class="form-row form-row-wide">
        <label for="reg_billing_phone"><?php _e( 'Telefono *', 'stc-tickets' ); ?></label>
        <input type="text" class="input-text" name="country_code" id="reg_country_code" value="<?php echo $country_code; ?>" disabled/>
        <input type="text" class="input-text" name="billing_phone" id="reg_billing_phone" value="<?php esc_attr_e( $billing_phone ); ?>" />
        <span id="phoneNumberError" style="color:red; display:none;"><?php esc_html_e( 'Invalid phone number', 'stc-tickets' ); ?></span>
    </p>
    <p class="form-row form-row-wide">
        <label for="reg_dob"><?php _e( 'Data di nascita *', 'stc-tickets' ); ?></label>
        <input type="date" class="input-text" name="dob" id="reg_dob" placeholder="GG-MM-AAAA" value="<?php esc_attr_e( $dob ); ?>" />
    </p>
    <p class="form-row form-row-wide">
        <label for="reg_place_of_birth"><?php _e( 'Luogo di nascita *', 'stc-tickets' ); ?></label>
        <input type="text" class="input-text" name="place_of_birth" id="reg_place_of_birth" value="<?php esc_attr_e( $pob ); ?>" />
    </p>
    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide otp-box" style="display:none;">
        <label for="registerotp"><?php esc_html_e( 'OTP', 'stc-tickets' ); ?>&nbsp;<span class="required">*</span></label>
        <input class="woocommerce-Input woocommerce-Input--text input-text" type="text" name="registerotp" id="registerotp" autocomplete="OTP" />
    </p>
    <?php
}
//add_action( 'woocommerce_register_form', 'wooc_extra_register_fields' );

/*
 * Validate Phone Number field at registration Page
 */
function wooc_validate_extra_register_fields($username, $email, $validation_errors) {
    if( isset( $_POST[ 'billing_phone' . FORM_FIELD_CHARS ] ) && empty( $_POST[ 'billing_phone' . FORM_FIELD_CHARS ] ) ) {
        $validation_errors->add( 'billing_phone_error', __( '<strong>Errore</strong>: Numero di telefono obbligatorio', 'stc-tickets' ) );
    }
    if( isset( $_POST[ 'registerotp' ] ) && empty( $_POST[ 'registerotp' ] ) ) {
        $validation_errors->add( 'otp_error', __( '<strong>Errore</strong>: Il codice OTP è obbligatorio', 'stc-tickets' ) );
    }
}
//add_action( 'woocommerce_register_post', 'wooc_validate_extra_register_fields', 10, 3 );
/**
 * Save Phone Number got from registration page
 */
function wooc_save_extra_register_fields($customer_id) {
    if( isset( $_POST[ 'billing_phone' . FORM_FIELD_CHARS ] ) ) {
        // Phone input filed which is used in WooCommerce
        update_user_meta( $customer_id, 'billing_phone', sanitize_text_field( $_POST[ 'billing_phone'. FORM_FIELD_CHARS ] ) );
    }
}
//add_action( 'woocommerce_created_customer', 'wooc_save_extra_register_fields' );
// After registration, logout the user and redirect to home page
function custom_registration_redirect() {
//    wp_logout();
    return (is_plugin_active('sitepress-multilingual-cms/sitepress.php') ? rtrim(apply_filters( 'wpml_home_url', get_option( 'home' ) ), '/') : get_site_url()) . "/mio-account";
}
add_action( 'woocommerce_registration_redirect', 'custom_registration_redirect', 20 );

add_action( 'user_register', 'new_contact', 10, 3 );
function new_contact($user_id) {

    if( isset( $_POST[ 'first_name' ] ) )
        update_user_meta( $user_id, 'first_name', $_POST[ 'first_name' ] );
        update_user_meta( $user_id, 'last_name', $_POST[ 'last_name' ] );

//    $update_user_meta = update_user_meta( $user_id, 'country_code', $_POST[ 'country_code' ] );
//    $update_user_meta = update_user_meta( $user_id, 'billing_phone', $_POST[ 'billing_phone' . FORM_FIELD_CHARS ] );
    $update_user_meta = update_user_meta( $user_id, 'dob', $_POST[ 'dob' ] );
    $update_user_meta = update_user_meta( $user_id, 'place_of_birth', $_POST[ 'place_of_birth' ] );
    $update_user_meta = update_user_meta( $user_id, 'user_ip', $_SERVER[ 'REMOTE_ADDR' ] );
    wp_set_password( $_POST['password'], $user_id);
}

/**
 * Empty the cart after an expiration time
 *
 * @param [type] $cart_item_key
 * @param [type] $cart
 * @return void
 */
function woocommerce_cart_updated($cart_item_key, $cart) {

    $user_id          = get_current_user_id();
    $transactionIds   = get_user_meta( $user_id, 'transactionIds', true );
    $transactions_str = "";
    if( ! empty( $transactionIds ) ) {
        foreach ( $transactionIds as $transactionIds_key => $transactionIds_value ) {
            $transactions_str .= "transactionCode[]=" . $transactionIds_value[ 'transaction_id' ] . "&";
        }
        $transactions_str = rtrim( $transactions_str, "&" );
        $curl_url         = API_HOST . 'backend/backend.php?id=' . APIKEY . '&cmd=setExpiry&' . $transactions_str . '&timeout=0&preserveOnError=1';
        $cart_cookie = tempnam ("/tmp", "CURLCOOKIE");
        $set_expiry_curl = curl_init();

        curl_setopt_array( $set_expiry_curl, array (
            CURLOPT_URL            => $curl_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEJAR => $cart_cookie,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
        ) );

        $set_expiry_response = curl_exec( $set_expiry_curl );
        $err                 = curl_error( $set_expiry_curl );
        curl_close( $set_expiry_curl );

        $xml                 = simplexml_load_string( $set_expiry_response );
        $json                = json_encode( $xml );
        $set_expiry_response = json_decode( $json, TRUE );
    }

    update_user_meta( $user_id, 'addToCartObject', array () );
    update_user_meta( $user_id, 'transactionIds', array () );
    update_user_meta( $user_id, 'subscriptionSeatList', array () );

    $cart->empty_cart();
}

add_action( 'woocommerce_remove_cart_item', 'woocommerce_cart_updated', 99, 2 );

function change_date_time_in_italy($date, $format) {
    $default_timezone = date_default_timezone_get();
    date_default_timezone_set( 'Europe/Rome' );
        
    // Define the language and locale
    
    $locale = vt_get_current_language_code();
//    $locale = 'it_IT';

    // Create an IntlDateFormatter object
    $date_formatter = new IntlDateFormatter( $locale, IntlDateFormatter::LONG, IntlDateFormatter::NONE, 'Europe/Rome', IntlDateFormatter::GREGORIAN );
    // Set the date pattern
    $date_formatter->setPattern( $format );

    // Format the date
    $italian_date = $date_formatter->format( $date );    
    
    // Print the formatted date
    date_default_timezone_set( $default_timezone );
    return $italian_date;
}
/**
 * Show custom user profile fields
 * 
 * @param  object $profileuser A WP_User object
 * @return void
 */
function custom_user_profile_fields($profileuser) {
    $dob             = get_user_meta( $profileuser->ID, 'dob', true );
    $place_of_birth  = get_user_meta( $profileuser->ID, 'place_of_birth', true );
    $user_ip         = get_user_meta( $profileuser->ID, 'user_ip', true );
    $user_registered = get_user_meta( $profileuser->ID, 'user_registered', true );
    $user_modified   = get_user_meta( $profileuser->ID, 'user_modified', true );
    $gender          = get_user_meta( $profileuser->ID, 'gender', true );
    $newsletter      = get_user_meta( $profileuser->ID, 'newsletter', true );
    $marketing       = get_user_meta( $profileuser->ID, 'marketing', true );
    $active          = get_user_meta( $profileuser->ID, 'active', true );
    $old_user_id     = get_user_meta( $profileuser->ID, 'old_user_id', true );
//    echo "<pre>";
//    print_r($_POST);
//    echo "</pre>";
    ?>
    <table class="form-table">
        <tr>
            <th>
                <label for="dob"><?php _e('Data di nascita','stc-tickets'); ?></label>
            </th>
            <td>
                <input type="text" name="dob" id="dob" value="<?php echo $dob; ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th>
                <label for="place_of_birth"><?php _e('Luogo di nascita','stc-tickets'); ?></label>
            </th>
            <td>
                <input type="text" name="place_of_birth" id="place_of_birth" value="<?php echo $place_of_birth; ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th>
                <label for="user_ip"><?php _e('Indirizzo IP di registrazione','stc-tickets'); ?></label>
            </th>
            <td>
                <input type="text" name="user_ip" id="user_ip" value="<?php echo $user_ip; ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th>
                <label for="user_registered"><?php _e('user registered time','stc-tickets'); ?></label>
            </th>
            <td>
                <input type="text" name="user_registered" id="user_registered" value="<?php echo $user_registered; ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th>
                <label for="user_modified"><?php _e('user modified time','stc-tickets'); ?></label>
            </th>
            <td>
                <input type="text" name="user_modified" id="user_modified" value="<?php echo $user_modified; ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th>
                <label for="gender"><?php _e('Gender','stc-tickets'); ?></label>
            </th>
            <td>
                <input type="text" name="gender" id="gender" value="<?php echo $gender; ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th>
                <label for="newsletter"><?php _e('Newsletter','stc-tickets'); ?></label>
            </th>
            <td>
                <input type="text" name="newsletter" id="newsletter" value="<?php echo $newsletter; ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th>
                <label for="marketing"><?php _e('Marketing','stc-tickets'); ?></label>
            </th>
            <td>
                <input type="text" name="marketing" id="marketing" value="<?php echo $marketing; ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th>
                <label for="active"><?php _e('Active user','stc-tickets'); ?></label>
            </th>
            <td>
                <input type="text" name="active" id="active" value="<?php echo $active; ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th>
                <label for="old_user_id"><?php _e('Old user id','stc-tickets'); ?></label>
            </th>
            <td>
                <input type="text" name="old_user_id" id="old_user_id" value="<?php echo $old_user_id; ?>" class="regular-text" />
            </td>
        </tr>
    </table>
    <?php
}
add_action( 'show_user_profile', 'custom_user_profile_fields', 10, 1 );
add_action( 'edit_user_profile', 'custom_user_profile_fields', 10, 1 );

/**
 * Save custom user profile fields.
 *
 * @param User Id $user_id
 */
function stc_custom_user_profile_fields_update( $user_id ) {
    if ( current_user_can( 'edit_user', $user_id ) ) {
        update_user_meta( $user_id, 'dob', sanitize_text_field( $_POST['dob'] ) );
        update_user_meta( $user_id, 'place_of_birth', sanitize_text_field( $_POST['place_of_birth'] ) );
        update_user_meta( $user_id, 'user_ip', sanitize_text_field( $_POST['user_ip'] ) );
        update_user_meta( $user_id, 'user_registered', sanitize_text_field( $_POST['user_registered'] ) );
        update_user_meta( $user_id, 'user_modified', sanitize_text_field( $_POST['user_modified'] ) );
        update_user_meta( $user_id, 'gender', sanitize_text_field( $_POST['gender'] ) );
        update_user_meta( $user_id, 'newsletter', sanitize_text_field( $_POST['newsletter'] ) );
        update_user_meta( $user_id, 'marketing', sanitize_text_field( $_POST['marketing'] ) );
        update_user_meta( $user_id, 'active', sanitize_text_field( $_POST['active'] ) );
        update_user_meta( $user_id, 'old_user_id', sanitize_text_field( $_POST['old_user_id'] ) );
    }
}
add_action( 'personal_options_update', 'stc_custom_user_profile_fields_update' );
add_action( 'edit_user_profile_update', 'stc_custom_user_profile_fields_update' );

add_filter( 'manage_spettacolo_posts_columns', 'manage_spettacolo_posts_columns_fun' );
function manage_spettacolo_posts_columns_fun($columns) {
    $columns = array (
        'cb'        => $columns[ 'cb' ],
        'title'     => __( 'Title' ,'stc-tickets'),
        'shortcode' => __( 'Shortcode' ,'stc-tickets'),
        'date'      => __( 'Date' ,'stc-tickets')
    );
    return $columns;
}
add_action( 'manage_spettacolo_posts_custom_column', 'manage_spettacolo_posts_custom_column_fun', 10, 2 );
function manage_spettacolo_posts_custom_column_fun($column, $post_id) {
    // Image column
    if( 'shortcode' === $column ) {
        echo '[spettacolo_event_listing id="' . $post_id . '"]';
    }
}
// create custom plugin settings menu
add_action( 'admin_menu', 'tickets_plugin_create_menu' );
function tickets_plugin_create_menu() {

    //create new top-level menu
    add_menu_page( 'Tickets Plugin Settings', 'Tickets Settings', 'administrator', __FILE__, 'tickets_plugin_settings_page', 'dashicons-tickets-alt' );

    //call register settings function
    add_action( 'admin_init', 'register_tickets_plugin_settings' );
}
function register_tickets_plugin_settings() {
    //register our settings
    register_setting( 'my-tickets-settings-group', 'select_product' );
}
function tickets_plugin_settings_page() {
    ?>
    <div class="wrap">
        <h1>Tickets</h1>
        <?php
        if( isset( $_POST[ 'wc-ticket-product' ] ) ) {
            update_option( 'wc_ticket_product', $_POST[ 'wc-ticket-product' ] );
        }
        ?>
        <form method="post" action="">
            <?php settings_fields( 'my-tickets-settings-group' ); ?>
            <?php do_settings_sections( 'my-tickets-settings-group' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <th><label for="wc-ticket-product"><?php _e('Choose a Product','stc-tickets'); ?>:</label></th>
                    <td><select name="wc-ticket-product" id="wc-ticket-product">
                            <?php
                            $products = wc_get_products( array ( 'status' => 'publish', 'limit' => -1 ) );
                            foreach ( $products as $product ) {
                                $product_status = $product->get_status();  // Product status
                                $product_id     = $product->get_id();    // Product ID
                                $product_title  = $product->get_title(); // Product title
                                $product_slug   = $product->get_slug(); // Product slug
                                if( $product_status == 'publish' ) {
                                    ?>
                                    <option value="<?php echo $product_id; ?>" data-slug="<?php echo $product_slug; ?>" data-id="<?php echo $product_id; ?>"><?php echo $product_title; ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
function stcticket_spettacolo_data($id) {
    $response = array (
        'titolo'   => '',
        'location' => '',
        'date'     => array ()
    );
    if( ! empty( $id ) ) {
        $post               = get_post( $id );
        $post_title         = ! empty( $post ) ? $post->post_title : '';
        $spe_start_date     = get_post_meta( $id, 'spt_startDate', true );
        $spe_end_date       = get_post_meta( $id, 'spt_endDate', true );
        $curr_start_date    = ! empty( $spe_start_date ) ? explode( "/", $spe_start_date ) : '';
        $final_start_date   = ! empty( $curr_start_date ) ? $curr_start_date[ 1 ] . '/' . $curr_start_date[ 0 ] . '/' . $curr_start_date[ 2 ] : '';
        $curr_end_date      = ! empty( $spe_end_date ) ? explode( "/", $spe_end_date ) : '';
        $final_end_date     = ! empty( $curr_end_date ) ? $curr_end_date[ 1 ] . '/' . $curr_end_date[ 0 ] . '/' . $curr_end_date[ 2 ] : '';
        $final_start_date   = change_date_time_in_italy( strtotime( $final_start_date ), 'dd MMMM y' );
        $final_end_date     = change_date_time_in_italy( strtotime( $final_end_date ), 'dd MMMM y' );
        $spt_vcode          = get_post_meta( $id, 'spt_vcode', true );
        $spt_tit_info_title = get_post_meta( $id, 'spt_tit_info_title', true );
        $tit_info_perform   = ! empty( $spt_tit_info_title[ 'tit_info_perform' ] ) ? $spt_tit_info_title[ 'tit_info_perform' ] : '';
        $spt_location       = ! empty( get_post_meta( $id, 'spt_location', true ) ) ? get_post_meta( $id, 'spt_location', true ) : __('Teatro San Carlo - NAPOLI','stc-tickets');
        $date_array         = array ();
        if( ! empty( $tit_info_perform ) ) {
            foreach ( $tit_info_perform as $tit_info_perform_key => $tit_info_perform_value ) {
                if( count( $tit_info_perform ) > 1 ) {
                    $tit_info_perform_value = $tit_info_perform_value[ '@attributes' ];
                }
                $info_start_date = $tit_info_perform_value[ 'dataInizio' ];
                $info_curr_date  = explode( "/", $info_start_date );
                $info_final_date = $info_curr_date[ 1 ] . '/' . $info_curr_date[ 0 ] . '/' . $info_curr_date[ 2 ];
                $info_final_date = change_date_time_in_italy( strtotime( $info_final_date ), 'EEEE dd MMMM y' );
                $info_time       = ! empty( $tit_info_perform_value[ 'time' ] ) ? $tit_info_perform_value[ 'time' ] : '';
                $cmd             = ! empty( $tit_info_perform_value[ 'cmd' ] ) ? $tit_info_perform_value[ 'cmd' ] : __('prices','stc-tickets');
                $pcode           = ! empty( $tit_info_perform_value[ 'code' ] ) ? $tit_info_perform_value[ 'code' ] : '9654179';
                $regData         = ! empty( $tit_info_perform_value[ 'regData' ] ) ? $tit_info_perform_value[ 'regData' ] : 0;
                array_push( $date_array,
                        array (
                            "date" => str_replace( "/", "-", $info_start_date ) . ' ' . $info_time,
                            //                    "date" => $info_final_date . ' ' . $info_time,
                            "url"  => (is_plugin_active('sitepress-multilingual-cms/sitepress.php') ? rtrim(apply_filters( 'wpml_home_url', get_option( 'home' ) ), '/') : get_site_url()) . '/spettacolo-prices/?cmd=prices&id=' . APIKEY . '&vcode=' . $spt_vcode . '&pcode=' . $pcode . '&postId=' . $id . '&regData=' . $regData . '&selectionMode=0'
                        )
                );
            }
        }
        $response = array (
            'titolo'   => $post_title,
            'location' => $spt_location,
            'date'     => $date_array
        );
    }
    return $response;
}

add_action( 'woocommerce_order_details_after_order_table', 'woocommerce_order_print_fun', 10 );
function woocommerce_order_print_fun($order) {
    ob_start();
//    echo "<pre>";
//    print_r($order);
//    echo "</pre>";
    $order_id                = $order->get_id();
    $user_id                 = $order->get_user_id();
    $order_status            = $order->get_status();
    $orderTransactionCodeArr = get_post_meta( $order->get_id(), 'orderTransactionCodeArr', true );
    $transactionIds          = get_post_meta( $order->get_id(), 'transactionIds', true );
    if( ! empty( $transactionIds ) && $order_status == 'completed' ) {
        ?>
        <h2><?php _e('Stampa biglietti','stc-tickets'); ?></h2>
        <?php // MOD SARAH - Elimino la tabella che crea problemi nella visualizzazione da mobile
        // <table style="text-align:center;">
        //     <thead>
        //         <tr>
        //             <th>
        //                  _e('Spettacolo','stc-tickets'); 
        //             </th>
        //             <th>
        //                  _e('POSTI','stc-tickets'); 
        //             </th>
        //             <th>
        //             </th>
        //         </tr>
        //     </thead>
        //     <tbody>
        ?>
        <div class="recap-wrap">
            
                <?php
                $print_avail = false;
                // test
                if( isset($_GET[ 'print' ]) && $_GET[ 'print' ] == 1 ) {
                    echo "<pre>";
                    print_r( $transactionIds );
                    echo "</pre>";
                }

                foreach ( $transactionIds as $transactionIds_key => $transactionIds_value ) {
                    $ticketName           = '';
                    $zoneName             = '';
                    $orderTransactionCode = '';
                    $regData              = isset($transactionIds_value[ 'regData' ]) ? $transactionIds_value[ 'regData' ] : '1';
                    $transaction_seats_arr = array();

                    if(array_key_first($transactionIds_value) == 'subscription_seat') {
                        foreach ( $transactionIds_value['subscription_seat'] as $tiv_key => $tiv_value ) {
                            $transaction_seats_arr[] = $tiv_value;
                        }
                    } else {
                        $transaction_seats_arr[] = $transactionIds_value;
                    }
                    // Add the print button only if for tickets
                    if(!empty($transaction_seats_arr) && $regData == '1') {
                        foreach ( $transaction_seats_arr as $tsa_key => $tsa_value ) {
//                                if( in_array( $tsa_value[ 'transaction_id' ], $orderTransactionCodeArr ) ) {
                                $ticketName           = isset($tsa_value[ 'ticketName' ]) ? $tsa_value[ 'ticketName' ] : '';
                                $zoneName             = isset($tsa_value[ 'zoneName' ]) ? $tsa_value[ 'zoneName' ] : '';
                                $subscription         = isset($tsa_value[ 'subscription' ]) ? $tsa_value[ 'subscription' ] : '';
                                $orderTransactionCode = isset($tsa_value[ 'transaction_id' ]) ? $tsa_value[ 'transaction_id' ] : '';
                                $orderTransactionUrl  = API_HOST . "/backend/backend.php?id=" . APIKEY . "&cmd=printAtHome&trcode=" . $orderTransactionCode;
//                                }Stampa
                            if( ! empty( $ticketName ) && ! empty( $orderTransactionCode ) && ! empty( $zoneName ) ) {
                                $print_avail = true;
                                ?>
                                <div class="order-print-row-wrap spettacolo flex fwrap content-between" data-tran-id="<?php echo $orderTransactionCode; ?>">
                                    <p><?php _e('Spettacolo','stc-tickets'); ?>: <br> <span><?php echo $ticketName; ?></span></p>
                                    <p><?php _e('POSTI','stc-tickets'); ?>: <br> <span><?php echo $zoneName; ?></span></p>
                                    <p class="order-print wm-100">
                                        <a href="javascript:;" class="bf-btn primary icon-ticket woocommerce-button button order-print-btn" data-order-id='<?php echo $orderTransactionCode; ?>'><?php _e('Stampa biglietti','stc-tickets'); ?></a>
                                    </p>
                                </div>
                                <div class="print-error-msg-wrap" data-tran-id="<?php echo $orderTransactionCode; ?>">
                                    <p class="print-error-msg" style="display:none;"></p>
                                </div>
                                <?php /*
                                    <tr class="order-print-row-wrap" data-tran-id="<?php echo $orderTransactionCode; ?>">
                                        <td>
                                            <?php echo $ticketName; ?>
                                        </td>
                                        <td>
                                            <?php echo $zoneName; ?>
                                        </td>
                                        <td>
                                            <p class="order-print">
                                                <a href="javascript:;" class="button order-print-btn" data-order-id='<?php echo $orderTransactionCode; ?>' target="_blank"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><?php _e('Stampa biglietti','stc-tickets'); ?></font></font></a>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr class="print-error-msg-wrap" data-tran-id="<?php echo $orderTransactionCode; ?>">
                                        <td>
                                            <p class="print-error-msg" style="display:none;"></p>
                                        </td>
                                    </tr>
                                */
                            }
                            
                        }
                    }
                }
                if( ! $print_avail ) {
                    ?>
                <p><?php _e('Il biglietto può essere ritirato in biglietteria','stc-tickets'); ?></p>
                <?php
            }
            ?>
        </tbody>
        </table>
        <?php
    } else if ( $order_status == 'pending' || $order_status == 'processing' || $order_status == 'on-hold' || $order_status == 'cancelled' ) {
        $contact_page = get_page_by_path( 'contatti' );
        $contact_page_link = get_permalink( $contact_page->ID );
        ?>
        <p style="color: red;"><?php wp_kses_post(printf(__('There was an error during order finalization. Please <a href="%s" target="_blank" style="display:inline;color:red;">contact the customer care</a>','stc-tickets'), $contact_page_link) ); ?></p>
        <?php
    }   
    $html = ob_get_clean();
    echo $html;
}
add_action( 'woocommerce_before_cart', 'woocommerce_before_cart_fun', 10 );
function woocommerce_before_cart_fun() {
    ob_start();
    global $woocommerce;
    $cart       = WC()->cart->cart_contents;
    $totalPrice = 0;
    $totalQty   = 0;

    if( ! empty( $cart ) ) {
        foreach ( $cart as $cart_item_key => $cart_item ) {
            $selected_seat_price = $cart_item[ 'selected_seat_price' ][ 0 ];

            // test
            if( isset($_GET[ 'print' ]) && $_GET[ 'print' ] == 1 ) {
                echo "<pre>";
                print_r($selected_seat_price);
                echo"</pre>";
            }

            $transaction_ids     = $cart_item[ 'transaction_ids' ][ 0 ];
            $booked_subs_seats   = $cart_item[ 'booked_subs_seats' ];
            foreach ( $transaction_ids as $transaction_ids_key => $transaction_ids_value ) {
                $zoneExists = false;

                // test
                if( isset($_GET[ 'print' ]) && $_GET[ 'print' ] == 1 ) {
                    echo"<pre>";
                    print_r($transaction_ids_value);
                    echo"</pre>";
                }

                if( is_array( $transaction_ids_value ) && array_key_first( $transaction_ids_value ) == 'subscription_seat' ) {
                    foreach ( $transaction_ids_value[ 'subscription_seat' ] as $subscription_seat_key => $subscription_seat_value ) {
                        $zoneExistsInSubscription = false;
                        $ticketName = $subscription_seat_value[ 'ticketName' ];
                        $zoneName   = $subscription_seat_value[ 'zoneName' ];
                        $zoneId     = $subscription_seat_value[ 'zoneId' ];
                        $timestamp  = $subscription_seat_value[ 'timestamp' ];
                        if( $timestamp < time() ) {
                            if(!empty($selected_seat_price[ $ticketName ])){
                                foreach ( $selected_seat_price[ $ticketName ] as $selected_seat_key => $selected_seat_value ) {
                                    if( $selected_seat_value[ 'zoneName' ] == $zoneName ) {
                                        unset( $selected_seat_price[ $ticketName ][ $selected_seat_key ] );
                                        unset( $transaction_ids_value[ 'subscription_seat' ][ $subscription_seat_key ] );
                                    }else{
                                        $zoneExistsInSubscription = true;
                                    }
                                }
                            }else{
                                $transaction_ids_value = array();
                            }
                        }
                        if($zoneExistsInSubscription){
                            unset( $transaction_ids_value[ 'subscription_seat' ][ $subscription_seat_key ] );
                        }
                    }
                    $transaction_ids[ $transaction_ids_key ] = $transaction_ids_value;
                    if( empty( $transaction_ids[ $transaction_ids_key ][ 'subscription_seat' ] ) ) {
                        $transaction_ids = array ();
                    }
                } else {
                    $ticketName = isset($transaction_ids_value[ 'ticketName' ]) ? $transaction_ids_value[ 'ticketName' ] : '';
                    $zoneName   = isset($transaction_ids_value[ 'zoneName' ]) ? $transaction_ids_value[ 'zoneName' ] : '';
                    $zoneId     = isset($transaction_ids_value[ 'zoneId' ]) ? $transaction_ids_value[ 'zoneId' ] : '';
                    $timestamp  = isset($transaction_ids_value[ 'timestamp' ]) ? $transaction_ids_value[ 'timestamp' ] : '';
                    if( $timestamp != '' && $timestamp < time() ) {
//                        echo "<pre>selected_seat_price_else";
//                        print_r($selected_seat_price);
//                        echo "</pre>";
//                        echo "<pre>";
//                        print_r($transaction_ids);
//                        echo "</pre>";
                        if(!empty($selected_seat_price[ $ticketName ])){
                            foreach ( $selected_seat_price[ $ticketName ] as $selected_seat_key => $selected_seat_value ) {
                                if( $selected_seat_value[ 'zoneName' ] == $zoneName ) {
                                    unset( $selected_seat_price[ $ticketName ][ $selected_seat_key ] );
                                }else{
                                    $zoneExists = true;
                                }
                            }
                        }else{
                            $transaction_ids_value = array();
                        }
                    }
                    if(empty($selected_seat_price[ $ticketName ])){
                        unset( $selected_seat_price[ $ticketName ] );
                    }
                    $transaction_ids[ $transaction_ids_key ] = $transaction_ids_value;
                }
                if($zoneExists){
                    unset( $transaction_ids[ $transaction_ids_key ]);
                }

            }
            if( ! empty( $selected_seat_price ) ) {
                foreach ( $selected_seat_price as $meta_key => $meta_value ) {
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
            $transaction_final_price = 0;
            if( ! empty( $transaction_ids ) ) {
                foreach ( $transaction_ids as $trans_key => $trans_value ) {
                    if(!empty($trans_value) && is_array($trans_value)){
                        /** MOD SARAH **/ // Error in managing subscription seats
                        // dd($trans_value);

                        if(isset($trans_value['seatObject']) && is_array($trans_value['seatObject']) && !empty($trans_value['seatObject']) && array_key_first( $trans_value['seatObject'] ) == 0){
                            foreach($trans_value['seatObject'] as $trans_seat_key => $trans_seat_value){
                                $current_seat_price = (int) $trans_seat_value['price'];
                                $transaction_final_price        = $transaction_final_price + ((int) $current_seat_price / 100);
                            }
                        }else{
                            // if (isset($trans_value['subscription_seat']) && !empty($trans_value['subscription_seat']) && array_key_first( $trans_value['subscription_seat'] ) == 0) {
                            //     foreach ($trans_value['subscription_seat'] as $trans_seat_key => $trans_seat_value) {
                            //         $current_seat_price = (int) $trans_seat_value['seatObject']['price'];
                            //         $transaction_final_price = $transaction_final_price + ((int) $current_seat_price / 100);
                            //     }
                            // } else {
                            //     $seat_price = (int) $trans_value['seatObject']['price'];
                            //     $transaction_final_price = $transaction_final_price + ((int) $seat_price / 100);
                            // }

                            // $seat_price = (int) $trans_value['seatObject']['price'];
                            // $transaction_final_price        = $transaction_final_price + ((int) $seat_price / 100);
                        }
                    }
                }
            }
            if(!empty($transaction_final_price) && $transaction_final_price != 0 && $transaction_final_price != $totalPrice){
                $totalPrice = $transaction_final_price;
            }
            if(empty( $selected_seat_price )){
                $transaction_ids = array();
            }
            // echo "<pre>";
            // print_r($selected_seat_price);
            // echo "</pre>";
            // echo "<pre>";
            // print_r($transaction_ids);
            // echo "</pre>";            
            if(( ! empty( $totalPrice ) && $totalPrice != 0 ) && !empty( $transaction_ids )) {
                $cart_item[ 'data' ]->set_price( $totalPrice );
                $cart_item[ 'selected_seat_price' ][ 0 ]    = $selected_seat_price;
                $cart_item[ 'transaction_ids' ][ 0 ]    = $transaction_ids;
                WC()->cart->cart_contents[ $cart_item_key ] = $cart_item;
                // WC()->cart->set_total($totalPrice);
                WC()->cart->calculate_totals();
            } else {
                if( empty( $booked_subs_seats ) ) {
                    WC()->cart->empty_cart();
                    $user_id = get_current_user_id();
                    update_user_meta( $user_id, 'addToCartObject', array () );
                    update_user_meta( $user_id, 'transactionIds', array () );
                    update_user_meta( $user_id, 'subscriptionSeatList', array () );
                    update_user_meta( $user_id, 'subscriptionOrderId', array () );
                } else {
                    if( empty( $transaction_ids ) ) {
                        WC()->cart->empty_cart();
                        $user_id = get_current_user_id();
                        update_user_meta( $user_id, 'addToCartObject', array () );
                        update_user_meta( $user_id, 'transactionIds', array () );
                        update_user_meta( $user_id, 'subscriptionSeatList', array () );
                        update_user_meta( $user_id, 'subscriptionOrderId', array () );
                    }
                    $cart_item[ 'data' ]->set_price( $totalPrice );
                    if(empty($transaction_ids)){
                        $cart_item[ 'selected_seat_price' ][ 0 ]    = $selected_seat_price;
                        $cart_item[ 'transaction_ids' ][ 0 ]        = $transaction_ids;
                        $cart_item[ 'booked_subs_seats' ]           = array();
                        $cart_item[ 'subscription_order_id' ]       = '';
                    }else{
                        $cart_item[ 'selected_seat_price' ][ 0 ]    = $selected_seat_price;
                        $cart_item[ 'transaction_ids' ][ 0 ]        = $transaction_ids;
                    }
                    WC()->cart->cart_contents[ $cart_item_key ] = $cart_item;
                    WC()->cart->calculate_totals();
                }
            }
        }
    } else {
        $user_id = get_current_user_id();
        update_user_meta( $user_id, 'addToCartObject', array () );
        update_user_meta( $user_id, 'transactionIds', array () );
        update_user_meta( $user_id, 'subscriptionSeatList', array () );
        update_user_meta( $user_id, 'subscriptionOrderId', array () );
    }
    WC()->cart->set_session();
    ?>

    <?php
    $html = ob_get_clean();
    echo $html;
}

add_action( 'woocommerce_before_cart_table', 'woocommerce_before_cart_table_fun', 10 );
function woocommerce_before_cart_table_fun() {
    ob_start();
    global $woocommerce;
    $cart = WC()->cart->cart_contents;
    $finaltimestamp = array(); // MOD SARAH
    if( ! empty( $cart ) ) {
        foreach ( $cart as $cart_item_key => $cart_item ) {
            $selected_seat_price = $cart_item[ 'selected_seat_price' ][ 0 ];
            $transaction_ids     = $cart_item[ 'transaction_ids' ][ 0 ];
            foreach ( $transaction_ids as $transaction_ids_key => $transaction_ids_value ) {
                if( is_array( $transaction_ids_value ) && array_key_first( $transaction_ids_value ) == 'subscription_seat' ) {
                    $timestamp = $transaction_ids_value[ 'subscription_seat' ][ 0 ][ 'timestamp' ];
                } else {
                    $timestamp = isset($transaction_ids_value[ 'timestamp' ]) ? $transaction_ids_value[ 'timestamp' ] : '';
                }
                if( $timestamp != '' && $timestamp > time() ) {
                    $finaltimestamp = $timestamp;
                }
            }
        }
    }
    if( ! empty( $finaltimestamp ) ) {
        ?>
        <div class="timer">
            <span id='timer_count' data-time='<?php echo $finaltimestamp; ?>' class="timetext"></span>
        </div>
        <?php
    }
    $html = ob_get_clean();
    echo $html;
}
add_filter( 'woocommerce_cart_item_removed_title', 'removed_from_cart_title', 12, 2 );
function removed_from_cart_title($message, $cart_item) {
    $items_title_str = "";
    if( ! empty( $cart_item[ 'selected_seat_price' ] ) ) {
        foreach ( $cart_item[ 'selected_seat_price' ][ 0 ] as $cart_item_key => $cart_item_value ) {
            $items_title_str .= $cart_item_key . ", ";
        }
        $items_title_str = rtrim( $items_title_str, ", " );
    }

    if( ! empty( $items_title_str ) ) {
        $message = $items_title_str;
    }
    return $message;
}


add_filter( 'woocommerce_my_account_my_orders_actions', 'modify_view_order_button_url', 999999999999999, 2 );
function modify_view_order_button_url($actions, $order) {
    // test
   if(isset($_GET['print']) && $_GET['print'] == 1) {
       echo "<pre>";
       print_r($actions);
       echo "</pre>";
       die();
   }
    $actions[ 'view' ][ 'url' ] = $order->get_view_order_url();
    return $actions;
}
// add subscription tab to My Account page

// Step 1: Add a new endpoint for the custom tab
function add_custom_endpoint() {
    add_rewrite_endpoint( 'abbonamenti', EP_PAGES );
}
add_action( 'init', 'add_custom_endpoint' );
// Step 2: Add a new tab to the "My Account" dashboard
function add_custom_tab($items) {
    $keyToInsertAfter = 'orders';
    $ites_new         = array ();
    if( ! empty( $items ) ) {
        foreach ( $items as $key => $value ) {
            $ites_new[ $key ] = $value;
            if( $key == 'orders' ) {
                $ites_new[ 'abbonamenti' ] = __( 'Abbonamenti', 'stc-tickets' );
            }
        }
    }
    $ites_new[ 'orders' ] = __( 'Spettacoli', 'stc-tickets' );
//    $position = array_search($keyToInsertAfter, array_keys($items)) + 1;
//    $items_new = array_splice($items, $position, 0, array('abbonamenti' => 'Abbonamenti'));
//    $items['abbonamenti'] = __('Abbonamenti', 'stc-tickets');
    return $ites_new;
}
add_filter( 'woocommerce_account_menu_items', 'add_custom_tab' );
// Step 3: Define content for the custom tab
function custom_tab_content() {
    echo '<span>'. __("Hai un codice di abbonamento da convertire per un carnet acquistato in biglietteria","stc-tickets").'? <a href="' . (is_plugin_active('sitepress-multilingual-cms/sitepress.php') ? rtrim(apply_filters( 'wpml_home_url', get_option( 'home' ) ), '/') : get_site_url()) . '/check-subscription/">'.__('Clicca qui','stc-tickets').'</a></span>';
    echo '<h3>'.__("Abbonamenti","stc-tickets").'</h3>';

    $orders = wc_get_orders( array (
        'customer'    => get_current_user_id(),
        'numberposts' => -1,
        'status'      => array ( 'completed', 'processing', 'on-hold' ) // Adjust status as needed
            ) );

    if( $orders ) {
        $orders_html = "";
        echo '<ul class="abbonamenti">';
        foreach ( $orders as $order ) {
            $order_id          = $order->get_id();
            $order_date        = $order->get_date_created()->format( 'm/d/Y' );            
            // $order_date        = $order->get_date_created()->format( 'M j, Y' );
            $order_total       = wc_price( $order->get_total() );
            $transactionIds    = get_post_meta( $order->get_id(), 'transactionIds', true );
            $booked_subs_seats = get_post_meta( $order->get_id(), 'booked_subs_seats', true );
            $subscription_flag = false;
            $errstring = "";
            $expired = false;

            if( ! empty( $transactionIds ) ) {
                foreach ( $transactionIds as $transactionIds_key => $transactionIds_value ) {
                    if( ! $subscription_flag ) {
                        if( isset($transactionIds_value[ 'subscription' ]) && $transactionIds_value[ 'subscription' ] == '1' ) {
                            $seatObject = $transactionIds_value[ 'seatObject' ];
                            $barcode = $seatObject[ 'barcode' ];
                            $xml_sub_cookie = tempnam ("/tmp", "CURLCOOKIE");
                            $curl = curl_init();

                            curl_setopt_array( $curl, array (
                                CURLOPT_URL            => API_HOST . 'backend/backend.php?id=SANCARLO&cmd=xmlSubscriptionData&barcode=' . $barcode,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING       => '',
                                CURLOPT_MAXREDIRS      => 10,
                                CURLOPT_TIMEOUT        => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_COOKIEJAR => $xml_sub_cookie,
                                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST  => 'GET',
                            ) );

                            $response = curl_exec( $curl );
                            if( curl_errno( $curl ) ) {
                                $error_msg = curl_error( $curl );
                            }
                            curl_close( $curl );
                            $xml              = simplexml_load_string( $response, 'SimpleXMLElement', LIBXML_NOCDATA );
                            $subscriptionJson = json_encode( $xml );
                            $subscriptionArr  = json_decode( $subscriptionJson, TRUE );
                            
                            if(!empty($subscriptionArr)){
                                if(isset($subscriptionArr['@attributes']['errcode']) && $subscriptionArr['@attributes']['errcode'] == "-1"){
                                    $errstring = $subscriptionArr['@attributes']['errstring'];
                                    $expired = true;
                                }else{
                                    $expired = false;
                                }
                            }else{
                                $expired = false;
                            }
                            $subscription_flag = true;
                        }

                        // MOD SARAH - Check if the order contains a subscription seat
                        if (isset($transactionIds_value['subscription_seat']) && !empty($transactionIds_value['subscription_seat'])) {
                            $subscription_flag = true;
                            $barcode = '';
                            
                            if (!empty($booked_subs_seats)) {
                                foreach($booked_subs_seats as $ticketName => $booked_subs_seats_arr){
                                    $barcode = isset($booked_subs_seats_arr[0]['abbonamento']) ? $booked_subs_seats_arr[0]['abbonamento'] : '';
                                }
                            }
                        }
                    }
                }
            }
            if( $subscription_flag ) {
                $order_date_new   = !empty($order_date) ? change_date_time_in_italy( strtotime( esc_html($order_date) ), 'MMMM d, y' ) : '';
                
                // testing
                if(isset($_GET['print']) && $_GET['print'] == '1') {
                    echo "<pre>";
                    print_r($order_date_new);
                    echo "</pre>";
                }

                $orders_html .= '<li><div class="subscription-order-detail"><p>'.__("Abbonamento","stc-tickets").' #' . esc_html( $order_id ) . ' - ' . ucfirst( $order_date_new  ). ' - ' . $order_total . '</p></div> <div class="subscription-orders-table">'.($expired ? '</p></div> <div class="subscription-orders-table"><p class="subscription-view-error error">'. __("Abbonamento non trovato","stc-tickets").'</p>' : '<a href="javascript:void(0);" data-url="'.site_url().'/mio-account/view-order/' . $order_id . '" class="subscription-view-button button alt">'.__("Visualizza","stc-tickets").'</a>').'</div></li>';
            }
        }
        if( ! empty( $orders_html ) ) {
            echo $orders_html;
        } else {
            echo '<p>'.__("Non hai ancora nessun abbonamento nel tuo account","stc-tickets").'.</p>';
        }
        echo '</ul>';
    } else {
        echo '<p>'.__("Non hai ancora nessun abbonamento nel tuo account","stc-tickets").'.</p>';
    }
}
add_action( 'woocommerce_account_abbonamenti_endpoint', 'custom_tab_content' );
// Hook to run when a new order is created and update current order meta into that relative subscription order.

function update_previous_order_meta_from_current_order($order_id) {
    // Get the current order
    $current_order = wc_get_order( $order_id );

    // Get the previous order ID from the current order's meta
    $subscription_order_id = $current_order->get_meta( 'subscriptionOrderId' );
    // Get data from the current order
    $transactionIds = $current_order->get_meta( 'transactionIds', true, 'view' ) !== null ? $current_order->get_meta( 'transactionIds', true, 'view' ) : array();
    // Merge order data for log
    $order_array = array();
    $order_array[$order_id]['transactionIds'] = $transactionIds;
    $order_array[$order_id]['orderdata'] = $current_order;
    $jsonOrderArray = json_encode($order_array);

    $log_message = "current order : " . $current_order;
    error_log( $log_message, 3, WP_CONTENT_DIR . '/order_detail.log' );
    $log_message_2 = "data order : " . $jsonOrderArray;
    error_log( $log_message_2, 3, WP_CONTENT_DIR . '/data_order_detail.log' );

    // Check if a previous order ID is found
    if( $subscription_order_id ) {
        // Get the previous order
        $subscription_order = wc_get_order( $subscription_order_id );

        $prev_booked_subs_seats = $subscription_order->get_meta( 'booked_subs_seats', true, 'view' );
        $prev_transaction_ids   = $subscription_order->get_meta( 'transactionIds', true, 'view' );

        $curr_booked_subs_seats = $current_order->get_meta( 'booked_subs_seats', true, 'view' );
        $curr_transaction_ids   = $current_order->get_meta( 'transactionIds', true, 'view' );

//        $current_cart = WC()->cart->get_cart();
//        foreach($current_cart as $current_cart_key => $current_cart_value){
//            $booked_subs_seats = $current_cart_value['booked_subs_seats'];
//            $transaction_ids = $current_cart_value['transaction_ids'];
//        }

        $final_transaction_ids   = array ();
        $final_booked_subs_seats = array ();

        $final_transaction_ids = recursiveArrayMerge( $prev_transaction_ids, $curr_transaction_ids );
        if( ! empty( $prev_booked_subs_seats ) ) {
            $final_booked_subs_seats = recursiveArrayMerge( $prev_booked_subs_seats, $curr_booked_subs_seats );
        } else {
            $final_booked_subs_seats = $curr_booked_subs_seats;
        }

        // Update your custom meta in the previous order
        $subscription_order->update_meta_data( 'transactionIds', $final_transaction_ids );
        $subscription_order->update_meta_data( 'booked_subs_seats', $final_booked_subs_seats );

        // Save the changes to the previous order
        $subscription_order->save();
        $log_message = "subscription order : " . $subscription_order;
        error_log( $log_message, 3, WP_CONTENT_DIR . '/subscription_order_detail.log' );
    }
//    die();
}
// add_action( 'woocommerce_new_order', 'update_previous_order_meta_from_current_order', 10, 1 );
function recursiveArrayMerge($array1, $array2) {
    if( ! empty( $array2 ) ) {
        foreach ( $array2 as $key => $value ) {
            if( isset( $array1[ $key ] ) && is_array( $value ) && is_array( $array1[ $key ] ) ) {
                $array1[ $key ] = recursiveArrayMerge( $array1[ $key ], $value );
            } else {
                $array1[ $key ] = $value;
            }
        }
    }
    return $array1;
}

function custom_language_selector() {
    // Get the list of active languages and their URLs
    $languages = icl_get_languages('skip_missing=0&orderby=code');

    // Start building the language selector HTML
    $output = '<div class="lang">';
    $output .= '<div class="wpml-ls-statics-shortcode_actions wpml-ls wpml-ls-legacy-dropdown js-wpml-ls-legacy-dropdown">';
    $output .= '<ul>';
    $menu_html = "";
    $sub_menu_html = "";

    foreach ($languages as $language) {
        // Get the URL for the language
        $url = $language['url'];

        // Append the parameters from the current page URL to the language URL
        $url_with_params = add_query_arg($_GET, $url);

        if($language['active']){
            $menu_html .= '<li class="wpml-ls-slot-shortcode_actions wpml-ls-item wpml-ls-item-' . esc_attr($language['language_code']) . ' ' . ($language['active'] ? 'wpml-ls-current-language' : '') . ' wpml-ls-first-item wpml-ls-item-legacy-dropdown">';
            $menu_html .= '<a href="#" class="js-wpml-ls-item-toggle wpml-ls-item-toggle">';
            $menu_html .= '<span class="wpml-ls-native">' . esc_html($language['translated_name']) . '</span>';
            $menu_html .= '</a>';
            
        }else{
            if(!$language['active']){
                $sub_menu_html .= '<li class="wpml-ls-slot-shortcode_actions wpml-ls-item wpml-ls-item-' . esc_attr($language['language_code']) . ' wpml-ls-last-item">';
                $sub_menu_html .= '<a href="' . esc_url($url_with_params) . '" class="wpml-ls-link">';
                $sub_menu_html .= '<span class="wpml-ls-native">' . esc_html($language['translated_name']) . '</span>';
                $sub_menu_html .= '</a>';
                $sub_menu_html .= '</li>';
            }
        }
        
        // Output the list item for each language
//        $output .= '<li class="wpml-ls-slot-shortcode_actions wpml-ls-item wpml-ls-item-' . esc_attr($language['language_code']) . ' ' . ($language['active'] ? 'wpml-ls-current-language' : '') . ' ' . ($language['language_code'] === reset($languages)['language_code'] ? 'wpml-ls-first-item' : '') . '">';
//        $output .= '<a href="' . esc_url($url_with_params) . '" class="wpml-ls-link">';
//        $output .= '<span class="wpml-ls-native">' . esc_html($language['translated_name']) . '</span>';
//        $output .= '</a>';
//        $output .= '</li>';
    }

    // Close the HTML tags
    $output .= $menu_html;
    $output .= '<ul class="wpml-ls-sub-menu">';
    $output .= $sub_menu_html;
    $output .= '</ul>';
    $output .= '</li>';
    $output .= '</ul>';
    $output .= '</div>';
    $output .= '</div>';

    // Output the language selector HTML
    echo $output;
}

add_filter( 'woocommerce_my_account_my_orders_query', 'exclude_orders_with_meta_value', 10, 1 );
function exclude_orders_with_meta_value( $args ) {
    // Add meta query to exclude orders with 'parent_order_id' meta value
//    $args['meta_query'][] = array(
//        'key'     => 'subscriptionOrderId',
//        'compare' => 'NOT EXISTS', // Exclude orders where 'parent_order_id' meta does not exist
//    );
    
    $orders = wc_get_orders( array (
        'customer'    => get_current_user_id(),
        'numberposts' => -1,
//        'status'      => array ( 'all') // Adjust status as needed
        'status'      => array ( 'completed', 'processing', 'under-processing', 'on-hold','pending','cancelled','refunded','failed' ) // Adjust status as needed
    ) );
    
    $exclude_orders = array();
    foreach ( $orders as $order ) {
        $order_id          = $order->get_id();
        $order_date        = $order->get_date_created()->format( 'M j, Y' );
        $order_total       = wc_price( $order->get_total() );
        $subscriptionOrderId    = get_post_meta( $order->get_id(), 'subscriptionOrderId', true );
        $booked_subs_seats    = get_post_meta( $order->get_id(), 'booked_subs_seats', true );
//        echo "<pre>";
//        print_r(get_post_meta( $order->get_id(), 'booked_subs_seats', true ));
//        echo "</pre>";
        if(!empty($subscriptionOrderId) || !empty($booked_subs_seats)){
            $exclude_orders[] = $order_id;
        }
    }
//    echo "<pre>";
//    print_r($exclude_orders);
//    echo "</pre>";
    $args['exclude'] = ! empty( $exclude_orders ) ? $exclude_orders : array( 0 );
    
    return $args;
}
/*
 * Get current language code from WPML
 */
function vt_get_current_language_code($short = false) {
    $locale_code = 'it_IT';
    if( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
        $languages             = apply_filters( 'wpml_active_languages', NULL, 'orderby=id&order=desc' );
        $current_language_code = '';
        if( ! empty( $languages ) ) {
            foreach ( $languages as $lang_code => $language ) {
                if( $language[ 'active' ] ) {
                    if( $short == true ) {
                        $locale_code = $language[ 'language_code' ];
                    } else {
                        $locale_code = $language[ 'default_locale' ];
                    }
                    break;
                }
            }
        }
    } else {
        if( $short == true ) {
            $locale_code = 'it';
        } else {
            $locale_code = 'it_IT';
        }
    }
    if($short == true) {        
        $locale_code = !empty($locale_code) ? $locale_code : 'it';
    } else {        
        $locale_code = !empty($locale_code) ? $locale_code : 'it_IT';
    }
    return $locale_code;
}

// Add a new menu item in the WordPress admin menu
add_action('admin_menu', 'user_csv_import_option_page');

function user_csv_import_option_page() {
  // Add a new top-level menu item
  add_menu_page(
    'User CSV Import Options', // Page title
    'User CSV Import',         // Menu title
    'manage_options',     // Capability (who can access this page)
    'user-csv-import-options', // Menu slug
    'user_csv_import_page'     // Callback function to render the page
  );
}

// Callback function to render the option page content
function user_csv_import_page() {
  ?>
  <div class="wrap">
    <h1>User Import CSV</h1>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data">
      <?php wp_nonce_field('csv_import_action', 'csv_import_nonce'); ?>
      <input type="hidden" name="action" value="csv_import_action">
      <label for="csv_file">Choose a CSV file to import:</label>
      <input type="file" name="csv_file" id="csv_file" accept=".csv">
      <input type="text" name="csv_count" id="csv_count">
      <p><input type="submit" value="Import" class="button button-primary"></p>
    </form>
  </div>
  <?php
}

add_action('admin_post_csv_import_action', 'csv_handle_import_action');

function csv_handle_import_action() {
  // Check if the current user has the required capability to import
  if (!current_user_can('manage_options')) {
    wp_die('You do not have sufficient permissions to access this page.');
  }

  // Verify the nonce for security
  if (!isset($_POST['csv_import_nonce']) || !wp_verify_nonce($_POST['csv_import_nonce'], 'csv_import_action')) {
    wp_die('Security check failed. Please try again.');
  }

  // Check if a file was uploaded
  if (empty($_FILES['csv_file']['tmp_name']) || empty($_POST['csv_count'])) {
    wp_die('Please choose a CSV file to import.');
  }

  // Process the CSV file
    $user_csv_file = $_FILES['csv_file']['tmp_name'];  
 
    $current_page = isset($_POST['csv_count']) ? $_POST['csv_count'] : 1;
    if($current_page == 1){
        $start_point = 1;
    }else{
        $start_point = 1000*($current_page-1);
    }
    $end_point = 1000*$current_page;
    echo "<pre>start_point = ";
    print_r($start_point);
    echo "</pre>";
    echo "<pre>end_point = ";
    print_r($end_point);
    echo "</pre>";
  // user csv import code
    $user_ids = array();
  
    if (($handle = fopen($user_csv_file, "r")) !== FALSE) {
        $c = 0;
        while(($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            if($c != 0 && $c >= $start_point && $c <= $end_point){

                $filteredData = array_filter($data, function ($key) {
                    return $key != '' || $key != null;
                });
                $error_msg = "";

                if(!empty($filteredData)){
                 // Get the data from CSV columns (adjust the array keys according to your CSV)
//                    echo "<pre>";
//                    print_r($data);
//                    echo "</pre>";
                    $user_name = $data[1];
                    $user_surname = $data[2];
                    $user_email = $data[3];
                    $user_dob = $data[4];
                    
                    $user_data = array(
                        "user_id" => $data[0],
                        "firstname" => $data[1],
                        "lastname" => $data[2],
                        "email" => $data[3],
                        "data_di_nascita" => $data[4],
                        "paese" => $data[5],
                        "provincia" => $data[6],
                        "newsletter" => $data[7],
                        "marketing" => $data[8],
                        "gender" => $data[9],
                        "updated_at" => $data[10],
                        "created_at" => $data[11],
                        "active" => $data[12],
                    );

                    $user_email = strtolower($user_email);
                    if( ! empty ( $user_email ) ) {
                        $customer = get_user_by ( 'email', $user_email );
                        if( !empty($customer)) {
                            $user_id = $customer->ID;
    //                        echo "<pre>";
    //                        print_r($customer);
    //                        echo "</pre>";
                        } else {
                            $password = wp_generate_password();
                            $user_id = wp_create_user( $user_email, $password, $user_email );
//                            echo "<pre>";
//                            print_r($user_email);
//                            echo "</pre>";
                        }
                        if ( ! is_wp_error( $user_id ) ) {
                            // User created successfully
                            $user_ids[] = $user_id;
                            update_user_meta( $user_id, 'first_name', $user_name );
                            update_user_meta( $user_id, 'last_name', $user_surname );
                            update_user_meta( $user_id, 'dob', $user_dob );
                            update_user_meta( $user_id, 'user_data', $user_data );
                        } else {
                            // Error creating user
                            $error_msg = $user_id->get_error_message();
                        }
                    }
                }
                if(!empty($error_msg)){
                    echo "<pre>";
                    print_r($error_msg);
                    echo "</pre>";
                }else{
                    echo "<pre>";
                    print_r($user_ids);
                    echo "</pre>";
                }
            }
            $c++;
        }     
    fclose($handle);
    }
    echo "<pre>";
    print_r(count($user_ids));
    echo "</pre>";
    
  // Redirect to the options page after import
//  wp_redirect(admin_url('admin.php?page=user-csv-import-options&imported=1'));
  exit;
}

add_action('admin_notices', 'csv_import_success_message');

function csv_import_success_message() {
  if (isset($_GET['imported']) && $_GET['imported'] === '1') {
    echo '<div class="notice notice-success is-dismissible"><p>CSV file imported successfully.</p></div>';
  }
}

// Step 1: Add a new endpoint for the custom tab
function add_update_phone_endpoint() {
    add_rewrite_endpoint( 'update-phone', EP_PAGES );
}
add_action( 'init', 'add_update_phone_endpoint' );

// Step 2: Add a new tab to the "My Account" dashboard
function add_update_phone_tab($items) {
    $keyToInsertAfter = 'edit-account';
    $ites_new         = array ();
    if( ! empty( $items ) ) {
        foreach ( $items as $key => $value ) {
            $ites_new[ $key ] = $value;
            if( $key == 'edit-account' ) {
                $ites_new[ 'update-phone' ] = __( 'Update Phone', 'stc-tickets' );
            }
        }
    }
//    $position = array_search($keyToInsertAfter, array_keys($items)) + 1;
//    $items_new = array_splice($items, $position, 0, array('abbonamenti' => 'Abbonamenti'));
//    $items['abbonamenti'] = __('Abbonamenti', 'stc-tickets');
    return $ites_new;
}
add_filter( 'woocommerce_account_menu_items', 'add_update_phone_tab' );

// Step 3: Define content for the custom tab
function custom_update_phone_content() {
    echo '<span>' . __( "Hai un codice di abbonamento da convertire per un carnet acquistato in biglietteria", "stc-tickets" ) . '? <a href="' . (is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ? rtrim( apply_filters( 'wpml_home_url', get_option( 'home' ) ), '/' ) : get_site_url()) . '/check-subscription/">' . __( 'Clicca qui', 'stc-tickets' ) . '</a></span>';
    echo '<h3>' . __( "Abbonamenti", "stc-tickets" ) . '</h3>';

    $current_user  = wp_get_current_user();
    $user_id       = $current_user->ID;
    $email         = $current_user->user_email;
    $billing_phone = get_user_meta( $user_id, 'billing_phone', true );
    $country_code  = get_user_meta( $user_id, 'country_code', true );
    $country_code  = ( ! empty( $country_code )) ? $country_code : "+39";

    if( ! empty( $billing_phone ) ) {
        ?>
        <p class="form-row form-row-wide">
        <span class="curr_phone_label"><?php _e( 'current Telefono :', 'stc-tickets' ); ?></span>
        <span class="curr_billing_phone"><?php echo $country_code . $billing_phone; ?></span>
        </p>
        <?php
    }
    ?>
    <form class="update-phone-form">
        <p class="form-row form-row-wide vivaticket-original-field">
            <label for="reg_billing_phone"><?php _e( 'Update Telefono *', 'stc-tickets' ); ?></label>
            <select class="input-text" name="country_code" id="reg_country_code">
                <?php echo do_shortcode( '[get_country_code_options]' ); ?>
            </select>
            <input type="text"  autocomplete="nope"  class="input-text" name="billing_phone" id="reg_billing_phone" value="" />
            <input type="text"  autocomplete="nope" class="input-text" name="reg_email" id="reg_email" value="" hidden="hidden" />            
        </p>
        <p class="upd-phone-msg"><?php  _e('Enter your mobile phone number in the field below to receive a SMS with a 6-digit OTP code to confirm your submission','stc-tickets');?></p>
        <p class="form-row form-row-wide">
            <label for="reg_billing_phone<?php echo FORM_FIELD_CHARS; ?>"><?php _e( 'Update Telefono *', 'stc-tickets' ); ?></label>
            <select class="input-text" name="country_code<?php echo FORM_FIELD_CHARS; ?>" id="reg_country_code<?php echo FORM_FIELD_CHARS; ?>">
                <?php echo do_shortcode( '[get_country_code_options]' ); ?>
            </select>
            <input type="text" autocomplete="nope"  class="input-text" name="billing_phone<?php echo FORM_FIELD_CHARS; ?>" id="reg_billing_phone<?php echo FORM_FIELD_CHARS; ?>" value="<?php esc_attr_e( $billing_phone ); ?>" />
            <input type="text" autocomplete="nope"  class="input-text" name="reg_email<?php echo FORM_FIELD_CHARS; ?>" id="reg_email<?php echo FORM_FIELD_CHARS; ?>" value="<?php esc_attr_e( $email ); ?>" hidden="hidden" />
            <span id="phoneNumberError" style="color:red; display:none;"><?php esc_html_e( 'Invalid phone number', 'stc-tickets' ); ?></span>
        </p>
        <p class="upd-phone-msg otp-msg" style="display:none;"><?php  _e('Enter the OTP code to verify your account and complete your purchase.','stc-tickets');?></p>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide otp-box" style="display:none;">
            <label for="registerotp"><?php esc_html_e( 'OTP', 'stc-tickets' ); ?>&nbsp;<span class="required">*</span></label>
            <input class="woocommerce-Input woocommerce-Input--text input-text"  autocomplete="nope" type="text" name="registerotp" id="registerotp" autocomplete="OTP" />
        </p>
        <p id="otpAttemptsError" style="color:red; display:none;"><?php esc_html_e( "Hai già richiesto l' OTP, attendi 15 minuti prima di riprovare", 'stc-tickets' ); ?></p>
        <!-- Turnstile captcha -->
        <div id="ts-container" class="cf-turnstile" data-sitekey="<?php echo TS_CAPTCHA_DEV_SITE_KEY; ?>"></div>
        <p class="form-row form-row-wide">
            <button class="wp-element-button otp-generate update_phone_otp" name="update_phone_otp" value="otp"><?php esc_html_e( 'Invia OTP', 'stc-tickets' ); ?></button>
            <button class="wp-element-button update_billing_phone" name="update_billing_phone" value="update" style="display:none;"><?php esc_html_e( 'Update', 'stc-tickets' ); ?></button>
        </p>
    </form>
    <?php
}
add_action( 'woocommerce_account_update-phone_endpoint', 'custom_update_phone_content' );

//// Step 1: Add a new endpoint for the custom tab
//function add_update_profile_endpoint() {
//    add_rewrite_endpoint( 'update-profile', EP_PAGES );
//}
//add_action( 'init', 'add_update_profile_endpoint' );
//
//// Step 2: Add a new tab to the "My Account" dashboard
//function add_update_profile_tab($items) {
//    $keyToInsertAfter = 'edit-account';
//    $ites_new         = array ();
//    if( ! empty( $items ) ) {
//        foreach ( $items as $key => $value ) {
//            $ites_new[ $key ] = $value;
//            if( $key == 'edit-account' ) {
//                $ites_new[ 'update-profile' ] = __( 'Update Profile', 'stc-tickets' );
//            }
//        }
//    }
////    $position = array_search($keyToInsertAfter, array_keys($items)) + 1;
////    $items_new = array_splice($items, $position, 0, array('abbonamenti' => 'Abbonamenti'));
////    $items['abbonamenti'] = __('Abbonamenti', 'stc-tickets');
//    return $ites_new;
//}
//add_filter( 'woocommerce_account_menu_items', 'add_update_profile_tab' );
//
//// Step 3: Define content for the custom tab
//function custom_update_profile_content() {
//    echo '<h3>'.__("Profile","stc-tickets").'</h3>';
//
//    $current_user = wp_get_current_user();
//    $user_id =  $current_user->ID;
//    $email =  $current_user->user_email;
//    $billing_phone = get_user_meta ( $user_id, 'billing_phone' , true );
//    $country_code = get_user_meta ( $user_id, 'country_code' , true );
//    $country_code  = (! empty( $country_code )) ? $country_code : "+39";
//    
//}
//add_action( 'woocommerce_account_update-profile_endpoint', 'custom_update_profile_content' );


// Add custom fields to Edit Account form
add_action('woocommerce_edit_account_form', 'add_custom_fields_to_edit_account_form');
function add_custom_fields_to_edit_account_form() {
    $user_id = get_current_user_id();
    $place_of_birth = get_user_meta($user_id, 'place_of_birth', true);
    $date_of_birth = get_user_meta($user_id, 'dob', true);

    // Place of Birth field
    echo '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
    woocommerce_form_field('place_of_birth', array(
        'type' => 'text',
        'class' => array('input-text'),
        'label' => __('Place of Birth', 'woocommerce'),
        'required' => true,
        'value' => $place_of_birth,
    ), $place_of_birth);
    echo '</p>';

    // Date of Birth field
    echo '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
    woocommerce_form_field('dob', array(
        'type' => 'date',
        'class' => array('input-text'),
        'label' => __('Date of Birth', 'woocommerce'),
        'required' => true,
        'value' => $date_of_birth,
    ), $date_of_birth);
    echo '</p>';
    echo '<div id="ts-container" class="cf-turnstile" data-sitekey="'.TS_CAPTCHA_DEV_SITE_KEY.'"></div>';
}

// Avoid change email account details
add_filter('woocommerce_save_account_details_required_fields', 'avoid_change_email_account_details');
function avoid_change_email_account_details($required_fields) {
    // Validate Turnstile captcha

    if ((isset($_POST['cf-turnstile-response']) && empty($_POST['cf-turnstile-response'])) || !isset($_POST['cf-turnstile-response'])) {
        wc_add_notice(__('Please complete the captcha.', 'woocommerce'), 'error');
        return;
    } else {
        $turnstile_response = sanitize_text_field($_POST['cf-turnstile-response']);
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
            wc_add_notice(__('Captcha verification failed. Please try again.', 'woocommerce'), 'error');
            return;

        } elseif (!isset($response['success']) || $response['success'] !== true) {
            wc_add_notice(__('Captcha verification failed. Please try again.', 'woocommerce'), 'error');
            return;
        } else {
            // reCAPTCHA verification passed
        }
    }
}


// Validate and save custom field data when account details are saved
add_action('woocommerce_save_account_details', 'validate_and_save_custom_fields_on_account_save');
function validate_and_save_custom_fields_on_account_save($user_id) {

    if (isset($_POST['place_of_birth']) && !empty($_POST['place_of_birth'])) {
        update_user_meta($user_id, 'place_of_birth', sanitize_text_field($_POST['place_of_birth']));
    } else {
        wc_add_notice(__('Place of Birth is required.', 'woocommerce'), 'error');
    }

    if (isset($_POST['dob']) && !empty($_POST['dob'])) {
        update_user_meta($user_id, 'dob', sanitize_text_field($_POST['dob']));
    } else {
        wc_add_notice(__('Date of Birth is required.', 'woocommerce'), 'error');
    }
}

/*
 * Add phone number column to user listing
 */
add_action('manage_users_columns','account_verification_status_column');
function account_verification_status_column($column_headers) {
    unset($column_headers['posts']);

    $column_headers['billing_phone'] = __('Phone Number');

    return $column_headers;
}
add_filter('manage_users_custom_column',  'add_user_column_value', 10, 3);
function add_user_column_value( $value, $column_name, $user_id ){
    if ( 'billing_phone' == $column_name ){
        $phone_no = get_user_meta( $user_id, 'billing_phone', true );                
        $value = $phone_no;
    }

    return $value;
}
// Add custom field to user search
function custom_user_search( $user_query ) {
    global $wpdb;

    if ( isset( $_REQUEST['s'] ) && ! empty( $_REQUEST['s'] ) ) {
        $search = esc_sql( $_REQUEST['s'] );

        // Add your custom field name here (e.g., 'job_title')
        $user_query->query_where .= " OR {$wpdb->users}.ID IN (
            SELECT DISTINCT user_id
            FROM {$wpdb->usermeta}
            WHERE meta_key = 'billing_phone' AND meta_value LIKE '%$search%'
        )";
    }
}
add_action( 'pre_user_query', 'custom_user_search' );

function woocommerce_review_order_before_payment_fun(){
//    echo '<div class="g-recaptcha" data-sitekey="'.CAPTCHA_SITE_KEY.'"></div>';
    echo '<div id="reCaptchDiv"></div>';
}

add_action( 'woocommerce_review_order_before_payment', 'woocommerce_review_order_before_payment_fun' );

add_filter('auto_update_plugin', '__return_false');

/**
 * Riprendo l'ordine creato su Tlite dalle API di Vivaticket, partendo dal codice di pagamento
 * @param type $order_id
 * @param type $payment_code
 * @return boolean
 */

 function retrieve_order_from_vt_tlite_shortcode() {
    $transactionCode = isset( $_GET[ 'transactionCode' ] ) ? $_GET[ 'transactionCode' ] : '';
    $paym_code = isset( $_GET[ 'paym_code' ] ) ? $_GET[ 'paym_code' ] : '';
    $xpp_restat = isset( $_GET[ 'xpp_restat' ] ) ? $_GET[ 'xpp_restat' ] : '';

    if( ! empty( $transactionCode ) && ! empty( $paym_code ) && ! empty( $xpp_restat ) ) {
        $xml_sub_cookie = tempnam( "/tmp", "CURLCOOKIE" );
        $curl = curl_init();

        curl_setopt_array( $curl, array (
            CURLOPT_URL            => API_HOST . 'backend/backend.php?id=SANCARLO&cmd=xmlSubscriptionData&barcode=' . $transactionCode,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEJAR      => $xml_sub_cookie,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
        ) );

        $response = curl_exec( $curl );
        if( curl_errno( $curl ) ) {
            $error_msg = curl_error( $curl );
        }
        curl_close( $curl );
        $xml              = simplexml_load_string( $response, 'SimpleXMLElement', LIBXML_NOCDATA );
        $subscriptionJson = json_encode( $xml );
        $subscriptionArr  = json_decode( $subscriptionJson, TRUE );

        echo '<pre>';
        print_r( $subscriptionArr );
        echo '</pre>';

        if( ! empty( $subscriptionArr ) ) {
            if( isset( $subscriptionArr[ '@attributes' ][ 'errcode' ] ) && $subscriptionArr[ '@attributes' ][ 'errcode' ] == "-1" ) {
                $errstring = $subscriptionArr[ '@attributes' ][ 'errstring' ];
                $expired = true;
            } else {
                $expired = false;
            }
            $subscription_flag = true;
        } else {
            $expired = false;
            $subscription_flag = false;
        }

        if( $subscription_flag ) {
            $order_id = isset($subscriptionArr[ 'order_id' ]) ? $subscriptionArr[ 'order_id' ] : '';
            $transactionIds = array();
            if ($order_id !== '') {
                $order = wc_get_order( $order_id );
                $order_status = $order->get_status();
                $order_total = $order->get_total();
                $order_date = $order->get_date_created()->format( 'M j, Y' );
                $transactionIds = get_post_meta( $order_id, 'transactionIds', true );
            }
            
            $subscription_flag = false;
            $errstring = "";
            $expired = false;

            if( ! empty( $transactionIds ) )
            {
                foreach ( $transactionIds as $transactionIds_key => $transactionIds_value )
                {
                    if( ! $subscription_flag )
                    {
                        if( $transactionIds_value[ 'subscription' ] == '1' )
                        {
                            $seatObject = $transactionIds_value[ 'seatObject' ];
                            $barcode = $seatObject[ 'barcode' ];
                            $xml_sub_cookie = tempnam( "/tmp", "CURLCOOKIE" );
                            $curl = curl_init();

                            curl_setopt_array( $curl, array (
                                CURLOPT_URL            => API_HOST . 'backend/backend.php?id=SANCARLO&cmd=xmlSubscriptionData&barcode=' . $barcode,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING       => '',
                                CURLOPT_MAXREDIRS      => 10,
                                CURLOPT_TIMEOUT        => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_COOKIEJAR      => $xml_sub_cookie,
                                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST  => 'GET',
                            ) );

                            $response = curl_exec( $curl );
                            if( curl_errno( $curl ) )
                            {
                                $error_msg = curl_error( $curl );
                            }
                            curl_close( $curl );
                            $xml              = simplexml_load_string( $response, 'SimpleXMLElement', LIBXML_NOCDATA );
                            $subscriptionJson = json_encode( $xml );
                            $subscriptionArr  = json_decode( $subscriptionJson, TRUE );

                            if( ! empty( $subscriptionArr ) )
                            {
                                if( isset( $subscriptionArr[ '@attributes' ][ 'errcode' ] ) && $subscriptionArr[ '@attributes' ][ 'errcode' ] == "-1" )
                                {
                                    $errstring = $subscriptionArr[ '@attributes' ][ 'errstring' ];
                                    $expired = true;
                                }
                                else
                                {
                                    $expired = false;
                                }
                                $subscription_flag = true;
                            }
                        }
                    }
                }
            }

            if( $subscription_flag )
            {
                $order_date_new = ! empty( $order_date ) ? change_date_time_in_italy( strtotime( esc_html( $order_date ) ), 'MMMM d, y' ) : '';
                $orders_html = '<li><div class="subscription-order-detail"><p>' . __( "Abbonamento", "stc-tickets" ) . ' #' . esc_html( $order_id ) . ' - ' . ucfirst( $order_date_new ) . ' - ' . $order_total . '</p></div> <div class="subscription-orders-table">' . ( $expired ? '</p></div> <div class="subscription-orders-table"><p class="subscription-view-error error">' . __( "Abbonamento non trovato", "stc-tickets" ) . '</p>' : '<a href="javascript:void(0);" data-url="' . site_url() . '/mio-account/view-order/' . $order_id . '" class="subscription-view-button button">' . __( "Visualizza", "stc-tickets" ) . '</a>' ) . '</div></li>';
            }

            if( ! empty( $orders_html ) )
            {
                echo '<ul class="subscription-orders">' . $orders_html . '</ul>';
            }
            else
            {
                echo '<p>' . __( "Non hai ancora nessun abbonamento nel tuo account", "stc-tickets" ) . '.</p>';
            }

            echo '</ul>';
        
        } else {
            echo '<p>' . __( "Non hai ancora nessun abbonamento nel tuo account", "stc-tickets" ) . '.</p>';
        }

    } else {
        echo '<p>' . __( "Aggiungere gli attributi necessari per recuperare l'ordine", "stc-tickets" ) . '.</p>';
    }

 }
add_shortcode( 'retrieve_order_from_vt_tlite', 'retrieve_order_from_vt_tlite_shortcode' );

/**
 * Add custom column to woocommerce order list in admin
 * This column will show if the order is a subscription order or not
 * 
 * @param array $columns
 * @return array
 */
function add_subscription_order_column($columns) {
    $columns['subscription_order'] = __('Ordini con abbonamento', 'stc-tickets');
    return $columns;
}
add_filter('manage_edit-shop_order_columns', 'add_subscription_order_column');

/**
 * Display the value of the custom column in the order list
 * 
 * @param string $column
 */
function display_subscription_order_column($column) {
    global $post;

    if ($column === 'subscription_order') {
        $transactionIds = get_post_meta($post->ID, 'transactionIds', true);
        $subs_seat_list = get_post_meta($post->ID, 'booked_subs_seats', true);
        $is_subscription_order = false;
        $is_barcode_order = false;

        if (!empty($transactionIds)) {
            foreach ($transactionIds as $transactionIds_key => $transactionIds_value) {
                if (isset($transactionIds_value['subscription']) && $transactionIds_value['subscription'] == '1') {
                    $is_subscription_order = true;
                    break;
                }
            }
        }

        if (! empty( $subs_seat_list ) ) {
            $is_barcode_order = true;
        }

        if ($is_subscription_order) {
            echo '<span class="subscription-order">' . __('Abbonamento', 'stc-tickets') . '</span>';
        } elseif ($is_barcode_order) {
            echo '<span class="barcode-order">' . __('Barcode', 'stc-tickets') . '</span>';
        } else {
            echo '<span class="standard-order">' . __('No', 'stc-tickets') . '</span>';
        }
    }
}
add_action('manage_shop_order_posts_custom_column', 'display_subscription_order_column');

// Add filter order list by subscription order
function filter_orders_by_subscription_order() {
    global $typenow;

    if ($typenow === 'shop_order') {
        $selected = isset($_GET['subscription_order']) ? $_GET['subscription_order'] : '';
        $options = array(
            '' => __('Tutti gli ordini', 'stc-tickets'),
            'yes' => __('Ordini con abbonamento', 'stc-tickets'),
            'no' => __('Ordini standard', 'stc-tickets')
        );

        echo '<select name="subscription_order">';
        foreach ($options as $value => $label) {
            echo '<option value="' . $value . '" ' . selected($selected, $value, false) . '>' . $label . '</option>';
        }
        echo '</select>';
    }
}
add_action('restrict_manage_posts', 'filter_orders_by_subscription_order');

// Filter orders by subscription order
function filter_orders_by_subscription_order_query($query) {
    global $pagenow;

    if (is_admin() && $pagenow === 'edit.php' && isset($_GET['subscription_order']) && $_GET['subscription_order'] !== '') {
        $subscription_order = $_GET['subscription_order'];

        if ($subscription_order === 'yes') {
            $subscription = '1';
            $query->set('meta_query', array(
                array(
                    'key' => 'transactionIds',
                    'value' => 'subscription',
                    'compare' => 'LIKE'
                )
            ));
        } elseif ($subscription_order === 'no') {
            $query->set('meta_query', array(
                array(
                    'key' => 'transactionIds',
                    'value' => 'subscription',
                    'compare' => 'NOT LIKE'
                )
            ));
        }
    }
}
add_action('pre_get_posts', 'filter_orders_by_subscription_order_query');

// Check if an order is a subscription order in the order details page
function is_subscription_order($order_id) {
    $transactionIds = get_post_meta($order_id, 'transactionIds', true);
    $is_subscription_order = false;

    if (!empty($transactionIds)) {
        foreach ($transactionIds as $transactionIds_key => $transactionIds_value) {
            if (isset($transactionIds_value['subscription']) && $transactionIds_value['subscription'] == '1') {
                $is_subscription_order = true;
                break;
            }
        }
    }

    return $is_subscription_order;
}

// Add custom field to order details page
function add_subscription_order_field($order) {
    $is_subscription_order = is_subscription_order($order->get_id());

    if ($is_subscription_order) {
        echo '<p><strong>' . __('Ordine con abbonamento:', 'stc-tickets') . '</strong> ' . __('Si', 'stc-tickets') . '</p>';
    } else {
        echo '<p><strong>' . __('Ordine con abbonamento:', 'stc-tickets') . '</strong> ' . __('No', 'stc-tickets') . '</p>';
    }
}
add_action('woocommerce_admin_order_data_after_billing_address', 'add_subscription_order_field', 10, 1);

// Print the order details in the order details page
function order_detail_data() {
    if(isset($_GET['print']) && $_GET['print'] == "1") {
        $order_id = isset($_GET['post']) ? $_GET['post'] : '';
        if(!empty($order_id)) {
            $order = wc_get_order($order_id);
            $order_data = $order->get_data();
            $order_items = $order->get_items();
            $order_total = $order->get_total();
            $order_date = $order->get_date_created()->format('M j, Y');
            $order_status = $order->get_status();
            $transactionIds = get_post_meta($order_id, 'transactionIds', true);
            $selected_seats = get_post_meta($order_id, 'confirmedOrderObject', true);
            $subscriptionOrderId = get_post_meta($order_id, 'subscriptionOrderId', true);
            // $user_order_id = get_post_meta($order_id, 'subscriptionOrderId', true);
            echo '<pre>';
            // print_r($order_data);
            // print_r($order_items);
            // print_r($order_total);
            // print_r($order_date);
            print_r($subscriptionOrderId);
            echo 'transactionIds';
            print_r($transactionIds);
            print_r($selected_seats);
            echo '</pre>';

        }
    }
}
// in woocommerce admin order details page
add_action('woocommerce_admin_order_data_after_order_details', 'order_detail_data');