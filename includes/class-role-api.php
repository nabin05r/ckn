<?php

if (! defined('ABSPATH')) {
    exit; // Prevent direct access
}

class CKN_Role_API
{
    public function __construct()
    {
        // Register REST API endpoint
        add_action('rest_api_init', [ $this, 'register_endpoints' ]);
    }

    /**
     * Register API endpoint
     */
    public function register_endpoints()
    {
        register_rest_route('ckn/v1', '/assign-role', [
            'methods'  => 'POST',
            'callback' => [ $this, 'assign_role' ],
            'permission_callback' => [ $this, 'check_permissions' ],
        ]);
    }

    /**
     * Handle role assignment
     */
    public function assign_role($request)
    {
        $params = $request->get_params();

        // Validate input
        if (empty($params['role']) || ( empty($params['email']) && ( empty($params['first_name']) || empty($params['last_name']) ) )) {
            return new WP_Error('invalid_data', __('Invalid data provided.', 'cool-kids-network'), [ 'status' => 400 ]);
        }

        $role = sanitize_text_field($params['role']);

        // Validate role
        $valid_roles = [ 'cool_kid', 'cooler_kid', 'coolest_kid' ];
        if (! in_array($role, $valid_roles, true)) {
            return new WP_Error('invalid_role', __('Invalid role provided.', 'cool-kids-network'), [ 'status' => 400 ]);
        }

        // Get user by email or first and last name
        if (! empty($params['email'])) {
            $user = get_user_by('email', sanitize_email($params['email']));
        } else {
            $user_query = new WP_User_Query([
                'meta_query' => [
                    [
                        'key'   => 'first_name',
                        'value' => sanitize_text_field($params['first_name']),
                    ],
                    [
                        'key'   => 'last_name',
                        'value' => sanitize_text_field($params['last_name']),
                    ],
                ],
            ]);
            $user = $user_query->get_results() ? $user_query->get_results()[0] : null;
        }

        // If user not found
        if (! $user) {
            return new WP_Error('user_not_found', __('User not found.', 'cool-kids-network'), [ 'status' => 404 ]);
        }

        // Assign role to the user
        // $user_id = $user->ID;
        $user->set_role($role);

        return [
            'status'  => 'success',
            'message' => sprintf(__('Role "%s" assigned to user "%s %s".', 'cool-kids-network'), $role, $user->first_name, $user->last_name),
        ];
    }

    /**
     * Check permissions for the API
     */
    public function check_permissions()
    {
        // Only allow administrators to use this endpoint
        return current_user_can('manage_options');
    }
}

new CKN_Role_API();
