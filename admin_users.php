<?php
require("components/bootstrap/alerts.php");
require("components/scls/document.php");
require("components/scls/mysqli.php");
require("components/scls/session.php");
require("components/sync.php");

// Forbid access if the user is not an admin
if (scls_session_user()["role"] != "admin") {
    scls_document_navigate("error.php?status=403", true);
}

function add_user(string $type, array $fields): void
{
    $valid = true;

    foreach ($fields as $key => $value) {
        if ($type != "member" && $key == "reference") {
            continue;
        }
        if (empty($value)) {
            $valid = false;
            break;
        }
    }
    if ($valid) {
        scls_mysqli()->begin_transaction();
        $insert_params = [
            $fields["username"], // Username
            $type, // Role (or Type)
            password_hash($fields["password"], PASSWORD_DEFAULT), // Password Hash
        ];
        $insert_stmt = scls_mysqli()->prepare("INSERT INTO users(name, role, password_hash) VALUES (?, ?, ?)");
        $insert_result = $insert_stmt->execute($insert_params);
        scls_mysqli()->commit();

        if ($insert_result) {
            if ($type == "member") {
                scls_mysqli()->begin_transaction();
                $query = scls_mysqli()->prepare("UPDATE students SET user = ? WHERE reference = ?");
                $query->bind_param("ss", $insert_params[0], $fields["reference"]);
                $result = $query->execute();
                scls_mysqli()->commit();

                if ($query->affected_rows > 0) {
                    bs_alerts_create(BSAlertClass::Success, "User added: <strong>$insert_params[0]</strong> ($fields[reference])");
                } else {
                    // TRANSACTION: Revert on Fail
                    scls_mysqli()->begin_transaction();
                    $query = scls_mysqli()->prepare("DELETE FROM users WHERE name = ?");
                    $query->bind_param("s", $username);
                    $query->execute();
                    scls_mysqli()->commit();

                    bs_alerts_create(BSAlertClass::Danger, "Failed to update the student record at this time, please try again later.");
                }
            } else {
                bs_alerts_create(BSAlertClass::Success, "User added: <strong>$insert_params[0]</strong> ($insert_params[1])");
            }
        } else {
            bs_alerts_create(BSAlertClass::Danger, "Failed to add user, please try again later.");
        }
    } else {
        bs_alerts_create(BSAlertClass::Danger, "Please fill out the required fields.");
    }
}

function edit_user(array $fields): void
{
    $valid = true;

    foreach ($fields as $value) {
        if (empty($value)) {
            $valid = false;
            break;
        }
    }
    if ($valid) {
        $get_query = "SELECT name FROM users WHERE name = ?";
        $get_stmt = scls_mysqli()->prepare($get_query);
        $get_stmt->bind_param("i", $fields["name"]);
        $get_stmt->execute();
        $get_result = $get_stmt->get_result();

        if ($get_result->num_rows > 0) {
            $user = $get_result->fetch_assoc();
            scls_mysqli()->begin_transaction();
            $update_params = [
                $fields["username"], // Username
                password_hash($fields["password"], PASSWORD_DEFAULT), // Password Hash
                $fields["name"] // Old Username
            ];
            $update_query = "UPDATE users SET name = ?, password_hash = ? WHERE name = ?";
            $update_stmt = scls_mysqli()->prepare($update_query);
            $update_result = $update_stmt->execute($update_params);
            scls_mysqli()->commit();

            if ($update_result) {
                bs_alerts_create(BSAlertClass::Success, "User updated: <strong>$user[name]</strong> -> <strong>$update_params[0]</strong>");
            } else {
                bs_alerts_create(BSAlertClass::Danger, "Failed to update the specified user, try again later.");
            }
        } else {
            bs_alerts_create(BSAlertClass::Danger, "Specified user is no longer available.");
        }
    } else {
        bs_alerts_create(BSAlertClass::Danger, "Please fill out the required fields.");
    }
}

