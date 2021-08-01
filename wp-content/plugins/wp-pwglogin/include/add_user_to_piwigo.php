<?php

/*
Description: This function adds a wordpress user as a user into piwigo
Version:     1.0
Date:        30-07-2021
Author:      Rob Visser thanks to Freddy Muriuki
Author URI:  https://www.example.com
License:     GPL2 etc
Text Domain: wp-pwg-text-domain
*/




function add_user_to_piwigo ($user_login, $user_email){

    $admin_user = get_option( 'pwg_admin_user_field' );
    $admin_password = get_option( 'pwg_admin_password_field' ) ;
    $url_field = get_option( 'pwg_url_field' );

    $retval = false;
// Step 1 start a session with admin capabilities    
    $ch = curl_init( $url_field . '/ws.php' );
    $payload = http_build_query( array( 
	            "method" => "pwg.session.login", "username" => $admin_user, "password" => $admin_password ) );
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload );
    $cookies = tempnam('/tmp','cookie.txt');
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookies); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
    $result = curl_exec($ch);
//    curl_close($ch);

    if (strpos($result, 'rsp stat="ok"')!== false) {

// Step 2 session has started, now retrieve the pwg_token 
//        $ch = curl_init( $url_field . '/ws.php' );
        $payload = http_build_query(array( 
    	            "method" => "pwg.session.getStatus" ) );
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookies);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        $result = curl_exec($ch);
	    if (preg_match('/<pwg_token>(.*?)<\/pwg_token>/', $result, $match) == 1) {
	        $pwg_token = $match[1];
// Step 3 token retrieved now add the user_login into piwigo
//          var_dump($pwg_token);
	    
            $payload = http_build_query(array( 
    	            "method" => "pwg.users.add", "username" => $user_login, 
                    "password" => mkPassword( 12 ), "email" => $user_email, "pwg_token" => $pwg_token ) );
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload );
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookies);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
	        $result = curl_exec($ch);
	        var_dump($result);
            if (strpos($result, 'rsp stat="ok"')!== false) {
		error_log("user " . $user_login . "sucessfully added to piwigo");
		$retval = true;
            } else {
                error_log("user " . $user_login . "could not be added to piwigo");
            }
        } else {
            error_log("could not retrieve pwg_token", 0);
        }
// now end the session
        $payload = http_build_query(array( 
                    "method" => "pwg.session.logout" ) );
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookies);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        $result = curl_exec($ch);
        error_log("session ended" . $result);

    } else {
        error_log("Could not login as admin", 0);
    }
    curl_close($ch);
    return ($retval);
}
?>
