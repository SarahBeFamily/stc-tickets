var mouseX = '';
var mouseY = '';
var cart_counter_flag = true;
var cart_count = 0;
jQuery(document).ready(function ($) {
    
    var widgetId;
    
//    setTimeout(function() {
//        grecaptcha.ready(function() {
//            // Render reCAPTCHA widget
//            widgetId = grecaptcha.render('reCaptchDiv', {
//                'sitekey' : '6LedGCcpAAAAAOGFFUqTQMl7ieQiSHh7ggKrXNnL',
//                'theme' : 'light'  // Optional: change theme if needed
//            });
//
//            // Example: reset reCAPTCHA on button click        
//        });
//    }, 1000);
    
    renderReCaptcha();
    
    if (jQuery(document).find('.spettacolo-prices-wrapper').length > 0) {
        jQuery(document).find('body').addClass('spettacolo-prices-page');
    }
    
    
    var incrementPlus;
    var incrementMinus;

    var buttonPlus = jQuery(".cart-qty-plus");
    var buttonMinus = jQuery(".cart-qty-minus");
    var remainingSeats = getCookie('remainingSeats');

    var incrementPlus = buttonPlus.click(function () {
        var $n = jQuery(this).parent(".button-counter").find(".qty");
        var max = jQuery(this).parent(".button-counter").find(".qty").attr("data-max");
        var UrlVars = getUrlVars();
        var barcode = UrlVars.barcode;
        if(typeof barcode != "undefined" && barcode != ""){
            if(typeof remainingSeats != 'undefined'){
                remainingSeats = JSON.parse(remainingSeats);
                console.log(remainingSeats);
                var remainingSeatsVal = remainingSeats[barcode];
        //        console.log(remainingSeatsVal,remainingSeatsVal.remaining);
                console.log(remainingSeatsVal);
                if(typeof remainingSeatsVal != 'undefined' && remainingSeatsVal.hasOwnProperty('remaining') && Number($n.val()) < Number(max)){
//                    if(remainingSeatsVal.remaining == 0){
//                        jQuery(document).find(".cart-qty-plus").attr("disabled","disabled");
////                        remainingSeatsVal.remaining = remainingSeatsVal.remaining - 1;
////                        remainingSeats[barcode] = remainingSeatsVal;
//                    }else{
//                        remainingSeatsVal.remaining = remainingSeatsVal.remaining - 1;
//                        remainingSeats[barcode] = remainingSeatsVal;
//                    }
                    
                    remainingSeatsVal.remaining = remainingSeatsVal.remaining - 1;
                    if(remainingSeatsVal.remaining == 0){
                        jQuery(document).find(".cart-qty-plus").attr("disabled","disabled");
                    }
                    remainingSeats[barcode] = remainingSeatsVal;
                    
                    
                }
                remainingSeats = JSON.stringify(remainingSeats);
                console.log('after = '+remainingSeats);
        //        setCookie('remainingSeats', JSON.stringify(remainingSeats), 1);
//                updateCookie('remainingSeats', encodeURIComponent(JSON.stringify(remainingSeats)), 1);
   
            }
        }
        
//            console.log(typeof max,max,typeof $n.val(),$n.val());
        if (Number($n.val()) < Number(max)) {
            $n.val(Number($n.val()) + 1);
        }
        total_values();
    });

    var incrementMinus = buttonMinus.click(function () {
        var UrlVars = getUrlVars();
        var barcode = UrlVars.barcode;
        var $n = jQuery(this).parent(".button-counter").find(".qty");
        var amount = Number($n.val());
        console.log(amount);
        if(typeof barcode != "undefined" && barcode != ""){
//            var remainingSeats = getCookie('remainingSeats');
            if(typeof remainingSeats != 'undefined'){
                remainingSeats = JSON.parse(remainingSeats);
                console.log(remainingSeats);
                var remainingSeatsVal = remainingSeats[barcode];
                console.log(remainingSeatsVal);
    //            var totalRemainingSeats = remainingSeatsVal.total;
                if(typeof remainingSeatsVal != 'undefined' && remainingSeatsVal.hasOwnProperty('remaining')){
                    if(remainingSeatsVal.total > remainingSeatsVal.remaining && amount != 0){
                        remainingSeatsVal.remaining = remainingSeatsVal.remaining + 1;
                    }
                }
        //        console.log(remainingSeatsVal);
                remainingSeats[barcode] = remainingSeatsVal;
                remainingSeats = JSON.stringify(remainingSeats);
    //            if(remainingSeatsVal == 0){
                    jQuery(document).find(".cart-qty-plus").prop("disabled",false);
    //            }
                console.log('after = '+remainingSeats);
        //        setCookie('remainingSeats', JSON.stringify(remainingSeats), 1);
//                updateCookie('remainingSeats', encodeURIComponent(JSON.stringify(remainingSeats)), 1);
            }
        }
        if (amount > 0) {
            $n.val(amount - 1);
        }
        total_values();
    });
    
    setTimeout(function(){
        if (jQuery(document).find('.spettacolo-prices-inner').length > 0) {
            let profile_status = jQuery(document).find('.spettacolo-prices-wrapper .cart-buy-btn').attr('data-profile-status');
            let user_logged_in = STCTICKETSPUBLIC.loggedIn;
            let addToCartObject = localStorage.getItem("addToCartObject");
            addToCartObject = JSON.parse(addToCartObject);
            
            console.log(profile_status,user_logged_in,addToCartObject);
            if(addToCartObject != null){
                /** MOD SARAH **/
                addToCartObject.showDate = jQuery(document).find('.spettacolo-info-inner-wrap .list-date p').text();

                if(addToCartObject.manualSelection){
                    let addToCart = addToCartObject.addToCart;
                    addToCart = Object.values(addToCart);
                    addToCart.forEach(function (zones) {
                    zones = Object.values(zones);
                        zones.forEach(function (zone) {
                            let reductions = zone.reductions;
                            reductions = Object.values(reductions);
                            reductions.forEach(function (reduction) {
                            let seatIds = reduction.seatId;
                                seatIds.forEach(function (seatId) {
                                    console.log(seatId);
                                    jQuery(document).find('#svgSeatSvg circle[data-id="'+seatId+'"]').trigger('click');
                                });
                            });
                        });
                    });
                }else{
                    let addToCart = addToCartObject.addToCart;
                    addToCart = Object.values(addToCart);
                    addToCart.forEach(function (zones) {
                    zones = Object.values(zones);
                        zones.forEach(function (zone) {
                            let zoneId = zone.zoneId;
                            let reductions = zone.reductions;
                            reductions = Object.values(reductions);
                            reductions.forEach(function (reduction) {
                                let reductionId = reduction.reductionId;
                                let reductionQuantity = reduction.reductionQuantity;
                                for(let i = 0; i < reductionQuantity; i++){
                                    console.log(zoneId,reductionId,reductionQuantity);
                                    jQuery(document).find('.price-table .table-row[data-zoneid="'+zoneId+'"] .list-row[data-reductionid="'+reductionId+'"] .priceqty .cart-qty-counter').show();
                                    jQuery(document).find('.price-table .table-row[data-zoneid="'+zoneId+'"] .list-row[data-reductionid="'+reductionId+'"] .priceqty .cart-qty-plus').trigger('click');
                                }
                            });
                        });
                    });
                }
            }
        }
    },3000);
    
    jQuery(document).on('click', '.cart-buy-btn', function () {
        var profile_status = jQuery(this).attr('data-profile-status');
        var manualSelection = false;
        var user_logged_in = STCTICKETSPUBLIC.loggedIn;
        var addToCart = new Object();
        var UrlVars = getUrlVars();

        var ticketTitle = jQuery(document).find('.spettacolo-info-inner .list-title h3').text();
        var vcode = jQuery(document).find('.spettacolo-prices-wrapper').attr('data-vcode');
        var pcode = jQuery(document).find('.spettacolo-prices-wrapper').attr('data-pcode');
        var regData = jQuery(document).find('.spettacolo-prices-wrapper').attr('data-regData');
        var subscription = jQuery(document).find('.spettacolo-prices-wrapper').attr('data-subscription');
        var barcode = jQuery(document).find('.spettacolo-prices-wrapper').attr('data-barcode');
        var orderId = UrlVars.hasOwnProperty("orderId") ? UrlVars.orderId : "";
        
        addToCart[ticketTitle] = [];
        if (jQuery('.spettacolo-prices-inner .table-row:visible').length > 0) {
            jQuery('.spettacolo-prices-inner .table-row').each(function () {
                var zoneArr = new Object();
                var reductionArr = [];
                var zoneTitle = jQuery(this).find('.title h2').text();
                var zoneId = jQuery(this).attr('data-zoneId');
                zoneArr.zoneName = zoneTitle;
                zoneArr.zoneId = zoneId;
                jQuery(this).find('.cart-qty-counter').each(function (index) {
                    var reductionValue = new Object();
                    var qty_value = jQuery(this).find('.qty').val();
                    if (Number(qty_value) > 0) {
                        var price_value = jQuery(this).parents('.list-row').find('.row-price').attr('data-price');
                        var reductionId = jQuery(this).parents('.list-row').attr('data-reductionId');
                        var reductionName = jQuery(this).parents('.list-row').find('.row-title p').text();

                        reductionValue.reductionName = reductionName;
                        reductionValue.reductionId = reductionId;
                        reductionValue.reductionQuantity = Number(qty_value);
                        reductionValue.reductionPrice = Number(price_value);
                        reductionArr.push(reductionValue);
                    }
                });
                zoneArr.reductions = reductionArr;
                zoneArr.doBooking = 1;
                /** MOD SARAH **/
                zoneArr.showDate = $('.spettacolo-info-inner-wrap .list-date p').text();

                if (reductionArr.length !== 0) {
                    addToCart[ticketTitle].push(zoneArr);
                }
            });
        } else {
            manualSelection = true;
            let man_addToCart = new Object();
            jQuery(document).find('.spettacolo-prices-inner .selected-seat-row').each(function () {
                var zone_id = jQuery(this).attr('data-zone-id');
                var reduction_name = jQuery(this).find('.seat-price').attr('data-reduction-name');
                var reduction_id = jQuery(this).find('.seat-price').attr('data-reduction-id');
                var reduction_price = jQuery(this).find('.seat-price').attr('data-price');
                var zone_title = jQuery(this).attr('data-zone-name');
                var seat_id = jQuery(this).attr('data-seat-id');

                if (Object.hasOwn(man_addToCart, zone_id)) {
                    let man_zoneArr = man_addToCart[zone_id];
                    let selectedReductionArr = man_zoneArr.reductions;
                    if (Object.hasOwn(selectedReductionArr, reduction_id)) {
                        let currReductionArr = selectedReductionArr[reduction_id];
                        currReductionArr.reductionQuantity = currReductionArr.reductionQuantity + 1;
                        let seatIdArr = currReductionArr.seatId;
                        seatIdArr = seatIdArr.push(seat_id);
                    } else {
                        let selectedReductionValue = new Object();
                        let selectedZoneVal = new Object();

                        selectedReductionValue.reductionName = reduction_name;
                        selectedReductionValue.reductionId = reduction_id;
                        selectedReductionValue.reductionPrice = Number(reduction_price);
                        selectedReductionValue.reductionQuantity = 1;
                        selectedReductionValue.seatId = [seat_id];
                        selectedReductionArr[reduction_id] = selectedReductionValue;
                        var temp_selectedReductionArr = Object.keys(selectedReductionArr).map((key) => [Number(key), selectedReductionArr[key]]);
                        const final_selectedReductionArr = Object.fromEntries(temp_selectedReductionArr);
                        selectedZoneVal.zoneName = zone_title;
                        selectedZoneVal.zoneId = zone_id;
                        selectedZoneVal.reductions = final_selectedReductionArr;
                        selectedZoneVal.doBooking = 1;
                        man_addToCart[zone_id] = selectedZoneVal;
                    }
                } else {
                    let man_zoneArr = new Object();
                    let selectedReductionArr = [];
                    let selectedReductionValue = new Object();
                    let selectedZoneVal = new Object();

                    selectedReductionValue.reductionName = reduction_name;
                    selectedReductionValue.reductionId = reduction_id;
                    selectedReductionValue.reductionPrice = Number(reduction_price);
                    selectedReductionValue.reductionQuantity = 1;
                    selectedReductionValue.seatId = [seat_id];
                    selectedReductionArr[reduction_id] = selectedReductionValue;
                    var temp_selectedReductionArr = Object.keys(selectedReductionArr).map((key) => [Number(key), selectedReductionArr[key]]);
                    const final_selectedReductionArr = Object.fromEntries(temp_selectedReductionArr);
                    selectedZoneVal.zoneName = zone_title;
                    selectedZoneVal.zoneId = zone_id;
                    selectedZoneVal.reductions = final_selectedReductionArr;
                    selectedZoneVal.doBooking = 1;
                    /** MOD SARAH **/
                    selectedZoneVal.showDate = $('.spettacolo-info-inner-wrap .list-date p').text();
                    man_addToCart[zone_id] = selectedZoneVal;
                }
            });
            addToCart[ticketTitle] = man_addToCart;
//            ////////////////////////////////////////////
//                    let selectedZoneArr = new Object();
////                    let selectedZoneArr = [];
//                jQuery(document).find('.spettacolo-prices-inner .selected-seat-row').each(function () {
//                    let selectedReductionArr = [];                    
//                    let selectedReductionValue = new Object();
//                    let selectedZoneVal = new Object();
////                    let selectedReductionValue = [];
////                    let selectedZoneVal = [];
//                    var zone_id = jQuery(this).attr('data-zone-id');
//                    var reduction_name = jQuery(this).find('.seat-price').attr('data-reduction-name');
//                    var reduction_id = jQuery(this).find('.seat-price').attr('data-reduction-id');
//                    var reduction_price = jQuery(this).find('.seat-price').attr('data-price');
//                    var zone_title = jQuery(this).attr('data-zone-name');
//                    
////                    console.log(zone_id,zone_title,reduction_name,reduction_id,reduction_price);
//                    selectedReductionValue.reduction_name = reduction_name;
//                    selectedReductionValue.reduction_id = reduction_id;
//                    selectedReductionValue.reduction_price = reduction_price;
////                    selectedReductionValue['reduction_name'] = reduction_name;
////                    selectedReductionValue['reduction_id'] = reduction_id;
////                    selectedReductionValue['reduction_price'] = reduction_price;
////                    console.log(selectedZoneArr);
//                    if(selectedZoneArr.length != 0){
////                    console.log('if',selectedReductionValue);
////                    if(typeof selectedReductionArr[reduction_id] != 'undefined'){
//                        selectedReductionValue.reduction_qty = (selectedReductionValue.reduction_qty ? selectedReductionValue.reduction_qty : 1) + 1;
////                        selectedReductionValue['reduction_qty'] = (selectedReductionValue['reduction_qty'] ? selectedReductionValue['reduction_qty'] : 1) + 1;
////                        selectedReductionArr.reduction_id = selectedReductionValue;
//                    }else{
////                    console.log('else',selectedReductionValue);
////                        selectedReductionArr.reduction_id = selectedReductionValue;
//                        selectedReductionValue.reduction_qty = 1;
//                        selectedReductionArr[reduction_id] = selectedReductionValue;
//                    }
//                    
////                        selectedReductionArr.push(selectedReductionValue);
//                    selectedZoneVal.zoneName = zone_title;
//                    selectedZoneVal.zoneId = zone_id;
//                    selectedZoneVal.reductions = selectedReductionArr;
////                    selectedZoneArr[zone_id]['zoneName'] = zone_title;
////                    selectedZoneArr[zone_id]['zoneId'] = zone_id;
////                    selectedZoneArr[zone_id]['reductions'] = selectedReductionArr;
//                    selectedZoneArr.zone_id = selectedZoneVal;
//                    console.log('selectedZoneVal');
//                    console.log(selectedZoneVal);
//                    console.log('selectedZoneArr');
//                    console.log(selectedZoneArr);
////                    if (selectedZoneArr.length !== 0) {
////                        addToCart[ticketTitle].push(selectedZoneArr);
////                    }
////                console.log(selectedZoneArr);
//                });
////            console.log(selectedZoneArr);
//            ////////////////////////////////////////////
        }
        var subscription_list = [];
        var selection_error = false;
        if(jQuery(document).find('.select-box-form').length > 0){
            jQuery(document).find('.select-box-form .event-ticketlist').each(function(){
                var reduction_detail = new Object();
                reduction_detail.reductionId = jQuery(this).find('.seat-name').attr('data-reductionId');
                reduction_detail.zoneId = jQuery(this).find('.seat-name').attr('data-zoneId');
                reduction_detail.zoneName = jQuery(this).find('.seat-name').attr('data-zoneName');
                reduction_detail.subscription = jQuery(this).find("#subscription-code:visible").length > 0 ? jQuery(this).find("#subscription-code option:selected").val() : jQuery(this).find("#subscription-input").val();
                if(reduction_detail.subscription == 0 || reduction_detail.subscription == -1 || reduction_detail.subscription == ''){
                    jQuery(this).addClass('error');
                    selection_error = true;
                }
                subscription_list.push(reduction_detail);
            });
            console.log(subscription_list);            
        }
        console.log(addToCart, JSON.parse(JSON.stringify(addToCart)));
        
        if(selection_error){
            return false;
        }
        let ajax_data = {
                    action: 'getBookingCart',
                    addToCart: addToCart,
                    vcode: vcode,
                    pcode: pcode,
                    regData: regData,
                    manualSelection: manualSelection,
                    subscription: subscription,
                    barcode: barcode,
                    subscription_list: subscription_list,
                    orderId:orderId
                };
        if (profile_status == 'complete') {
                
            jQuery.ajax({
                url: STCTICKETSPUBLIC.ajaxurl,
                method: 'post',
                beforeSend: function () {
                    jQuery( "body" ).css('opacity', '0.2');
                },
                data: ajax_data,
                success: function (data) {
                    jQuery( "body" ).css('opacity', '1');
                    console.log(data);
                    var responseData = JSON.parse(data);
                    if (responseData.status) {
                        console.log(responseData.message);
                        if(localStorage.getItem("addToCartObject")){
                            localStorage.removeItem("addToCartObject");
                        }
                        updateCookie('remainingSeats', encodeURIComponent(remainingSeats), 1);
                        window.location.href = STCTICKETSPUBLIC.siteurl + "/" + STCTICKETSPUBLIC.cartSlug;

//                        window.location.href = STCTICKETSPUBLIC.siteurl + "/cart";
                        //                    window.location.href = STCTICKETSPUBLIC.siteurl + "/spettacolo-cart";
                    }else{
                        addToCart = {};
                        if(jQuery(document).find('.selected-seat-row').length > 0){
                            jQuery(document).find('.selected-seat-row').each(function () {
                                let data_seat_id = jQuery(this).attr("data-seat");
                                jQuery(document).find('#svgSeatSvg circle#'+data_seat_id).removeAttr("selected");
                                jQuery(document).find('#svgSeatSvg circle#'+data_seat_id).attr("fill", jQuery(document).find('#svgSeatSvg circle#'+data_seat_id).attr("data-color"));
                                jQuery(this).remove();
                                if(jQuery(document).find('.selected-seat-row').length == 0){
                                    jQuery(document).find(".total-values-wrap .total-qty-value").attr('data-count',0);
                                    jQuery(document).find(".total-values-wrap .total-qty-value").text(0);
                                    jQuery(document).find(".total-values-wrap .total-price-value").attr('data-count',0);
                                    jQuery(document).find(".total-values-wrap .total-price-value").text(0);
                                    jQuery(document).find(".total-values-wrap").hide();
                                    jQuery(document).find(".seat-selection-warning").show();                        
                                }

                            });
                        }else{
                            jQuery(document).find(".seat-selection-warning").show();
                        }
                        if(responseData.subscription){
                            jQuery(document).find('.subscription-fancybox-wrap .replace-me').append('<p class="seat-selection-error" style="color:red;">'+responseData.message+'</p>');
                        }else{
                            jQuery(document).find('.spettacolo-prices-inner').append('<p class="seat-selection-error" style="color:red;">'+responseData.message+'</p>');
                        }
                    }
                },
                error: function (request, status, error) {
                    console.log(error);
                }
            });
        } else {
            console.log('user profile is incompleted..!!');            
            localStorage.setItem("addToCartObject", JSON.stringify(ajax_data));
        }
    });
    
//    jQuery(document).on('click', '.cart-buy-btn-extra', function () {
//        var subscription_list = [];
//        if(jQuery(document).find('.select-box-form').length > 0){
//            jQuery(document).find('.select-box-form .event-ticketlist').each(function(){
//                var reduction_detail = new Object();
//                reduction_detail.reductionId = jQuery(this).find('.seat-name').attr('data-reductionId');
//                reduction_detail.zoneId = jQuery(this).find('.seat-name').attr('data-zoneId');
//                reduction_detail.zoneName = jQuery(this).find('.seat-name').attr('data-zoneName');
//                reduction_detail.subscription = jQuery(this).find("#subscription-code option:selected").val();
//                subscription_list.push(reduction_detail);
//            });
//            console.log(subscription_list);            
//        }
//    });
//    jQuery(document).on('click','.checkout-btn',function(){
//        var totalPrice = jQuery('.ticket-total-wrap .total-price').attr('data-price');
//        var totalQuantity = jQuery('.ticket-total-wrap .total-qty').attr('data-qty');
//        
//        jQuery.ajax({
//            url: STCTICKETSPUBLIC.ajaxurl,
//            method: 'post',
//            data: {
//                action: 'getCheckout',
//                totalPrice : totalPrice,
//                totalQuantity : totalQuantity
//            },
//            success: function (data) {
////                    console.log(data);
//                var responseData = JSON.parse(data);
//                if(responseData.statue){
//                    console.log(responseData.message);
//                    var responseArr = responseData.message;
//                    var redirecturl = responseArr.redirecturl
//                    console.log(redirecturl);
//                    if(redirecturl){
//                        window.location.href = redirecturl;
//                    }
//                }
//            },
//            error: function (request, status, error) {
//                console.log(error);
//            }
//        });
//    });
    jQuery(document).on('click', '.empty-cart-btn', function () {
//        alert('clicked');
        jQuery.ajax({
            url: STCTICKETSPUBLIC.ajaxurl,
            method: 'post',
            beforeSend: function () {
                jQuery( "body" ).css('opacity', '0.2');
            },
            data: {
                action: 'emptyCart'
            },
            success: function (data) {
                jQuery( "body" ).css('opacity', '1');
//                    console.log(data);
                var responseData = JSON.parse(data);
                if (responseData.status) {
                    console.log(responseData.message);
//                    window.location.href = STCTICKETSPUBLIC.siteurl + "/spettacolo-cart";
                }
            },
            error: function (request, status, error) {
                console.log(error);
            }
        });
    });
    jQuery(document).on('click', '.woocommerce-cart-form .cart_item .product-remove a', function () {
//        jQuery('.empty-cart-btn').click();
        jQuery(document).find('.empty-cart-btn').trigger("click");
//        console.log('empty-cart-btn clicked');
    });
    if (jQuery('.wc-spettacolo-cart-wrapper').length > 0) {
        jQuery('body').addClass('wc-spettacolo-cart-body');
    }
    if (jQuery('.spettacolo-prices-wrapper').length > 0) {
        jQuery('body').addClass('spettacolo-seat-map-body');
    }
    if (jQuery('.spettacolo-prices-inner').length > 0) {
        jQuery('.spettacolo-prices-inner .tab-content').hide();
        var target_class = jQuery('.spettacolo-prices-inner .tab-links.active').attr('data-target-tab');
        jQuery('.spettacolo-prices-inner .tab-content.' + target_class).show();
    }

    jQuery(document).on('click', '#place_order', function (e) {
        e.preventDefault();
        
        var recaptcha = "";
        if(jQuery(document).find('#g-recaptcha-response').length > 0){
            recaptcha = jQuery(document).find('#g-recaptcha-response').val();
        }
        
        jQuery.ajax({
            url: STCTICKETSPUBLIC.ajaxurl,
            method: 'post',
            beforeSend: function () {
                jQuery( "body" ).css('opacity', '0.2');
            },
            data: {
                action: 'checkRecaptcha',
                recaptcha:recaptcha
            },
            success: function (data) {
                jQuery( "body" ).css('opacity', '1');
                var responseData = JSON.parse(data);
                if (responseData.status) {
                    console.log(responseData.message);
                    var valid = true;
                    jQuery(document).find('input').each(function () {
                        if (jQuery(this).parents('.form-row').hasClass('validate-required')) {
                            if (jQuery(this).val() == '') {
                                valid = false;
                            }
                        }
                    });
                    if (valid == false) {
            //            alert('per favore inserisci i tuoi dati');
                        jQuery(document).find('#order_review').append('<p style="color:red;">per favore inserisci i tuoi dati</p>');
                        jQuery(document).find('#order_review .checkout-error').hide();
                    } else {
                        jQuery.ajax({
                            url: STCTICKETSPUBLIC.ajaxurl,
                            method: 'post',
                            beforeSend: function () {
                                jQuery( "body" ).css('opacity', '0.2');
                            },
                            data: {
                                action: 'getCheckout',
                            },
                            success: function (data) {
                                jQuery( "body" ).css('opacity', '1');
                                var responseData = JSON.parse(data);
                                if (responseData.status) {
                                    console.log(responseData.message);
                                    var responseArr = responseData.message;
                                    var redirecturl = responseArr.redirecturl
                                    console.log(redirecturl);
                                    if (redirecturl) {
                                        window.location.href = redirecturl;
                                    }
                                } else {
                                    console.log(responseData.message['@attributes']);
                                    let responseError = responseData.message['@attributes'];
                                    if(responseError.errcode){
                                        if(jQuery(document).find('#order_review .checkout-error').length > 0){
                                            jQuery(document).find('#order_review .checkout-error').text(stcTicketsText.str_8 + (responseError.hasOwnProperty ? ' : '+responseError.errstring : ''));                            
                                        }else{
                                            jQuery(document).find('#order_review').append('<p class="checkout-error" style="color:red;">'+ stcTicketsText.str_8 + (responseError.hasOwnProperty ? ' : '+responseError.errstring : '') +'</p>');                            
                                        }
                                    }
                                }
                            },
                            error: function (request, status, error) {
                                console.log(error);
                            }
                        });
                    }
                } else {
                    console.log(responseData.message);
                    let responseError = responseData.message;
                    if(responseError){
                        if(jQuery(document).find('#order_review .checkout-error').length > 0){
                            jQuery(document).find('#order_review .checkout-error').text(responseError);                            
                        }else{
                            jQuery(document).find('#order_review').append('<p class="checkout-error" style="color:red;">'+ responseError +'</p>');                            
                        }
                    }
                }
            },
            error: function (request, status, error) {
                console.log(error);
            }
        });
        
//        var first_name = jQuery('#customer_details').find('input#billing_first_name').val();
//        var last_name = jQuery('#customer_details').find('input#billing_last_name').val();
//        var last_name = jQuery('#customer_details').find('input#billing_last_name').val();
//        
//        if(first_name && last_name){
//            alert('not empty');
//        }else{
//            alert('empty');
//        }
    });
    
    jQuery(document).on('input','#reg_billing_phone'+ STCTICKETSPUBLIC.FORM_FIELD_CHARS, function() {
        var phoneNumber = jQuery(this).val();
        var country_code = jQuery(document).find('#reg_country_code'+ STCTICKETSPUBLIC.FORM_FIELD_CHARS).val();
        var isValid = validatePhoneNumber(phoneNumber,country_code);

        if (isValid) {
            jQuery(document).find('#phoneNumberError').hide();
            jQuery(this).removeClass('is-invalid');
        } else {
            jQuery(document).find('#phoneNumberError').show();
            jQuery(this).addClass('is-invalid');
        }
    });

    jQuery(document).on('click', '.send-register-otp', function (e) {
        e.preventDefault();
                
        var data = {};
            var password = jQuery(document).find('.woocommerce-form-register #reg_password').val();
            var first_name = jQuery(document).find('#reg_fname').val();
            var last_name = jQuery(document).find('#reg_lname').val();
//            var billing_phone = jQuery(document).find('#reg_billing_phone' + STCTICKETSPUBLIC.FORM_FIELD_CHARS ).val();
//            var billing_phone_spam = jQuery(document).find('#reg_billing_phone').val();
            var country_code = jQuery(document).find('#reg_country_code'+ STCTICKETSPUBLIC.FORM_FIELD_CHARS).val();
            var email = jQuery(document).find('#reg_email').val();
            var dob = jQuery(document).find('#reg_dob').val();
            var place_of_birth = jQuery(document).find('#reg_place_of_birth').val();
            var registerOtp = jQuery(document).find('#registerotp').val();
            var $this = jQuery(this);
            
            var username = email;
            var privacy_checkbox_1 = jQuery(document).find("#privacy_policy_reg").prop('checked');
//            var privacy_checkbox_2 = jQuery(document).find("input[name=_mc4wp_subscribe_wp-registration-form]:visible").prop('checked');
            
//            var isValid = validatePhoneNumber(billing_phone);
//            
//            if (!isValid) {
//                e.preventDefault();
//                jQuery(document).find('#phoneNumberError').show();
//                jQuery(document).find('#reg_billing_phone'+ STCTICKETSPUBLIC.FORM_FIELD_CHARS).addClass('is-invalid');
//            } else {
//                jQuery(document).find('#phoneNumberError').hide();
//                jQuery(document).find('#reg_billing_phone'+ STCTICKETSPUBLIC.FORM_FIELD_CHARS).removeClass('is-invalid');                
//            }
            
            console.log('send-register-otp clicked');
            var recaptcha = "";
            if(jQuery(document).find('#g-recaptcha-response').length > 0){
                recaptcha = jQuery(document).find('#g-recaptcha-response').val();
            }

            jQuery.ajax({
                url: STCTICKETSPUBLIC.ajaxurl,
                method: 'post',
                beforeSend: function () {
                    jQuery( "body" ).css('opacity', '0.2');
                },
                data: {
                    action: 'checkRecaptcha',
                    recaptcha:recaptcha
                },
                success: function (data) {
                    jQuery( "body" ).css('opacity', '1');
                    var responseData = JSON.parse(data);
                    if (responseData.status) {
                        console.log(responseData.message);
                        
//                        setTimeout(function() {
                        if (first_name && last_name && username && email && password && dob && place_of_birth) {
                            if(jQuery(document).find("form .error-empty").length > 0){
                                jQuery(document).find('form .error-empty').remove();
                            }
                            if(!privacy_checkbox_1 ){
                                if(jQuery(document).find(".privacy-policy-err").length > 0){
                                    jQuery(document).find(".privacy-policy-err").text("Si prega di accettare l'informativa sulla privacy.");
                                }else{
                                    jQuery(document).find(".woocommerce-form-register__submit").parent().prepend("<p class='privacy-policy-err' style='color: red !important;'>Si prega di accettare l'informativa sulla privacy.</p>");
                                }
                                return false;
                            }else{
                                if(jQuery(document).find(".privacy-policy-err").length > 0){
                                    jQuery(document).find(".privacy-policy-err").remove();
                                }
                            }
                            console.log('send-register-otp clicked');
                            jQuery(document).find('.woocommerce-form-register #reg_fname').attr('style', 'border-color: #d8dbe2 !important');
                            jQuery(document).find('.woocommerce-form-register #reg_lname').attr('style', 'border-color: #d8dbe2 !important');
                            jQuery(document).find('.woocommerce-form-register #reg_username').attr('style', 'border-color: #d8dbe2 !important');
                            jQuery(document).find('.woocommerce-form-register #reg_password').attr('style', 'border-color: #d8dbe2 !important');
                            jQuery(document).find('.woocommerce-form-register #reg_email').attr('style', 'border-color: #d8dbe2 !important');
                            jQuery(document).find(".no-sms-err").remove();                
                            var isUserBlocked = false;
                            jQuery.ajax({
                                url: STCTICKETSPUBLIC.ajaxurl,
                                method: 'post',
                                    beforeSend: function () {
                                        jQuery( "body" ).css('opacity', '0.2');
                                    },
                                        data: {
                                            action: 'checkUserEmail',
                                            email: email,
                                            username: username,
                                        },
                                success: function (data) {
                                        jQuery( "body" ).css('opacity', '1');
                                    var responseData = JSON.parse(data);
                                            console.log(responseData);
                                            if (responseData.error != '' && responseData.status == false) {
                                        if(jQuery(document).find('form .error-display').length > 0){
                                            jQuery(document).find('form .error-display').text(responseData.error);
                                        }else{
                                            jQuery(document).find('form .otp-box').after('<p class="error-display" style="color: red !important; text-align: center;">' + responseData.error + '</p>');                                        
                                        }
                                    }else{
                                        jQuery(document).find('form .error-display').remove();
                                        if(jQuery(document).find('.woocommerce-form-update__submit').length > 0){
                                            jQuery.ajax({
                                                url: STCTICKETSPUBLIC.ajaxurl,
                                                method: 'post',
                                                beforeSend: function () {
                                                    jQuery( "body" ).css('opacity', '0.2');
                                                },
                                                data: {
                                                    action: 'UpdateUserProfile',
            //                                                billing_phone: billing_phone,
                                                    country_code: country_code,
                                                    place_of_birth: place_of_birth,
                                                    dob: dob,
                                                    email: email,       
                                                    first_name: first_name,
                                                    last_name: last_name,
                                                },
                                                success: function (data) {
                                                    jQuery( "body" ).css('opacity', '1');
                                                    var responseData = JSON.parse(data);
                                                    console.log(responseData);
                                                    jQuery(document).find('.cart-buy-btn').attr('data-profile-status','complete');
                                                    jQuery(document).find('.cart-buy-btn[data-profile-status="complete"]').removeAttr('data-fancybox data-src');
                                                    jQuery.fancybox.close();
                                                },
                                                error: function (request, status, error) {
                                                    console.log(error);
                                                }
                                            });
                                        }else {
                                            jQuery(document).find('.woocommerce-form-register__submit').click();
                                        }
                                    }
                                },
                                error: function (request, status, error) {
                                    console.log(error);
                                }
                            });
                        } else {
//                            jQuery(document).find('#g-recaptcha-response').val('');
//                            if (typeof grecaptcha !== 'undefined') {
//                                console.log('test',grecaptcha);
//                                // reCAPTCHA script is loaded
//                                // Your code that interacts with grecaptcha object here
//                            } else {
//                                console.log('reCAPTCHA script not yet loaded.');
//                            }                        
//jQuery('#reCaptchDiv').html('');
//        // Render reCAPTCHA widget
//        widgetId = grecaptcha.render('reCaptchDiv', {
//            'sitekey' : '6LfqMQgqAAAAADbNO6xczPwkjiKYhSN8KN9J9vLd',
//            'theme' : 'light'  // Optional: change theme if needed
//        });
//                            console.log('widgetId', widgetId);
//                            grecaptcha.reset(widgetId);
//                            grecaptcha.render('reCaptchDiv', {
//                                 'sitekey' : '6LfqMQgqAAAAADbNO6xczPwkjiKYhSN8KN9J9vLd',
//                                'theme' : 'light'  // Optional: change theme if needed
//                            });
                            grecaptcha.reset(widgetId);
                            if(jQuery(document).find('form .error-empty').length > 0){
                                jQuery(document).find('form .error-empty').text("Non hai compilato tutti i campi obbligatori");
                            }else{
                                jQuery(document).find('form .otp-box').after('<p class="error-empty" style="color: red !important; text-align: center;">Non hai compilato tutti i campi obbligatori</p>');
                            }
                            if (!first_name) {
                                jQuery(document).find('.woocommerce-form-register #reg_fname').attr('style', 'border-color: red !important');
                            }else{
                                jQuery(document).find('.woocommerce-form-register #reg_fname').removeAttr('style');
                            }
                            if (!last_name) {
                                jQuery(document).find('.woocommerce-form-register #reg_lname').attr('style', 'border-color: red !important');
                            }else{
                                jQuery(document).find('.woocommerce-form-register #reg_lname').removeAttr('style');
                            }
                            if (!password) {
                                jQuery(document).find('.woocommerce-form-register #reg_password').attr('style', 'border-color: red !important');
                            }else{
                                jQuery(document).find('.woocommerce-form-register reg_password').removeAttr('style');
                            }
                            if (!email) {
                                jQuery(document).find('.woocommerce-form-register #reg_email').attr('style', 'border-color: red !important');
                            }else{
                                jQuery(document).find('.woocommerce-form-register #reg_email').removeAttr('style');
                            }
                            if (!dob) {
                                jQuery(document).find('.woocommerce-form-register #reg_dob').attr('style', 'border-color: red !important');
                            }else{
                                jQuery(document).find('.woocommerce-form-register #reg_dob').removeAttr('style');
                            }
                            if (!place_of_birth) {
                                jQuery(document).find('.woocommerce-form-register #reg_place_of_birth').attr('style', 'border-color: red !important');
                            }else{
                                jQuery(document).find('.woocommerce-form-register #reg_place_of_birth').removeAttr('style');
                            }
                        }
//                        }, 1000);
                    } else {
                        console.log(responseData.message);
                        let responseError = responseData.message;
                        if(responseError){
                            if(jQuery(document).find('form .error-empty').length > 0){
                                jQuery(document).find('form .error-empty').text(responseError);
                            }else{
                                jQuery(document).find('form .otp-box').after('<p class="error-empty" style="color: red !important; text-align: center;">'+ responseError +'</p>');
                            }
                        }
                    }
                },
                error: function (request, status, error) {
                    console.log(error);
                }
            });
            
            
//        }
    });
    jQuery(document).on('click', '.spettacolo-prices-inner .tab-links', function (e) {
        e.preventDefault();
        if (!jQuery(this).hasClass('active')) {
            var UrlVars = getUrlVars();
            if (UrlVars.selectionMode == 0) {
                UrlVars.selectionMode = 1;
            } else {
                UrlVars.selectionMode = 0;
            }
            var queryString = '?';
            for (var i in UrlVars) {
                queryString += i + '=' + encodeURIComponent(UrlVars[i]) + '&';
            }
            queryString = queryString.slice(0, -1);
            var url = window.location.href;
            var page = url.split('?')[0];
            window.location.href = page + queryString;
        }
    });
    
//    $(document).mousemove(function(event) {
//        mouseX = event.pageX;
//        mouseY = event.pageY;
//    });
    if (jQuery(document).find('.spettacolo-prices-inner .tab-links.active').attr('data-target-tab') == 'selected-seats-table') {
        var UrlVars = getUrlVars();
        var barcode = UrlVars.barcode;
        
        jQuery(document).on('mouseenter', '#svgSeatSvg circle', function (event) {
            var circle_click_status = jQuery(this).attr('data-status');// 11-05-2023 Change
            if (circle_click_status == '1') {// 11-05-2023 Change
                    var tooltip_seat_desc = jQuery(this).attr('data-seat-desc');
                    var tooltip_zone_desc = jQuery(this).attr('data-zone-desc');
                    var tooltip_price = jQuery(this).attr('data-price');
                    jQuery(document).find(".tooltip .tooltip-title").html(tooltip_zone_desc);
                    jQuery(document).find(".tooltip .tooltip-subtitle").html(tooltip_seat_desc);
                    jQuery(document).find(".tooltip .tooltip-price").html(tooltip_price+' &euro;');
                    var mouseX = event.pageX;
                    var mouseY = event.pageY;
                    console.log(mouseX,mouseY,jQuery(document).find(".tooltip").outerHeight(),Number(jQuery(document).find(".spettacolo-prices-img").outerWidth()) + Number(jQuery(document).find(".spettacolo-prices-img").offset().left))
                    if(mouseX + 100 > Number(jQuery(document).find(".spettacolo-prices-img").outerWidth()) + Number(jQuery(document).find(".spettacolo-prices-img").offset().left)){
                        jQuery(document).find(".tooltip").css({
                            top: mouseY - jQuery(document).find(".spettacolo-prices-img").offset().top - jQuery(document).find(".tooltip").outerHeight() - 10, // Adjust for the tooltip's height
                            left: mouseX - jQuery(document).find(".spettacolo-prices-img").offset().left - 100,
                            display: "block",
                        });
                    }else{
                        jQuery(document).find(".tooltip").css({
                            top: mouseY - jQuery(document).find(".spettacolo-prices-img").offset().top - jQuery(document).find(".tooltip").outerHeight() - 10, // Adjust for the tooltip's height
                            left: mouseX - jQuery(document).find(".spettacolo-prices-img").offset().left - 20,
                            display: "block",
                        });
                    }
//                if(jQuery(document).find(".tooltip").not(":visible")){
//                    jQuery(document).find(".tooltip").show();
//                }

//                
                
//                var text_width = (text.length * 15);
//                var circle_x = jQuery(this).attr('cx');
//                var circle_y = jQuery(this).attr('cy');
//
//                var rect_width = text_width > 200 ? 250 : 200;
//                var rect_height = 30;
//
//                var rect_x = jQuery(this).attr('cx');
//                var rect_y = jQuery(this).attr('cy');
//                console.log('before',rect_x,rect_y);
//                rect_x = Number(rect_x) - 448.765625;
//                rect_y = Number(rect_y) - 187.5;
//                console.log('after',rect_x,rect_y);
////                if(circle_x < 700 && circle_y < 400){
////                    rect_x = jQuery(document).find('.spettacolo-prices-img').width() - rect_width - 10;
////                    rect_y = 10;
////                }else{
////                    rect_x = 10;
////                    rect_y = 10;
////                }
//                
//                var text_x = Number(rect_x) + 10;
//                var text_y = Number(rect_y) + 20;
//
//                createRectInSvg(rect_width, rect_height, rect_x, rect_y);
//                createTextInSvg(text_x, text_y, text);
                //        jQuery(document).find('#svgSeatSvg').append('<rect id="svgSeatRectTooltip" width="300" height="60" fill="#404040" x="1002" y="850" rx="10" ry="10"></rect><text id="svgSeatTextTooltip" font-family="arial" font-size="25" size="25" family="arial" x="1012" y="880" fill="white">I love SVG!</text>');
                //        jQuery(document).find('#svgSeatSvg').append('<rect id="svgSeatRectTooltip" width="'+rect_width+'" height="'+rect_height+'" fill="#404040" x="'+rect_x+'" y="'+rect_y+'" rx="10" ry="10"></rect><text id="svgSeatTextTooltip" font-family="arial" font-size="25" size="25" family="arial" x="'+text_x+'" y="'+text_y+'" fill="white">'+text+'</text>');
            }// 11-05-2023 Change
        });
        jQuery(document).on('mouseleave', '#svgSeatSvg circle', function (e) {
//            jQuery(document).find('#svgSeatSvg #svgSeatRectTooltip,#svgSeatSvg #svgSeatTextTooltip').remove();
            jQuery(document).find(".tooltip").hide();
//            jQuery(document).find(".tooltip").css({
//                    top: 0, // Adjust for the tooltip's height
//                    left: 0,
//                });
        });
        jQuery(document).on('click', '#svgSeatSvg circle', function (e) {
            var UrlVars = getUrlVars();
            var barcode = UrlVars.barcode;
            if(typeof barcode != "undefined" && barcode != ""){
                if(typeof remainingSeats != 'undefined'){
                    remainingSeats = JSON.parse(remainingSeats);
                    var remainingSeatsVal = remainingSeats[barcode];
                    if(typeof remainingSeatsVal != 'undefined' && remainingSeatsVal.hasOwnProperty('remaining')){
                        let current_selected_seat = jQuery(document).find('.spettacolo-prices-inner .selected-seat-row:visible').length;
                            if(current_selected_seat < remainingSeatsVal.remaining){
                                console.log('if ',remainingSeatsVal.remaining,current_selected_seat);
//                                remainingSeatsVal.remaining = remainingSeatsVal.remaining - current_selected_seat - 1;
                                if(current_selected_seat + 1 == remainingSeatsVal.remaining){
                                    jQuery(document).find('circle:not([selected="selected"])').prop('disabled', true);
//                                    jQuery(document).find('circle:not([selected="selected"])').css('pointer-events', 'none');
                                }
                            }else{
                                jQuery(document).find('circle:not([selected="selected"])').prop('disabled', true);
                                console.log('else ',remainingSeatsVal.remaining,current_selected_seat);
                            }
                    }
                    remainingSeats[barcode] = remainingSeatsVal;
                    remainingSeats = JSON.stringify(remainingSeats);
                }
            }
            
            var user_logged_in = STCTICKETSPUBLIC.loggedIn;
            var circle_click_status = jQuery(this).attr('data-status');// 11-05-2023 Change
            if (circle_click_status == '1') {// 11-05-2023 Change
                var currentSeat = jQuery(this).attr('id');
                var currentSeatId = jQuery(this).attr('data-id');
                if (jQuery(this).attr("selected")) {
                    jQuery(this).removeAttr("selected");
                    jQuery(this).attr("fill", jQuery(this).attr("data-color"));
                    if(jQuery(document).find('.selected-seat-row').length > 0){
                        jQuery(document).find('.selected-seat-row').each(function () {
                            if (currentSeat == jQuery(this).attr("data-seat")) {
                                jQuery(this).remove();
                                if(jQuery(document).find('.selected-seat-row').length == 0){
                                    jQuery(document).find(".seat-selection-warning").show();                        
                                }
//                                jQuery(document).find('circle').css('pointer-events', '');
                                jQuery(document).find('circle').prop('disabled', false);
                            }
                        });
                    }else{
                        jQuery(document).find(".seat-selection-warning").show();
                    }
                } else {
                    jQuery(document).find(".seat-selection-warning").hide();
                    jQuery(this).attr("selected", "selected");
                    jQuery(this).attr("fill", "#FF0000");

                    // listing of selected seats
                    var macrozones, zone, reductions, reduction_price, zone_id, zone_name, reduction_name, reduction_id;
                    var currentZoneId = jQuery(this).attr('data-zone-id');
                    if (jQuery('.spettacolo-prices-inner .tab-links.active').attr('data-target-tab') == 'selected-seats-table') {
                        var currentPricing = JSON.parse(globalJsPricing);
                        var currentextGetMapData = JSON.parse(extGetMapDataPricing);
                        macrozones = currentPricing.macrozone;
                    }
                    console.log(currentextGetMapData);
                    if (Object.keys(macrozones)[0] == '0') {
                        macrozones.forEach(function (macrozone) {
                            zone = macrozone.zone;
                            if (zone['@attributes'].extGetMapDataId == currentZoneId) {
                                zone_id = zone['@attributes'].id;
                                zone_name = zone.description;
                                reductions = zone.reduction;
//                                console.log(reductions);
                                reduction_price = reductions.hasOwnProperty(0) ? (reductions[0].price) / 100 : (reductions.price) / 100;
                                reduction_name = reductions.hasOwnProperty(0) ? reductions[0].description : reductions.description;
                                reduction_id = reductions.hasOwnProperty(0) ? reductions[0]['@attributes'].id : reductions['@attributes'].id;
                            }
                        });
                    } else {
                        zone = macrozones.zone;
                        if (zone['@attributes'].extGetMapDataId == currentZoneId) {
                            zone_id = zone['@attributes'].id;
                            zone_name = zone.description;
                            reductions = zone.reduction;
                            reduction_price = reductions.hasOwnProperty(0) ? (reductions[0].price) / 100 : (reductions.price) / 100;
                            reduction_name = reductions.hasOwnProperty(0) ? reductions[0].description : reductions.description;
                            reduction_id = reductions.hasOwnProperty(0) ? reductions[0]['@attributes'].id : reductions['@attributes'].id;
                        }
                    }
                    var reduction_html = '',
                    curr_reduction_name = '',
                    curr_reduction_price = '',
                    curr_reduction_id = '';
//                    console.log(reductions);
                    if(reductions.hasOwnProperty(0)){
                        reductions.forEach(function (reduction, index) {
                            let reduction_flag = false;
                            var temp_reduction_name = reduction.description;
                            if(user_logged_in){
                                if(temp_reduction_name != "INTERO"){
                                    reduction_html = '';
                                    curr_reduction_name = reduction.description;
                                    curr_reduction_price = (reduction.price) / 100;
                                    if(typeof barcode != "undefined" && barcode != ""){
                                        curr_reduction_price = 0.00;                                        
                                    }
                                    curr_reduction_id = reduction['@attributes'].id;
                                    reduction_html += '<option value="' + index + '" data-price="' + curr_reduction_price + '" data-reduction-name="' + curr_reduction_name + '" data-reduction-id="' + curr_reduction_id + '">' + curr_reduction_name + ' ' + curr_reduction_price + ' &euro;' + '</option>';
                                    reduction_flag = true;                                    
                                } else{
                                    if(temp_reduction_name == "INTERO"){
                                        curr_reduction_name = reduction.description;
                                        curr_reduction_price = (reduction.price) / 100;
                                        if(typeof barcode != "undefined" && barcode != ""){
                                            curr_reduction_price = 0.00;                                        
                                        }
                                        curr_reduction_id = reduction['@attributes'].id;
                                        reduction_html += '<option value="' + index + '" data-price="' + curr_reduction_price + '" data-reduction-name="' + curr_reduction_name + '" data-reduction-id="' + curr_reduction_id + '">' + curr_reduction_name + ' ' + curr_reduction_price + ' &euro;' + '</option>';
                                        reduction_flag = true;
                                    }
                                }
                            }else{
                                if(temp_reduction_name == "INTERO"){
                                    curr_reduction_name = reduction.description;
                                    curr_reduction_price = (reduction.price) / 100;
                                    if(typeof barcode != "undefined" && barcode != ""){
                                        curr_reduction_price = 0.00;                                        
                                    }
                                    curr_reduction_id = reduction['@attributes'].id;
                                    reduction_html += '<option value="' + index + '" data-price="' + curr_reduction_price + '" data-reduction-name="' + curr_reduction_name + '" data-reduction-id="' + curr_reduction_id + '">' + curr_reduction_name + ' ' + curr_reduction_price + ' &euro;' + '</option>';
                                    reduction_flag = true;
                                }
                            }
                        });
                    }else{
                        let reduction_flag = false;
                        var temp_reduction_name = reductions.description;
                        if(user_logged_in){
                            if(temp_reduction_name != "INTERO"){
                                reduction_html = '';
                                curr_reduction_name = reductions.description;
                                curr_reduction_price = (reductions.price) / 100;
                                if(typeof barcode != "undefined" && barcode != ""){
                                    curr_reduction_price = 0.00;                                        
                                }
                                curr_reduction_id = reductions['@attributes'].id;
                                reduction_html += '<option value="0" data-price="' + curr_reduction_price + '" data-reduction-name="' + curr_reduction_name + '" data-reduction-id="' + curr_reduction_id + '">' + curr_reduction_name + ' ' + curr_reduction_price + ' &euro;' + '</option>';
                                reduction_flag = true;                                    
                            } else{
                                if(temp_reduction_name == "INTERO"){
                                    curr_reduction_name = reductions.description;
                                    curr_reduction_price = (reductions.price) / 100;
                                    if(typeof barcode != "undefined" && barcode != ""){
                                        curr_reduction_price = 0.00;                                        
                                    }
                                    curr_reduction_id = reductions['@attributes'].id;
                                    reduction_html += '<option value="0" data-price="' + curr_reduction_price + '" data-reduction-name="' + curr_reduction_name + '" data-reduction-id="' + curr_reduction_id + '">' + curr_reduction_name + ' ' + curr_reduction_price + ' &euro;' + '</option>';
                                    reduction_flag = true;
                                }
                            }
                        } else{
                            if(temp_reduction_name == "INTERO"){
                                curr_reduction_name = reductions.description;
                                curr_reduction_price = (reductions.price) / 100;
                                if(typeof barcode != "undefined" && barcode != ""){
                                    curr_reduction_price = 0.00;                                        
                                }
                                curr_reduction_id = reductions['@attributes'].id;
                                reduction_html += '<option value="0" data-price="' + curr_reduction_price + '" data-reduction-name="' + curr_reduction_name + '" data-reduction-id="' + curr_reduction_id + '">' + curr_reduction_name + ' ' + curr_reduction_price + ' &euro;' + '</option>';
                                reduction_flag = true;
                            }
                        }
                    }
                    var html = '<div class="change-seat-dropdown">' +
                            '<select class="event-selector custom-select">' +
                            reduction_html +
                            '</select>' +
                            '</div>'
                    jQuery(document).find('.selected-seats-table').append("<div class='selected-seat-row' data-seat='" + currentSeat + "' data-seat-id='" + currentSeatId + "' data-zone-id='" + zone_id + "' data-zone-name='" + zone_name + "'><div class='seat-desc'>" + jQuery(this).attr('data-seat-desc') + "</div>" + html + "<div class='seat-price-wrap'><div class='seat-price' data-price='" + curr_reduction_price + "' data-reduction-name='" + curr_reduction_name + "' data-reduction-id='" + curr_reduction_id + "'>" + curr_reduction_price + ' &euro;' + "</div><div class='seat-delete' data-seat-id='" + currentSeatId + "'><svg width='800px' height='800px' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M10 12V17' stroke='#000000' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/><path d='M14 12V17' stroke='#000000' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/><path d='M4 7H20' stroke='#000000' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/><path d='M6 10V18C6 19.6569 7.34315 21 9 21H15C16.6569 21 18 19.6569 18 18V10' stroke='#000000' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/><path d='M9 5C9 3.89543 9.89543 3 11 3H13C14.1046 3 15 3.89543 15 5V7H9V5Z' stroke='#000000' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/></svg></div></div></div>");
                    
                    jQuery(document).find('.seat-selection-error').remove();
                }
                total_values();
                var current_zone_id = jQuery(this).attr('data-zone-id');
                jQuery('.price-table .table-row').each(function () {
                    var data_zoneid = jQuery(this).attr('data-zoneid');
                    if (current_zone_id == data_zoneid) {
//                        console.log(data_zoneid);
                    }
                });
            }// 11-05-2023 Change
        });
        jQuery(document).on('change', '.event-selector', function (e) {
            var new_selected_price = jQuery(this).find('option:selected').attr('data-price');
            var data_reduction_name = jQuery(this).find('option:selected').attr('data-reduction-name');
            var data_reduction_id = jQuery(this).find('option:selected').attr('data-reduction-id');
            jQuery(this).parents('.selected-seat-row').find('.seat-price').html(new_selected_price + ' &euro;');
            jQuery(this).parents('.selected-seat-row').find('.seat-price').attr('data-price', new_selected_price);
            jQuery(this).parents('.selected-seat-row').find('.seat-price').attr('data-reduction-name', data_reduction_name);
            jQuery(this).parents('.selected-seat-row').find('.seat-price').attr('data-reduction-id', data_reduction_id);
            total_values();
        });
        jQuery(document).on('click', '.selected-seats-table .seat-delete', function (e) {
            var seat_id = jQuery(this).attr('data-seat-id');
            jQuery(document).find('#svgSeatSvg circle[data-id="'+seat_id+'"]').trigger('click');
        });
    } else {
//        console.log('price-table');
        jQuery(document).on('mouseenter', '.price-table .table-row', function (e) {
            var currentZoneId = jQuery(this).attr('data-zoneid');
            var currentColor = jQuery(this).attr('data-color');
            jQuery('.price-table .table-row').removeClass('active');
            jQuery(this).addClass('active');
            jQuery('.price-table .table-row:not(.active)').find('.title span').css('background-color',"#e0e5e9");
            jQuery(this).find('.title span').css('background-color',currentColor);
            jQuery('#svgSeatSvg circle.active').each(function () {// 11-05-2023 Change
//            jQuery('#svgSeatSvg circle').each(function () {
                if (jQuery(this).attr('data-zone-id') == currentZoneId) {
                    jQuery(this).attr('fill', currentColor);
                } else {
                    jQuery(this).attr('fill', "#404040");
                }
            });
        });
        jQuery(document).on('mouseleave', '.price-table .table-row', function (e) {
            jQuery('.price-table .table-row').removeClass('active');
            jQuery('.price-table .table-row').each(function () {
                jQuery(this).find('.title span').css('background-color',jQuery(this).attr('data-color'));
            });
            jQuery('#svgSeatSvg circle.active').each(function () {// 11-05-2023 Change
//            jQuery('#svgSeatSvg circle').each(function () {
                jQuery(this).attr('fill', jQuery(this).attr('data-color'));
            });
        });
    }
    jQuery(".spettacolo-prices-wrap .table-row .list-row").css("display", "none");
    jQuery(document).on('click', '.spettacolo-prices-wrap .table-row .title-wrap', function () {
        jQuery(".open").not(this).removeClass("open").nextAll().slideUp(300);
        jQuery(this).toggleClass("open").nextAll().slideToggle(300);
    });
    setInterval(makeTimer, 1000);
    
    // Function to update the cart count
    
    if(jQuery(document).find('.cart-count').length > 0){
        // Function to calculate the total reductionQuantity
    function calculateTotalReductionQuantity(data) {
        var totalQuantity = 0;
        let bookedSeatCount = 0;

        jQuery.each(data.cart_contents, function (key, content) {
            jQuery.each(content.booked_subs_seats, function (index, booked_subs_seat) {
                bookedSeatCount = bookedSeatCount + (booked_subs_seat.length > 0 ? booked_subs_seat.length : 0);
            });
            jQuery.each(content.selected_seat_price, function (index, selectedPrice) {
                jQuery.each(selectedPrice, function (artist, zones) {
                    jQuery.each(zones, function (zoneId, zoneData) {
                        jQuery.each(zoneData.reductions, function (reductionId, reduction) {
                            totalQuantity += parseInt(reduction.reductionQuantity);
                        });
                    });
                });
            });
        });
        console.log(totalQuantity,bookedSeatCount);
        totalQuantity = totalQuantity - bookedSeatCount;

        return totalQuantity;
    }

    // Calculate the total reductionQuantity
    var cartData = JSON.parse(STCTICKETSPUBLIC.cartData);
    console.log(cartData);
    var totalQuantity = calculateTotalReductionQuantity(cartData);
    console.log(totalQuantity);
    jQuery(document).find('.cart-count').text(Math.abs(totalQuantity));
    }
    
    if(jQuery(document).find('.spettacolo-tickets .barcode').length > 0){
        jQuery(document).find('.spettacolo-tickets .barcode').each(function(){
            var barcode = jQuery(this).attr('data-barcode');
            var remainingSeats = getCookie('remainingSeats');
            if(typeof remainingSeats != 'undefined' && remainingSeats != ''){
                remainingSeats = JSON.parse(remainingSeats);
                var remainingSeatsVal = remainingSeats[barcode];
                if(typeof remainingSeatsVal != 'undefined' && remainingSeatsVal.hasOwnProperty('remaining')){
                    if(remainingSeatsVal.remaining == 0){
                        jQuery(this).parents('.zone-reductions').find('.go-to-subscription').remove();
                    }else{
                        jQuery(this).parents('.zone-reductions').find('.go-to-subscription').show();
                    }
                }
            }
        });
    }
    
    jQuery(document).on('click', '.order-print .order-print-btn', function (e) {
        e.preventDefault();
        var $this = jQuery(this);
        var order_id = jQuery(this).attr('data-order-id');
        jQuery.ajax({
            url: STCTICKETSPUBLIC.ajaxurl,
            method: 'post',
            dataType: 'json',
            beforeSend: function () {
                jQuery( "body" ).css('opacity', '0.2');
            },
            data: {
                action: 'printOrder',
                order_id: order_id
            },
            success: function (data) {
                jQuery( "body" ).css('opacity', '1');
                console.log('data', data);
                var responseData = data;
//                var responseData = JSON.parse(data);
                if (responseData.status) {
                    console.log(responseData.message);
                    jQuery(document).find('.print-error-msg').hide();
//                    window.open(responseData.message, '_blank');
                    var pdf_a = document.createElement('a');
                    pdf_a.href = responseData.message;
                    pdf_a.setAttribute('target', '_blank');
                    pdf_a.click();
                    pdf_a.remove();
                }else{
                    console.log(responseData.message);
                    let data_tran_id = $this.parents(".order-print-row-wrap").attr("data-tran-id");
                    jQuery(document).find(".print-error-msg-wrap[data-tran-id='"+data_tran_id+"'] .print-error-msg").show().text(responseData.message);
//                    jQuery(document).find('.print-error-msg').show().text(responseData.message);
                }
            },
            error: function (request, status, error) {
                console.log(error);
            }
        });
    });
    
    
    if(jQuery(document).find('.ticket-order-table').length > 0){
        let table = jQuery(document).find('.ticket-order-table').parents('table');
        table.find('thead, .woocommerce-table__line-item.order_item').remove();
    }
    
    jQuery('.subscription-buy-btn').fancybox({
        beforeLoad: function(){
            jQuery(document).find( "body" ).css('opacity', '0.2');
        },
        afterLoad: function(instance, current) {
            console.log('fancybox open');
            // Get the existing content of the FancyBox
            var content = instance ? instance.current.content : '';
            var selectedRow = [];
            var subscription_html = '';
            var selectbox_html = '';
            var getbarcodes = getBarcodes();
            var barcode_options = '';
            var UrlVars = getUrlVars();
            var curr_barcode = UrlVars.barcode;
            var orderId = UrlVars.orderId;
            console.log(typeof orderId,orderId);
            if(typeof getbarcodes != 'undefined' && getbarcodes.length > 0){
                var finalbarcodes = getbarcodes.filter(function(getbarcode) {
                    // Access the minPrice variable from the outer scope
                    return getbarcode != curr_barcode;
                });
                for (let i = 0; i < finalbarcodes.length; i++) {
//                    console.log(getbarcodes.length,getbarcodes[i]);
                    barcode_options += "<option value="+finalbarcodes[i]+">"+finalbarcodes[i]+"</option>";
                }                                
            }
            if(typeof curr_barcode != 'undefined' && curr_barcode != ""){
                barcode_options += "<option selected='selected' value="+curr_barcode+">"+curr_barcode+"</option>";
            }
//            console.log(getbarcodes,barcode_options);
            
            if (jQuery(document).find('.spettacolo-prices-inner .table-row:visible').length > 0) {
                jQuery(document).find('.spettacolo-prices-inner .table-row').each(function () {
                    let zoneArr = new Object();
                    let reductionArr = [];
                    let zoneTitle = jQuery(this).find('.title h2').text();
                    let zoneId = jQuery(this).attr('data-zoneId');
                    zoneArr.zoneName = zoneTitle;
                    zoneArr.zoneId = zoneId;
                    jQuery(this).find('.cart-qty-counter').each(function (index) {
                        let reductionValue = new Object();
                        let qty_value = jQuery(this).find('.qty').val();
                        if (Number(qty_value) > 0) {
                            let price_value = jQuery(this).parents('.list-row').find('.row-price').attr('data-price');
                            let reductionId = jQuery(this).parents('.list-row').attr('data-reductionId');
                            let reductionName = jQuery(this).parents('.list-row').find('.row-title p').text();

                            reductionValue.reductionName = reductionName;
                            reductionValue.reductionId = reductionId;
                            reductionValue.reductionQuantity = Number(qty_value);
                            reductionValue.reductionPrice = Number(price_value);
                            console.log(selectbox_html,barcode_options);
//                            selectbox_html += `test`;
//                            console.log(selectbox_html);
                            selectbox_html += `<li class="event-ticketlist">
                                                    <div class="table-row">
                                                        <div class="seat-name-wrap">
                                                            <span class="seat-name" data-reductionId="`+reductionId+`" data-zoneId="`+zoneId+`" data-zoneName="`+zoneTitle+`">`+zoneTitle+`</span>
                                                        </div>
                                                        <div class="subscription-select-wrap">`+
                                                            (typeof orderId == 'undefined' || orderId == '' ? 
                                                            `<select class="custom-select" name="custom-select" id="subscription-code">
                                                                <option value="0" selected="selected" disabled="disabled"> `+stcTicketsText.str_1+`</option>`+
                                                                (barcode_options != '' ? barcode_options : '')
                                                                +`<optgroup label="---">
                                                                    <option value="-1">`+stcTicketsText.str_2+`</option>
                                                                </optgroup>
                                                            </select>
                                                            <div class="subscription-input-wrap" style="display:none;">
                                                                <input type="text" id="subscription-input" name="subscription-input">
                                                                <button class="dropdown-back-btn">`+stcTicketsText.str_3+`</button>
                                                            <div>` : 
                                                            `<div class="subscription-input-wrap">
                                                                <input type="text" id="subscription-input" name="subscription-input" value="`+curr_barcode+`" disabled="disabled">
                                                            <div>`)+
                                                        `</div>
                                                    </div>
                                                </li>`;
                            console.log(selectbox_html);
                            if(Number(qty_value) > 1){
                                for(let j = 1; j < qty_value; j++){
                                    selectbox_html += `<li class="event-ticketlist">
                                                    <div class="table-row">
                                                        <div class="seat-name-wrap">
                                                            <span class="seat-name" data-reductionId="`+reductionId+`" data-zoneId="`+zoneId+`" data-zoneName="`+zoneTitle+`">`+zoneTitle+`</span>
                                                        </div>
                                                        <div class="subscription-select-wrap">`+
                                                            (typeof orderId == 'undefined' || orderId == '' ?
                                                            `<select class="custom-select" name="custom-select" id="subscription-code">
                                                                <option value="0" selected="selected" disabled="disabled"> `+stcTicketsText.str_1+`</option>`+
                                                                (barcode_options != '' ? barcode_options : '')
                                                                +`<optgroup label="---">
                                                                    <option value="-1">`+stcTicketsText.str_2+`</option>
                                                                </optgroup>
                                                            </select>
                                                            <div class="subscription-input-wrap" style="display:none;">
                                                                <input type="text" id="subscription-input" name="subscription-input">
                                                                <button class="dropdown-back-btn">`+stcTicketsText.str_3+`</button>
                                                            <div>` : 
                                                            `<div class="subscription-input-wrap">
                                                                <input type="text" id="subscription-input" name="subscription-input" value="`+curr_barcode+`" disabled="disabled">
                                                            <div>`)+
                                                        `</div>
                                                    </div>
                                                </li>`;
                                    
                                }
                                console.log(selectbox_html);
                            }
                            reductionArr.push(reductionValue);
                        }
                    });
                    zoneArr.reductions = reductionArr;
                    zoneArr.doBooking = 1;
                    if (reductionArr.length !== 0) {
                        selectedRow.push(zoneArr);
                    }
                });
                subscription_html += `<div class="select-subscription-wrap">
                                        <div class="subscription-desc">
                                            <h2>`+stcTicketsText.str_4+`: `+stcTicketsText.str_5+`</h2>
                                            <p>`+stcTicketsText.str_6+`<br>`+stcTicketsText.str_10+`<br>`+stcTicketsText.str_11+` «<b>`+stcTicketsText.str_12+`</b>» `+stcTicketsText.str_13+`</p>
                                        </div>
                                        <div class="select-box-form">
                                            <div class="select-box-title-wrap">
                                                <p><strong>`+stcTicketsText.str_7+`:</strong></p>
                                            </div> 
                                            <ul class="event-ticketlist-wrap">`
                                            +selectbox_html+
                                            `</ul>
                                        </div>
                                    </div>`;
                
            } else if (jQuery(document).find('.spettacolo-prices-inner .selected-seat-row:visible').length > 0){
                jQuery(document).find('.spettacolo-prices-inner .selected-seat-row').each(function () {
                    let zoneArr = new Object();
                    let reductionArr = [];
                    let zoneTitle = jQuery(this).find('.seat-desc').text();
                    let zoneId = jQuery(this).attr('data-zone-id');
                    zoneArr.zoneName = zoneTitle;
                    zoneArr.zoneId = zoneId;
                    let reductionValue = new Object();
                    let qty_value = 1;
                    let price_value = jQuery(this).find('.seat-price-wrap .seat-price').attr('data-price');
                    let reductionId = jQuery(this).find('.seat-price-wrap .seat-price').attr('data-reduction-id');
                    let reductionName = jQuery(this).find('.seat-price-wrap .seat-price').attr('data-reduction-name');

                    reductionValue.reductionName = reductionName;
                    reductionValue.reductionId = reductionId;
                    reductionValue.reductionQuantity = Number(qty_value);
                    reductionValue.reductionPrice = Number(price_value);

                    selectbox_html += `<li class="event-ticketlist">
                                            <div class="table-row">
                                                <div class="seat-name-wrap">
                                                    <span class="seat-name" data-reductionId="`+reductionId+`" data-zoneId="`+zoneId+`" data-zoneName="`+zoneTitle+`">`+zoneTitle+`</span>
                                                </div>
                                                <div class="subscription-select-wrap">`+
                                                    (typeof orderId == 'undefined' || orderId == '' ?
                                                    `<select class="custom-select" name="custom-select" id="subscription-code">
                                                        <option value="0" selected="selected" disabled="disabled"> `+stcTicketsText.str_1+`</option>`+
                                                        (barcode_options != '' ? barcode_options : '')
                                                        +`<optgroup label="---">
                                                            <option value="-1">`+stcTicketsText.str_2+`</option>
                                                        </optgroup>
                                                    </select>
                                                    <div class="subscription-input-wrap" style="display:none;">
                                                        <input type="text" id="subscription-input" name="subscription-input">
                                                        <button class="dropdown-back-btn">`+stcTicketsText.str_3+`</button>
                                                    <div>` : 
                                                    `<div class="subscription-input-wrap">
                                                        <input type="text" id="subscription-input" name="subscription-input" value="`+curr_barcode+`" disabled="disabled">
                                                    <div>`)+
                                                `</div>
                                            </div>
                                        </li>`;
                    console.log(selectbox_html,barcode_options);
                    reductionArr.push(reductionValue);
                    zoneArr.reductions = reductionArr;
                    zoneArr.doBooking = 1;
                    if (reductionArr.length !== 0) {
                        selectedRow.push(zoneArr);
                    }
                });
                subscription_html += `<div class="select-subscription-wrap">
                                        <div class="subscription-desc">
                                            <h2>`+stcTicketsText.str_4+`: `+stcTicketsText.str_5+`</h2>
                                            <p>`+stcTicketsText.str_6+`<br>`+stcTicketsText.str_10+`<br>`+stcTicketsText.str_11+` «<b>`+stcTicketsText.str_12+`</b>» `+stcTicketsText.str_13+`</p>
                                        </div>
                                        <div class="select-box-form">
                                            <div class="select-box-title-wrap">
                                                <p><strong>`+stcTicketsText.str_7+`:</strong></p>
                                            </div> 
                                            <ul class="event-ticketlist-wrap">`
                                            +selectbox_html+
                                            `</ul>
                                        </div>
                                    </div>`;
            }
            
            console.log(selectedRow,subscription_html,getbarcodes,selectbox_html);
            // Find and replace the content of the specific div
//            var newContent = '<p>New content replacing the existing content.</p>';
            var $content = jQuery('#subscription-fancybox-wrap').html(content);
            console.log($content);
            $content.find('#replace-me').html(subscription_html);

            // Set the modified content inside the FancyBox
            instance.current.content = $content.html();
//            jQuery(document).find( "body" ).css('opacity', '1');
        }
    });
    
    jQuery(document).on('change','#subscription-code', function() {
        if (jQuery(this).val() === '-1') {
            // Replace the select with an input field
            jQuery(this).hide();
            jQuery(this).parents('.subscription-select-wrap').find('.subscription-input-wrap').show();
//            var inputField = jQuery('<input type="text" id="subscription-input" name="subscription-input">');
//            jQuery(this).replaceWith(inputField);
        }
    });
    jQuery(document).on('click','.subscription-select-wrap .dropdown-back-btn', function() {
        jQuery(this).parents('.subscription-input-wrap').hide();
        jQuery(this).parents('.subscription-select-wrap').find('#subscription-code').show();
        jQuery(this).parents('.subscription-select-wrap').find('#subscription-code option:eq(0)').prop('selected', true);
    });
    jQuery(document).on('click','.subscription-show-btn a', function(e) {
        e.preventDefault();
        var vcode = jQuery(this).parents(".drop-select-box").find('#subscription-show option:selected').attr('data-vcode');
        var pcode = jQuery(this).parents(".drop-select-box").find('#subscription-show option:selected').attr('data-pcode');
        var barcode = jQuery(this).parents(".drop-select-box").find('#subscription-show option:selected').attr('data-barcode');
        var order_id = jQuery(this).parents(".drop-select-box").find('#subscription-show option:selected').attr('data-order-id');
        var redirect_url = STCTICKETSPUBLIC.siteurl+"/spettacolo-prices/?cmd=prices&id="+STCTICKETSPUBLIC.APIKEY+"&vcode="+vcode+"&pcode="+pcode+"&selectionMode=0&regData=1&barcode="+barcode+(order_id != "" ? "&orderId="+order_id : "");
        console.log(redirect_url);
        window.location.href = redirect_url;
    });
    jQuery(document).on('click','.subscription-orders-table .subscription-view-button', function(e) {
        e.preventDefault();
        var data_url = jQuery(this).attr('data-url');
        window.location.href = data_url;
    });
    jQuery(document).on('click','.subscription-check-form .subscription-check-btn', function(e) {
        e.preventDefault();
        let barcode = jQuery(document).find('.subscription-check-input').val();
//        if(barcode != ""){
//            window.location.href = STCTICKETSPUBLIC.siteurl+"/subscription/?barcode="+barcode;
//        }
        jQuery.ajax({
            url: STCTICKETSPUBLIC.ajaxurl,
            method: 'post',
            beforeSend: function () {
                jQuery( "body" ).css('opacity', '0.2');
            },
            data: {
                action: 'subscriptionCheck',
                barcode: barcode
            },
            success: function (data) {
                jQuery( "body" ).css('opacity', '1');
//                    console.log(data);
                var responseData = JSON.parse(data);
                if (responseData.status) {
                    console.log(responseData.message);
                    window.location.href = STCTICKETSPUBLIC.siteurl+"/subscription/?barcode="+barcode;
                }else{
                    console.log(responseData.message);
                    if(jQuery(document).find('.subscription-check-form .subscription-check-error').length > 0){
                        jQuery(document).find('.subscription-check-form .subscription-check-error').text(responseData.message);
                    }else{
                        jQuery(document).find('.subscription-check-form').append('<div class="subscription-check-error"><p>'+responseData.message+'</p></div>');
                    }
                }
            },
            error: function (request, status, error) {
                console.log(error);
            }
        });
    });
    if(jQuery(document).find('.woocommerce-MyAccount-navigation-link--abbonamenti').hasClass('is-active')) {
        jQuery(document).find('.woocommerce-MyAccount-navigation-link--dashboard').removeClass('is-active');
    }
    jQuery(document).on('click', '.spettacolo-tickets .ticket-delete', function (e) {
//        console.log("clicked");
        var delete_transaction_ids = [];
        var ticket_title = jQuery(this).parents('.ticket-datails-wrap').find('.ticket-title h2').text();
        if(jQuery(this).parents('.ticket-datails-wrap').find('.ticket-zone').length > 0) {
            jQuery(this).parents('.ticket-datails-wrap').find('.ticket-zone').each(function () {
                let transaction_id = jQuery(this).attr('data-transaction-id');
                    delete_transaction_ids.push(transaction_id);
                    if(jQuery(this).find('.subscription-seats p').length > 0) {
                        jQuery(this).find('.subscription-seats p').each(function () {
                            let sub_transaction_id = jQuery(this).attr('data-transaction-id');
                            if(typeof sub_transaction_id != 'undefined' && sub_transaction_id != ''){
                                delete_transaction_ids.push(sub_transaction_id);
                            }
                        });
                    }
//                console.log(transaction_id);
            });
//            console.log(delete_transaction_ids);
        }
        jQuery.ajax({
            url: STCTICKETSPUBLIC.ajaxurl,
            method: 'post',
            beforeSend: function () {
                jQuery( "body" ).css('opacity', '0.2');
            },
            data: {
                action: 'deleteTickets',
                delete_transaction_ids: delete_transaction_ids,
                ticket_title: ticket_title
            },
            success: function (data) {
                jQuery( "body" ).css('opacity', '1');
//                    console.log(data);
                var responseData = JSON.parse(data);
                if (responseData.status) {
                    console.log(responseData.message);
                    location.reload();
                }else{
                    console.log(responseData.message);
                }
            },
            error: function (request, status, error) {
                console.log(error);
            }
        });
//        jQuery(document).find('#svgSeatSvg circle[data-id="'+seat_id+'"]').trigger('click');
    });
    jQuery(document).on('click', '.update_phone_otp', function (e) {
        e.preventDefault();
        var data = {};
            var billing_phone_spam = jQuery(document).find('#reg_billing_phone').val();            
            var billing_phone = jQuery(document).find('#reg_billing_phone'+ STCTICKETSPUBLIC.FORM_FIELD_CHARS ).val();
            var country_code = jQuery(document).find('#reg_country_code'+ STCTICKETSPUBLIC.FORM_FIELD_CHARS ).val();
            var email = jQuery(document).find('#reg_email'+ STCTICKETSPUBLIC.FORM_FIELD_CHARS).val();
            var registerOtp = jQuery(document).find('#registerotp').val();
            var $this = jQuery(this);
            
            var isValid = validatePhoneNumber(billing_phone,country_code);
            
            if (!isValid) {
                e.preventDefault();
                jQuery(document).find('#phoneNumberError').show();
                jQuery(document).find('#reg_billing_phone'+ STCTICKETSPUBLIC.FORM_FIELD_CHARS).addClass('is-invalid');
                return false;
            }


            if (billing_phone && email) {
                console.log('update_phone_otp clicked');
                jQuery(document).find('.update-phone-form #reg_billing_phone'+ STCTICKETSPUBLIC.FORM_FIELD_CHARS).attr('style', 'border-color: #d8dbe2 !important');
                if(billing_phone_spam) {                    
                    jQuery(document).find('#phoneNumberError').show();                
                    return false;
                }                                
                var isUserBlocked = false;
                
                if ($this.hasClass('otp-generate')) {
                    /// otpattempts
                    isUserBlocked = checkOtpAttemptsBlocked();
                    data = {
                        action: 'UpdateUserPhone',
                        billing_phone: billing_phone,
                        country_code: country_code,
                        email: email,
                        generate_otp_now: true,
                    }
                } else if ($this.hasClass('otp-verified-disabled')) {
                    data = {
                        action: 'UpdateUserPhone',
                        billing_phone: billing_phone,
                        country_code: country_code,
                        email: email,
                        registerotp: registerOtp,
                    }
                }       
                if(isUserBlocked == true) {
                    jQuery(document).find('#otpAttemptsError').show();
                    return false;
                } else {
                    jQuery(document).find('#otpAttemptsError').hide();
                }
                jQuery.ajax({
                    url: STCTICKETSPUBLIC.ajaxurl,
                    method: 'post',
                        beforeSend: function () {
                            jQuery( "body" ).css('opacity', '0.2');
                        },
                    data: data,
                    success: function (data) {
                            jQuery( "body" ).css('opacity', '1');
                        var responseData = JSON.parse(data);
                            if (responseData.status) {
                            if (responseData.otpCreated) {
                                    jQuery(document).find('.woocommerce-form-row.otp-box').show();
                                    jQuery(document).find('.upd-phone-msg.otp-msg').show();
                                $this.removeClass('otp-generate');
                                $this.addClass('otp-verified-disabled');
                                $this.text(stcTicketsText.str_17);
                                    jQuery(document).find('form .error-display').remove();
                            }
                            if (responseData.otpMatched) {
                                if (responseData.error != '') {                                    
                                    jQuery(document).find('form .otp-box').after('<p class="error-display">' + responseData.error + '</p>');
                                }else{
                                    console.log("success",responseData);
                                    jQuery(document).find('form .otp-box').after('<p class="success-msg">' + stcTicketsText.str_18 + '</p>');
                                    if(jQuery(document).find('.cart-buy-btn').length > 0){
                                        if(jQuery(document).find('#edit-fancybox-form .cta-with-phone').length > 0){
                                            jQuery(document).find('#edit-fancybox-form .cta-with-phone').show();
                                            jQuery(document).find('.cart-buy-btn').attr('data-profile-status','incomplete');
                                            jQuery(document).find('#edit-fancybox-form form').hide();
                                        }else{
                                            jQuery(document).find('.cart-buy-btn').attr('data-profile-status','complete');
                                            jQuery(document).find('.cart-buy-btn[data-profile-status="complete"]').removeAttr('data-fancybox data-src');
                                            jQuery.fancybox.close();
                                        }

                                    }else{
                                        window.location.reload();
                                    }
                                }
                            } else {
                                console.log("error",responseData);
                                if (responseData.error != '') {
                                        jQuery(document).find('form .otp-box').after('<p class="error-display">' + responseData.error + '</p>');
                                }
                            }
                        } else {
                            console.log(responseData);
                        }
                    },
                    error: function (request, status, error) {
                        console.log(error);
                    }
                });

            } else {
                if (!billing_phone) {
                    jQuery(document).find('.update-phone-form #reg_billing_phone'+ STCTICKETSPUBLIC.FORM_FIELD_CHARS).attr('style', 'border-color: red !important');
                }else{
                    jQuery(document).find('.update-phone-form #reg_billing_phone'+ STCTICKETSPUBLIC.FORM_FIELD_CHARS).removeAttr('style');
                }
            }
    });
});

