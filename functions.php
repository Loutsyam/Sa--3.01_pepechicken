<?php
// Charge le style du thème parent (Storefront) puis celui du thème enfant
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style( 'storefront-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'pepe-chicken-style', get_stylesheet_uri(), array( 'storefront-style' ), wp_get_theme()->get( 'Version' ) );
} );
