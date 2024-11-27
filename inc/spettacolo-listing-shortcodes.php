<?php
/*
 * spettacoli listing
 */
add_shortcode ( 'spettacolo_listing', 'stcTickets_spettacolo_listing_callback' );

function stcTickets_spettacolo_listing_callback() {
    ob_start ();
    $paged     = (get_query_var ( 'paged' )) ? get_query_var ( 'paged' ) : 1;
    $spe_args  = array (
        'post_type'      => 'spettacolo',
        'post_status'    => 'publish',
        'posts_per_page' => '20',
        'paged'          => $paged,
        'orderby'        => 'title',
        'order'          => 'ASC',
    );
    $spe_query = new WP_Query ( $spe_args );
    ?>
    <div class="spettacolo-filters-wrapper">
        <div class="container-fluid">
            <div class="spettacolo-month-filter">
                <div class="spettacolo-month january ">Gennaio</div>
                <div class="spettacolo-month february ">Febbraio</div>
                <div class="spettacolo-month march ">Marzo</div>
                <div class="spettacolo-month april ">Aprile</div>
                <div class="spettacolo-month may ">Maggio</div>
                <div class="spettacolo-month june ">Giugno</div>
                <div class="spettacolo-month july ">Luglio</div>
                <div class="spettacolo-month august ">Agosto</div>
                <div class="spettacolo-month september ">Settembre</div>
                <div class="spettacolo-month october ">Ottobre</div>
                <div class="spettacolo-month november ">Novembre</div>
                <div class="spettacolo-month december ">Dicembre</div>
            </div>
        </div>
    </div>
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
//                $final_start_date   = date ( "j F Y", strtotime ( $final_start_date ) );
//                $final_end_date     = date ( "j F Y", strtotime ( $final_end_date ) );
                $spt_tit_info_title = get_post_meta ( $spe_id, 'spt_tit_info_title', true );
                $spt_location       = !empty(get_post_meta( $spe_id, 'spt_location', true )) ? get_post_meta( $spe_id, 'spt_location', true ) : __('Teatro San Carlo - NAPOLI','stc-tickets');
                $spt_img            = get_the_post_thumbnail_url($spe_id) ? get_the_post_thumbnail_url($spe_id) : plugin_dir_url( __DIR__ ) . 'assets/img/emiliano_test.jpg';
//            echo "<pre>";
//            print_r($spt_tit_info_title);
//            echo "</pre>";
                ?>
                <div class="spettacolo-listing-box-wrapper">
                    <div class="spettacolo-list-box">
                        <div class="spettacolo-thumb">
                           <img src="<?php echo $spt_img; ?>" alt="alt"/>
                        </div>
                        <div class="datetitlewrap">
                        <div class="list-date">
                            <svg data-v-e1017caa="" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="calendar" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-calendar fa-fw fa-sm"><path data-v-e1017caa="" fill="currentColor" d="M96 32V64H48C21.5 64 0 85.5 0 112v48H448V112c0-26.5-21.5-48-48-48H352V32c0-17.7-14.3-32-32-32s-32 14.3-32 32V64H160V32c0-17.7-14.3-32-32-32S96 14.3 96 32zM448 192H0V464c0 26.5 21.5 48 48 48H400c26.5 0 48-21.5 48-48V192z" class=""></path></svg>
                            <p><?php echo ( ! empty ( $final_start_date ) ? 'Dal ' . $final_start_date : '') . ' ' . ( ! empty ( $final_end_date ) ? 'al ' . $final_end_date : ''); ?></p>
                        </div>
                        <div class="list-title">
                            <?php if( ! empty ( $spe_title ) ) { ?>
                                <h3><a href="<?php echo $spe_permalink; ?>"><?php echo $spe_title; ?></a></h3>
                            <?php } ?>
                        </div>
                        </div>
                        <div class="list-location">
                            <svg data-v-e1017caa="" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="location-dot" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="svg-inline--fa fa-location-dot fa-fw fa-sm"><path data-v-e1017caa="" fill="currentColor" d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 256c-35.3 0-64-28.7-64-64s28.7-64 64-64s64 28.7 64 64s-28.7 64-64 64z" class=""></path></svg>
                            <p><?php echo $spt_location; ?></p>
                        </div>
                        <div class="list-cta">
                            <a href="<?php echo $spe_permalink; ?>"><?php _e('acquista biglietti','stc-tickets'); ?></a>
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
                    'prev_text'    => sprintf ( '<i></i> %1$s', __ ( 'Prev', 'text-domain' ) ),
                    'next_text'    => sprintf ( '%1$s <i></i>', __ ( 'Next', 'text-domain' ) ),
                    'add_args'     => false,
                    'add_fragment' => '',
                ) );
                ?>
            </div>
            <?php
        } else {
            ?>
            <p><?php _e('No Location Available','stc-tickets'); ?></p>
        <?php } ?>
    </div>   
    <?php
    $html = ob_get_clean ();
    return $html;
}