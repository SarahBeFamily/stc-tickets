<?php
/*
 * Shortcode for location listing
 */
add_shortcode( 'location_listing', 'sptTicket_location_listing_callback' );
function sptTicket_location_listing_callback() {
    ob_start();
    $paged      = (get_query_var( 'paged' )) ? get_query_var( 'paged' ) : 1;
    $list_args  = array (
        'post_type'      => 'location',
        'post_status'    => 'publish',
        'posts_per_page' => '30',
        'paged'          => $paged,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'meta_query'     => array (
            array (
                'key'     => 'location_data',
                'compare' => '!=',
                'value'   => ''
            )
        )
    );
    $list_query = new WP_Query( $list_args );
//    $r = get_option('start_cron_ticket');
//    $rs = get_option('ticket_cur_page');
//    echo "<pre>";
//    print_r($r);
//    echo "</pre>";
//    echo "<pre>";
//    print_r($rs);
//    echo "</pre>";
    ?>
    <div class="show-listing-wrappper">
        <div class="show-listing-wrap">
            <?php
            if( $list_query->have_posts() ) {

                while ( $list_query->have_posts() ) {
                    $list_query->the_post();
                    $sp_id       = get_the_ID();
                    $sp_data     = get_post_meta( $sp_id, 'location_data', true );
                    $sp_data_arr = json_decode( $sp_data, true );
                    if( ! empty( $sp_data_arr ) ) {
                        ?>
                        <div class="show-listing-inner <?php echo $sp_id; ?>">
                            <div>
                                <h4><?php echo $sp_data_arr[ 'nome' ]; ?></h4>
                                <p class="address">
                                    <?php
                                    if( ! empty( $sp_data_arr[ 'indirizzo' ] ) && ! is_array( $sp_data_arr[ 'indirizzo' ] ) ) {
                                        echo $sp_data_arr[ 'indirizzo' ];
                                    }
                                    ?>
                                </p>
                            </div>            
                        </div>
                        <?php
                    }
                }
                ?>
                <div class="pagination-links">
                    <?php
                    echo paginate_links( array (
                        'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
                        'total'        => $list_query->max_num_pages,
                        'current'      => max( 1, get_query_var( 'paged' ) ),
                        'format'       => '?paged=%#%',
                        'show_all'     => false,
                        'type'         => 'plain',
                        'prev_next'    => true,
                        'prev_text'    => sprintf( '<i></i> %1$s', __( 'Prev', 'stc-tickets' ) ),
                        'next_text'    => sprintf( '%1$s <i></i>', __( 'Next', 'stc-tickets' ) ),
                        'add_args'     => false,
                        'add_fragment' => '',
                    ) );
                    ?>
                </div>
            <?php } else {
                ?>
                <p><?php _e('Nessun abbonamento disponibile','stc-tickets'); ?></p>
            <?php }
            ?>
        </div>
    </div>
    <?php
    $html = ob_get_clean();
    return $html;
}
/*
 * spettacoli cart
 */