function renderReCaptcha() {
    if (typeof grecaptcha !== 'undefined') {
        grecaptcha.ready(function() {
            // Render reCAPTCHA widget
            widgetId = grecaptcha.render('reCaptchDiv', {
                'sitekey' : '6LedGCcpAAAAAOGFFUqTQMl7ieQiSHh7ggKrXNnL',
                'theme' : 'light'  // Optional: change theme if needed
            });

            // Example: reset reCAPTCHA on button click        
        });
    } else {
        // Retry after a delay if grecaptcha is not available
        setTimeout(renderReCaptcha, 1000);
    }
}

function makeTimer() {
    var endTime = jQuery(document).find("#timer_count").attr('data-time');
    var endTime = Number(endTime);
//    var endTime = 1684396952 + 600;
    
    var now = new Date();
    now = (Date.parse(now) / 1000);

    var timeLeft = endTime - now;

    var days = Math.floor(timeLeft / 86400); 
    var hours = Math.floor((timeLeft - (days * 86400)) / 3600);
    var minutes = Math.floor((timeLeft - (days * 86400) - (hours * 3600 )) / 60);
    var seconds = Math.floor((timeLeft - (days * 86400) - (hours * 3600) - (minutes * 60)));

    if (hours < "10") { hours = "0" + hours; }
    if (minutes < "10") { minutes = "0" + minutes; }
    if (seconds < "10") { seconds = "0" + seconds; }
    if(endTime > now){
        if(jQuery(document).find("#timer_count").length > 0){
            jQuery(document).find("#timer_count").html(minutes + " : " + seconds);
        }
    }else{
        if(jQuery(document).find("#timer_count").length > 0){
            jQuery(document).find("#timer_count").remove();
            window.location.reload();
        }
    }
}

