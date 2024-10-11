<?php
get_header();

$spe_id             = get_the_ID();
$spe_title          = get_the_title();
$spe_start_date     = get_post_meta( $spe_id, 'spt_startDate', true );
$spe_end_date       = get_post_meta( $spe_id, 'spt_endDate', true );
$curr_start_date    = !empty($spe_start_date) ? explode( "/", $spe_start_date ) : '';
$final_start_date   = !empty($curr_start_date) ? $curr_start_date[ 1 ] . '/' . $curr_start_date[ 0 ] . '/' . $curr_start_date[ 2 ] : '';
$curr_end_date      = !empty($spe_end_date) ? explode( "/", $spe_end_date ) : '';
$final_end_date     = !empty($curr_end_date) ? $curr_end_date[ 1 ] . '/' . $curr_end_date[ 0 ] . '/' . $curr_end_date[ 2 ] : '';
$final_start_date   = change_date_time_in_italy(strtotime ( $final_start_date ),'dd MMMM y');
$final_end_date     = change_date_time_in_italy(strtotime ( $final_end_date ),'dd MMMM y') ;
$spt_tit_info_title = get_post_meta( $spe_id, 'spt_tit_info_title', true );
$tit_info_perform   = !empty($spt_tit_info_title[ 'tit_info_perform' ]) ? $spt_tit_info_title[ 'tit_info_perform' ] : '';
$spt_vcode          = get_post_meta( $spe_id, 'spt_vcode', true );
$spt_location       = !empty(get_post_meta( $spe_id, 'spt_location', true )) ? get_post_meta( $spe_id, 'spt_location', true ) : __('Teatro San Carlo - NAPOLI','stc-tickets');
$spt_img            = get_the_post_thumbnail_url($spe_id) ? get_the_post_thumbnail_url($spe_id) : plugin_dir_url( __DIR__ ) . 'assets/img/emiliano_test.jpg';


//$my_current_lang = apply_filters( 'wpml_current_language', NULL );
//if($my_current_lang == 'it') {
//    $final_start_date   = change_date_time_in_italy(strtotime ( $final_start_date ),'dd MMMM y');
//    $final_end_date     = change_date_time_in_italy(strtotime ( $final_end_date ),'dd MMMM y') ;
//}else {
//    $final_start_date   = date('dd MMMM y', strtotime ( $final_start_date ));
//    $final_end_date     = date('dd MMMM y', strtotime ( $final_end_date )) ;
//}
?>
<div class="spettacolo-single-wrap dev">
    <div class="container">
        <div class="spettacolo-single-wrapper">
            <div class="spe-half-wrap">

                <div class="spettacolo-single-img">
                    <img src="<?php echo $spt_img; ?>" alt="alt"/>
                </div>
            </div>
            <div class="spe-half-wrap">
                <div class="spettacolo-single-inner">
                    <div class="list-title">
                        <?php if( ! empty( $spe_title ) ) { ?>
                            <h3><?php echo $spe_title; ?></h3>
                        <?php } ?>
                    </div>
                    <div class="list-location">
                        <svg data-v-e1017caa="" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="location-dot" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="svg-inline--fa fa-location-dot fa-fw fa-sm"><path data-v-e1017caa="" fill="currentColor" d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 256c-35.3 0-64-28.7-64-64s28.7-64 64-64s64 28.7 64 64s-28.7 64-64 64z" class=""></path></svg>
                        <p><?php echo $spt_location;?></p>
                    </div>
                    <div class="list-date">
                        <svg data-v-e1017caa="" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="calendar" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-calendar fa-fw fa-sm"><path data-v-e1017caa="" fill="currentColor" d="M96 32V64H48C21.5 64 0 85.5 0 112v48H448V112c0-26.5-21.5-48-48-48H352V32c0-17.7-14.3-32-32-32s-32 14.3-32 32V64H160V32c0-17.7-14.3-32-32-32S96 14.3 96 32zM448 192H0V464c0 26.5 21.5 48 48 48H400c26.5 0 48-21.5 48-48V192z" class=""></path></svg>
                        <p><?php echo ( ! empty( $final_start_date ) ? 'Dal ' . $final_start_date : '') . ' ' . ( ! empty( $final_end_date ) ? 'al ' . $final_end_date : ''); ?></p>
                    </div>
                    <div class="single-date-list-wrapper">
                        <div class="single-date-list-title">
                            <h4> <?php _e('Seleziona una data','stc-tickets');?></h4>
                        </div>
                        <?php echo do_shortcode('[spettacolo_event_listing id="'.$spe_id.'"]'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
get_footer();
