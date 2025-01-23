//AJAX Handler for the Register Form
jQuery(document).ready(function ($) {
    $('#ckn-register-form').on('submit', function (e) {
        e.preventDefault(); // Prevent default form submission

        const email = $('#ckn_email').val(); // Get email input value
        const $message = $('#ckn-message'); // Message container
        const $loader = $('#ckn-loader'); // Loader element
        const $form = $(this); // Form element

        // Show loader
        $loader.show();
        $message.html(''); // Clear previous messages

        // AJAX request
        $.ajax({
            url: ckn_ajax.ajax_url, // AJAX URL from localized script
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'ckn_register_user', // Action defined in PHP
                email: email,
                nonce: ckn_ajax.nonce, // Nonce from localized script
            },
            success: function (response) {
                // Hide loader
                $loader.hide();

                if (response.success) {
                    $message.html(`<p style="color:green;">${response.data.message}</p>`);
                    $form.trigger('reset'); // Reset the form
                } else {
                    $message.html(`<p style="color:red;">${response.data.message}</p>`);
                }
            },
            error: function (xhr, status, error) {
                // Hide loader
                $loader.hide();

                // Display error message
                $message.html('<p style="color:red;">An error occurred. Please try again.</p>');
                console.error('AJAX Error:', status, error);
            },
        });
    });
});

//Changing the Login button text to Logout when user is logged in 
jQuery(window).on('load', function () {
    const $loginButton = jQuery('.login-btn a'); // Target the link inside the login-btn class

    if (jQuery('body').hasClass('logged-in')) {
        // Log to the console for debugging
        console.log('User is logged in. Updating login button.');

        // Update the button text to "Logout"
        $loginButton.text('Logout');

        // Update the URL to the logout action with a redirect to the homepage
        $loginButton.attr(
            'href',
            ckn_ajax.logoutUrl
        );
    } else {
        console.log('User is not logged in.');
    }
});

