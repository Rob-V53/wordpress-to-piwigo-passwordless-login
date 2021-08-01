<?php

/*
Description: This function provides the content of a shortcode that is a login to Piwigo
Version:     1.0
Date:        29-07-2021
Author:      Rob Visser
License:     GPL2 etc
Text Domain: wp-pwg-text-domain
*/


function piwigo_login_shortcode() { 

// Things that you want to do. 
    global $wpdb;
    $the_user = wp_get_current_user();
    $the_user_name = $the_user->user_login;
    $token = $wpdb->get_var( $wpdb->prepare( 
        "
        SELECT one_time_token 
        FROM piwigo_users
        WHERE username = %s
        ", 
        $the_user_name
    ) );
    if ($token){
        $theContent =  '<p>Open Piwigo in a new window or tab: <a href=' . get_option( 'pwg_url_field' ) .
        '?username=' . $the_user_name .
        '&token=' . $token .
        ' target="_blank"> &lt;&lt; Login &gt;&gt; </a></p>';
    } else {
        $theContent="<p> not available </p>";
    }
    return $theContent;
} 
// register shortcode
add_shortcode('PIWIGO_LOGIN', 'piwigo_login_shortcode');

?>

