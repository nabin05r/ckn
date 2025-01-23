<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
    // Prevent direct access
}

class CKN_Register_Form {

    public function __construct() {
        // Add shortcode for the registration form
        add_shortcode( 'ckn_register_form', [ $this, 'render_registration_form' ] );

        // Handle AJAX requests
        add_action( 'wp_ajax_ckn_register_user', [ $this, 'register_user_ajax' ] );
        add_action( 'wp_ajax_nopriv_ckn_register_user', [ $this, 'register_user_ajax' ] );
    }

    /**
    * Render the registration form
    */

    public function render_registration_form() {
        if ( is_user_logged_in() ) {
            return '<p>' . __( 'You are already registered.', 'cool-kids-network' ) . '</p>';
        }

        ob_start();
        ?>
        <form id='ckn-register-form' method='POST' class='ckn-form'>
            <p class='ckn-form-group'>
                <label for='ckn_email'><?php _e( 'Email', 'cool-kids-network' );
                    ?></label>
                <input type='email' name='ckn_email' id='ckn_email' required class='ckn-input'>
            </p>
            <p class='ckn-form-group'>
                <button type='submit' id='ckn_register_btn' class='ckn-btn'><?php _e( 'Confirm', 'cool-kids-network' );
                    ?></button>
            </p>
            <div id='ckn-loader' class='ckn-spinner' style='display: none;'></div>
            <div id='ckn-message' class='ckn-message'></div>
        </form>

    <?php
        return ob_get_clean();
    }

    /**
    * Handle AJAX user registration
    */

    public function register_user_ajax() {
        check_ajax_referer( 'ckn_register_nonce', 'nonce' );

        $email = isset( $_POST[ 'email' ] ) ? sanitize_email( $_POST[ 'email' ] ) : '';

        if ( ! is_email( $email ) ) {
            wp_send_json_error( [ 'message' => __( 'Invalid email address.', 'cool-kids-network' ) ] );
        }

        if ( email_exists( $email ) ) {
            wp_send_json_error( [ 'message' => __( 'This email is already registered.', 'cool-kids-network' ) ] );
        }

        // Generate fake identity
        $response = wp_remote_get( 'https://randomuser.me/api/' );
        if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
            wp_send_json_error( [ 'message' => __( 'Error fetching identity data.', 'cool-kids-network' ) ] );
        }

        $data = json_decode( wp_remote_retrieve_body( $response ), true );
        $user_data = $data[ 'results' ][ 0 ];

        $username   = sanitize_user( $user_data[ 'login' ][ 'username' ] );
        $first_name = sanitize_text_field( $user_data[ 'name' ][ 'first' ] );
        $last_name  = sanitize_text_field( $user_data[ 'name' ][ 'last' ] );
        $country    = sanitize_text_field( $user_data[ 'location' ][ 'country' ] );

        $user_id = wp_insert_user( [
            'user_login' => $username,
            'user_email' => $email,
            'user_pass'  => wp_generate_password(),
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'role'       => 'cool_kid',
        ] );

        if ( is_wp_error( $user_id ) ) {
            wp_send_json_error( [ 'message' => __( 'Error creating user.', 'cool-kids-network' ) ] );
        }

        update_user_meta( $user_id, 'country', $country );

        wp_send_json_success( [ 'message' => __( 'Registration successful! You can now log in.', 'cool-kids-network' ) ] );
    }

}

new CKN_Register_Form();