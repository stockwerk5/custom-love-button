<?php
/*
Plugin Name: Custom Love Button
Description: Fügt einen "Love It ❤" Button am Ende jedes Artikels hinzu.
Version: 1.0
Author: Sebastian Ulbert
*/

function clb_enqueue_scripts() {
    wp_enqueue_script('clb-js', plugin_dir_url(__FILE__) . 'custom-love-button.js', array('jquery'), '1.0', true);
    wp_localize_script('clb-js', 'clb_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('clb_nonce')
    ));
    wp_enqueue_style('clb-css', plugin_dir_url(__FILE__) . 'custom-love-button.css');
}
add_action('wp_enqueue_scripts', 'clb_enqueue_scripts');

function clb_add_love_button($content) {
    if (is_single()) {
        global $post;
        $love_count = get_post_meta($post->ID, 'clb_love_count', true) ?: '0';
        $button_html = '<button class="clb-love-button" data-post_id="' . $post->ID . '">Love It ❤</button>';
        $counter_html = '<span class="clb-love-counter">' . $love_count . '</span>';
        $content .= $button_html . $counter_html;
    }
    return $content;
}
add_filter('the_content', 'clb_add_love_button');

function clb_handle_love() {
    if (!check_ajax_referer('clb_nonce', 'nonce', false)) {
        wp_send_json_error('Invalid nonce', 401);
    }
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    if ($post_id <= 0) {
        wp_send_json_error('Invalid post ID', 400);
    }
    $love_count = get_post_meta($post_id, 'clb_love_count', true) ?: 0;
    $love_count++;
    update_post_meta($post_id, 'clb_love_count', $love_count);
    echo $love_count;
    wp_die();
}
add_action('wp_ajax_clb_handle_love', 'clb_handle_love');
add_action('wp_ajax_nopriv_clb_handle_love', 'clb_handle_love');

// Backend-Code für die Spaltenanzeige und Quick Edit-Funktionalität
include('custom-love-button-admin.php');
