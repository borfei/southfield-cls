<?php
require("components/bootstrap/alerts.php");
require("components/scls/document.php");
require("components/scls/mysqli.php");
require("components/scls/session.php");

/**
 * Signs the user in using the given username and password
 *
 * @param string $username The username to sign in with
 * @param string $password The password to sign in with
 */
function sign_in(string $username, string $password): void
{
    if (empty($username) || empty($password)) {
        bs_alerts_create(BSAlertClass::Danger, "Please fill out the required fields.");
        return;
    }

    // CHECK: User

    $query = scls_mysqli()->prepare("SELECT * FROM users WHERE name = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows < 1) {
        bs_alerts_create(BSAlertClass::Danger, "User does not exist.");
        return;
    }

    $user = $result->fetch_assoc();

    // CHECK: Password
    
    if (password_verify($password, $user["password_hash"])) {
        scls_session_user_set($user);
        scls_document_navigate(scls_document_referrer("index.php"), true);
    } else {
        bs_alerts_create(BSAlertClass::Danger, "Incorrect password.");
    }

    $result->free();
}

/**
 * Signs the user out of the system.
 *
 * If the user is successfully signed out, the page will be refreshed
 * and the user will be shown a success message. If the user is not
 * signed out, the page will be refreshed and the user will be shown
 * an error message.
 *
 * @return void
 */
function sign_out(): void
{
    if (!scls_session_user()) {
        bs_alerts_create(BSAlertClass::Warning, "You are not signed in.");
        return;
    }

    scls_session_user_unset();

    if (!scls_session_user()) {
        bs_alerts_create(BSAlertClass::Success, "You have been signed out.");
    } else {
        bs_alerts_create(BSAlertClass::Danger, "Sign out failed.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require("components/document/charset.php"); ?>
    <?php require("components/document/viewport.php"); ?>
    <?php require("components/document/styles.php"); ?>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <title>Login &mdash; SCLS</title>
</head>

<body class="login">
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) {
        $action = strtolower($_POST["action"]);

        switch ($action) {
            case "sign_in":
                sign_in(
                    htmlspecialchars($_POST["username"]) ?? "",
                    htmlspecialchars($_POST["password"]) ?? ""
                );
                break;
            case "sign_out":
                sign_out();
                break;
            default:
                bs_alerts_create(BSAlertClass::Warning, "Action invalid or not recognized.");
                break;
        }
    }
    if (scls_session_user()) {
        scls_document_navigate(scls_document_referrer("index.php"), true);
    }
    ?>
    <div class="container content root">
        <div class="row login main">
            <!-- Header -->
            <div class="col-md-8">
                <div class="login landing">
                    <img class="img-responsive login logo" src="assets/logo-alt.png" width="250px" alt="PSS Logo" />
                    <div>
                        <h2 class="hidden-xs hidden-sm">Computerized Library System</h2>
                        <h4 class="hidden-lg hidden-md text-center">Computerized Library System</h4>
                    </div>
                </div>
            </div>
            <!-- Form -->
            <div class="col-md-4">
                <div class="well well-lg login">
                    <div class="page-header login header">
                        <h1>Log In</h1>
                    </div>
                    <?php bs_alerts_display(); ?>
                    <form method="post" action="login.php">
                        <input type="hidden" name="action" value="sign_in">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="username" class="form-control" name="username" minlength="6" value="<?php echo $_POST["username"] ?? ""; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password" data-field="password" minlength="4" maxlength="32" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-block btn-primary">Sign in</button>
                        </div>
                    </form>
                    <a class="btn btn-block btn-sm btn-default" href="registration.php">Register</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>