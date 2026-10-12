<?php
require("components/bootstrap/alerts.php");
require("components/scls/document.php");
require("components/scls/mysqli.php");
require("components/scls/session.php");

/**
 * Registers a new user based on a given learner reference number and username.
 *
 * @param string $reference The learner reference number of the student.
 * @param string $username The username the user wants to register with.
 * @param string $password The password the user will use to log in.
 */
function register(string $reference, string $username, string $password): void
{
    if (empty($reference) || empty($username) || empty($password)) {
        bs_alerts_create(BSAlertClass::Danger, "Please fill the required fields.");
        return;
    }

    // CHECK: Student

    $query = scls_mysqli()->prepare("SELECT * FROM students WHERE reference = ?");
    $query->bind_param("s", $reference);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows < 1) {
        bs_alerts_create(BSAlertClass::Danger, "No such student with the following LRN exists: <strong>$reference</strong>");
        return;
    } elseif ($result->num_rows > 0 && $result->fetch_assoc()["user"] != null) {
        bs_alerts_create(BSAlertClass::Danger, "The student with the following LRN is already registered: <strong>$reference</strong>");
        return;
    }

    $result->close();

    // CHECK: User

    $query = scls_mysqli()->prepare("SELECT * FROM users WHERE name = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 1) {
        bs_alerts_create(BSAlertClass::Danger, "The following username is already taken: <strong>$username</strong>");
        return;
    }

    $result->close(); // needed for later queries

    // TRANSACTION: Register

    scls_mysqli()->begin_transaction();
    $query = scls_mysqli()->prepare("INSERT INTO users(name, password_hash) VALUES (?, ?)");
    $result = $query->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
    scls_mysqli()->commit();

    if (!$result) {
        bs_alerts_create(BSAlertClass::Danger, "An unknown error has occured while finalizing the registration process, aborting.");
        return;
    }

    // TRANSACTION: Update on Student

    scls_mysqli()->begin_transaction();
    $query = scls_mysqli()->prepare("UPDATE students SET user = ? WHERE reference = ?");
    $query->bind_param("ss", $username, $reference);
    $result = $query->execute();
    scls_mysqli()->commit();

    if ($query->affected_rows > 0) {
        bs_alerts_create(BSAlertClass::Success, "You have been registered successfully.");
    } else {
        // TRANSACTION: Revert on Fail
        scls_mysqli()->begin_transaction();
        $query = scls_mysqli()->prepare("DELETE FROM users WHERE name = ?");
        $query->bind_param("s", $username);
        $query->execute();
        scls_mysqli()->commit();

        bs_alerts_create(BSAlertClass::Warning, "The student records cannot be updated at this time, try again later.");
        bs_alerts_create(BSAlertClass::Danger, "An unknown error has occured while finalizing the registration process, aborting.");
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
    <title>Registration &mdash; SCLS</title>
</head>

<body>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) {
        $action = strtolower($_POST["action"]);

        switch ($action) {
            case "register":
                register(
                    htmlspecialchars($_POST["reference"] ?? ""),
                    htmlspecialchars($_POST["username"] ?? ""),
                    htmlspecialchars($_POST["password"] ?? "")
                );
                break;
            default:
                bs_alerts_create(BSAlertClass::Warning, "Action invalid or not recognized.");
        }
    }
    ?>
    <header><?php require("components/navbar/default.php"); ?></header>
    <div class="jumbotron registration">
        <div class="container">
            <h1>Registration</h1>
        </div>
    </div>
    <div class="container content">
        <!-- Alert -->
        <?php bs_alerts_display(); ?>
        <!-- Form -->
        <div class="well well-lg">
            <form method="post" id="registration">
                <input type="hidden" name="action" value="register">
                <div class="form-group">
                    <label>Learning Reference Number</label>
                    <input type="text" class="form-control" name="reference" pattern="\d*" minlength="12" maxlength="12" value="<?php echo $_POST["reference"] ?? ""; ?>" required>
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control" name="username" minlength="6" value="<?php echo $_POST["username"] ?? ""; ?>" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control" name="password"  data-field="password" minlength="4" maxlength="32" required>
                </div>
            </form>
        </div>
        <div class="row">
            <div class="col-md-6">
                <a class="btn btn-default pull-left" data-toggle="modal" data-target="#scls-glossary-modal-reference">
                    <span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span>
                    What's my LRN?
                </a>
                <?php require("components/modals/glossary/reference.php"); ?>
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary pull-right" form="registration">Register</button>
            </div>
        </div>
    </div>
    <?php require("components/footer.php"); ?>
</body>

</html>