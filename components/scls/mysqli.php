<?php
$__scls_mysqli = null;

/**
 * Initialize the MySQLi connection.
 * 
 * The connection is stored in the $__mysqli global variable.
 * Use scls_mysqli() to retrieve the connection.
 * 
 * @return void
 */
function scls_mysqli_init(): void
{
    global $__scls_mysqli;
    // mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $__scls_mysqli = new mysqli("localhost", "root", null, "scls");
}

/**
 * Checks if the MySQLi connection is initialized.
 * 
 * @return bool Returns true if the connection is initialized, false otherwise.
 */
function scls_mysqli_is_init(): bool
{
    global $__scls_mysqli;
    return $__scls_mysqli != null;
}

/**
 * Retrieves the initialized MySQLi instance or null if the instance is not initialized.
 * 
 * @return mysqli|null The MySQLi instance if initialized, otherwise null.
 */
function scls_mysqli(): mysqli|null
{
    if (!scls_mysqli_is_init()) {
        return null;
    }

    global $__scls_mysqli;
    return $__scls_mysqli;
}

// Initialize MySQLi connection after including this file
scls_mysqli_init();
?>