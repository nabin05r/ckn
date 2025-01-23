<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Prevent direct access
}

class CKN_Profile {

    public function __construct() {
        // Add a shortcode for the profile page
        add_shortcode( 'ckn_profile', [ $this, 'render_profile' ] );
    }

    /**
     * Render the user profile
     */
    public function render_profile() {
        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'You need to log in to view this page.', 'cool-kids-network' ) . '</p>';
        }

        // Get the current user data
        $current_user = wp_get_current_user();
        
        ob_start();
        ?>
        <div id="ckn-profile">
            <h2><?php _e( 'Profile Details', 'cool-kids-network' ); ?></h2>
            <table class="ckn-user-profile-table">
                <tr>
                    <th><?php _e( 'First Name', 'cool-kids-network' ); ?></th>
                    <td><?php echo esc_html( $current_user->first_name ); ?></td>
                </tr>
                <tr>
                    <th><?php _e( 'Last Name', 'cool-kids-network' ); ?></th>
                    <td><?php echo esc_html( $current_user->last_name ); ?></td>
                </tr>
                <tr>
                    <th><?php _e( 'Email', 'cool-kids-network' ); ?></th>
                    <td><?php echo esc_html( $current_user->user_email ); ?></td>
                </tr>
                <tr>
                    <th><?php _e( 'Country', 'cool-kids-network' ); ?></th>
                    <td><?php echo esc_html( get_user_meta( $current_user->ID, 'country', true ) ); ?></td>
                </tr>
                <tr>
                    <th><?php _e( 'Role', 'cool-kids-network' ); ?></th>
                    <td><?php echo esc_html( implode( ', ', $current_user->roles ) ); ?></td>
                </tr>
            </table>
        </div>
        <?php
        return ob_get_clean();
    }
}

new CKN_Profile();
