<?php
$session_timeout = 28800;

ini_set('session.gc_maxlifetime', $session_timeout);

session_start();

if (isset($_SESSION['last_activity'])) {
    $elapsed_time = time() - $_SESSION['last_activity'];

    if ($elapsed_time > $session_timeout) {
        session_regenerate_id(true);
    }
}

$_SESSION['last_activity'] = time();

$_SESSION = array();

session_destroy();

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

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

header("Location: index.php");
exit();