add_shortcode( 'spettacolo_cart', 'stcTickets_spettacolo_cart_callback' );
function stcTickets_spettacolo_cart_callback() {
    ob_start();
    $user_id        = get_current_user_id();
    $get_user_meta  = get_user_meta( $user_id, 'addToCartObject' );
    $transactionIds = get_user_meta( $user_id, 'transactionIds' );
    $totalPrice     = 0;
    $totalQty       = 0;
//    echo "<pre>";
//    print_r($get_user_meta);
//    echo "</pre>";
    ?>
    <div class="spettacolo-cart-wrapper">
        <div class="container">
            <div class="spettacolo-cart-inner">
                <div class="spettacolo-tickets">
                    <?php
                    if( ! empty( $get_user_meta[ 0 ] ) ) {
                        foreach ( $get_user_meta[ 0 ] as $meta_key => $meta_value ) {
                            $ticket_title = $meta_key;
                            ?>
                            <div class="ticket-title">
                                <h2><?php echo $ticket_title; ?></h2>
                            </div>
                            <?php
                            if( ! empty( $meta_value ) ) {
                                foreach ( $meta_value as $meta_k => $meta_v ) {
                                    $zoneName   = $meta_v[ 'zoneName' ];
                                    $zoneId     = $meta_v[ 'zoneId' ];
                                    $reductions = $meta_v[ 'reductions' ];
                                    ?>
                                    <div class="ticket-zone">
                                        <div class="zone-title" data-zoneId="<?php echo $zoneId; ?>">
                                            <h4><?php echo $zoneName; ?></h4>
                                        </div>
                                        <?php
                                        foreach ( $reductions as $reductions_key => $reductions_value ) {
                                            $reductionName     = $reductions_value[ 'reductionName' ];
                                            $reductionId       = $reductions_value[ 'reductionId' ];
                                            $reductionQuantity = $reductions_value[ 'reductionQuantity' ];
                                            $reductionPrice    = $reductions_value[ 'reductionPrice' ];
                                            $totalPrice        = $totalPrice + (int) $reductionPrice;
                                            $totalQty          = $totalQty + (int) $reductionQuantity;
                                            ?>
                                            <div class="zone-reductions">
                                                <div class="reduction-title" data-reductionId="<?php echo $reductionId; ?>">
                                                    <p><?php echo $reductionName; ?></p>
                                                </div>
                                                <div class="reduction-qty">
                                                    <p><?php echo __('qty','stc-tickets')." : " . $reductionQuantity; ?></p>
                                                </div>
                                                <div class="reduction-price">
                                                    <p><?php echo __('price','stc-tickets')." : " . $reductionPrice . " &euro;"; ?></p>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <?php
                                }
                            }
                        }
                    }
                    ?>
                </div>
                <div class="ticket-total-wrap">
                    <div class="total-price" data-price="<?php echo $totalPrice; ?>">
                        <p><?php echo __('Total Price','stc-tickets')." : " . $totalPrice . " &euro;"; ?></p>
                    </div>
                    <div class="total-qty" data-qty="<?php echo $totalQty; ?>">
                        <p><?php echo __('Total Quantity','stc-tickets')." : " . $totalQty; ?></p>
                    </div>
                </div>
                <div class="checkout-btn cart-page-btn">
                    <button><?php _e('checkout','stc-tickets'); ?></button>
                </div>
                <div class="empty-cart-btn cart-page-btn">
                    <button><?php _e('Empty Cart','stc-tickets'); ?></button>
                </div>
            </div>
        </div>
    </div>
    <?php
    $html = ob_get_clean();
    return $html;
}
/*
 * spettacoli checkout
 */
add_shortcode( 'spettacolo_checkout', 'stcTickets_spettacolo_checkout_callback' );
function stcTickets_spettacolo_checkout_callback() {
    ob_start();
    $user_id       = get_current_user_id();
    $get_user_meta = get_user_meta( $user_id, 'addToCartObject' );
    $totalPrice    = 0;
    $totalQty      = 0;
//    echo "<pre>";
//    print_r($get_user_meta);
//    echo "</pre>";
    ?>
    <div class="spettacolo-cart-wrapper spettacolo-checkout-wrapper">
        <div class="container">
            <div class="spettacolo-cart-inner spettacolo-checkout-inner">
                <div class="spettacolo-tickets">
                    <?php
                    if( ! empty( $get_user_meta[ 0 ] ) ) {
                        foreach ( $get_user_meta[ 0 ] as $meta_key => $meta_value ) {
                            $ticket_title = $meta_key;
                            ?>
                            <div class="ticket-title">
                                <h2><?php echo $ticket_title; ?></h2>
                            </div>
                            <?php
                            if( ! empty( $meta_value ) ) {
                                foreach ( $meta_value as $meta_k => $meta_v ) {
                                    $zoneName   = $meta_v[ 'zoneName' ];
                                    $zoneId     = $meta_v[ 'zoneId' ];
                                    $reductions = $meta_v[ 'reductions' ];
                                    ?>
                                    <div class="ticket-zone">
                                        <div class="zone-title" data-zoneId="<?php echo $zoneId; ?>">
                                            <h4><?php echo $zoneName; ?></h4>
                                        </div>
                                        <?php
                                        foreach ( $reductions as $reductions_key => $reductions_value ) {
                                            $reductionName     = $reductions_value[ 'reductionName' ];
                                            $reductionId       = $reductions_value[ 'reductionId' ];
                                            $reductionQuantity = $reductions_value[ 'reductionQuantity' ];
                                            $reductionPrice    = $reductions_value[ 'reductionPrice' ];
                                            $totalPrice        = $totalPrice + (int) $reductionPrice;
                                            $totalQty          = $totalQty + (int) $reductionQuantity;
                                            ?>
                                            <div class="zone-reductions">
                                                <div class="reduction-title" data-reductionId="<?php echo $reductionId; ?>">
                                                    <p><?php echo $reductionName; ?></p>
                                                </div>
                                                <div class="reduction-qty">
                                                    <p><?php echo __('qty','stc-tickets')." : " . $reductionQuantity; ?></p>
                                                </div>
                                                <div class="reduction-price">
                                                    <p><?php echo __('price','stc-tickets')." : " . $reductionPrice . " &euro;"; ?></p>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <?php
                                }
                            }
                        }
                    }
                    ?>
                </div>
                <div class="ticket-total-wrap">
                    <div class="total-price">
                        <p><?php echo __('Total Price','stc-tickets')." : " . $totalPrice . " &euro;"; ?></p>
                    </div>
                    <div class="total-qty">
                        <p><?php echo __('Total Quantity','stc-tickets')." : " . $totalQty; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    $html = ob_get_clean();
    return $html;
}
/*
 * spettacoli thankyou
 */
add_shortcode( 'spettacolo_thankyou', 'stcTickets_spettacolo_thankyou_callback' );
function stcTickets_spettacolo_thankyou_callback() {
    ob_start();
    $transactionCode = isset( $_GET[ 'transactionCode' ] ) ? $_GET[ 'transactionCode' ] : '';
    $paym_code = isset( $_GET[ 'paym_code' ] ) ? $_GET[ 'paym_code' ] : '';
    $xpp_restat = isset( $_GET[ 'xpp_restat' ] ) ? $_GET[ 'xpp_restat' ] : '';
    if($xpp_restat == 1){
        $user_id         = get_current_user_id();
        if( $user_id == 'undefined' || $user_id == 0 ) {
            return __("No user is looged in",'stc-tickets');
        }
        $addToCartObject            = get_user_meta( $user_id, 'addToCartObject', true );
        $transactionIds             = get_user_meta( $user_id, 'transactionIds', true );
        $transactionCodeArr         = explode( ",", $transactionCode );
        $beforeTransactionIds       = get_user_meta( $user_id, 'finalTransactionIds', true );
        $confirmed_order            = array ();
        $confirmed_order_arr        = array ();
        $confirmed_order_new        = array ();
        $final_confirmed_order_arr  = array ();
        $confirmedOrderObjectBefore = get_user_meta( $user_id, 'finalConfirmedOrder', true );
    //    echo "<pre>";
    //    print_r($addToCartObject);
    //    echo "</pre>";
    //    echo "<pre>";
    //    print_r($transactionIds);
    //    echo "</pre>";
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
            $get_confirmed_order_arr       = get_confirmed_order_arr_fun( $confirmed_order_arr );
            $get_final_confirmed_order_arr = get_confirmed_order_arr_fun( $final_confirmed_order_arr );
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
        update_user_meta( $user_id, 'paym_code', $paym_code );
        $subscriptionSeatList  = get_user_meta ( $user_id, 'subscriptionSeatList',true );
        $subscriptionOrderId  = get_user_meta ( $user_id, 'subscriptionOrderId',true );

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
            $order->payment_complete();
            update_user_meta( $user_id, 'confirmedOrder', $get_confirmed_order_arr );
            $finalTransactionIds = get_user_meta( $user_id, 'finalTransactionIds', true );
            $order->add_meta_data( 'confirmedOrderObject', $get_confirmed_order_arr, true );
            $order->add_meta_data( 'transactionIds', $transactionIds, true );
            $order->add_meta_data( 'orderTransactionCodeArr', $transactionCodeArr, true );
            $order->add_meta_data( 'booked_subs_seats', $subscriptionSeatList, true );
            $order->add_meta_data( 'subscriptionOrderId', $subscriptionOrderId, true );
            $order->update_status( "completed" );
            $cart->empty_cart();
            update_user_meta( $user_id, 'addToCartObject', array () );
            update_user_meta( $user_id, 'transactionIds', array () );
            update_user_meta( $user_id, 'subscriptionSeatList', array () );
            update_user_meta( $user_id, 'subscriptionOrderId', array () );

            // 2024-11-18 mailchimp subscription start
            if(!empty($user_email)) {
                $subscribe_user = stc_mailchimp_subscribe_user($user_email);
                error_log("Subscribe USer to mailchimp for orderid - $order_id.");
                // error_log("response for Subscribe User to mailchimp for orderid - $order_id - " . json_encode( $subscribe_user));
                update_post_meta( $order_id, '_mailchimp_subscribe_attempt', 1 );
                update_post_meta( $order_id, '_mailchimp_subscribe_email', $user_email );
                if($subscribe_user['code'] == '200') {
                    update_post_meta( $order_id, '_mailchimp_subscribe_response', $subscribe_user['data'] );                
                } else if($subscribe_user['code'] == '201') {
                    update_post_meta( $order_id, '_mailchimp_subscribe_already', 1 );
                    update_post_meta( $order_id, '_mailchimp_subscribe_response', $subscribe_user['data'] );
                } else {
                    update_post_meta( $order_id, '_mailchimp_subscribe_error', $subscribe_user['message'] );                
                }
            }
            // 2024-11-18 mailchimp subscription end

            if ($subscriptionOrderId) {
                // Get the previous order
                $subscription_order = wc_get_order($subscriptionOrderId);

                $prev_booked_subs_seats = $subscription_order->get_meta('booked_subs_seats',true,'view');
                $prev_transaction_ids = $subscription_order->get_meta('transactionIds',true,'view');

                $final_transaction_ids = array();
                $final_booked_subs_seats = array();

                $final_transaction_ids = recursiveArrayMerge($prev_transaction_ids, $transactionIds);
                if(!empty($prev_booked_subs_seats)){
                    $final_booked_subs_seats = recursiveArrayMerge($prev_booked_subs_seats, $subscriptionSeatList);
                }else{
                    $final_booked_subs_seats = $subscriptionSeatList;
                }

                // Update your custom meta in the previous order
                $subscription_order->update_meta_data('transactionIds', $final_transaction_ids);
                $subscription_order->update_meta_data('booked_subs_seats', $final_booked_subs_seats);

                // Save the changes to the previous order
                $subscription_order->save();
            }
        }
        $confirmedOrderObject = get_user_meta( $user_id, 'confirmedOrder', true );
        ?>
        <div class="spettacolo-cart-wrapper spettacolo-thank-you-wrapper">
            <div class="container">
                <div class="spettacolo-cart-inner spettacolo-thank-you-inner">
                    <div class="spettacolo-tickets">
                        <?php
                        if( ! empty( $confirmedOrderObject ) ) {
                            // test
                            if(isset($_GET['print']) && $_GET['print'] == '1'){
                                // echo '<pre>';
                                // print_r($order);
                                // echo '</pre>';
                                echo '<pre>';
                                print_r($confirmedOrderObject);
                                echo '</pre>';
                            }
                            foreach ( $confirmedOrderObject as $meta_key => $meta_value ) {
                                $ticket_title = $meta_key;
                                $showDate     = isset($meta_value[ 'showDate' ]) ? $meta_value[ 'showDate' ] : '';
                                ?>
                                <div class="events-wrapper">
                                    <div class="ticket-title">
                                        <h2><?php echo $ticket_title; ?></h2>
                                        <div class="data">
                                            <p><?php echo $showDate; ?></p>
                                        </div>
                                    </div>
                                    <?php
                                    if( ! empty( $meta_value ) ) {
                                        foreach ( $meta_value as $meta_k => $meta_v ) {
                                            $zoneName   = $meta_v[ 'zoneName' ];
                                            $zoneId     = $meta_v[ 'zoneId' ];
                                            $reductions = $meta_v[ 'seats' ];
                                            ?>
                                            <div class="ticket-zone">
                                                <div class="zone-title" data-zoneId="<?php echo $zoneId; ?>">
                                                    <h4><?php echo $zoneName; ?></h4>
                                                </div>
                                                <ul>
                                                    <?php
                                                    foreach ( $reductions as $reductions_key => $reductions_value ) {
                                                        $reductionName     = $reductions_value[ 'reductionName' ];
                                                        $reductionId       = $reductions_value[ 'reductionId' ];
                                                        $reductionQuantity = $reductions_value[ 'reductionQuantity' ];
                                                        $reductionPrice    = $reductions_value[ 'reductionPrice' ];
                                                        ?>

                                                        <li><?php echo $reductionName; ?> <span>(x<?php echo $reductionQuantity; ?>)</span> </li>                                                    
                                                        <?php
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                            <?php
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
    }else{
        ?>
        <div class="spettacolo-cart-wrapper spettacolo-thank-you-wrapper">
            <div class="container">
                <div class="spettacolo-cart-inner spettacolo-thank-you-inner">
                    <p class="thank-you-error" style="font-size: 50px !important; color: black; font-weight: 600;"><?php _e('Si è verificato un problema con il tuo pagamento, ti preghiamo di riprovare','stc-tickets');?></p>
                </div>
            </div>
        </div>
        <style>
            .simple-title {
                display: none;
            }
            section.wpb-content-wrapper {
                padding-top: 100px !important;
            }
        </style>
        <?php
    }
    $html = ob_get_clean();
    return $html;
}
/*
 * my account listing
 */
add_shortcode( 'my_account_listing', 'stcTickets_my_account_listing_callback' );
function stcTickets_my_account_listing_callback() {
    ob_start();
    $user_id              = get_current_user_id();
    $confirmedOrderObject = get_user_meta( $user_id, 'finalConfirmedOrder' );
    ?>
    <div class="spettacolo-cart-wrapper spettacolo-thank-you-wrapper">
        <div class="container">
            <div class="spettacolo-cart-inner spettacolo-thank-you-inner">
                <div class="spettacolo-tickets">
                    <?php
                    if( ! empty( $confirmedOrderObject ) ) {
                        foreach ( $confirmedOrderObject[ 0 ] as $meta_key => $meta_value ) {
                            $ticket_title = $meta_key;
                            ?>
                            <div class="events-wrapper">
                                <div class="ticket-title">
                                    <h2><?php echo $ticket_title; ?></h2>
                                </div>
                                <?php
                                if( ! empty( $meta_value ) ) {
                                    foreach ( $meta_value as $meta_k => $meta_v ) {
                                        $zoneName   = $meta_v[ 'zoneName' ];
                                        $zoneId     = $meta_v[ 'zoneId' ];
                                        $reductions = $meta_v[ 'seats' ];
                                        ?>
                                        <div class="ticket-zone">
                                            <div class="zone-title" data-zoneId="<?php echo $zoneId; ?>">
                                                <h4><?php echo $zoneName; ?></h4>
                                            </div>
                                            <ul>
                                                <?php
                                                foreach ( $reductions as $reductions_key => $reductions_value ) {
                                                    $reductionName     = $reductions_value[ 'reductionName' ];
                                                    $reductionId       = $reductions_value[ 'reductionId' ];
                                                    $reductionQuantity = $reductions_value[ 'reductionQuantity' ];
                                                    $reductionPrice    = $reductions_value[ 'reductionPrice' ];
                                                    ?>

                                                    <li><?php echo $reductionName; ?> <span>(x<?php echo $reductionQuantity; ?>)</span> </li>
                                                    <?php /* ?>
                                                      <div class="zone-reductions">
                                                      <div class="reduction-title" data-reductionId="<?php echo $reductionId; ?>">
                                                      <p><?php echo $reductionName; ?></p>
                                                      </div>
                                                      <div class="reduction-qty">
                                                      <p><?php echo "qty : " . $reductionQuantity; ?></p>
                                                      </div>
                                                      <div class="reduction-price">
                                                      <p><?php echo "price : " . $reductionPrice . " &euro;"; ?></p>
                                                      </div>
                                                      </div>
                                                      <?php */ ?>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                        <?php
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
    $html = ob_get_clean();
    return $html;
}
/*
 * login form
 */
add_shortcode( 'login_form', 'stcTickets_login_form_callback' );
function stcTickets_login_form_callback() {
    // $current_user_ip = $_SERVER['REMOTE_ADDR'];
    // $HTTP_X_REAL_IP = $_SERVER['HTTP_X_REAL_IP'];    
    // $show_form = false;
    // $allowed_user_ips = array(
    //     '103.215.158.90',
    //     '5.95.217.229',
    //     '93.35.199.137',
    //     '47.53.219.91'
    // );
    ob_start();    
    // if(in_array($current_user_ip , $allowed_user_ips) || in_array( $HTTP_X_REAL_IP, $allowed_user_ips ) ) {
    //     $show_form = true;        
    // }
//    if($current_user_ip == '103.215.158.90' || $current_user_ip ==  '5.95.217.229') {
//        $show_form = true;        
//    }
    // if($show_form == true && !is_user_logged_in()) {
    if(!is_user_logged_in()) {
    $user_id       = get_current_user_id();
    ?>
    <div class="u-columns col2-set" id="customer_login">
        <?php /* <div class="u-column1 col-1">
          <h2><?php esc_html_e( 'Login', 'woocommerce' ); ?></h2>

          <form class="woocommerce-form woocommerce-form-login login" method="post">

          <?php do_action( 'woocommerce_login_form_start' ); ?>

          <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
          <label for="username"><?php esc_html_e( 'Username or email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
          <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
          </p>
          <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
          <label for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
          <input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" />
          </p>
          <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide otp-box" style="display:none;">
          <label for="userotp"><?php esc_html_e( 'OTP', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
          <input class="woocommerce-Input woocommerce-Input--text input-text" type="text" name="userotp" id="userotp" autocomplete="OTP" />
          </p>

          <?php do_action( 'woocommerce_login_form' ); ?>

          <p class="form-row">
          <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
          <input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></span>
          </label>
          <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
          <button type="submit" class="woocommerce-button otp-verified-disabled button woocommerce-form-login__submit<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></button>
          </p>
          <p class="woocommerce-LostPassword lost_password">
          <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
          </p>

          <?php do_action( 'woocommerce_login_form_end' ); ?>

          </form>
          </div> */ ?>
        <div class="u-column2 col-2">
            <h2><?php esc_html_e( 'Register', 'stc-tickets' ); ?></h2>
            <p class="introtext"><?php _e('Compila il form con i tuoi dati e crea il tuo account. Potrai modificare o cancellare le informazioni inserite in qualsiasi momento accedendo alla tua area riservata.','stc-tickets'); ?></p>
            <p class="introtext"><?php _e('I campi contrassegnati con * sono obbligatori','stc-tickets'); ?></p>
            <form method="post" autocomplete="off" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >
                <?php
                $country_code  = (isset( $_POST[ 'country_code' ] ) && ! empty( $_POST[ 'country_code' ] )) ? $_POST[ 'country_code' ] : "+39";
                $billing_phone = (isset( $_POST[ 'billing_phone' ] ) && ! empty( $_POST[ 'billing_phone' ] )) ? $_POST[ 'billing_phone' ] : '';
                $billing_phone_original = (isset( $_POST[ 'billing_phone' . FORM_FIELD_CHARS ] ) && ! empty( $_POST[ 'billing_phone' . FORM_FIELD_CHARS ] )) ? $_POST[ 'billing_phone' . FORM_FIELD_CHARS ] : '';
                $dob           = (isset( $_POST[ 'dob' ] ) && ! empty( $_POST[ 'dob' ] )) ? $_POST[ 'dob' ] : '';
                $pob           = (isset( $_POST[ 'place_of_birth' ] ) && ! empty( $_POST[ 'place_of_birth' ] )) ? $_POST[ 'place_of_birth' ] : '';

                do_action( 'woocommerce_register_form_start' );
                ?>
                <div class="form-control-row">
                    <div class="form-control">
                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                            <label for="reg_fname"><?php esc_html_e( 'Nome *', 'stc-tickets' ); ?>&nbsp;</label>
                            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text dev" autocomplete="nope"  name="first_name" id="reg_fname" autocomplete="Nome" value="<?php echo ( ! empty( $_POST[ 'first_name' ] ) ) ? esc_attr( wp_unslash( $_POST[ 'first_name' ] ) ) : ''; ?>" />
                        </p>                
                    </div>
                    <div class="form-control">
                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                            <label for="reg_lname"><?php esc_html_e( 'Cognome *', 'stc-tickets' ); ?>&nbsp;</label>
                            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" autocomplete="nope"  name="last_name" id="reg_lname" autocomplete="Cognome" value="<?php echo ( ! empty( $_POST[ 'last_name' ] ) ) ? esc_attr( wp_unslash( $_POST[ 'last_name' ] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine         ?>
                        </p>
                    </div>
                </div>
                <div class="form-control-row">
<!--                    <div class="form-control">
                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                            <label for="reg_username"><?php esc_html_e( 'Username', 'stc-tickets' ); ?>&nbsp;<span class="required">*</span></label>
                            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text dev" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST[ 'username' ] ) ) ? esc_attr( wp_unslash( $_POST[ 'username' ] ) ) : ''; ?>" />
                        </p>                
                    </div>-->
                    <div class="form-control">
                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                            <label for="reg_email"><?php esc_html_e( 'Email address', 'stc-tickets' ); ?>&nbsp;<span class="required">*</span></label>
                            <input type="email"  autocomplete="nope" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST[ 'email' ] ) ) ? esc_attr( wp_unslash( $_POST[ 'email' ] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine         ?>
                        </p>
                    </div>
                    <div class="form-control">
                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                            <label for="reg_password"><?php esc_html_e( 'Password', 'stc-tickets' ); ?>&nbsp;<span class="required">*</span></label>
                            <input type="password"  autocomplete="nope"  class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" />
                        </p>
                    </div>
                </div>                
                <div class="form-control-row">
                    <div class="form-control">
                        <p class="form-row form-row-wide">
                            <label for="reg_dob"><?php _e( 'Data di nascita *', 'stc-tickets' ); ?></label>
                            <input type="date"  autocomplete="nope"  class="input-text" name="dob" id="reg_dob" placeholder="GG-MM-AAAA" value="<?php esc_attr_e( $dob ); ?>" />
                        </p>
                    </div>
                    <div class="form-control">
                        <p class="form-row form-row-wide">
                            <label for="reg_place_of_birth"><?php _e( 'Luogo di nascita *', 'woocommerce' ); ?></label>
                            <input type="text"  autocomplete="nope"  class="input-text" name="place_of_birth" id="reg_place_of_birth" value="<?php esc_attr_e( $pob ); ?>" />
                        </p>
                    </div>
                </div>
                <?php /*
                <div class="form-control-row vivaticket-original-field">
                    <div class="form-control">
                        <p class="form-row form-row-wide">
                            <label for="reg_billing_phones"><?php _e( 'Telefono *', 'stc-tickets' ); ?></label>
                            <select class="input-text" name="country_code" id="reg_country_code">
                                <?php echo do_shortcode('[get_country_code_options]');?>
                            </select>
                            <input type="text"   autocomplete="nope" class="input-text" autocomplete="nope" name="billing_phone" id="reg_billing_phone" value=""/>
                        </p>                        
                    </div>
                </div>
                <div class="form-control-row form-control-row-phone">
                    <div class="form-control">
                        <p class="form-row form-row-wide">
                            <label for="reg_billing_phone<?php echo FORM_FIELD_CHARS; ?>"><?php _e( 'Telefono *', 'stc-tickets' ); ?></label>
                            <select class="input-text" name="country_code<?php echo FORM_FIELD_CHARS; ?>" id="reg_country_code<?php echo FORM_FIELD_CHARS; ?>">
                                <?php echo do_shortcode('[get_country_code_options]');?>
                            </select>
                            <input type="text" class="input-text" autocomplete="nope" name="billing_phone<?php echo FORM_FIELD_CHARS; ?>" id="reg_billing_phone<?php echo FORM_FIELD_CHARS; ?>" value="<?php esc_attr_e( $billing_phone_original ); ?>"/>
                        </p>
                        <span id="phoneNumberError" style="color:red; display:none;"><?php esc_html_e( 'Invalid phone number', 'stc-tickets' ); ?></span>
                    </div>
                </div>
                 if( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
                  <?php else : ?>
                  <p><?php esc_html_e( 'A link to set a new password will be sent to your email address.', 'woocommerce' ); ?></p>
                  <?php endif; */
                ?>
                <div class="form-control-row">
                    <div class="form-control full-width">
                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide otp-box" style="display:none;">
                            <label for="registerotp"><?php esc_html_e( 'OTP', 'stc-tickets' ); ?>&nbsp;<span class="required">*</span></label>
                            <input class="woocommerce-Input woocommerce-Input--text input-text" type="text" name="registerotp" id="registerotp" autocomplete="OTP" />
                        </p>
                    </div>
                </div>
                <div id="reCaptchDiv"></div>
                <p id="otpAttemptsError" style="color:red; display:none;"><?php esc_html_e( "OTP already requested the code, please wait 15 minutes to try again", 'stc-tickets' ); ?></p>                
                <?php do_action( 'woocommerce_register_form' ); ?>
                <p class="woocommerce-form-row form-row">
                    <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
                    <button class="woocommerce-Button woocommerce-button send-register-otp button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : ''  ); ?>" name="register" value="<?php esc_attr_e( 'Register', 'stc-tickets' ); ?>"><?php esc_html_e( 'Register', 'stc-tickets' ); ?></button>
                    <!--<button class="woocommerce-Button otp-generate woocommerce-button send-register-otp button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : ''  ); ?>" name="register" value="<?php esc_attr_e( 'Register', 'stc-tickets' ); ?>"><?php esc_html_e( 'Invia OTP', 'stc-tickets' ); ?></button>-->
                    <button type="submit" class="woocommerce-Button woocommerce-button button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : ''  ); ?> woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Register', 'stc-tickets' ); ?>" hidden="hidden" style="display: none;"><?php esc_html_e( 'Register', 'stc-tickets' ); ?></button>
                </p>
                <?php do_action( 'woocommerce_register_form_end' ); ?>
            </form>
        </div>
    </div>
    <?php
    }
    $html = ob_get_clean();
    return $html;
}
/*
 * spettacoli event listing
 */
add_shortcode( 'spettacolo_event_listing', 'stcTickets_spettacolo_event_listing_callback' );
function stcTickets_spettacolo_event_listing_callback($atts) {
    ob_start();
    $spe_id             = $atts[ 'id' ];
    $title_show         = isset( $atts[ 'title_show' ] ) ? $atts[ 'title_show' ] : false;
    $spe_title          = get_the_title( $spe_id );
    $spe_start_date     = get_post_meta( $spe_id, 'spt_startDate', true );
    $spe_end_date       = get_post_meta( $spe_id, 'spt_endDate', true );
    $curr_start_date    = ! empty( $spe_start_date ) ? explode( "/", $spe_start_date ) : '';
    $final_start_date   = ! empty( $curr_start_date ) ? $curr_start_date[ 1 ] . '/' . $curr_start_date[ 0 ] . '/' . $curr_start_date[ 2 ] : '';
    $curr_end_date      = ! empty( $spe_end_date ) ? explode( "/", $spe_end_date ) : '';
    $final_end_date     = ! empty( $curr_end_date ) ? $curr_end_date[ 1 ] . '/' . $curr_end_date[ 0 ] . '/' . $curr_end_date[ 2 ] : '';
    $final_start_date   = change_date_time_in_italy( strtotime( $final_start_date ), 'dd MMMM y' );
    $final_end_date     = change_date_time_in_italy( strtotime( $final_end_date ), 'dd MMMM y' );
    $spt_vcode          = get_post_meta( $spe_id, 'spt_vcode', true );
    $spt_tit_info_title = get_post_meta( $spe_id, 'spt_tit_info_title', true );
    $tit_info_perform   = ! empty( $spt_tit_info_title[ 'tit_info_perform' ] ) ? $spt_tit_info_title[ 'tit_info_perform' ] : '';
    $spt_location       = ! empty( get_post_meta( $spe_id, 'spt_location', true ) ) ? get_post_meta( $spe_id, 'spt_location', true ) : __('Teatro San Carlo - NAPOLI','stc-tickets');
    ?>
    <div class="single-date-list-lists">
        <?php
        if( $title_show ) {
            ?>
            <div class="list-title">
                <?php if( ! empty( $spe_title ) ) { ?>
                    <h3><?php echo $spe_title; ?></h3>
                <?php } ?>
            </div>
            <div class="list-location">
                <svg data-v-e1017caa="" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="location-dot" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="svg-inline--fa fa-location-dot fa-fw fa-sm"><path data-v-e1017caa="" fill="currentColor" d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 256c-35.3 0-64-28.7-64-64s28.7-64 64-64s64 28.7 64 64s-28.7 64-64 64z" class=""></path></svg>
                <p><?php echo $spt_location; ?></p>
            </div>
            <div class="list-date">
                <svg data-v-e1017caa="" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="calendar" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-calendar fa-fw fa-sm"><path data-v-e1017caa="" fill="currentColor" d="M96 32V64H48C21.5 64 0 85.5 0 112v48H448V112c0-26.5-21.5-48-48-48H352V32c0-17.7-14.3-32-32-32s-32 14.3-32 32V64H160V32c0-17.7-14.3-32-32-32S96 14.3 96 32zM448 192H0V464c0 26.5 21.5 48 48 48H400c26.5 0 48-21.5 48-48V192z" class=""></path></svg>
                <p><?php echo ( ! empty( $final_start_date ) ? 'Dal ' . $final_start_date : '') . ' ' . ( ! empty( $final_end_date ) ? 'al ' . $final_end_date : ''); ?></p>
            </div>
        <?php } ?>
        <?php
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
                $cmd             = ! empty( $tit_info_perform_value[ 'cmd' ] ) ? $tit_info_perform_value[ 'cmd' ] : 'prices';
                $pcode           = ! empty( $tit_info_perform_value[ 'code' ] ) ? $tit_info_perform_value[ 'code' ] : '9654179';
                $tcode           = ! empty( $tit_info_perform_value[ 'tcode' ] ) ? $tit_info_perform_value[ 'tcode' ] : 'vt0001012';
                $qubsq           = ! empty( $tit_info_perform_value[ 'qubsq' ] ) ? $tit_info_perform_value[ 'qubsq' ] : '330d95af-0cee-45dd-a9e2-253fd0c1d14c';
                $qubsp           = ! empty( $tit_info_perform_value[ 'qubsp' ] ) ? $tit_info_perform_value[ 'qubsp' ] : '6a69f969-9992-45fe-99b2-64989e9c1870';
                $qubsts          = ! empty( $tit_info_perform_value[ 'qubsts' ] ) ? $tit_info_perform_value[ 'qubsts' ] : '1677651753';
                $qubsc           = ! empty( $tit_info_perform_value[ 'qubsc' ] ) ? $tit_info_perform_value[ 'qubsc' ] : 'bestunion';
                $qubse           = ! empty( $tit_info_perform_value[ 'qubse' ] ) ? $tit_info_perform_value[ 'qubse' ] : 'vivaticketserver';
                $qubsrt          = ! empty( $tit_info_perform_value[ 'qubsrt' ] ) ? $tit_info_perform_value[ 'qubsrt' ] : 'Safetynet';
                $qubsh           = ! empty( $tit_info_perform_value[ 'qubsh' ] ) ? $tit_info_perform_value[ 'qubsh' ] : 'da5900342464d50a4c2b75cfe48ecf52';
                $regData         = ! empty( $tit_info_perform_value[ 'regData' ] ) ? $tit_info_perform_value[ 'regData' ] : 0;
                ?>
                <div class="single-date-list">
                    <div class="single-date-left">
                        <?php if( ! empty( $info_final_date ) ) { ?>
                            <p><?php echo $info_final_date . ' ' . $info_time; ?></p>
                        <?php } ?>
                    </div>
                    <div class="single-date-right">
                        <a href="<?php echo (is_plugin_active('sitepress-multilingual-cms/sitepress.php') ? rtrim(apply_filters( 'wpml_home_url', get_option( 'home' ) ), '/') : get_site_url()) . '/spettacolo-prices/?cmd=prices&id=' . APIKEY . '&vcode=' . $spt_vcode . '&pcode=' . $pcode . '&postId=' . $spe_id . '&regData=' . $regData . '&selectionMode=0'; ?>"><button><?php _e('BUY','stc-tickets'); ?></button></a>
                        <?php /* <a href="https://shop.vivaticket.com/it/sell/?cmd=<?php echo $cmd; ?>&pcode=<?php echo $pcode; ?>&tcode=<?php echo $tcode; ?>&qubsq=<?php echo $qubsq; ?>&qubsp=<?php echo $qubsp; ?>&qubsts=<?php echo $qubsts; ?>&qubsc=<?php echo $qubsc; ?>&qubse=<?php echo $qubse; ?>&qubsrt=<?php echo $qubsrt; ?>&qubsh=<?php echo $qubsh; ?>"><button>BUY</button></a> */ ?>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <?php
    $html = ob_get_clean();
    return $html;
}
/*
 * Get Live Users
 */
add_shortcode( 'spettacolo_get_users_live', 'stcTickets_spettacolo_get_users_live_callback' );
function stcTickets_spettacolo_get_users_live_callback($atts) {
    ob_start();
    ?>
    <style>
        .page-header {
            text-align: center;
            padding-top: 20px;
            margin-bottom: 25px;
        }
        form#get-customers-form {
            display: flex;
            justify-content: center;
        }
        #get-customers-form .form-control {
            margin: 0 20px;
        }
        .no-margin{
            margin: 0;
        }
        p {
            text-align: center;
            margin-bottom: 10px;
        }
        .dt-buttons {display: inline-block;}
        #data-table-accept-report_wrapper{padding: 0 20px;}
        .table-responsive.scrollbar.customer-table-wrap {
            max-width: 100%;
            overflow: hidden;
            overflow-x: auto;
        }
    </style>
    <div class="page-wrap">

        <div class="page-header">
            <h3><?php _e('Seleziona la data di inizio e la data di fine','stc-tickets'); ?></h3>
        </div>
        <p class="no-margin"><?php _e('La differenza di data deve essere al massimo di 60 giorni','stc-tickets'); ?></p>
        <form action="" id="get-customers-form">
            <div class="form-control">        
                <label for="fname"><?php _e('Start Date','stc-tickets'); ?>:</label>
                <input type="text" id="start_date" name="start_date" class="form-date-input" autocomplete="off" value=""><br>
            </div>
            <div class="form-control">
                <label for="lname"><?php _e('End date','stc-tickets'); ?>:</label>
                <input type="text" id="end_date" name="end_date" class="form-date-input" autocomplete="off" value=""><br><br>
            </div>
            <div class="form-control">
                <input type="submit" value="Submit">
            </div>
            <div class="form-control">
                <input type="button" name="import_user" id="import_user" value="Import User">
            </div>
        </form>
        <div class="table-responsive scrollbar customer-table-wrap" style="display:none;">
            <p class="tbl-msg"><?php _e("Di seguito è riportato l'elenco dei clienti creati/aggiornati tra le date selezionate",'stc-tickets'); ?></p>
            <table id="data-table-accept-report" class="display">
                <thead>
                    <tr>
                        <th  data-id="count"><?php _e('No','stc-tickets'); ?>.</th>
                        <th  data-id="cusid"><?php _e('Customer Id','stc-tickets'); ?></th>
                        <th  data-id="cusfcode"><?php _e('Code','stc-tickets'); ?></th>
                        <th  data-id="cusfname"><?php _e('First Name','stc-tickets'); ?></th>
                        <th  data-id="cuslname"><?php _e('Last Name','stc-tickets'); ?></th>
                        <th  data-id="cuscompany"><?php _e('Company','stc-tickets'); ?></th>
                        <th  data-id="cusadd1"><?php _e('Address 1','stc-tickets'); ?></th>
                        <th  data-id="cusadd2"><?php _e('Address 2','stc-tickets'); ?></th>
                        <th  data-id="cuscity"><?php _e('City','stc-tickets'); ?></th>
                        <th  data-id="cusstate"><?php _e('State','stc-tickets'); ?></th>
                        <th  data-id="cuscountry"><?php _e('Country','stc-tickets'); ?></th>
                        <th  data-id="cuspcode"><?php _e('Postcode','stc-tickets'); ?></th>
                        <th  data-id="cusmobile"><?php _e('Mobile','stc-tickets'); ?></th>
                        <th  data-id="cusbdate"><?php _e('Birth Date','stc-tickets'); ?></th>
                        <th  data-id="cusemail"><?php _e('Email','stc-tickets'); ?></th>
                        <th  data-id="cusprivacy" class="privacy1"><?php _e('Privacy1','stc-tickets'); ?></th>
                        <th  data-id="cusprivacy" class="privacy2"><?php _e('Privacy2','stc-tickets'); ?></th>
                    </tr>
                </thead>
                <tbody>            
                </tbody>
            </table>
        </div>
    </div>
    <link rel='stylesheet' id='custom-dataTables-theme-css'  href="<?php echo plugin_dir_url( __DIR__ ) . 'assets/css/jquery.dataTables.min.css'; ?>" type='text/css' media='all' />
    <script type='text/javascript' src="<?php echo plugin_dir_url( __DIR__ ) .'assets/js/jquery.dataTables.min.js'; ?>"></script>
    <script type="text/javascript" src="<?php echo plugin_dir_url( __DIR__ ) .'assets/js/dataTables.buttons.min.js'; ?>"></script>
    <script type="text/javascript" src="<?php echo plugin_dir_url( __DIR__ ) .'assets/js/buttons.html5.min.js'; ?>"></script>
    <script>
        jQuery(document).ready(function ($) {
    //    jQuery(document).find('.form-date-input').datepicker();
            jQuery("#start_date").datepicker({
                dateFormat: "dd/mm/yy",
                onSelect: function (selectedDate) {
                    var startDate = jQuery(this).datepicker("getDate");
                    var endDate = new Date(startDate.getTime());
                    endDate.setDate(endDate.getDate() + 60);
    //      console.log(endDate);
                    // Disable dates in end date input before the selected start date
                    jQuery("#end_date").datepicker("option", "minDate", startDate);
                    jQuery("#end_date").datepicker("option", "maxDate", endDate);
                }
            });

            // Initialize Datepicker for end date input
            jQuery("#end_date").datepicker({
                dateFormat: "dd/mm/yy"
            });

            jQuery(document).on('click', '#get-customers-form input[type=submit]', function (event) {
                event.preventDefault();
                var startDate = jQuery(document).find('#get-customers-form #start_date').val();
                var endDate = jQuery(document).find('#get-customers-form #end_date').val();
                jQuery.ajax({
                    url: STCTICKETSPUBLIC.ajaxurl,
                    method: 'post',
                    beforeSend: function () {
                        jQuery("body").css('opacity', '0.2');
                    },
                    data: {
                        action: 'getCustomers',
                        startDate: startDate,
                        endDate: endDate
                    },
                    success: function (data) {
                        jQuery("body").css('opacity', '1');
                        var responseData = JSON.parse(data);
                        var customersData = responseData.message.customersData;
                        var privacyData = responseData.message.privacyData;
                        var rows = customersData.rows.row;
                        var row_html = '';
                        if (rows) {
                            if (jQuery(document).find('#data-table-accept-report').length > 0) {
                                // console.log("test");
                                if ($.fn.DataTable.isDataTable('#data-table-accept-report')) {
                                    // Get the DataTable instance
                                    var dataTableInstance = jQuery(document).find('#data-table-accept-report').DataTable();

                                    // Check if the DataTable instance is not undefined or null
                                    if (dataTableInstance) {
                                        // Destroy the DataTable instance
                                        dataTableInstance.destroy();

                                        // Remove the DataTable element from the DOM
                                        jQuery(document).find('#data-table-accept-report tbody').html('');
                                    //    console.log("test 1");
                                    }
                                }
                            rows.forEach(function (singlerow, index) {
                                var number = index + 1;
                                row_html += '<tr data-count="' + index + '">';
    //                             row_html += '<td data-id="count">-</td>';
    //                             row_html += '<td data-id="cusid">-</td>';                                
    //                             row_html += '<td data-id="cusfcode">-</td>';
    //                             row_html += '<td data-id="cusfname">-</td>';
    //                             row_html += '<td data-id="cuslname">-</td>';
    //                             row_html += '<td data-id="cuscompany">-</td>';
    //                             row_html += '<td data-id="cusadd1">-</td>';
    //                             row_html += '<td data-id="cusadd2">-</td>';
    //                             row_html += '<td data-id="cuscity">-</td>';
    //                             row_html += '<td data-id="cusstate">-</td>';
    //                             row_html += '<td data-id="cuscountry">-</td>';
    //                             row_html += '<td data-id="cuspcode">-</td>';
    //                             row_html += '<td data-id="cusmobile">-</td>';
    //                             row_html += '<td data-id="cusbdate">-</td>';
    //                             row_html += '<td data-id="cusemail">-</td>';
    //                             console.log(singlerow['@attributes'], number, singlerow['@attributes'].cusprivacy);
    // //                                if(singlerow['@attributes'].hasOwnProperty('cusprivacy')){
    //                             if(singlerow['@attributes'].cusprivacy != ''){
                                    
    // //                                    var privacyValues = singlerow['@attributes'];
    //                                 var privacyValues = singlerow['@attributes'].cusprivacy.split(",");
    //                                 var privacy_val_count = 0;
    // //                                    console.log(number, singlerow['@attributes'].cusprivacy);
    //                                 privacyValues.forEach(function (singleprivacy, index) {
    //                                     if(index < 2) {                                            
    //                                         if(singleprivacy.split("=")[1] == '1'){
    //                                             jQuery(document).find('.privacy'+(index+1)).html(privacyData[singleprivacy.split("=")[0]]['description']);
    //                                             row_html += '<td data-id="cusprivacy">' + privacyData[singleprivacy.split("=")[0]]['body'] + '</td>';
    //                                             privacy_val_count++;
    //                                         }
    //                                     }
    //                                 });
    //                                 for(var i = privacy_val_count;i<2;i++) {
    //                                     row_html += '<td data-id="cusprivacy">-</td>';                                    
    //                                 }
    //                             }else{
    //                                 row_html += '<td data-id="cusprivacy">-</td>';
    //                                 row_html += '<td data-id="cusprivacy">-</td>';
    //                             }
                                               
                                row_html += '<td data-id="count">' + number + '</td>';
                                row_html += '<td data-id="cusid">' + singlerow['@attributes'].cusid + '</td>';                                
                                row_html += '<td data-id="cusfcode">' + singlerow['@attributes'].cusfcode + '</td>';
                                row_html += '<td data-id="cusfname">' + singlerow['@attributes'].cusfname + '</td>';
                                row_html += '<td data-id="cuslname">' + singlerow['@attributes'].cuslname + '</td>';
                                row_html += '<td data-id="cuscompany">' + singlerow['@attributes'].cuscompany + '</td>';
                                row_html += '<td data-id="cusadd1">' + singlerow['@attributes'].cusadd1 + '</td>';
                                row_html += '<td data-id="cusadd2">' + singlerow['@attributes'].cusadd2 + '</td>';
                                row_html += '<td data-id="cuscity">' + singlerow['@attributes'].cuscity + '</td>';
                                row_html += '<td data-id="cusstate">' + singlerow['@attributes'].cusstate + '</td>';
                                row_html += '<td data-id="cuscountry">' + singlerow['@attributes'].cuscountry + '</td>';
                                row_html += '<td data-id="cuspcode">' + singlerow['@attributes'].cuspcode + '</td>';
                                row_html += '<td data-id="cusmobile">' + singlerow['@attributes'].cusmobile + '</td>';
                                row_html += '<td data-id="cusbdate">' + singlerow['@attributes'].cusbdate + '</td>';
                                row_html += '<td data-id="cusemail">' + singlerow['@attributes'].cusemail + '</td>';
                                if(singlerow['@attributes'].cusprivacy){
                                    var privacyValues = singlerow['@attributes'].cusprivacy.split(",");
                                    var privacy_val_count = 0;
                                    privacyValues.forEach(function (singleprivacy, index) {
                                        if(index < 2) {   
                                            if(singleprivacy.split("=")[1] == '1'){
                                                // console.log(privacyData[singleprivacy.split("=")[0]]);
                                                
                                                if(typeof privacyData[singleprivacy.split("=")[0]] != 'undefined' && privacyData[singleprivacy.split("=")[0]]['description'] != ''){
                                                    jQuery(document).find('.privacy'+(index+1)).html(privacyData[singleprivacy.split("=")[0]]['description']);
                                                }
                                                if(typeof privacyData[singleprivacy.split("=")[0]] != 'undefined' && privacyData[singleprivacy.split("=")[0]]['body'] != ''){
                                                    row_html += '<td data-id="cusprivacy">' + privacyData[singleprivacy.split("=")[0]]['body'] + '</td>';
                                                }else{
                                                    row_html += '<td data-id="cusprivacy"></td>';
                                                }
                                                privacy_val_count++;
                                            }
                                        }
                                    });
                                    for(var i = privacy_val_count;i<2;i++) {
                                        row_html += '<td data-id="cusprivacy">-</td>';                                    
                                    }
                                }else{
                                    row_html += '<td data-id="cusprivacy"></td>';
                                    row_html += '<td data-id="cusprivacy"></td>';
                                }
                                row_html += '</tr>';
                            });
                            jQuery(document).find('#data-table-accept-report tbody').html(row_html);
                            jQuery(document).find('.customer-table-wrap').show();
 //                                if ($.fn.DataTable.isDataTable('#data-table-accept-report')) {
 //                                    jQuery(document).find('#data-table-accept-report').DataTable().destroy();
 //                                }
 //                                    console.log("test 2",$.fn.DataTable.isDataTable('#data-table-accept-report'));
                                jQuery(document).find('#data-table-accept-report').DataTable({
                                    dom: 'Bfrtip',
                                    buttons: [{
                                            extend: 'csv',
                                            title: 'customers-list',
                                            exportOptions: {
                                                columns: "thead th:not(.noExport)"
                                            }
                                        }],
                                    aoColumnDefs: [{
                                            'bSortable': false,
                                            'aTargets': ['nosort']
                                        }],
                                    oLanguage: {"sSearch": ""},
                                    info: false,
                                    lengthChange: false
                                });
                            }
                        }
                    },
                    error: function (request, status, error) {
                        console.log(error);
                    }
                });
            });
            
            jQuery(document).on('click', '#get-customers-form #import_user', function (event) {
                event.preventDefault();
                var startDate = jQuery(document).find('#get-customers-form #start_date').val();
                var endDate = jQuery(document).find('#get-customers-form #end_date').val();
                jQuery.ajax({
                    url: STCTICKETSPUBLIC.ajaxurl,
                    method: 'post',
                    beforeSend: function () {
                        jQuery("body").css('opacity', '0.2');
                    },
                    data: {
                        action: 'importCustomers',
                        startDate: startDate,
                        endDate: endDate
                    },
                    success: function (data) {
                        jQuery("body").css('opacity', '1');
                        var responseData = JSON.parse(data);
                        var user_ids = responseData.user_ids;
                        console.log(user_ids);
                        if(responseData.statue){
                            if(typeof user_ids != "undefined" && user_ids != ""){
                                if(jQuery(document).find('.import-customers-error').length > 0){
                                    jQuery(document).find('.import-customers-error').text(stcTicketsText.str_16);
                                }else{
                                    jQuery(document).find('#get-customers-form').after('<p class="import-customers-error">'+(stcTicketsText.str_16)+'</p>');
                                }
                            }else{
                                if(jQuery(document).find('.import-customers-error').length > 0){
                                    jQuery(document).find('.import-customers-error').text(stcTicketsText.str_15);
                                }else{
                                    jQuery(document).find('#get-customers-form').after('<p class="import-customers-error">'+(stcTicketsText.str_15)+'</p>');
                                }
                            }
                        }else{
                            if(jQuery(document).find('.import-customers-error').length > 0){
                                jQuery(document).find('.import-customers-error').text(responseData.message ? responseData.message : stcTicketsText.str_14+"!!");
                            }else{
                                jQuery(document).find('#get-customers-form').after('<p class="import-customers-error">'+(responseData.message ? responseData.message : stcTicketsText.str_14+"!!")+'</p>');
                            }
                        }
                    },
                    error: function (request, status, error) {
                        console.log(error);
                    }
                });
            });

        });
    </script>
    <?php
    $html = ob_get_clean();
    return $html;
}

function get_confirmed_order_arr_fun($confirmed_order_array) {
    if(!empty($confirmed_order_array)) {
        foreach ( $confirmed_order_array as $confirmed_order_arr_key => $confirmed_order_arr_value ) {
            if(!empty($confirmed_order_arr_value)) {                
                $zone_arr_new = array ();
                foreach ( $confirmed_order_arr_value as $confirmed_order_arr_k => $confirmed_order_arr_v ) {
                    $zone_arr_new[ $confirmed_order_arr_v[ 'zoneId' ] ][ 'zoneName' ] = $confirmed_order_arr_v[ 'zoneName' ];
                    $zone_arr_new[ $confirmed_order_arr_v[ 'zoneId' ] ][ 'zoneId' ]   = $confirmed_order_arr_v[ 'zoneId' ];
                    // Add the show date
                    $zone_arr_new[ $confirmed_order_arr_v[ 'zoneId' ] ][ 'showDate' ] = isset($confirmed_order_arr_v[ 'showDate' ]) ? $confirmed_order_arr_v[ 'showDate' ] : '';
                    if( ! empty( $zone_arr_new[ $confirmed_order_arr_v[ 'zoneId' ] ][ 'seats' ] ) ) {
                        $temp_zone_arr                                                 = array_merge( $zone_arr_new[ $confirmed_order_arr_v[ 'zoneId' ] ][ 'seats' ], $confirmed_order_arr_v[ 'seats' ] );
                        $zone_arr_new[ $confirmed_order_arr_v[ 'zoneId' ] ][ 'seats' ] = $temp_zone_arr;
                    } else {
                        $zone_arr_new[ $confirmed_order_arr_v[ 'zoneId' ] ][ 'seats' ] = $confirmed_order_arr_v[ 'seats' ];
                    }
                }
                $confirmed_order_array[ $confirmed_order_arr_key ] = array_values( $zone_arr_new );
            }
        }

        foreach ( $confirmed_order_array as $order_arr_key => $order_arr_value ) {
            if(!empty($order_arr_value)) {                
                foreach ( $order_arr_value as $order_arr_k => $order_arr_v ) {
                    $seats_arr_new = array ();
                    foreach ( $order_arr_v[ 'seats' ] as $seats_k => $seats_v ) {
                        $seats_arr_new[ $seats_v[ 'reductionId' ] ] = array (
                            'reductionName'     => $seats_v[ 'reductionName' ],
                            'reductionId'       => $seats_v[ 'reductionId' ],
                            'reductionQuantity' => !empty($seats_arr_new) ? $seats_arr_new[ $seats_v[ 'reductionId' ] ][ 'reductionQuantity' ] + $seats_v[ 'reductionQuantity' ] : $seats_v[ 'reductionQuantity' ],
                            'reductionPrice'    => $seats_v[ 'reductionPrice' ]
                        );
                    }
                    $order_arr_v[ 'seats' ]          = array_values( $seats_arr_new );
                    $order_arr_value[ $order_arr_k ] = $order_arr_v;
                }
                $confirmed_order_array[ $order_arr_key ] = $order_arr_value;
            }
        }        
    }
        
    return $confirmed_order_array;
}
function xmlToArray($xml, $options = array ()) {
    if(empty($xml)) {
        return array();
    }
    $defaults         = array (
        'namespaceSeparator' => ':', //you may want this to be something other than a colon
        'attributePrefix'    => '@attribute', //to distinguish between attributes and nodes with the same name
//        'attributePrefix' => '@',   //to distinguish between attributes and nodes with the same name
        'alwaysArray'        => array (), //array of xml tag names which should always become arrays
        'autoArray'          => true, //only create arrays for tags which appear more than once
        'textContent'        => 'value', //key used for the text content of elements
        'autoText'           => true, //skip textContent key if node has no attributes or child nodes
        'keySearch'          => false, //optional search and replace on tag and attribute names
        'keyReplace'         => false       //replace values for above search values (as passed to str_replace())
    );
    $options          = array_merge( $defaults, $options );
    $namespaces       = $xml->getDocNamespaces();
    $namespaces[ '' ] = null; //add base (empty) namespace
    //get attributes from all namespaces
    $attributesArray  = array ();
    foreach ( $namespaces as $prefix => $namespace ) {
        foreach ( $xml->attributes( $namespace ) as $attributeName => $attribute ) {
            //replace characters in attribute name
            if( $options[ 'keySearch' ] )
                $attributeName                                      = str_replace( $options[ 'keySearch' ], $options[ 'keyReplace' ], $attributeName );
            $attributeKey                                       = $options[ 'attributePrefix' ];
//            $attributeKey = $options['attributePrefix']
//                    . ($prefix ? $prefix . $options['namespaceSeparator'] : '')
//                    . $attributeName;
//            $attributesArray[$attributeKey] = (string)$attribute;
            $attributesArray[ $attributeKey ][ $attributeName ] = (string) $attribute;
        }
    }

    //get child nodes from all namespaces
    $tagsArray = array ();
    foreach ( $namespaces as $prefix => $namespace ) {
        foreach ( $xml->children( $namespace ) as $childXml ) {
            //recurse into child nodes
            $childArray      = xmlToArray( $childXml, $options );
//          this functions is depricated in php 7.2 version
//          list($childTagName, $childProperties) = each($childArray);
//          use instead of that list() and each() function
            $childTagName    = key( $childArray );
            $childProperties = current( $childArray );

            //replace characters in tag name
            if( $options[ 'keySearch' ] )
                $childTagName = str_replace( $options[ 'keySearch' ], $options[ 'keyReplace' ], $childTagName );
            //add namespace prefix, if any
            if( $prefix )
                $childTagName = $prefix . $options[ 'namespaceSeparator' ] . $childTagName;

            if( empty( $tagsArray[ $childTagName ] ) ) {
                //only entry with this key
                //test if tags of this type should always be arrays, no matter the element count
                $tagsArray[ $childTagName ] = in_array( $childTagName, $options[ 'alwaysArray' ] ) || ! $options[ 'autoArray' ] ? array ( $childProperties ) : $childProperties;
            } elseif(
                    is_array( $tagsArray[ $childTagName ] ) && array_keys( $tagsArray[ $childTagName ] ) === range( 0, count( $tagsArray[ $childTagName ] ) - 1 )
            ) {
                //key already exists and is integer indexed array
                $tagsArray[ $childTagName ][] = $childProperties;
            } else {
                //key exists so convert to integer indexed array with previous value in position 0
                $tagsArray[ $childTagName ] = array ( $tagsArray[ $childTagName ], $childProperties );
            }
        }
    }

    //get text content of node
    $textContentArray                              = array ();
    $plainText                                     = trim( (string) $xml );
    if( $plainText !== '' )
        $textContentArray[ $options[ 'textContent' ] ] = $plainText;

    //stick it all together
    $propertiesArray = ! $options[ 'autoText' ] || $attributesArray || $tagsArray || ($plainText === '') ? array_merge( $attributesArray, $tagsArray, $textContentArray ) : $plainText;

    //return node as array
    return array (
        $xml->getName() => $propertiesArray
    );
}
/*
 * get country code options
 */
add_shortcode( 'get_country_code_options', 'stcTickets_get_country_code_options_fun' );
function stcTickets_get_country_code_options_fun() {
    ?>
    <option data-countryCode="DZ" value="213">Algeria (+213)</option>
    <option data-countryCode="AD" value="376">Andorra (+376)</option>
    <option data-countryCode="AO" value="244">Angola (+244)</option>
    <option data-countryCode="AI" value="1264">Anguilla (+1264)</option>
    <option data-countryCode="AG" value="1268">Antigua &amp; Barbuda (+1268)</option>
    <option data-countryCode="AR" value="54">Argentina (+54)</option>
    <option data-countryCode="AM" value="374">Armenia (+374)</option>
    <option data-countryCode="AW" value="297">Aruba (+297)</option>
    <option data-countryCode="AU" value="61">Australia (+61)</option>
    <option data-countryCode="AT" value="43">Austria (+43)</option>
    <option data-countryCode="AZ" value="994">Azerbaijan (+994)</option>
    <option data-countryCode="BS" value="1242">Bahamas (+1242)</option>
    <option data-countryCode="BH" value="973">Bahrain (+973)</option>
    <option data-countryCode="BD" value="880">Bangladesh (+880)</option>
    <option data-countryCode="BB" value="1246">Barbados (+1246)</option>
    <option data-countryCode="BY" value="375">Belarus (+375)</option>
    <option data-countryCode="BE" value="32">Belgium (+32)</option>
    <option data-countryCode="BZ" value="501">Belize (+501)</option>
    <option data-countryCode="BJ" value="229">Benin (+229)</option>
    <option data-countryCode="BM" value="1441">Bermuda (+1441)</option>
    <option data-countryCode="BT" value="975">Bhutan (+975)</option>
    <option data-countryCode="BO" value="591">Bolivia (+591)</option>
    <option data-countryCode="BA" value="387">Bosnia Herzegovina (+387)</option>
    <option data-countryCode="BW" value="267">Botswana (+267)</option>
    <option data-countryCode="BR" value="55">Brazil (+55)</option>
    <option data-countryCode="BN" value="673">Brunei (+673)</option>
    <option data-countryCode="BG" value="359">Bulgaria (+359)</option>
    <option data-countryCode="BF" value="226">Burkina Faso (+226)</option>
    <option data-countryCode="BI" value="257">Burundi (+257)</option>
    <option data-countryCode="KH" value="855">Cambodia (+855)</option>
    <option data-countryCode="CM" value="237">Cameroon (+237)</option>
    <option data-countryCode="CA" value="1">Canada (+1)</option>
    <option data-countryCode="CV" value="238">Cape Verde Islands (+238)</option>
    <option data-countryCode="KY" value="1345">Cayman Islands (+1345)</option>
    <option data-countryCode="CF" value="236">Central African Republic (+236)</option>
    <option data-countryCode="CL" value="56">Chile (+56)</option>
    <option data-countryCode="CN" value="86">China (+86)</option>
    <option data-countryCode="CO" value="57">Colombia (+57)</option>
    <option data-countryCode="KM" value="269">Comoros (+269)</option>
    <option data-countryCode="CG" value="242">Congo (+242)</option>
    <option data-countryCode="CK" value="682">Cook Islands (+682)</option>
    <option data-countryCode="CR" value="506">Costa Rica (+506)</option>
    <option data-countryCode="HR" value="385">Croatia (+385)</option>
    <option data-countryCode="CU" value="53">Cuba (+53)</option>
    <option data-countryCode="CY" value="90392">Cyprus North (+90392)</option>
    <option data-countryCode="CY" value="357">Cyprus South (+357)</option>
    <option data-countryCode="CZ" value="420">Czech Republic (+420)</option>
    <option data-countryCode="DK" value="45">Denmark (+45)</option>
    <option data-countryCode="DJ" value="253">Djibouti (+253)</option>
    <option data-countryCode="DM" value="1809">Dominica (+1809)</option>
    <option data-countryCode="DO" value="1809">Dominican Republic (+1809)</option>
    <option data-countryCode="EC" value="593">Ecuador (+593)</option>
    <option data-countryCode="EG" value="20">Egypt (+20)</option>
    <option data-countryCode="SV" value="503">El Salvador (+503)</option>
    <option data-countryCode="GQ" value="240">Equatorial Guinea (+240)</option>
    <option data-countryCode="ER" value="291">Eritrea (+291)</option>
    <option data-countryCode="EE" value="372">Estonia (+372)</option>
    <option data-countryCode="ET" value="251">Ethiopia (+251)</option>
    <option data-countryCode="FK" value="500">Falkland Islands (+500)</option>
    <option data-countryCode="FO" value="298">Faroe Islands (+298)</option>
    <option data-countryCode="FJ" value="679">Fiji (+679)</option>
    <option data-countryCode="FI" value="358">Finland (+358)</option>
    <option data-countryCode="FR" value="33">France (+33)</option>
    <option data-countryCode="GF" value="594">French Guiana (+594)</option>
    <option data-countryCode="PF" value="689">French Polynesia (+689)</option>
    <option data-countryCode="GA" value="241">Gabon (+241)</option>
    <option data-countryCode="GM" value="220">Gambia (+220)</option>
    <option data-countryCode="GE" value="7880">Georgia (+7880)</option>
    <option data-countryCode="DE" value="49">Germany (+49)</option>
    <option data-countryCode="GH" value="233">Ghana (+233)</option>
    <option data-countryCode="GI" value="350">Gibraltar (+350)</option>
    <option data-countryCode="GR" value="30">Greece (+30)</option>
    <option data-countryCode="GL" value="299">Greenland (+299)</option>
    <option data-countryCode="GD" value="1473">Grenada (+1473)</option>
    <option data-countryCode="GP" value="590">Guadeloupe (+590)</option>
    <option data-countryCode="GU" value="671">Guam (+671)</option>
    <option data-countryCode="GT" value="502">Guatemala (+502)</option>
    <option data-countryCode="GN" value="224">Guinea (+224)</option>
    <option data-countryCode="GW" value="245">Guinea - Bissau (+245)</option>
    <option data-countryCode="GY" value="592">Guyana (+592)</option>
    <option data-countryCode="HT" value="509">Haiti (+509)</option>
    <option data-countryCode="HN" value="504">Honduras (+504)</option>
    <option data-countryCode="HK" value="852">Hong Kong (+852)</option>
    <option data-countryCode="HU" value="36">Hungary (+36)</option>
    <option data-countryCode="IS" value="354">Iceland (+354)</option>
    <option data-countryCode="IN" value="91">India (+91)</option>
    <option data-countryCode="ID" value="62">Indonesia (+62)</option>
    <option data-countryCode="IR" value="98">Iran (+98)</option>
    <option data-countryCode="IQ" value="964">Iraq (+964)</option>
    <option data-countryCode="IE" value="353">Ireland (+353)</option>
    <option data-countryCode="IL" value="972">Israel (+972)</option>
    <option data-countryCode="IT" value="39" selected="selected">Italy (+39)</option>
    <option data-countryCode="JM" value="1876">Jamaica (+1876)</option>
    <option data-countryCode="JP" value="81">Japan (+81)</option>
    <option data-countryCode="JO" value="962">Jordan (+962)</option>
    <option data-countryCode="KZ" value="7">Kazakhstan (+7)</option>
    <option data-countryCode="KE" value="254">Kenya (+254)</option>
    <option data-countryCode="KI" value="686">Kiribati (+686)</option>
    <option data-countryCode="KP" value="850">Korea North (+850)</option>
    <option data-countryCode="KR" value="82">Korea South (+82)</option>
    <option data-countryCode="KW" value="965">Kuwait (+965)</option>
    <option data-countryCode="KG" value="996">Kyrgyzstan (+996)</option>
    <option data-countryCode="LA" value="856">Laos (+856)</option>
    <option data-countryCode="LV" value="371">Latvia (+371)</option>
    <option data-countryCode="LB" value="961">Lebanon (+961)</option>
    <option data-countryCode="LS" value="266">Lesotho (+266)</option>
    <option data-countryCode="LR" value="231">Liberia (+231)</option>
    <option data-countryCode="LY" value="218">Libya (+218)</option>
    <option data-countryCode="LI" value="417">Liechtenstein (+417)</option>
    <option data-countryCode="LT" value="370">Lithuania (+370)</option>
    <option data-countryCode="LU" value="352">Luxembourg (+352)</option>
    <option data-countryCode="MO" value="853">Macao (+853)</option>
    <option data-countryCode="MK" value="389">Macedonia (+389)</option>
    <option data-countryCode="MG" value="261">Madagascar (+261)</option>
    <option data-countryCode="MW" value="265">Malawi (+265)</option>
    <option data-countryCode="MY" value="60">Malaysia (+60)</option>
    <option data-countryCode="MV" value="960">Maldives (+960)</option>
    <option data-countryCode="ML" value="223">Mali (+223)</option>
    <option data-countryCode="MT" value="356">Malta (+356)</option>
    <option data-countryCode="MH" value="692">Marshall Islands (+692)</option>
    <option data-countryCode="MQ" value="596">Martinique (+596)</option>
    <option data-countryCode="MR" value="222">Mauritania (+222)</option>
    <option data-countryCode="YT" value="269">Mayotte (+269)</option>
    <option data-countryCode="MX" value="52">Mexico (+52)</option>
    <option data-countryCode="FM" value="691">Micronesia (+691)</option>
    <option data-countryCode="MD" value="373">Moldova (+373)</option>
    <option data-countryCode="MC" value="377">Monaco (+377)</option>
    <option data-countryCode="MN" value="976">Mongolia (+976)</option>
    <option data-countryCode="MS" value="1664">Montserrat (+1664)</option>
    <option data-countryCode="MA" value="212">Morocco (+212)</option>
    <option data-countryCode="MZ" value="258">Mozambique (+258)</option>
    <option data-countryCode="MN" value="95">Myanmar (+95)</option>
    <option data-countryCode="NA" value="264">Namibia (+264)</option>
    <option data-countryCode="NR" value="674">Nauru (+674)</option>
    <option data-countryCode="NP" value="977">Nepal (+977)</option>
    <option data-countryCode="NL" value="31">Netherlands (+31)</option>
    <option data-countryCode="NC" value="687">New Caledonia (+687)</option>
    <option data-countryCode="NZ" value="64">New Zealand (+64)</option>
    <option data-countryCode="NI" value="505">Nicaragua (+505)</option>
    <option data-countryCode="NE" value="227">Niger (+227)</option>
    <option data-countryCode="NG" value="234">Nigeria (+234)</option>
    <option data-countryCode="NU" value="683">Niue (+683)</option>
    <option data-countryCode="NF" value="672">Norfolk Islands (+672)</option>
    <option data-countryCode="NP" value="670">Northern Marianas (+670)</option>
    <option data-countryCode="NO" value="47">Norway (+47)</option>
    <option data-countryCode="OM" value="968">Oman (+968)</option>
    <option data-countryCode="PW" value="680">Palau (+680)</option>
    <option data-countryCode="PA" value="507">Panama (+507)</option>
    <option data-countryCode="PG" value="675">Papua New Guinea (+675)</option>
    <option data-countryCode="PY" value="595">Paraguay (+595)</option>
    <option data-countryCode="PE" value="51">Peru (+51)</option>
    <option data-countryCode="PH" value="63">Philippines (+63)</option>
    <option data-countryCode="PL" value="48">Poland (+48)</option>
    <option data-countryCode="PT" value="351">Portugal (+351)</option>
    <option data-countryCode="PR" value="1787">Puerto Rico (+1787)</option>
    <option data-countryCode="QA" value="974">Qatar (+974)</option>
    <option data-countryCode="RE" value="262">Reunion (+262)</option>
    <option data-countryCode="RO" value="40">Romania (+40)</option>
    <option data-countryCode="RU" value="7">Russia (+7)</option>
    <option data-countryCode="RW" value="250">Rwanda (+250)</option>
    <option data-countryCode="SM" value="378">San Marino (+378)</option>
    <option data-countryCode="ST" value="239">Sao Tome &amp; Principe (+239)</option>
    <option data-countryCode="SA" value="966">Saudi Arabia (+966)</option>
    <option data-countryCode="SN" value="221">Senegal (+221)</option>
    <option data-countryCode="CS" value="381">Serbia (+381)</option>
    <option data-countryCode="SC" value="248">Seychelles (+248)</option>
    <option data-countryCode="SL" value="232">Sierra Leone (+232)</option>
    <option data-countryCode="SG" value="65">Singapore (+65)</option>
    <option data-countryCode="SK" value="421">Slovak Republic (+421)</option>
    <option data-countryCode="SI" value="386">Slovenia (+386)</option>
    <option data-countryCode="SB" value="677">Solomon Islands (+677)</option>
    <option data-countryCode="SO" value="252">Somalia (+252)</option>
    <option data-countryCode="ZA" value="27">South Africa (+27)</option>
    <option data-countryCode="ES" value="34">Spain (+34)</option>
    <option data-countryCode="LK" value="94">Sri Lanka (+94)</option>
    <option data-countryCode="SH" value="290">St. Helena (+290)</option>
    <option data-countryCode="KN" value="1869">St. Kitts (+1869)</option>
    <option data-countryCode="SC" value="1758">St. Lucia (+1758)</option>
    <option data-countryCode="SD" value="249">Sudan (+249)</option>
    <option data-countryCode="SR" value="597">Suriname (+597)</option>
    <option data-countryCode="SZ" value="268">Swaziland (+268)</option>
    <option data-countryCode="SE" value="46">Sweden (+46)</option>
    <option data-countryCode="CH" value="41">Switzerland (+41)</option>
    <option data-countryCode="SI" value="963">Syria (+963)</option>
    <option data-countryCode="TW" value="886">Taiwan (+886)</option>
    <option data-countryCode="TJ" value="7">Tajikstan (+7)</option>
    <option data-countryCode="TH" value="66">Thailand (+66)</option>
    <option data-countryCode="TG" value="228">Togo (+228)</option>
    <option data-countryCode="TO" value="676">Tonga (+676)</option>
    <option data-countryCode="TT" value="1868">Trinidad &amp; Tobago (+1868)</option>
    <option data-countryCode="TN" value="216">Tunisia (+216)</option>
    <option data-countryCode="TR" value="90">Turkey (+90)</option>
    <option data-countryCode="TM" value="7">Turkmenistan (+7)</option>
    <option data-countryCode="TM" value="993">Turkmenistan (+993)</option>
    <option data-countryCode="TC" value="1649">Turks &amp; Caicos Islands (+1649)</option>
    <option data-countryCode="TV" value="688">Tuvalu (+688)</option>
    <option data-countryCode="UG" value="256">Uganda (+256)</option>
    <option data-countryCode="GB" value="44">UK (+44)</option> 
    <option data-countryCode="UA" value="380">Ukraine (+380)</option>
    <option data-countryCode="AE" value="971">United Arab Emirates (+971)</option>
    <option data-countryCode="UY" value="598">Uruguay (+598)</option>
    <option data-countryCode="US" value="1">USA (+1)</option> 
    <option data-countryCode="UZ" value="7">Uzbekistan (+7)</option>
    <option data-countryCode="VU" value="678">Vanuatu (+678)</option>
    <option data-countryCode="VA" value="379">Vatican City (+379)</option>
    <option data-countryCode="VE" value="58">Venezuela (+58)</option>
    <option data-countryCode="VN" value="84">Vietnam (+84)</option>
    <option data-countryCode="VG" value="84">Virgin Islands - British (+1284)</option>
    <option data-countryCode="VI" value="84">Virgin Islands - US (+1340)</option>
    <option data-countryCode="WF" value="681">Wallis &amp; Futuna (+681)</option>
    <option data-countryCode="YE" value="969">Yemen (North)(+969)</option>
    <option data-countryCode="YE" value="967">Yemen (South)(+967)</option>
    <option data-countryCode="ZM" value="260">Zambia (+260)</option>
    <option data-countryCode="ZW" value="263">Zimbabwe (+263)</option>
    <?php
}

/*
 * subscription check form
 */
add_shortcode( 'spettacolo_subscription_check', 'stcTickets_spettacolo_subscription_check_callback' );
function stcTickets_spettacolo_subscription_check_callback($atts) {
    ob_start();
    ?>
    <div class="subscription-check-wrap">
        <div class="subscription-check-form">
            <div class="subscription-check-desc">
                <p><?php _e('Inserisci il tuo codice abbonamento','stc-tickets'); ?></p>
            </div>
            <div class="subscription-check-input-wrap">
                <input type="text" class="subscription-check-input" name="subscription-check-input">
            </div>
            <div class="subscription-check-btn-wrap">
                <button class="subscription-check-btn"><?php _e('PASSO SUCCESSIVO','stc-tickets'); ?></button>
            </div>
        </div>
    </div>
    <?php
    $html = ob_get_clean();
    return $html;
}

/*
 * Subscription listing
 */
add_shortcode ( 'abbonamento_listing', 'stcTickets_subscription_listing_callback' );

function stcTickets_subscription_listing_callback() {
    ob_start ();
    $paged     = (get_query_var ( 'paged' )) ? get_query_var ( 'paged' ) : 1;
    $spe_args  = array (
        'post_type'      => 'spettacolo',
        'post_status'    => 'publish',
        'posts_per_page' => '20',
        'paged'          => $paged,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'meta_query' => array(
            array(
                'key'=> 'spt_subscription_show',
                'compare' => 'EXISTS'
            ),
            array(
                'key' => 'spt_subscription_show',
                'value' => '1',
                'compare' => '='
            )
        )
    );
    $spe_query = new WP_Query ( $spe_args );    
    ?>
    <div class="spettacolo-listing-wrapper">
        <?php        
        if( $spe_query->have_posts () ) {
            while ( $spe_query->have_posts () ) {
                $spe_query->the_post ();
                $spe_id             = get_the_ID ();
                $spe_title          = get_the_title ();
                $spe_permalink      = get_the_permalink ();
                $spe_start_date     = get_post_meta ( $spe_id, 'spt_startDate', true );
                $spe_end_date       = get_post_meta ( $spe_id, 'spt_endDate', true );
                $curr_start_date    = explode ( "/", $spe_start_date );
                $final_start_date   = $curr_start_date[ 1 ] . '/' . $curr_start_date[ 0 ] . '/' . $curr_start_date[ 2 ];
                $curr_end_date      = explode ( "/", $spe_end_date );
                $final_end_date     = $curr_end_date[ 1 ] . '/' . $curr_end_date[ 0 ] . '/' . $curr_end_date[ 2 ];
                $final_start_date   = change_date_time_in_italy(strtotime ( $final_start_date ),'dd MMMM y');
                $final_end_date     = change_date_time_in_italy(strtotime ( $final_end_date ),'dd MMMM y') ;
                $spt_vcode          = get_post_meta( $spe_id, 'spt_vcode', true );
                $spt_tit_info_title = get_post_meta ( $spe_id, 'spt_tit_info_title', true );
                $tit_info_perform   = ! empty( $spt_tit_info_title[ 'tit_info_perform' ] ) ? $spt_tit_info_title[ 'tit_info_perform' ] : '';
                $spt_location       = !empty(get_post_meta( $spe_id, 'spt_location', true )) ? get_post_meta( $spe_id, 'spt_location', true ) : __('Teatro San Carlo - NAPOLI','stc-tickets');
                $spt_img            = get_the_post_thumbnail_url($spe_id) ? get_the_post_thumbnail_url($spe_id) : plugin_dir_url( __DIR__ ) . 'assets/img/emiliano_test.jpg';
                if( ! empty( $tit_info_perform ) ) {
                    foreach ( $tit_info_perform as $tit_info_perform_key => $tit_info_perform_value ) {
                        if( count( $tit_info_perform ) > 1 ) {
                            $tit_info_perform_value = $tit_info_perform_value[ '@attributes' ];
                        }
                        $pcode           = ! empty( $tit_info_perform_value[ 'code' ] ) ? $tit_info_perform_value[ 'code' ] : '9654179';
                        $regData         = ! empty( $tit_info_perform_value[ 'regData' ] ) ? $tit_info_perform_value[ 'regData' ] : 0;
                        $redirect_url    = (is_plugin_active('sitepress-multilingual-cms/sitepress.php') ? rtrim(apply_filters( 'wpml_home_url', get_option( 'home' ) ), '/') : get_site_url()) . '/spettacolo-prices/?cmd=prices&id=' . APIKEY . '&vcode=' . $spt_vcode . '&pcode=' . $pcode . '&postId=' . $spe_id . '&regData=' . $regData . '&selectionMode=0';
                    }
                }
                ?>
                <div class="spettacolo-listing-box-wrapper">
                    <div class="spettacolo-list-box">
                        <div class="spettacolo-thumb">
                           <img src="<?php echo $spt_img; ?>" alt="alt"/>
                        </div>
                        <div class="datetitlewrap">
                        <div class="list-title">
                            <?php if( ! empty ( $spe_title ) ) { ?>
                                <h3><a href="<?php echo $redirect_url; ?>"><?php echo $spe_title; ?></a></h3>
                            <?php } ?>
                        </div>
                        <div class="list-date">
                            <svg data-v-4f3415b9="" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="clipboard-list" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="svg-inline--fa fa-clipboard-list fa-fw fa-sm"><path data-v-4f3415b9="" fill="currentColor" d="M192 0c-41.8 0-77.4 26.7-90.5 64H64C28.7 64 0 92.7 0 128V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V128c0-35.3-28.7-64-64-64H282.5C269.4 26.7 233.8 0 192 0zm0 64a32 32 0 1 1 0 64 32 32 0 1 1 0-64zM72 272a24 24 0 1 1 48 0 24 24 0 1 1 -48 0zm104-16H304c8.8 0 16 7.2 16 16s-7.2 16-16 16H176c-8.8 0-16-7.2-16-16s7.2-16 16-16zM72 368a24 24 0 1 1 48 0 24 24 0 1 1 -48 0zm88 0c0-8.8 7.2-16 16-16H304c8.8 0 16 7.2 16 16s-7.2 16-16 16H176c-8.8 0-16-7.2-16-16z" class=""></path></svg>
                            <span><?php _e('Abbonamento','stc-tickets'); ?></span>
                        </div>
                        </div>
                        <div class="list-location">
                            <svg data-v-e1017caa="" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="location-dot" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="svg-inline--fa fa-location-dot fa-fw fa-sm"><path data-v-e1017caa="" fill="currentColor" d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 256c-35.3 0-64-28.7-64-64s28.7-64 64-64s64 28.7 64 64s-28.7 64-64 64z" class=""></path></svg>
                            <p><?php echo $spt_location; ?></p>
                        </div>
                        <div class="list-cta">
                            <a href="<?php echo $redirect_url; ?>"><?php _e('acquista biglietti','stc-tickets'); ?></a>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="pagination-links">
                <?php
                echo paginate_links ( array (
                    'base'         => str_replace ( 999999999, '%#%', esc_url ( get_pagenum_link ( 999999999 ) ) ),
                    'total'        => $spe_query->max_num_pages,
                    'current'      => max ( 1, get_query_var ( 'paged' ) ),
                    'format'       => '?paged=%#%',
                    'show_all'     => false,
                    'type'         => 'plain',
                    'prev_next'    => true,
                    'prev_text'    => sprintf ( '<i></i> %1$s', __ ( 'Prev', 'stc-tickets' ) ),
                    'next_text'    => sprintf ( '%1$s <i></i>', __ ( 'Next', 'stc-tickets' ) ),
                    'add_args'     => false,
                    'add_fragment' => '',
                ) );
                ?>
            </div>
            <?php
        } else {
            ?>
            <p><?php _e('Nessun abbonamento disponibile','stc-tickets'); ?></p>
        <?php } ?>
    </div>   
    <?php
    $html = ob_get_clean ();
    return $html;
}