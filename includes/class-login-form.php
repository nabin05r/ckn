<?php

if (! defined('ABSPATH')) {
    exit; // Prevent direct access
}

class CKN_Login_Form
{
    public function __construct()
    {
        // Add a shortcode for the login form
        add_shortcode('ckn_login_form', [ $this, 'render_login_form' ]);
        // Redirect logged-in users to the profile page
        add_action('template_redirect', [ $this, 'redirect_logged_in_users' ]);
    }

    /**
     * Render the login form
     */
    public function render_login_form()
    {
        if (is_user_logged_in()) {
            return '<p>' . __('You are already logged in.', 'cool-kids-network') . '</p>';
        }

        ob_start();
        ?>
        <form id="ckn-login-form" method="POST" class="ckn-form">
            <p class="ckn-form-group">
                <label for="ckn_login_email"><?php _e('Email', 'cool-kids-network'); ?></label>
                <input type="email" name="ckn_login_email" id="ckn_login_email" required class="ckn-input">
            </p>
            <p class="ckn-form-group">
                <label for="ckn_login_password"><?php _e('Password', 'cool-kids-network'); ?></label>
                <input type="password" name="ckn_login_password" id="ckn_login_password" required class="ckn-input">
            </p>
            <p class="ckn-form-group">
                <button type="submit" name="ckn_login" class="ckn-btn"><?php _e('Log In', 'cool-kids-network'); ?></button>
            </p>
            <div id="ckn-login-message" class="ckn-message"></div>
        </form>
        <?php

        if (isset($_POST['ckn_login'])) {
            $this->handle_login();
        }

        return ob_get_clean();
    }

    /**
     * Handle login logic
     */
    public function handle_login()
    {
        // Sanitize email
        $email = sanitize_email($_POST['ckn_login_email']);
        $password = sanitize_text_field($_POST['ckn_login_password']);

        if (! is_email($email)) {
            echo '<p style="color: red;">' . __('Invalid email address.', 'cool-kids-network') . '</p>';
            return;
        }

        // Check if the email exists
        $user = get_user_by('email', $email);
        if (! $user) {
            echo '<p style="color: red;">' . __('Email not registered.', 'cool-kids-network') . '</p>';
            return;
        }

        // Check the password
        if ($password !== 'ckn@123') {
            echo '<p style="color: red;">' . __('Invalid password.', 'cool-kids-network') . '</p>';
            return;
        }

        // Log the user in
        wp_set_auth_cookie($user->ID);

        // Redirect to profile page
        wp_redirect(home_url('/profile')); // Adjust '/profile' to your profile page slug
        exit;
    }

    /**
     * Redirect logged-in users to the profile page
     */
    public function redirect_logged_in_users()
    {
        if (is_user_logged_in() && is_page('login')) { // Replace 'login' with your login page slug
            wp_redirect(home_url('/profile')); // Adjust '/profile' to your profile page slug
            exit;
        }
    }
}

new CKN_Login_Form();
