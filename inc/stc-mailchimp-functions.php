<?php
// Function to unsubscribe a user from Mailchimp by email
function stc_unsubscribe_user_from_mailchimp($email) { 
    // Your Mailchimp API credentials
    $api_key         = stc_mailchimp_get_api_key();
    $list_id         = stc_mailchimp_get_checkout_list_id();  // Replace with your Mailchimp list ID
    // Mailchimp API endpoint
    $datacenter      = substr( $api_key, strpos( $api_key, '-' ) + 1 );
    $subscriber_hash = md5( strtolower( $email ) );  // MD5 hash of the email
    // URL for the API request
    $url             = "https://$datacenter.api.mailchimp.com/3.0/lists/$list_id/members/$subscriber_hash";

    // Data to send (change status to unsubscribed)
    $data = [
        'status' => 'unsubscribed', // Unsubscribe the user
    ];

    // Make the API request to unsubscribe the user
    $response = wp_remote_request(
            $url,
            [
                'method'  => 'PATCH',
                'body'    => json_encode( $data ),
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode( 'user:' . $api_key ),
                    'Content-Type'  => 'application/json',
                ],
            ]
    );

    // Handle the response
    if( is_wp_error( $response ) ) {
        // Log error or handle failure
        error_log( 'Mailchimp API Error (unsubscribing user): ' . $response->get_error_message() );
        return [ 'success' => false, 'code' => '404', 'message' => 'Mailchimp API Error (unsubscribing user): ' . $response->get_error_message() ];
        return false;
    } else {
        // Optionally log success or process the response
        $response_body = wp_remote_retrieve_body( $response );
        $data          = json_decode( $response_body, true );

        // If successful, Mailchimp will return a 200 OK response
        if( isset( $data[ 'status' ] ) && $data[ 'status' ] === 'unsubscribed' ) {
            return [ 'success' => true, 'code' => '200', 'data' => $data ];  // Successfully unsubscribed
        } else {
            return [ 'success' => false, 'code' => '404', 'message' => 'Mailchimp API Error (unexpected response): ' . $response_body ];
            return false;
        }
    }
}
function stc_mailchimp_check_if_user_already_subscribed($email) {
    // Your Mailchimp API credentials
    $api_key = stc_mailchimp_get_api_key();
    $list_id = stc_mailchimp_get_checkout_list_id();  // Replace with your Mailchimp list ID
    // Mailchimp API endpoint
    $url     = "https://<dc>.api.mailchimp.com/3.0/lists/$list_id/members/";

    // Set the Mailchimp datacenter prefix (replace <dc> with your Mailchimp datacenter)
    $datacenter = substr( $api_key, strpos( $api_key, '-' ) + 1 );

    // First, check if the user is already subscribed
    $check_url = "https://$datacenter.api.mailchimp.com/3.0/lists/$list_id/members/" . md5( strtolower( $email ) );

    $response = wp_remote_get(
            $check_url,
            [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode( 'user:' . $api_key ),
                ]
            ]
    );
    if( is_wp_error( $response ) ) {
        // Handle error in the API request (could be a network error or invalid request)
        return [ 'code' => '403', 'message' => 'Mailchimp API Error (checking subscription): ' . $response->get_error_message() ];
        error_log( 'Mailchimp API Error (checking subscription): ' . $response->get_error_message() );
    } else {
        $response_body = wp_remote_retrieve_body( $response );
        $data          = json_decode( $response_body, true );

        // Check if the user is already subscribed
        if( isset( $data[ 'status' ] ) && $data[ 'status' ] === 'subscribed' ) {
            // If the user is already subscribed, don't re-add them
            return [ 'code' => '200', 'message' => 'already subscribed', 'data' => $data ];
        } else {
            return [ 'code' => '201', 'message' => 'not subscribed' ];
        }
    }
}
function stc_mailchimp_subscribe_user($email) {
    // Your Mailchimp API credentials
    $api_key = stc_mailchimp_get_api_key();
    $list_id = stc_mailchimp_get_checkout_list_id();  // Replace with your Mailchimp list ID
    // Mailchimp API endpoint
    $url     = "https://<dc>.api.mailchimp.com/3.0/lists/$list_id/members/";

    // Set the Mailchimp datacenter prefix (replace <dc> with your Mailchimp datacenter)
    $datacenter = substr( $api_key, strpos( $api_key, '-' ) + 1 );

    // First, check if the user is already subscribed
    $check_url = "https://$datacenter.api.mailchimp.com/3.0/lists/$list_id/members/" . md5( strtolower( $email ) );

    $response = wp_remote_get(
            $check_url,
            [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode( 'user:' . $api_key ),
                ]
            ]
    );

    if( is_wp_error( $response ) ) {
        // Handle error in the API request (could be a network error or invalid request)
        return [ 'code' => '403', 'message' => 'Mailchimp API Error (checking subscription): ' . $response->get_error_message() ];
        error_log( 'Mailchimp API Error (checking subscription): ' . $response->get_error_message() );
    } else {
        $response_body = wp_remote_retrieve_body( $response );
        $data          = json_decode( $response_body, true );

        // Check if the user is already subscribed
        if( isset( $data[ 'status' ] ) && $data[ 'status' ] === 'subscribed' ) {
            // If the user is already subscribed, don't re-add them
            return [ 'code' => '201', 'message' => 'already subscribed' , 'data' => $response_body];
        } else {

            // User is not subscribed, proceed with adding them to Mailchimp
            $data_to_send = [
                'email_address' => $email,
                'status'        => 'subscribed', // 'subscribed' to add user to the list
            ];

            // Make the API request to add the user to Mailchimp
            $add_response = wp_remote_post(
                    "https://$datacenter.api.mailchimp.com/3.0/lists/$list_id/members",
                    [
                        'method'  => 'POST',
                        'body'    => json_encode( $data_to_send ),
                        'headers' => [
                            'Authorization' => 'Basic ' . base64_encode( 'user:' . $api_key ),
                            'Content-Type'  => 'application/json',
                        ]
                    ]
            );

            // Handle the response if needed
            if( is_wp_error( $add_response ) ) {
                // Log error or handle failure
                error_log( 'Mailchimp API Error (adding user): ' . $add_response->get_error_message() );
                return [ 'code' => '401', 'message' => 'Mailchimp API Error (adding user): ' . $add_response->get_error_message() ];
            } else {
                // Optionally log success or process the response
                $add_response_body = wp_remote_retrieve_body( $add_response );
                return [ 'code' => '200', 'data' => $add_response_body ];
                // Log or process $add_response_body if necessary
            }
        }
    }
    return [ 'code' => '404', 'message' => 'There are some issue with the API..' ];
}
// Function to get all subscribers from a Mailchimp list
function get_all_mailchimp_subscribers($api_key, $list_id) {
    // Set up the Mailchimp datacenter prefix based on the API key
    $datacenter = substr( $api_key, strpos( $api_key, '-' ) + 1 );  // Extract data center (us1, us2, etc.)
    // Mailchimp API endpoint for getting list members
    $url        = "https://$datacenter.api.mailchimp.com/3.0/lists/$list_id/members";

    // Headers for the API request (including the API key for authorization)
    $headers = [
        'Authorization' => 'Basic ' . base64_encode( 'user:' . $api_key ),
    ];

    // Initialize an empty array to store all the subscribers
    $subscribers = [];
    $offset      = 0; // Initialize the offset for pagination

    do {
        // Make the API request to get the list members
        $response = wp_remote_get(
                $url . '?offset=' . $offset . '&count=10&email_address=kvardas@gmail.com', // Adjust count per request (max 1000)
                [
                    'headers' => $headers,
                ]
        );

        // Check for errors in the response
        if( is_wp_error( $response ) ) {

            $error = ('Mailchimp API Error (fetching subscribers): ' . $response->get_error_message());
            return [ 'code' => 410, 'message' => $error ];
        }

        // Get the response body and decode it into an associative array
        $response_body = wp_remote_retrieve_body( $response );
        $data          = json_decode( $response_body, true );

        // Check if the response contains members
        if( isset( $data[ 'members' ] ) ) {
            // Add the members to the $subscribers array
            $subscribers = array_merge( $subscribers, $data[ 'members' ] );
        }

        // Check if there are more members (pagination)
        $offset += 1000;  // Move to the next page (increase the offset)
    }
    while ( isset( $data[ 'members' ] ) && count( $data[ 'members' ] ) > 0 );
    return [ 'code' => 200, 'data' => $subscribers ];
}
function stc_mailchimp_get_api_key() {
    if( function_exists( 'mc4wp_get_api_key' ) ) {
        $api_key = mc4wp_get_api_key();
        return $api_key;
    }
    return '';
}
function stc_mailchimp_get_checkout_list_id() {
    $list_id = 'de09090ea2';
    return $list_id;
}
