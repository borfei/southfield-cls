<?php
require("components/scls/document.php");
require("components/scls/session.php");

// Automatically navigate to desired landing page if logged in
// otherwise, require user to have it's credentials authorized
if (scls_session_user()) {
    // Detect user role and navigate to proper landing page
    $role = scls_session_user()["role"];

    if ($role == "admin") {
        $document = "admin_transactions.php";
    } elseif ($role == "member") {
        $document = "member_transactions.php";
    }

    scls_document_navigate($document ?? "error.php?status=404", true);
} else {
    // Properly unset user credentials and redirect to login page
    scls_session_user_unset();
    scls_document_navigate("login.php", true);
}