function total_values() {
    var qty_count_value, total_qty_count = 0;
    var price_count_value, total_price_count = 0;
    if (jQuery('.list-table-wrap .price-table:visible').length > 0) {
        jQuery('.cart-qty-counter').each(function () {
            qty_count_value = jQuery(this).find('.qty').val();
            if (Number(qty_count_value) > 0) {
                total_qty_count += Number(qty_count_value);
                price_count_value = jQuery(this).siblings('.row-price').attr('data-price');
                total_price_count += Number(price_count_value) * Number(qty_count_value);
            }
        });
    } else {
        jQuery('.selected-seat-row').each(function () {
            total_qty_count = total_qty_count + 1;
            var selected_seat_price = jQuery(this).find('.seat-price').attr('data-price');
            total_price_count += Number(selected_seat_price);
        });
    }
    if (total_qty_count > 0 || total_price_count > 0) {
        jQuery('.total-qty-count').find('.total-qty-value').text(total_qty_count);
        jQuery('.total-qty-count').find('.total-qty-value').attr('data-count', total_qty_count);
        jQuery('.total-price-count').find('.total-price-value').html(total_price_count + ' &euro;');
        jQuery('.total-price-count').find('.total-price-value').attr('data-count', total_price_count);
        jQuery('.total-values-wrap').show();
    } else {
        jQuery('.total-values-wrap').hide();
    }
    if(jQuery(document).find('.cart-count').length > 0){
        console.log(cart_counter_flag);
        if(cart_counter_flag){
            cart_count = jQuery(document).find('.cart-count').text();
        }
        cart_counter_flag = false;
        var UrlVars = getUrlVars();
        var barcode = UrlVars.barcode;
        if(typeof barcode == "undefined" && barcode == ""){
            jQuery(document).find('.cart-count').text(Number(total_qty_count) + Number(cart_count));
        }
    }
//    console.log( typeof cart_counter_flag,cart_counter_flag,typeof cart_count,cart_count);
}
function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
        vars[key] = decodeURIComponent(value);
    });
    return vars;
}

