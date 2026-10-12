<?php

/**
 * Initializes the PHP session.
 * 
 * @return void
 */
function scls_session_init(): void
{
    if (session_status() == PHP_SESSION_NONE) {
        session_set_cookie_params([
            "httponly" => true, // Prevent JS access
            "samesite" => "Strict" // Mitigate CSRF risks
        ]);

        session_start();
    }
}

/**
 * Checks if the PHP session is active.
 *
 * @return bool True if the PHP session is active, otherwise false.
 */
function scls_session_is_active(): bool
{
    return session_status() == PHP_SESSION_ACTIVE;
}

/**
 * Sets the current user instance for this session.
 *
 * Stores the provided user data in the session and removes
 * the password hash from the session data for security purposes.
 *
 * @param array $user The user data to be set in the session.
 * @return void
 */
function scls_session_user_set(array $user): void
{
    if (!scls_session_is_active()) {
        return;
    }

    $_SESSION["user"] = $user;
    $_SESSION["user"]["password_hash"] = null;
}

/**
 * Unsets the current user instance for this session.
 *
 * This function is used to sign out the user from the system.
 *
 * @return void
 */
function scls_session_user_unset(): void
{
    if (!scls_session_is_active()) {
        return;
    }

    unset($_SESSION["user"]);
}

/**
 * Retrieves the current user instance for this session.
 *
 * If the session is not active or no user is set in the session,
 * this function returns null.
 *
 * @return array|null The user data or null if the session is not active.
 */
function scls_session_user(): array|null
{
    if (!scls_session_is_active() || empty($_SESSION["user"])) {
        return null;
    }

    return $_SESSION["user"];
}

// Initialize the session after including this file
scls_session_init();
?>