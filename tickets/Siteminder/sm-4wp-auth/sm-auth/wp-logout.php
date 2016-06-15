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


// We wish to destroy any session id that might currently exist for this
// connection.
sec_session_start();

// Unset all of the session variables.
$_SESSION = array();


// We wish to remove the session cookie and data here.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Now, we try destroy the session.
session_destroy();


# We should redirect to the LogoffUri which the SiteMinder agent needs to see in order to end the session.
# However, if available, we should also send along (in the dest variable) the location we would like to arrive
# at afterwards.
$initialURL = "/bin/logout";


$serverurl = parse_url($_SERVER["SERVER_NAME"], PHP_URL_PATH);
$finalDestURL = "http://$serverurl";

if (!(empty($_GET["dest"]))) {
        $finalDestURL = $_GET["dest"];
}

$redirectURL = $initialURL . "?dest=" . urldecode($finalDestURL);

# Now we proceed with sending a redirect ...
header("Location: " . $redirectURL);


?>
