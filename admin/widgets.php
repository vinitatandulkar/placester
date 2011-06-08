<?php
/**
 * Public Widgets
 * @file /admin/widgets.php
 */

add_action('widgets_init', 'placester_plugin_register_widgets');
/// Registers widgets.
function placester_plugin_register_widgets() {
    require('widgets/contact.php');
    register_widget('Placester_Contact_Widget');

    require('widgets/search.php');
    register_widget('Placester_Search_Widget');

    require('widgets/listings_map.php');
	register_widget( 'Placester_Listing_Map_Widget' );
}

 
// Add ajax function to wp_ajax 
add_action('wp_ajax_nopriv_placester_contact', 'ajax_placester_contact');
add_action('wp_ajax_placester_contact', 'ajax_placester_contact');

add_action( 'pre_get_posts', 'hide_contact');
/// Hides contact widget if not on a post page.
function hide_contact() {
        if (!is_single()) {
          wp_enqueue_script('contact.widget.hide', WP_PLUGIN_URL . '/placester/js/contact.widget.hide.js', array('jquery'));
        }
}

