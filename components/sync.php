<?php
//
// This file is used for synchronizing current user credentials
// and keeping it up-to-date. When it is no longer available
// the user will be redirected to the login page instead
//
require_once("components/scls/document.php");
require_once("components/scls/mysqli.php");
require_once("components/scls/session.php");

if (scls_session_user()) {
    $user = scls_session_user();
    $get_query = "SELECT * FROM users WHERE name = ?";
    $get_stmt = scls_mysqli()->prepare($get_query);
    $get_stmt->bind_param("s", $user["name"]);
    $get_stmt->execute();
    $get_result = $get_stmt->get_result();
    
    if ($get_result->num_rows > 0) {
        scls_session_user_set($get_result->fetch_assoc());
    } else {
        $__navigate_params = http_build_query([
            "referrer" => basename($_SERVER["SCRIPT_FILENAME"])
        ]);

        scls_session_user_unset();
        scls_document_navigate("login.php?$__navigate_params", true);
    }
} else {
    scls_document_navigate("index.php", true);
}
?>