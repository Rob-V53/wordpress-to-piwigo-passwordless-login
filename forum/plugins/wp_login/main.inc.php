<?php
/*
Plugin Name: wp_login
Version: xxxxx
Description: This plugin is a proof of concept for passwordless login from wordpress
Plugin URI: http://piwigo.org/
Author: Rob Visser
Author URI: http://www.xxx.xx
*/

/**
 * This is the main file of the plugin, called by Piwigo in "include/common.inc.php" line 137.
 * At this point of the code, Piwigo is not completely initialized, so nothing should be done directly
 * except define constants and event handlers (see http://piwigo.org/doc/doku.php?id=dev:plugins)
 */

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');


// +-----------------------------------------------------------------------+
// | Define plugin constants                                               |
// +-----------------------------------------------------------------------+
global $prefixeTable;

define('WP_LOGIN_ID',      basename(dirname(__FILE__)));
define('WP_LOGIN_PATH' ,   PHPWG_PLUGINS_PATH . WP_LOGIN_ID . '/');
define('WP_LOGIN_TABLE',   $prefixeTable . 'wp_login');
define('WP_LOGIN_ADMIN',   get_root_url() . 'admin.php?page=plugin-' . WP_LOGIN_ID);
define('WP_LOGIN_PUBLIC',  get_absolute_root_url() . make_index_url(array('section' => 'wp_login')) . '/');
define('WP_LOGIN_DIR',     PHPWG_ROOT_PATH . PWG_LOCAL_DIR . 'wp_login/');


//
add_event_handler('try_log_user', 'mywp_login',
    EVENT_HANDLER_PRIORITY_NEUTRAL);



function mywp_login ($success, $username, $password, $remember_me)
{
    global $logger;
	
	$logger->info('message', array(
  'success' => $success,
  'username' => $username,
  'remember_me' => $remember_me
));

  if ($success===true)
  {
    return true;
  }
  $ott='';
  if (isset($_COOKIE['one_time_token'])){
	    $ott = $_COOKIE['one_time_token'];
  	    if (preg_match("/^[a-z0-9]+$/i", $ott)){
		  // we force the session table to be clean
  		pwg_session_gc();

  		global $conf;
		var_error_log($conf['user_fields']);

  // add field to user_fields
                if (!in_array('one_time_token', $conf['user_fields']))
                {
                     $conf['user_fields']['one_time_token'] = 'one_time_token';
  //		     var_error_log($conf['user_fields']);
                }

  // retrieving the encrypted password of the login submitted
  		$query = '
SELECT '.$conf['user_fields']['id'].' AS id,
       '.$conf['user_fields']['one_time_token'].' AS token 
  FROM '.USERS_TABLE.'
  WHERE '.$conf['user_fields']['username'].' = \''.pwg_db_real_escape_string($username).'\'
;';
		
		
  		$row = pwg_db_fetch_assoc(pwg_query($query));
  
  		if ((isset($row['id'])) and ($row['token'] == $ott)){
    		    log_user($row['id'], $remember_me);
    		    trigger_notify('login_success', stripslashes($username));
    		    return true;
  		} else {
  		    trigger_notify('login_failure', stripslashes($username));
  		    return false;
   		} 
	    } else {
		var_error_log($_COOKIE['one_time_token']);
		return false;		
	    }
  	} else {
	  return false;
	}
  return false;
}

function var_error_log( $object=null ){
    ob_start();                    // start buffer capture
    var_dump( $object );           // dump the values
    $contents = ob_get_contents(); // put the buffer into a variable
    ob_end_clean();                // end capture
    error_log( $contents );        // log contents of the result of var_dump( $object )
}


