<?php
/*
 * spettacoli subscription
 */
add_shortcode( 'spettacolo_subscription', 'stcTickets_spettacolo_subscription_callback' );
function stcTickets_spettacolo_subscription_callback() {
    $remove = ob_get_clean();
    ob_start();
    $barcode        = isset( $_GET[ 'barcode' ] ) ? $_GET[ 'barcode' ] : '';
    $order_id       = isset( $_GET[ 'order_id' ] ) ? $_GET[ 'order_id' ] : '';
    $spe_sub_cookie = tempnam ("/tmp", "CURLCOOKIE");
    $curl           = curl_init();
    $error_msg      = '';

    curl_setopt_array($curl, array(
        CURLOPT_URL => API_HOST.'backend/backend.php?id=SANCARLO&cmd=xmlSubscriptionData&barcode='.$barcode,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_COOKIEJAR => $spe_sub_cookie,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);
    if (curl_errno($curl)) {
        $error_msg = curl_error($curl);
    }
    curl_close($curl);
    $xml                = simplexml_load_string( $response, 'SimpleXMLElement', LIBXML_NOCDATA );
    $subscriptionJson   = json_encode( $xml );
    $subscriptionArr    = json_decode( $subscriptionJson, TRUE );

    // test
    if(isset($_GET['print']) && $_GET['print'] == 1) {
        echo "<pre>";
            print_r($subscriptionArr);
        echo "</pre>";
    }
        
    if(!empty($subscriptionArr) && isset($subscriptionArr['subscriptiondata'])){
        $perfs = $subscriptionArr['subscriptiondata']['perfs'];
        $subscription_attr = $subscriptionArr['subscriptiondata']['@attributes'];
        if(!empty($subscription_attr)){
            $cookie_name = "remainingSeats";
            // setcookie($cookie_name, "test", time() + 3600, "/"); // 86400 = 1 day
            if(!isset($_COOKIE[$cookie_name])) {
                $subs_remaining_seats = array();
            } else {
                $subs_remaining_seats = json_decode($_COOKIE[$cookie_name]);
            }
            $subscription_barcode = $subscription_attr['barcode'];
            $remainingaccruals = $subscription_attr['remainingaccruals'];
            if($remainingaccruals == '0'){
                $error_msg = __("Questo abbonamento non può essere più modificato","stc-tickets");
            }
            $subs_remaining_seats[$subscription_barcode] = array('remaining' => $remainingaccruals,'total' => $remainingaccruals);
            // setcookie($cookie_name, urlencode(json_encode($subs_remaining_seats)), time() + (86400 * 30), "/"); // 86400 = 1 day
            echo '<script>document.cookie = "'.$cookie_name.'=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";</script>';  // Remove existing cookie
            echo '<script>document.cookie = "'.$cookie_name.'='.urlencode(json_encode($subs_remaining_seats)).'; expires='.strtotime('+1 hour').'; path=/";</script>';
            // echo '<script>document.cookie = "'.$cookie_name.'='.json_encode($subs_remaining_seats).'; expires='.strtotime('+1 hour').'; path=/";</script>';
        }
        if(!empty($perfs['perf']) && $remainingaccruals != '0'){
            $subsciption_list = array();
            foreach($perfs['perf'] as $perf_key => $perf_value){
                $pcode = $perf_value['@attributes']['pcode'];
                $vcode = $perf_value['@attributes']['vcode'];
                $title = $perf_value['@attributes']['title'];
//                    $starttime = $perf_value['@attributes']['starttime'];
                
//                    if($_GET['print'] == 1){
//                        echo "<pre>";
//                        print_r($perf_value);
//                        echo "</pre>";
//                    }
                
//                    $info_curl = curl_init();
//
//                    curl_setopt_array($info_curl, array(
//                      CURLOPT_URL => API_HOST.'backend/backend.php?cmd=prices&id=SANCARLO&vcode='.$vcode.'&pcode='.$pcode,
//                      CURLOPT_RETURNTRANSFER => true,
//                      CURLOPT_ENCODING => '',
//                      CURLOPT_MAXREDIRS => 10,
//                      CURLOPT_TIMEOUT => 0,
//                      CURLOPT_FOLLOWLOCATION => true,
//                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//                      CURLOPT_CUSTOMREQUEST => 'GET',
//                    ));
//
//                    $info_response = curl_exec($info_curl);
//                    if (curl_errno($curl)) {
//                        $error_msg = curl_error($curl);
//                    }
//                    curl_close($info_curl);
////                    echo $info_response;
//                    $xml             = simplexml_load_string( $info_response, 'SimpleXMLElement', LIBXML_NOCDATA );
//                    $infoJson      = json_encode( $xml );
//                    $infoArr       = json_decode( $infoJson, TRUE );
//                    if($_GET['print'] == 1){
//                        echo "<pre>";
//                        print_r($infoArr);
//                        echo "</pre>";
//                    }
//                    $subsciption_list[$infoArr['title']][] = array("data" => $infoArr,"vcode" => $vcode,"pcode" => $pcode);
                $subsciption_list[$title][] = array("data" => $perf_value,"vcode" => $vcode,"pcode" => $pcode);
                $spt_img       = plugin_dir_url( __DIR__ ) . 'assets/img/emiliano_test.jpg';
            }
            if(!empty($subsciption_list)){
                foreach($subsciption_list as $subsciption_list_key => $subsciption_list_value){
                ?>
                    <div class="subsciption-list">
                        <div class="spettacolo-info-inner-wrap">
                            <div class="spe-info-half-wrap">
                                <div class="spettacolo-info-img">
                                    <img src="<?php echo $spt_img; ?>" alt="alt"/>
                                </div>
                            </div>
                            <div class="spe-info-half-wrap descpription-right">
                                <div class="spettacolo-info-inner">
                                    <div class="list-title">
                                        <?php if( ! empty( $subsciption_list_key ) ) { ?>
                                            <h3><?php echo $subsciption_list_key; ?></h3>
                                        <?php } ?>
                                    </div>
                                    <?php /* ?>
                                    <div class="list-date">
                                        <?php // if( ! empty( $infoArr['date'] ) ) { ?>
                                            <svg data-v-e1017caa="" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="calendar" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-calendar fa-fw fa-sm"><path data-v-e1017caa="" fill="currentColor" d="M96 32V64H48C21.5 64 0 85.5 0 112v48H448V112c0-26.5-21.5-48-48-48H352V32c0-17.7-14.3-32-32-32s-32 14.3-32 32V64H160V32c0-17.7-14.3-32-32-32S96 14.3 96 32zM448 192H0V464c0 26.5 21.5 48 48 48H400c26.5 0 48-21.5 48-48V192z" class=""></path></svg>
                                            <p><?php // echo $infoArr['date']; ?></p>
                                        <?php // } ?>
                                    </div>
                                    <?php */ ?>
                                </div>
                            </div>
                            <div class="drop-select-box">
                                <div class="drop-select">
                                    <select id="subscription-show">
                                        <?php
                                        if(!empty($subsciption_list_value)){
                                            foreach($subsciption_list_value as $subsciption_list_k => $subsciption_list_v){
                                                $date = new DateTime($subsciption_list_v['data']['@attributes']['starttime']);
                                                $formattedDate = $date->format('d/m/Y - H:i');
                                                if(!empty($formattedDate)){
                                                ?>
                                                    <option data-vcode="<?php echo $subsciption_list_v['vcode']; ?>" data-pcode="<?php echo $subsciption_list_v['pcode']; ?>" data-barcode="<?php echo $barcode; ?>" data-order-id="<?php echo $order_id; ?>"><?php echo $formattedDate; ?></option>
                                                <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="single-date-right subscription-show-btn">
                                    <a href="javascript:void(0);"><button><?php _e('PRENOTA','stc-tickets'); ?></button></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }
            }
            // echo "<pre>";
            // print_r($subsciption_list);
            // echo "</pre>";
        }
    }
    
    /** MOD SARAH **/ // Setting and displaying the error message
    if (!isset($subscriptionArr['subscriptiondata'])) { // Errors
        $errData = isset($subscriptionArr['@attributes']['errstring']) ? $subscriptionArr['@attributes']['errstring'] : __('Errore generico','stc-tickets');
        $error_msg = $error_msg != '' ? $error_msg : $errData;
        if($error_msg != ''){
            $errcode = isset($subscriptionArr['@attributes']['errcode']) ? ' "'.$subscriptionArr['@attributes']['errcode'].'"' : '';
            echo '<div class="error-msg">'.sprintf(__('Error%s: %s','stc-tickets'),$errcode,$error_msg).'</div>';
        }
    }

    $html = ob_get_clean();
    return $html;
}