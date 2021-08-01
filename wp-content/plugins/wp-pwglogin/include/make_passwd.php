<?php

/*
Description: This function creates a string filled with random characters
Version:     1.0
Author:      Rob Visser thanks to Freddy Muriuki
Author URI:  https://www.hoogeslag.eu/gallery
License:     GPL2 etc
Text Domain: wp-pwg-text-domain
*/



function mkPassword($length)
{
    $psswd = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789";

    for ($i=0; $i < $length; $i++) {
	$index = hexdec(bin2hex(openssl_random_pseudo_bytes(2))) % strlen($codeAlphabet);
        $psswd .= $codeAlphabet[$index];
    }

    return $psswd;
}
?>

