# Explanation.md

## Problem to be Solved

The requirement is to create a custom login and dashboard functionality for a Cool Kids Network (CKN) WordPress site, ensuring different user roles (`cool_kid`, `cooler_kid`, `coolest_kid`) have tailored experiences. All the functionality is done by creating the plugin CKN. The admin should be able to:

1. Allow users to log in via a simple login form without needing individual passwords.
2. Allow users to sign up using a seamless and user-friendly AJAX-based experience.
3. Restrict the visibility of the WordPress admin bar for non-admin users.
4. Enable logged-in users to view specific data based on their role:
   - `cool_kid`: Access their profile.
   - `cooler_kid`: View basic information of all users (excluding email and roles).
   - `coolest_kid`: View detailed information of all users (including email and roles).
   - Administrators are excluded from being listed to ensure privacy.
5. Change a login button to "Logout" when a user is logged in.
6. Provide an API endpoint for managing user roles dynamically.

### Challenges:

- Ensure the solution respects WordPress’s architecture and coding standards.
- Maintain a user-friendly experience.
- Dynamically update the login/logout button.
- Prevent unauthorized access to restricted information.
- Provide a scalable solution for future integrations.

---

## Technical Specification

### 1. Login System

- **Login Form:** A custom shortcode `[ckn_login_form]` renders a login form for users. This form uses a single common password (`ckn@123`) for authentication.
- **Password Handling:** For simplicity and as per the requirement, all users share the same password. The system validates the email and password to log users in using WordPress’s `wp_set_auth_cookie()`.
- **Redirects:** After successful login, users are redirected to their dashboard.

### 2. Signup System

- **AJAX-Based Signup:** The signup form is designed to work seamlessly using AJAX to improve the user experience. When users submit their information, an AJAX call processes the signup request in the background without reloading the page, providing instant feedback and reducing friction.
- **User Data via API:** During signup, user details such as first name, last name, and country are fetched dynamically using the `randomuser.me` API. This ensures a seamless experience and provides enriched user data.
- **Validation:** The system ensures that the email address is valid and not already in use. Any errors (e.g., missing fields or invalid email) are displayed dynamically.

### 3. Dashboard

- **Dashboard Shortcode:** A shortcode `[ckn_dashboard]` displays a user-specific dashboard.

- **Role-Based Content:** The dashboard content is tailored to the user’s role:

  - `cool_kid`: Only sees their profile information.
  - `cooler_kid`: Can view a list of users with basic details (excluding email and role).
  - `coolest_kid`: Can view a detailed user list (including email and roles).
  - Administrators are excluded from being listed to ensure privacy.

- **User Country Data:** The `country` field is fetched using `get_user_meta()`.

- **Filtering Admin Users:** Administrators are filtered out using:

### 4. Admin Bar Restriction

- The admin bar is hidden for non-admin users by hooking into `show_admin_bar`:


### 5. Dynamic Login/Logout Button

- Using a unique class (`login-btn`), the login/logout button is updated dynamically using jQuery:

### 6. Role Management via API

- **Custom REST API Endpoint:** A custom API endpoint `ckn/v1/assign-role` allows authorized administrators to change user roles. For example:

  - Admins can send a POST request with `email` and `new_role` to update a user’s role.
  - Alternatively, they can send `first_name` and `last_name` along with `new_role` to update roles.
  - The API validates the user and role before updating it.
  - A success message is returned upon completion.

- **Authentication:**
  - The API uses the JWT Authentication plugin to validate requests.
  - The `JWT_AUTH_SECRET_KEY` has been added to the `wp-config.php` file to enable secure token-based authentication.
  - Admins must first obtain an authorization token by sending a POST request to the endpoint `wp-json/jwt-auth/v1/token` with `username` and `password` as parameters.
  - The token must then be included in the headers of the role assignment request as an `Authorization` key.

- **Data Format:**
  - Requests should be sent using `x-www-form-urlencoded` with the following parameters in the body:
    - `email` or `first_name` and `last_name`
    - `new_role`

- **How It Works:**
  - Admins can integrate external systems with this endpoint to manage roles programmatically.
  - The API ensures only valid roles are assigned to users, maintaining data integrity.

---

## Technical Decisions

### Why Certain Choices Were Made:

1. **Shared Password**: Simplified proof-of-concept requirement avoids individual password management.
2. **AJAX Signup**: Improves the user experience by reducing page reloads and providing instant feedback.
3. **Shortcodes**: Shortcodes ensure easy integration into WordPress pages and posts.
4. **Role-Based Dashboard**: Tailored dashboards keep functionality simple while respecting user roles.
5. **Admin Bar Restriction**: Removing the admin bar for non-admins enhances the user experience for non-technical users.
6. **Dynamic Button Handling**: Using jQuery allows real-time updates to the login/logout button without reloading the page.
7. **REST API for Role Management**: Adds flexibility for integrations with external systems and easier role management.

---

## How the Solution Achieves the Admin's Desired Outcome

1. **Login Form**: Simplifies login with a shared password while ensuring email validation.
2. **Signup Form**: Provides a seamless AJAX-based signup experience to improve user engagement.
3. **Dashboard Features**:
   - Users can only access information permitted by their role.
   - Admin users are excluded from being listed.
4. **Admin Bar Restriction**: Ensures a clean interface for non-admin users.
5. **Dynamic Login Button**: Provides a seamless user experience by dynamically updating the button to "Logout" when logged in.
6. **API Role Management**: Enables easy role changes and integration with external systems.

---

## Shortcodes Implemented

The following shortcodes are used for rendering various forms and the dashboard:

- `[ckn_register_form]`: Displays the registration form.
- `[ckn_login_form]`: Displays the login form.
- `[ckn_profile]`: Displays the user profile.
- `[ckn_dashboard]`: Displays the user dashboard.

These shortcodes allow for flexible integration into WordPress pages and posts.

---

## Thought Process

### Approach

- **Understand Requirements**: Break down the problem into manageable tasks.
- **Modular Design**: Use shortcodes for modularity and reusability.
- **Leverage WordPress Hooks**: Use built-in WordPress features like `get_user_meta`, `wp_set_auth_cookie`, and hooks for robust functionality.

### Why This Direction?

- **Scalability**: Using shortcodes, AJAX, and REST API ensures future extensibility.
- **User-Focused**: Role-based dashboards and AJAX ensure users only see what they need.
- **Compliance**: Sticking to WordPress best practices makes the solution maintainable.

---

## Plugin Implementation

All of the above features have been implemented as a WordPress plugin CKN to ensure easy installation, management, and scalability.

