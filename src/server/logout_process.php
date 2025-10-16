<?php
try {
    // start session first before deleting
    session_start();
    // delete all variables in the session
    session_unset();
    // delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    // destroy the session
    session_destroy();
    // redirect to index page
    header("Location: ../index.php");

} catch (\Throwable $th) {
    echo "Error logging out: " . $th->getMessage();
}

?>