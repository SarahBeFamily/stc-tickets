<?php
/*
 *  Register a post type opf spettacolo 
 */
function create_ticket_posttype() {

    $textdomain    = 'stc-tickets';
    $spettacolo_labels = array (
        'name'               => _x( 'Spettacolo', 'spettacolo', $textdomain ),
        'singular_name'      => _x( 'Spettacolo', 'spettacolo', $textdomain ),
        'menu_name'          => _x( 'Spettacolo', 'spettacolo', $textdomain ),
        'name_admin_bar'     => _x( 'Spettacolo', 'spettacolo', $textdomain ),
        'add_new'            => _x( 'Add New', 'spettacolo', $textdomain ),
        'add_new_item'       => __( 'Add New Spettacolo', $textdomain ),
        'new_item'           => __( 'New Spettacolo', $textdomain ),
        'edit_item'          => __( 'Edit Spettacolo', $textdomain ),
        'view_item'          => __( 'View Spettacolo', $textdomain ),
        'all_items'          => __( 'All Spettacoli', $textdomain ),
        'search_items'       => __( 'Search Spettacoli', $textdomain ),
        'parent_item_colon'  => __( 'Parent Spettacolo:', $textdomain ),
        'not_found'          => __( 'No spettacolo found.', $textdomain ),
        'not_found_in_trash' => __( 'No spettacolo found in Trash.', $textdomain ),
    );

    $spettacolo_args = array (
        'labels'             => $spettacolo_labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array ( 'slug' => 'spettacolo', 'with_front' => false ),
        'capability_type'    => 'post',
        'has_archive'        => false,
        "exclude_from_search" => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-pressthis', // Menu Icon from https://developer.wordpress.org/resource/dashicons/
        'supports'           => array ( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' )
    );

    register_post_type( 'spettacolo', $spettacolo_args );    
    /*
     * Location CPT
     */
    $location_labels = array (
        'name'               => _x( 'Location', 'location', $textdomain ),
        'singular_name'      => _x( 'Location', 'location', $textdomain ),
        'menu_name'          => _x( 'Location', 'location', $textdomain ),
        'name_admin_bar'     => _x( 'Location', 'location', $textdomain ),
        'add_new'            => _x( 'Add New', 'location', $textdomain ),
        'add_new_item'       => __( 'Add New Location', $textdomain ),
        'new_item'           => __( 'New Location', $textdomain ),
        'edit_item'          => __( 'Edit Location', $textdomain ),
        'view_item'          => __( 'View Location', $textdomain ),
        'all_items'          => __( 'All Locations', $textdomain ),
        'search_items'       => __( 'Search Locations', $textdomain ),
        'parent_item_colon'  => __( 'Parent Location:', $textdomain ),
        'not_found'          => __( 'No location found.', $textdomain ),
        'not_found_in_trash' => __( 'No location found in Trash.', $textdomain ),
    );

    $location_args = array (
        'labels'             => $location_labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array ( 'slug' => 'location', 'with_front' => false ),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        "exclude_from_search" => true,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-location', // Menu Icon from https://developer.wordpress.org/resource/dashicons/
        'supports'           => array ( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' )
    );

    register_post_type( 'location', $location_args );
}
add_action( 'init', 'create_ticket_posttype' );

