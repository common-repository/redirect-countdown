<?php
/**
* Plugin Name: Redirect countdown
* Plugin URI: https://wordtune.me/wordtune-plugins/
* Description: Use the shortcode [countdown] on any page ore post and redirect users to a target URL after a set period of time..
* Author: WordTune
* Author URI: https://wordtune.me
* Version:           1.0
* License:           GPL-2.0+
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
**/



// Define a custom admin menu page to set the timeout duration and redirect URL

function countdown_settings_page() {
    add_menu_page(
        'Countdown Settings',
        'Countdown',
        'manage_options',
        'countdown_settings',
        'countdown_settings_content',
        'dashicons-clock',
        100 // Menu position
    );
}

function countdown_settings_content() {
    $timeout = get_option('countdown_timeout', 60);
    $redirect_url = get_option('countdown_redirect_url', 'https://example.com');
    $custom_text = get_option('countdown_custom_text', 'You will be redirected in');

    if (isset($_POST['countdown_timeout']) && isset($_POST['countdown_redirect_url']) && isset($_POST['countdown_custom_text'])) {
        $timeout = intval($_POST['countdown_timeout']);
$redirect_url = filter_input( INPUT_POST, 'countdown_redirect_url', FILTER_SANITIZE_URL );
        $custom_text = sanitize_text_field($_POST['countdown_custom_text']);
        update_option('countdown_timeout', $timeout);
        update_option('countdown_redirect_url', $redirect_url);
        update_option('countdown_custom_text', $custom_text);
        echo '<div class="notice notice-success"><p>' . esc_html__('Timeout duration, redirect URL and custom text updated.', 'redirect-countdown') . '</p></div>';
    }

    echo '<div class="wrap"><h1>' . esc_html__('Countdown Settings', 'redirect-countdown') . '</h1>';
    echo '<form method="post">';
    echo '<table class="form-table">';
    echo '<tr><th><label for="countdown_timeout">' . esc_html__('Timeout duration (in seconds):', 'redirect-countdown') . '</label></th><td><input type="number" id="countdown_timeout" name="countdown_timeout" min="1" value="' . esc_attr($timeout) . '"></td></tr>';
    echo '<tr><th><label for="countdown_redirect_url">' . esc_html__('Redirect URL:', 'redirect-countdown') . '</label></th><td><input type="url" id="countdown_redirect_url" name="countdown_redirect_url" value="' . esc_attr($redirect_url) . '"></td></tr>';
    echo '<tr><th><label for="countdown_custom_text">' . esc_html__('Custom text:', 'redirect-countdown') . '</label></th><td><input type="text" id="countdown_custom_text" name="countdown_custom_text" value="' . esc_attr($custom_text) . '"></td></tr>';
    echo '</table>';
    echo '<p><input type="submit" class="button button-primary" value="' . esc_attr__('Save Changes', 'redirect-countdown') . '"></p>';
    echo '</form></div>';
}

function countdown_shortcode() {
   $timeout = absint( get_option( 'countdown_timeout', 60 ) );
   $redirect_url = esc_url( get_option( 'countdown_redirect_url', 'https://example.com' ) );
   $custom_text = sanitize_text_field( get_option( 'countdown_custom_text', 'You will be redirected in' ) );
   $endTime = time() + $timeout; // End time is $timeout seconds from now

   if ( time() >= $endTime ) {
       echo '<script>window.location.replace("' . esc_url( $redirect_url ) . '");</script>'; // Redirect to the specified URL if time is up
   } else {
       $timeLeft = $endTime - time(); // Calculate time left in seconds
       echo '<div class="countdown-wrapper"><span class="countdown-label">' . esc_html( $custom_text ) . '</span> <span class="countdown-time"><span id="countdown">' . esc_html( $timeLeft + 1 ) . '</span> seconds</span></div>'; // Display countdown
       echo '<script>var countdown = document.getElementById("countdown"); setInterval(function() {
           var timeLeft = parseInt(countdown.innerText);
           if (--timeLeft < 0) {
               window.location.replace("' . esc_url( $redirect_url ) . '");
           } else {
               countdown.innerText = timeLeft;
           }
       }, 1000);</script>';
   }
}

add_action( 'admin_menu', 'countdown_settings_page' );
add_shortcode( 'countdown', 'countdown_shortcode' );
