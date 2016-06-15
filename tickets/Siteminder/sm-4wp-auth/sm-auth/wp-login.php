<?php

/* This script should be run by someone who has authenticated via SiteMinder.
   If this does not appear to be the case, then we should not attempt to 
   continue further here.
*/

function sec_session_start() {
    // $session_name = 'default_sec_session_id';   // Set a custom session name
    $secure = FALSE;
    // This stops JavaScript being able to access the session id.
    $httponly = TRUE;
    // Forces sessions to only use cookies.
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        // header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
        exit();
    }
    // Gets current cookies params.
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"],
        $cookieParams["path"],
        $cookieParams["domain"],
        $secure,
        $httponly);
    // Sets the session name to the one set above.
    // session_name($session_name);
    session_start();            // Start the PHP session
    session_regenerate_id();    // regenerated the session, delete the old one.
}


$headers = apache_request_headers();


if (empty($headers['SM_USER'])) {
	echo "You must first login in order to access this page.<br>\n";
	exit;
}

if ((empty($_SERVER['REMOTE_USER'])) || (!(strcmp($_SERVER['REMOTE_USER'],$headers['SM_USER']) == 0))) {
	echo "You must first login in order to access this page.<br>\n";
	exit;
}


// We define the JHED id based upon the value of the person who has 
// apparently logged in via SiteMinder.
$jhedid = $headers['SM_USER'];
$emailaddress = $headers['JHED_mail'];
$firstname = $headers['JHED_givenname'];
$lastname = $headers['JHED_sn'];


// Now we need to make sure that a session variable exists, to reflect
// the login session for $jhedid.

// The use of session_status would require at least PHP 5.4 or above ...
// if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}

sec_session_start();
$_SESSION['SM_JHEDID'] = $jhedid;
$_SESSION['SM_EMAIL'] = $emailaddress;
$_SESSION['SM_FNAME'] = $firstname;
$_SESSION['SM_LNAME'] = $lastname;



// We wish to redirect to a given URL.  If a redirection location does not
// appear to have been specified, though, then we should use a default value.
$serverurl = parse_url($_SERVER["SERVER_NAME"], PHP_URL_PATH);
$redirectURL = "http://$serverurl";

if (!(empty($_GET["dest"]))) {
	$redirectURL = $_GET["dest"];
}


header("Location: " . urldecode($redirectURL));


?>