function getBarcodes() {
    var barcodes;
    jQuery.ajax({
        url: STCTICKETSPUBLIC.ajaxurl,
        method: 'post',
        beforeSend: function () {
            jQuery( "body" ).css('opacity', '0.2');
        },
        data: {
            action: 'getbarcodes'
        },
        async:false,
        success: function (data) {
            jQuery( "body" ).css('opacity', '1');
//                    console.log(data);
            var responseData = JSON.parse(data);
            if (responseData.status) {
                console.log(responseData.message);
                barcodes = responseData.message;
            }else{
                console.log(responseData.message);
            }
        },
        error: function (request, status, error) {
            console.log(error);
        }
    });
    console.log(barcodes);
    return barcodes;
}

function SVG(tag) {
    return document.createElementNS('http://www.w3.org/2000/svg', tag);
}

var createRectInSvg = function (width, height, x, y) {
    var $svg = jQuery("#svgSeatSvg");
    jQuery(SVG('rect'))
            .attr('id', 'svgSeatRectTooltip')
            .attr('width', width)
            .attr('height', height)
            .attr('x', x)
            .attr('y', y)
            .attr('rx', '10')
            .attr('ry', '10')
            .attr('fill', '#404040')
            .appendTo($svg);
};

var createTextInSvg = function (x, y, text) {
    var $svg = jQuery("#svgSeatSvg");
    jQuery(SVG('text'))
            .attr('id', 'svgSeatTextTooltip')
            .attr('font-family', 'arial')
             .attr('font-size', '15')
             .attr('size', '15')
            .attr('family', 'arial')
            .attr('x', x)
            .attr('y', y)
            .attr('fill', 'white')
            .text(text)
            .appendTo($svg);
};

