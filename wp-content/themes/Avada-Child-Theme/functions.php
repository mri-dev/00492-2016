<?php
define('DEVMODE', true);
define('THEMEROOT', get_stylesheet_directory_uri() );
define('IMGROOT', THEMEROOT.'images/' );

// Includes
require_once "includes/include.php";

function theme_enqueue_styles() {
    wp_enqueue_style( 'avada-parent-stylesheet', get_template_directory_uri() . '/style.css?t=' . ( (DEVMODE === true) ? time() : '' ) );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

function custom_theme_enqueue_styles() {
    wp_enqueue_style( 'tenerifeingatlan-css', THEMEROOT . '/tenerifeingatlan.css?t=' . ( (DEVMODE === true) ? time() : '' ) );
}
add_action( 'wp_enqueue_scripts', 'custom_theme_enqueue_styles', 100 );

function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'Avada', $lang );
}
add_action( 'after_setup_theme', 'avada_lang_setup' );

function after_logo_slogan()
{
    echo '<div class="logo-slogan">a kanári szigetek szakértője</div>';
}
add_action( 'avada_logo_append', 'after_logo_slogan' );
