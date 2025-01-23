<?php
/*
 * Plugin Name:       Cool Kids Network
 * Plugin URI:        https://nabinmagar.com
 * Description:       A user-management system for the Cool Kids Network.
 * Version:           1.0.0
 * Requires at least: 5.9
 * Requires PHP:      7.2
 * Author:            Nabin Gharti Magar
 * Author URI:        https://nabinmagar.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       cool-kids-network
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Prevent direct access
}

if ( ! class_exists( 'Cool_Kids_Network' ) ) {

    class Cool_Kids_Network {

        public function __construct() {
            $this->define_constants();
            $this->include_files();

            // Hook initialization functions

            register_activation_hook( __FILE__, [ $this, 'activate' ] );
            register_deactivation_hook( __FILE__, [ $this, 'deactivate' ] );
            add_action( 'wp_enqueue_scripts', [ $this, 'ckn_styles']);
            add_action( 'admin_menu', [ $this, 'add_menu' ]); 
            add_action( 'after_setup_theme', [ $this, 'hide_wpadminbar' ]); 
        }

        /**
         * Define plugin constants
         */
        private function define_constants() {
            define( 'CKN_PATH', plugin_dir_path( __FILE__ ) );
            define( 'CKN_URL', plugin_dir_url( __FILE__ ) );
            define( 'CKN_VERSION', '1.0.0' );
        }

        /**
         * Include required files
         */
        private function include_files() {
            require_once CKN_PATH . 'includes/class-register-form.php';
            require_once CKN_PATH . 'includes/class-login-form.php';
            require_once CKN_PATH . 'includes/class-dashboard.php';
            require_once CKN_PATH . 'includes/class-profile.php';
            require_once CKN_PATH . 'includes/class-role-api.php';
        }

        /**
         * Actions to perform on plugin activation
         */
        public function activate() {
            // Create roles when plugin is activated
            add_role( 'cool_kid', __( 'Cool Kid', 'cool-kids-network' ), [ 'read' => true ] );
            add_role( 'cooler_kid', __( 'Cooler Kid', 'cool-kids-network' ), [ 'read' => true ] );
            add_role( 'coolest_kid', __( 'Coolest Kid', 'cool-kids-network' ), [ 'read' => true ] );
        }

        /**
         * Actions to perform on plugin deactivation
         */
        public function deactivate() {
            // Remove custom roles after deactivating plugin
            remove_role( 'cool_kid' );
            remove_role( 'cooler_kid' );
            remove_role( 'coolest_kid' );
        }

        /**
         * Enqueue Scripts and Styles
         */
        public function ckn_styles(){
            wp_enqueue_style( 'ckn-login-form', CKN_URL . 'assets/css/style.css', [], CKN_VERSION );
            // wp_enqueue_script($handle, $src, $deps, $ver, $in_footer)
            wp_enqueue_script( 'ckn-main-js', CKN_URL . '/assets/js/ckn-main.js', [ 'jquery' ], null, true );
            wp_localize_script( 'ckn-main-js', 'ckn_ajax', [
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'ckn_register_nonce' ),
                'logoutUrl' => wp_logout_url(home_url()), // Dynamically generate logout URL'
            ] );
        }

        /**
         * Adding Plugin to WP Menu 
         */
        public function add_menu(){
            add_menu_page(
                'Cool Kids Network',
                esc_html__('CKN', 'cool-kids-network'),
                'manage_options',
                'ckn-settings',
                array( $this, 'ckn_setting_page' ),
                // 'dashicons-images-alt2',
                
            );
            
        }

        public function ckn_setting_page(){
            ?>
            <h1><?php esc_html_e( "Shortcodes", 'cool-kids-network') ?></h1>
            <p><?php esc_html_e("Use this shortcode [ckn_register_form] to display register form" ,'cool-kids-network')?></p>
            <p><?php esc_html_e("Use this shortcode [ckn_login_form] to display register form" ,'cool-kids-network')?></p>
            <p><?php esc_html_e("Use this shortcode [ckn_profile] to display register form" ,'cool-kids-network')?></p>
            <p><?php esc_html_e("Use this shortcode [ckn_dashboard] to display register form" ,'cool-kids-network')?></p>
        <?php
        }

     
        public function hide_wpadminbar(){
            if ( ! current_user_can( 'administrator' ) && ! is_admin() ) {
                show_admin_bar( false );
            }
        }

    }

    

    $cool_kid_network = new Cool_Kids_Network();
}
