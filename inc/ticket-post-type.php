<?php
/*
 *  Register a post type opf spettacolo 
 */
function create_ticket_posttype() {

    $spettacolo_labels = array(
        'name'               => 'Spettacolo',
        'singular_name'      => 'Spettacolo',
        'menu_name'          => 'Spettacolo',
        'name_admin_bar'     => 'Spettacolo',
        'add_new'            => 'Aggiungi nuovo',
        'add_new_item'       => 'Aggiungi nuovo Spettacolo',
        'new_item'           => 'Nuovo Spettacolo',
        'edit_item'          => 'Modifica Spettacolo',
        'view_item'          => 'Vedi Spettacolo',
        'all_items'          => 'Tutti gli Spettacoli',
        'search_items'       => 'Cerca Spettacoli',
        'parent_item_colon'  => 'Spettacolo parente:',
        'not_found'          => 'Nessuno spettacolo trovato.',
        'not_found_in_trash' => 'Nessuno spettacolo trovato nel cestino.',
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
    $location_labels = array(
        'name'               => 'Location',
        'singular_name'      => 'Location',
        'menu_name'          => 'Location',
        'name_admin_bar'     => 'Location',
        'add_new'            => 'Aggiungi nuovo',
        'add_new_item'       => 'Aggiungi nuova Location',
        'new_item'           => 'Nuova Location',
        'edit_item'          => 'Modifica Location',
        'view_item'          => 'Vedi Location',
        'all_items'          => 'Tutte le Locations',
        'search_items'       => 'Cerca Locations',
        'parent_item_colon'  => 'Location parente:',
        'not_found'          => 'Nessuna location trovata.',
        'not_found_in_trash' => 'Nessuna location trovata nel cestino.',
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

