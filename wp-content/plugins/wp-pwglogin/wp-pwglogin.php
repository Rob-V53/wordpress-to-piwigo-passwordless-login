<?php
/*
Plugin Name: WP PWGLOGIN
Description: This plugin provides paswordless login to piwigo by adding a token in the piwigo user table and a login link
Date:        29-07-2021
Version:     1.0
Author:      Rob Visser
License:     GPL2 etc
Text Domain: wp-pwg-text-domain
*/


require_once plugin_dir_path( __FILE__ ) . 'admin/admin.php';
require_once plugin_dir_path( __FILE__ ) . 'include/add_user_to_piwigo.php';
require_once plugin_dir_path( __FILE__ ) . 'include/make_passwd.php';
require_once plugin_dir_path( __FILE__ ) . 'include/pwg_shortcode.php';

function wp_pwglogin_link( $content ) {
	global $post;
	global $wpdb;
	$newcontent='';
	$the_user = wp_get_current_user();
	$the_user_name = $the_user->user_login;
	if ( $post->post_name == 'documenten' ) {
		$meta_key = 'miles';
		$token = $wpdb->get_var( $wpdb->prepare( 
		"
			SELECT one_time_token 
			FROM piwigo_users
			WHERE username = %s
		", 
		$the_user_name
	) );
//		echo "<p>Token is {$token}</p>";

		$newcontent =  '<p>Open link in a new window or tab: <a href=' . get_option( 'pwg_url_field' ) .
			'?username=' . $the_user_name .
			'&token=' . $token .
		       	' target="_blank"> &lt;&lt; Login in het forum zonder wachtwoord &gt;&gt; </a></p>' . $content;
		return $newcontent;
      } else {
	        return $content;
      }
}
add_filter( 'the_content', 'wp_pwglogin_link' );

 
// ------------------------------------------------------------------
//
function prepare_piwigo_user ($user_login, $user){
	global $wpdb;

	// First check if the user is present in the piwigo_users table
	$piwigo_users = 'piwigo_users';
        $row = $wpdb->get_row( $wpdb->prepare('SELECT * FROM '. $piwigo_users .' WHERE username = %s', $user_login) );
//	var_error_log($row);
	$one_time_token = mkPassword( 12 );
	if ( $row == null ) {
	    error_log("add user", 0);
	    
		// add user because he/she is not yet in piwigo_users table
		// first: retrieve the user email address from wordpress
 	    $the_mail = $wpdb->get_row( $wpdb->prepare( 'SELECT user_email FROM '. $wpdb->users .' WHERE user_login = %s' , $user_login) , ARRAY_A );
            error_log($the_mail['user_email']); 
	    if (add_user_to_piwigo($user_login, $the_mail['user_email']))
	    {
                $wpdb->update( $piwigo_users, ['one_time_token' => $one_time_token],  ['username' => $user_login] );
	    }
	} else {
	    $wpdb->update( $piwigo_users, ['one_time_token' => $one_time_token],  ['username' => $user_login] );
        }
//	var_error_log($one_time_token);
//	var_error_log($user);
}

add_action('wp_login', 'prepare_piwigo_user', 10, 2);

function var_error_log( $object=null ){
    ob_start();                    // start buffer capture
    var_dump( $object );           // dump the values
    $contents = ob_get_contents(); // put the buffer into a variable
    ob_end_clean();                // end capture
//    error_log(“ERROR LOG error_log ERROR LOG”);
    error_log( $contents );        // log contents of the result of var_dump( $object )
}
 