function setCookie(key, value, expiry) {
    var expires = new Date();
    expires.setTime(expires.getTime() + (expiry * 24 * 60 * 60 * 1000)); // 1 Day
    document.cookie = key + '=' + value + ';expires=' + expires.toUTCString() + ";path=/";
}

//function setCookie(cookieName, cookieValue, expirationDays) {
//    var d = new Date();
//    d.setTime(d.getTime() + (expirationDays * 24 * 60 * 60 * 1000));
//    var expires = "expires=" + d.toUTCString();
//    document.cookie = encodeURIComponent(cookieName) + "=" + encodeURIComponent(cookieValue) + ";" + expires + ";path=/";
//}

function delete_cookie(name) {
  document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

function getCookie(cookieName) {
    var name = cookieName + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var cookieArray = decodedCookie.split(';');
    for (var i = 0; i < cookieArray.length; i++) {
        var cookie = cookieArray[i];
        while (cookie.charAt(0) == ' ') {
            cookie = cookie.substring(1);
        }
        if (cookie.indexOf(name) == 0) {
            return cookie.substring(name.length, cookie.length);
        }
    }
    return "";
}

function updateCookie(cookieName, newValue, expirationDays) {
    // Remove existing cookie
    document.cookie = cookieName + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";

    // Set a new cookie with the updated value
    setCookie(cookieName, newValue, expirationDays);
}
    
function validatePhoneNumber(number,country_code) {
    if(country_code == 49 || country_code == 43){
        var regex = /^[0-9]{1,12}$/; // Regular expression for 1 to 12 digits
    }else if(country_code == 55){
        var regex = /^[0-9]{1,15}$/; // Regular expression for 1 to 15 digits
    }else{
        var regex = /^[0-9]{1,12}$/; // Regular expression for 1 to 12 digits for all other countries
    }
    return regex.test(number);
}
    
function addMinutes(date, minutes) {
    return new Date(date.getTime() + minutes*60000);
}
function checkOtpAttemptsBlocked() {
    var userOtpAttempts = getCookie('otpAttempts');
    var userOtpAttemptsExpiryStr = getCookie('OtpAttemptsExpiry');                    
    var curDate = new Date();
    var userBlocked = false;
    if(userOtpAttempts) {
        userOtpAttempts = parseInt(userOtpAttempts)
        if(userOtpAttempts > 2) {
            if(userOtpAttemptsExpiryStr) {
                var userOtpAttemptsExpiry = new Date(userOtpAttemptsExpiryStr);
                if(curDate > userOtpAttemptsExpiry) {                                    
                    updateCookie('otpAttempts', '1', 1);
                    delete_cookie('OtpAttemptsExpiry');
                } else {
                    userBlocked = true;
                }
            } else {
                var newOtpAttemptsExpiry = addMinutes(curDate, 15);                        
                updateCookie('OtpAttemptsExpiry', newOtpAttemptsExpiry, 1);
                userBlocked = true;
            }
        } else {
            var newAttempt = userOtpAttempts + 1;
            updateCookie('otpAttempts', newAttempt, 1);                            
        }
    } else {                        
        updateCookie('otpAttempts', '1', 1);
    }
    return userBlocked;
}