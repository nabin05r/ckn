<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Prevent direct access
}

class CKN_Dashboard {

    public function __construct() {
        // Add a shortcode for the dashboard
        add_shortcode( 'ckn_dashboard', [ $this, 'render_dashboard' ] );

    }

    /**
     * Render the user dashboard
     */
    public function render_dashboard() {
        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'You need to log in to view this page.', 'cool-kids-network' ) . '</p>';
        }

        // Get the current user and their role
        $current_user = wp_get_current_user();
        $user_role = $this->get_user_role( $current_user->ID );

        ob_start();
        ?>
        <div id="ckn-dashboard">
            <h2><?php _e( 'User Dashboard', 'cool-kids-network' ); ?></h2>
            <p><?php echo sprintf( __( 'Welcome, %s!', 'cool-kids-network' ), $current_user->display_name ); ?></p>

            <?php if ( $user_role === 'cool_kid' ): ?>
                <p><?php _e( 'You can view your profile page to see your personal data.', 'cool-kids-network' ); ?></p>
            <?php elseif ( $user_role === 'cooler_kid' ): ?>
                <!-- Users List (Excluding Email and Role) -->
                <table class="ckn-users-table">
                    <thead>
                        <tr>
                            <th><?php _e( 'First Name', 'cool-kids-network' ); ?></th>
                            <th><?php _e( 'Last Name', 'cool-kids-network' ); ?></th>
                            <th><?php _e( 'Country', 'cool-kids-network' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $this->list_users( false ); ?>
                    </tbody>
                </table>
            <?php elseif ( $user_role === 'coolest_kid' ): ?>
             <!-- Users List (Including Email and Role) -->
                <table class="ckn-users-table">
                    <thead>
                        <tr>
                            <th><?php _e( 'First Name', 'cool-kids-network' ); ?></th>
                            <th><?php _e( 'Last Name', 'cool-kids-network' ); ?></th>
                            <th><?php _e( 'Email', 'cool-kids-network' ); ?></th>
                            <th><?php _e( 'Role', 'cool-kids-network' ); ?></th>
                            <th><?php _e( 'Country', 'cool-kids-network' ); ?></th> <!-- Added Country column -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php $this->list_users( true ); ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p><?php _e( 'Invalid role detected.', 'cool-kids-network' ); ?></p>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Get the role of a user
     */
    private function get_user_role( $user_id ) {
        $user = get_userdata( $user_id );
        if ( ! empty( $user->roles ) && is_array( $user->roles ) ) {
            return $user->roles[0];
        }
        return '';
    }

    /**
     * List users based on role permissions
     */
    private function list_users( $include_email_and_role = false ) {
        $users = get_users();

        foreach ( $users as $user ) {
             // Exclude administrators
            if ( in_array( 'administrator', $user->roles, true ) ) {
                continue;
            }
            
            $user_country = get_user_meta( $user->ID, 'country', true ); // Get the country from user meta

            echo '<tr>';
            echo '<td>' . esc_html( $user->first_name ) . '</td>';
            echo '<td>' . esc_html( $user->last_name ) . '</td>';

            if ( $include_email_and_role ) {
                echo '<td>' . esc_html( $user->user_email ) . '</td>';
                echo '<td>' . esc_html( implode( ', ', $user->roles ) ) . '</td>';
            }

            // Display the country column
            echo '<td>' . esc_html( $user_country ? $user_country : 'N/A' ) . '</td>';
            echo '</tr>';
        }
    }
}

new CKN_Dashboard();
