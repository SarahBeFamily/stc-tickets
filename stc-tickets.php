<?php
/**
 * Plugin Name: Tickets
 * Plugin URI: 
 * Description: Display the Shows list from vivaticket
 * Version: 1.0
 * Author: Emiliano
 * Author URI: 
 * Text Domain: stc-tickets
 */
if( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
//register_activation_hook(__FILE__, 'stc_ticket_create_plugin_page');
//register_activation_hook(__FILE__, 'stc_ticket_create_product');
//register_deactivation_hook( __FILE__, 'stc_ticket_remove_plugin_page' ); 

register_activation_hook( __FILE__, array( 'ticket_plugin_loader', 'start_activation' ) );
register_deactivation_hook( __FILE__, array( 'ticket_plugin_delete', 'start_deactivation' ) );

class ticket_plugin_loader {
    function start_activation() {
        self::stc_ticket_create_plugin_page(); // or $this->stc_ticket_create_plugin_page();
        self::stc_ticket_create_woocommerce_product(); //    $this->stc_ticket_create_woocommerce_product(); for static methods
     }
     function stc_ticket_create_plugin_page() {
        // Define the page's title and content
        $pages   = array(
            array (
            'post_name'    => 'spettacoli',
            'post_title'   => 'Spettacoli',
            'post_content' => '[spettacolo_listing]',
            ),
            array (
            'post_name'    => 'thank-you',
            'post_title'   => 'Grazie per il vostro acquisto',
            'post_content' => '[spettacolo_thankyou]',
            ),
            array (
            'post_name'    => 'my-account-listing',
            'post_title'   => 'My Account Listing',
            'post_content' => '[my_account_listing]',
            ),
            array (
            'post_name'    => 'spettacolo-prices',
            'post_title'   => 'Spettacolo Prices',
            'post_content' => '[spettacolo_prices]',
            ),
            array (
            'post_name'    => 'user-registration',
            'post_title'   => 'User Registration',
            'post_content' => '[login_form]',
            )
        );

        foreach($pages as $pages_key => $pages_value){
            $page_name = $pages_value['post_name'];
            $page_title = $pages_value['post_title'];
            $page_content = $pages_value['post_content'];
            // Create the page
            $page = array (
                'post_name'    => $page_name,
                'post_title'   => $page_title,
                'post_content' => $page_content,
                'post_type'    => 'page',
                'post_status'  => 'publish',
                'post_author'  => 1
            );
            $page_id = wp_insert_post ( $page );
            update_option( $page_name, $page_id );
        }
    }
     function stc_ticket_create_woocommerce_product() {
        $demo_product = new WC_Product_Simple();
        $demo_product->set_name( 'Il tuo spettacolo' );
        $demo_product->set_status( 'publish' ); 
        $demo_product->set_catalog_visibility( 'visible' );
        $demo_product->set_price( 0 );
        $demo_product->set_regular_price( 0 );
        $demo_product->set_sold_individually( true );
        $demo_product_id = $demo_product->save();
        update_option( 'TICKET_WOOCOMMERCE_PRODUCT_ID', $demo_product_id );
     }
 }
class ticket_plugin_delete {
    function start_deactivation() {
        self::stc_ticket_remove_plugin_page(); // or $this->stc_ticket_remove_plugin_page();
        self::stc_ticket_remove_woocommerce_product(); //    $this->stc_ticket_remove_woocommerce_product(); for static methods
     }
    function stc_ticket_remove_plugin_page() {
        //  the id of our page...
        $pages   = ['spettacoli','thank-you','my-account-listing','spettacolo-prices','user-registration'];
        foreach($pages as $pages_key => $pages_value){
            $the_page_id = get_option($pages_value);
            if( $the_page_id ) {
                wp_delete_post( $the_page_id , true ); // this will trash, not delete
            }
        }
    }
    function stc_ticket_remove_woocommerce_product() {
        //  the id of our page...
            $demo_product_id = get_option('TICKET_WOOCOMMERCE_PRODUCT_ID');
            if( $demo_product_id ) {
                wp_delete_post( $demo_product_id,true);
            }
    }
 }
class stcTickets {

    /**
     * Constructor
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function __construct() {
        add_action( 'init', [ $this, 'i18n' ] );
        $this->define_public_hooks();
        $this->include_custom_function_files();
    }
    /**
     * Load Textdomain
     *
     * Load plugin localization files.
     *
     * Fired by `init` action hook.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function i18n() {

        load_plugin_textdomain( 'stc-tickets' );
    }
    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    public function define_public_hooks() {
        add_action( 'wp_enqueue_scripts', [ $this, 'stctickets_styles' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'stctickets_scripts' ] );
    }
    /*
     * Enqueue styles
     */
    public function stctickets_styles() {
        /*wp_enqueue_style( 'stctickets-jquery-ui-min-style', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css', array (), false, 'all' );*/
        wp_enqueue_style( 'stctickets-fancybox-min-style', 'https://cdn.jsdelivr.net/npm/@fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css', array (), false, 'all' );
        wp_enqueue_style( 'stctickets-jquery-ui-style', 'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css', array (), false, 'all' );
        wp_register_style( 'stctickets-public-style', plugin_dir_url( __FILE__ ) . 'assets/css/ticket-style.css', array (), time() );
        wp_enqueue_style( 'stctickets-public-style' );
    }
    /*
     * enqueue scripts
     */
    public function stctickets_scripts() {
        if( ! wp_script_is( 'jquery', 'enqueued' ) ) {
            //Enqueue
            wp_enqueue_script( 'jquery' );
        }
        /*wp_enqueue_script( 'stctickets-jquery-ui-min-script', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array (), time(),true );*/
        wp_enqueue_script( 'stctickets-jquery-ui-script', 'https://code.jquery.com/ui/1.13.2/jquery-ui.js', array (), NULL );
        wp_enqueue_script( 'stctickets-svg-pan-zoom', 'https://cdn.jsdelivr.net/npm/svg-pan-zoom@3.5.0/dist/svg-pan-zoom.min.js', array (), NULL );
        wp_enqueue_script( 'stctickets-fancybox-min-script', 'https://cdn.jsdelivr.net/npm/@fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js', array (), time() );
        wp_enqueue_script( 'stctickets-recaptcha-script', 'https://www.google.com/recaptcha/api.js', array (), NULL );
        wp_enqueue_script( 'stctickets-map-script', plugin_dir_url( __FILE__ ) . 'assets/js/stc-map-script.js', array (), time() );
        wp_enqueue_script( 'stctickets-public-script', plugin_dir_url( __FILE__ ) . 'assets/js/stc-tickets-script.js', array (), time() );
        
        //localize public script
        wp_localize_script('stctickets-public-script', 'STCTICKETSPUBLIC', array(
            'ajaxurl' => admin_url('admin-ajax.php', ( is_ssl() ? 'https' : 'http')),
            'siteurl' => is_plugin_active('sitepress-multilingual-cms/sitepress.php') ? rtrim(apply_filters( 'wpml_home_url', get_option( 'home' ) ), '/') : get_site_url(),
            'loggedIn' => is_user_logged_in(),
            'cartSlug' => basename(wc_get_cart_url()),
            'cartData' => json_encode(WC()->cart),
            'APIKEY' => APIKEY,
            'FORM_FIELD_CHARS' => FORM_FIELD_CHARS
        ));
        
        wp_localize_script('stctickets-public-script', 'stcTicketsText', array(
            'str_1' => __('Seleziona un codice','stc-tickets'),
            'str_2' => __('Voglio utilizzare un differente codice abbonamento','stc-tickets'),
            'str_3' => __('Voglio selezionare il codice di abbonamento','stc-tickets'),
            'str_4' => __('Prenotazione','stc-tickets'),
            'str_5' => __('riepilogo richiesta posti','stc-tickets'),
            'str_6' => __("Se sei già in possesso di un abbonamento libero e vuoi scegliere i biglietti dei singoli spettacoli, inserisci il codice dell'abbonamento nell'apposito campo.",'stc-tickets'),
            'str_7' => __('Riepilogo Biglietti in abbonamento','stc-tickets'),
            'str_8' => __('Qualcosa è andato storto nella scelta del posto','stc-tickets'),
            'str_9' => __('Register','stc-tickets'),
            'str_10' => __("Se non lo ricordi, puoi utilizzare il menu a tendina a fianco, che elenca tutti i codici abbonamento che hai attualmente all'interno del tuo carrello.",'stc-tickets'),
            'str_11' => __("Una volta inserito il codice, clicca sul pulsante",'stc-tickets'),
            'str_12' => __("PASSO SUCCESSIVO",'stc-tickets'),
            'str_13' => __("per procedere con l'assegnazione dei posti.",'stc-tickets'),
            'str_14' => __("something went wrong",'stc-tickets'),
            'str_15' => __("Failed to import users",'stc-tickets'),
            'str_16' => __("sucessfully imported users",'stc-tickets'),
            'str_17' => __('Update','stc-tickets'),
            'str_18' => __('Telephone Updated!!','stc-tickets'),
            'str_19' => __('you are not allowed to register on this site','stc-tickets')
        ));
    }
    /*
     * include custom function files
     */
    public function include_custom_function_files() {
        // add custom post type
        require_once plugin_dir_path( __FILE__ ) . 'inc/ticket-post-type.php';
//        // add form submit file
        require_once plugin_dir_path( __FILE__ ) . 'inc/ticket-functions.php';
        require_once plugin_dir_path( __FILE__ ) . 'inc/custom-functions.php';
        // add form submit file
        require_once plugin_dir_path( __FILE__ ) . 'inc/general-shortcodes.php';
        require_once plugin_dir_path( __FILE__ ) . 'inc/spettacolo-listing-shortcodes.php';
        require_once plugin_dir_path( __FILE__ ) . 'inc/spettacolo-prices-shortcodes.php';
        require_once plugin_dir_path( __FILE__ ) . 'inc/spettacolo-subscription-shortcodes.php';
        require_once plugin_dir_path( __FILE__ ) . 'inc/developer-test-shortcodes.php';
        require_once plugin_dir_path( __FILE__ ) . 'inc/tickets-ajax.php';
        require_once plugin_dir_path( __FILE__ ) . 'inc/custom-define.php';
        require_once plugin_dir_path( __FILE__ ) . 'inc/stc-mailchimp-functions.php';
    }
}

$stcTickets = new stcTickets();

// function stc_ticket_create_plugin_page() {
//        // Define the page's title and content
//        $pages   = array(
//            array (
//            'post_name'    => 'spettacoli',
//            'post_title'   => 'Spettacoli',
//            'post_content' => '[spettacolo_listing]',
//            ),
//            array (
//            'post_name'    => 'thank-you',
//            'post_title'   => 'Grazie per il vostro acquisto',
//            'post_content' => '[spettacolo_thankyou]',
//            ),
//            array (
//            'post_name'    => 'my-account-listing',
//            'post_title'   => 'My Account Listing',
//            'post_content' => '[my_account_listing]',
//            ),
//            array (
//            'post_name'    => 'spettacolo-prices',
//            'post_title'   => 'Spettacolo Prices',
//            'post_content' => '[spettacolo_prices]',
//            ),
//            array (
//            'post_name'    => 'user-registration',
//            'post_title'   => 'User Registration',
//            'post_content' => '[login_form]',
//            )
//        );
//
//        foreach($pages as $pages_key => $pages_value){
//            $page_name = $pages_value['post_name'];
//            $page_title = $pages_value['post_title'];
//            $page_content = $pages_value['post_content'];
//            // Create the page
//            $page = array (
//                'post_name'    => $page_name,
//                'post_title'   => $page_title,
//                'post_content' => $page_content,
//                'post_type'    => 'page',
//                'post_status'  => 'publish',
//                'post_author'  => 1
//            );
//            $page_id = wp_insert_post ( $page );
//            update_option( $page_name, $page_id );
//        }
//    }

//function stc_ticket_remove_plugin_page() {
//    //  the id of our page...
//    $pages   = ['spettacoli','thank-you','my-account-listing','spettacolo-prices','user-registration'];
//    foreach($pages as $pages_key => $pages_value){
//        $the_page_id = get_option($pages_value);
//        if( $the_page_id ) {
//            wp_delete_post( $the_page_id , true ); // this will trash, not delete
//        }
//    }
//}
//function stc_ticket_remove_woocommerce_product() {
//    //  the id of our page...
//        $demo_product_id = get_option('demo_product');
//        if( $demo_product_id ) {
//            wp_delete_post( $demo_product_id,true);
//        }
//}