function remove_user(string $name): void
{
    if (!empty($name)) {
        $get_query = "SELECT name, role FROM users WHERE name = ?";
        $get_stmt = scls_mysqli()->prepare($get_query);
        $get_stmt->bind_param("s", $name);
        $get_stmt->execute();
        $get_result = $get_stmt->get_result();

        if ($get_result->num_rows > 0) {
            $user = $get_result->fetch_assoc();
            scls_mysqli()->begin_transaction();
            $delete_query = "DELETE FROM users WHERE name = ?";
            $delete_stmt = scls_mysqli()->prepare($delete_query);
            $delete_stmt->bind_param("s", $name);
            $delete_result = $delete_stmt->execute();
            scls_mysqli()->commit();

            if ($delete_result) {
                bs_alerts_create(BSAlertClass::Success, "User removed: <strong>$user[name]</strong> ($user[role])");
            } else {
                bs_alerts_create(BSAlertClass::Danger, "Failed to remove the specified user, try again later.");
            }
        } else {
            bs_alerts_create(BSAlertClass::Danger, "Specified user is no longer available.");
        }
    } else {
        bs_alerts_create(BSAlertClass::Danger, "Invalid user name.");
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
    <title>Users &mdash; SCLS (Admin)</title>
</head>

<body>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) {
        switch ($_POST["action"]) {
            case "add":
                add_user($_POST["type"], [
                    "reference" => htmlspecialchars($_POST["reference"] ?? ""),
                    "username" => htmlspecialchars($_POST["username"] ?? ""),
                    "password" => htmlspecialchars($_POST["password"] ?? "")
                ]);
                break;
            case "edit":
                edit_user([
                    "name" => $_POST["name"],
                    "username" => htmlspecialchars($_POST["username"] ?? ""),
                    "password" => htmlspecialchars($_POST["password"] ?? "")
                ]);
                break;
            case "remove":
                remove_user(htmlspecialchars($_POST["name"] ?? ""));
                break;
            default:
                bs_alerts_create(BSAlertClass::Danger, "Action invalid or not recognized.");
        }
    }

    $search = htmlspecialchars($_GET["search"] ?? "");
    $search_type = htmlspecialchars($_GET["type"] ?? "");
    ?>
    <div class="container content">
        <header><?php require("components/navbar/admin.php"); ?></header>
        <div class="page-header">
            <h1>Users</h1>
        </div>
        <!-- Alert -->
        <?php bs_alerts_display(); ?>
        <!-- Members -->
        <div id="members">
            <?php
            if ($search_type == "member") {
                $search_member = $search;
            } else {
                $search_member = "";
            }

            $statement = "SELECT
                                u.name,
                                u.created_at,
                                u.updated_at,
                                s.reference as student_reference
                            FROM users u
                            LEFT JOIN students s ON u.name = s.user
                            WHERE
                                u.role = 'member' AND
                                (
                                    u.name LIKE '%{$search_member}%' OR
                                    s.reference LIKE '%{$search_member}%'
                                )
                            ORDER BY u.updated_at DESC";
            $query = scls_mysqli()->prepare($statement);
            $query->execute();
            $result = $query->get_result();
            ?>
            <h2>
                Members
                <?php if ($result->num_rows > 0): ?>
                    <small>(<?php echo $result->num_rows; ?>)</small>
                <?php endif; ?>
            </h2>
            <div class="row">
                <!-- Register Member -->
                <div class="col-md-9">
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#scls-user-member-modal-add">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Register
                    </button>
                    <?php require("components/modals/user/member_add.php"); ?>
                </div>
                <br class="visible-xs-block">
                <!-- Search Input -->
                <div class="col-md-3">
                    <form method="get" role="search">
                        <input type="hidden" name="type" value="member">
                        <div class="form-group input-group">
                            <input type="search" class="form-control" name="search" placeholder="Search for specific member" value="<?php echo $search_member; ?>">
                            <span class="input-group-btn">
                                <?php if (!empty($search_member)): ?>
                                    <a class="btn btn-default" href="<?php echo basename($_SERVER["SCRIPT_FILENAME"]); ?>"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                                <?php endif; ?>
                                <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Table -->
            <?php if ($result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th>Username</th>
                            <th>Learner Reference Number</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th>Actions</th>
                        </tr>
                        <?php while ($member = $result->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo $member["name"]; ?></strong></td>
                                <td><?php echo $member["student_reference"]; ?></td>
                                <td><?php echo $member["created_at"] ?? "N/A"; ?></td>
                                <td><?php echo $member["updated_at"] ?? "N/A"; ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#scls-user-member-modal-edit-<?php echo $member["name"]; ?>">
                                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                        </button>
                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#scls-user-member-modal-remove-<?php echo $member["name"]; ?>">
                                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                    <?php require("components/modals/user/member_edit.php"); ?>
                                    <?php require("components/modals/user/member_remove.php"); ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            <?php elseif (!empty($search_member)): ?>
                <p class="text-muted">The system couldn't find any results from the search query.</p>
            <?php else: ?>
                <p class="text-muted">Hmm, there aren't any members found.</p>
            <?php endif; ?>
        </div>
        <!-- Admins -->
        <div id="admins">
            <?php
            if ($search_type == "admin") {
                $search_admin = $search;
            } else {
                $search_admin = "";
            }

            $statement = "SELECT *
                            FROM users u
                            WHERE u.role = 'admin' AND u.name LIKE '%{$search_admin}%'
                            ORDER BY u.updated_at DESC";
            $query = scls_mysqli()->prepare($statement);
            $query->execute();
            $result = $query->get_result();
            ?>
            <h2>
                Administrators
                <?php if ($result->num_rows > 0): ?>
                    <small>(<?php echo $result->num_rows; ?>)</small>
                <?php endif; ?>
            </h2>
            <div class="row">
                <!-- Create New Admin -->
                <div class="col-md-9">
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#scls-user-admin-modal-add">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Create
                    </button>
                    <?php require("components/modals/user/admin_add.php"); ?>
                </div>
                <br class="visible-xs-block">
                <!-- Search Input -->
                <div class="col-md-3">
                    <form method="get" role="search">
                        <input type="hidden" name="type" value="admin">
                        <div class="form-group input-group">
                            <input type="search" class="form-control" name="search" placeholder="Search for specific admin" value="<?php echo $search_admin; ?>">
                            <span class="input-group-btn">
                                <?php if (!empty($search_admin)): ?>
                                    <a class="btn btn-default" href="<?php echo basename($_SERVER["SCRIPT_FILENAME"]); ?>"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                                <?php endif; ?>
                                <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Table -->
            <?php if ($result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th>Username</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th>Actions</th>
                        </tr>
                        <?php while ($admin = $result->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo $admin["name"]; ?></strong></td>
                                <td><?php echo $admin["created_at"] ?? "N/A"; ?></td>
                                <td><?php echo $admin["updated_at"] ?? "N/A"; ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#scls-user-admin-modal-edit-<?php echo $admin["name"]; ?>">
                                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                        </button>
                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#scls-user-admin-modal-remove-<?php echo $admin["name"]; ?>">
                                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                    <?php require("components/modals/user/admin_edit.php"); ?>
                                    <?php require("components/modals/user/admin_remove.php"); ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            <?php elseif (!empty($search_admin)): ?>
                <p class="text-muted">The system couldn't find any results from the search query.</p>
            <?php else: ?>
                <p class="text-muted">Hmm, there aren't any admins found.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php require("components/footer.php"); ?>
</body>

</html>