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

/**
 * Adds a new student to the database, given the specified fields.
 *
 * @param array $fields The fields to be inserted into the database.
 */
function add_student(array $fields): void
{
    $valid = true;

    foreach ($fields as $key => $value) {
        if ($key == "initial_name") {
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
            $fields["reference"], // Learner Reference Number
            ucwords($fields["given_name"]), // Given Name (capitalized first letter of every word)
            ucwords($fields["initial_name"]), // Initial Name (capitalized first letter of every word)
            ucwords($fields["family_name"]), // Family Name (capitalized first letter of every word)
            $fields["grade_level"], // Grade Level
            $fields["grade_section"] // Grade Section
        ];
        $insert_query = "INSERT INTO students(
                                            reference,
                                            given_name, initial_name, family_name,
                                            grade_level, grade_section
                                        )
                            VALUES (?, ?, ?, ?, ?, ?)";
        $insert_stmt = scls_mysqli()->prepare($insert_query);
        $insert_result = $insert_stmt->execute($insert_params);
        scls_mysqli()->commit();

        if ($insert_result) {
            bs_alerts_create(BSAlertClass::Success, "Student added: <strong>$insert_params[0]</strong> ($insert_params[2], <strong>$insert_params[1]</strong>)");
        } else {
            bs_alerts_create(BSAlertClass::Danger, "Failed to add student, please try again later.");
        }
    } else {
        bs_alerts_create(BSAlertClass::Danger, "Please fill out the required fields.");
    }
}

/**
 * Edits an existing student in the database, given the specified fields.
 *
 * @param array $fields The fields to be updated in the database.
 */
function edit_student(array $fields): void
{
    $valid = true;

    foreach ($fields as $key => $value) {
        if ($key == "initial_name") {
            continue;
        }
        if (empty($value)) {
            $valid = false;
            break;
        }
    }
    if ($valid) {
        $get_query = "SELECT given_name, family_name FROM students WHERE reference = ?";
        $get_stmt = scls_mysqli()->prepare($get_query);
        $get_stmt->bind_param("i", $fields["reference"]);
        $get_stmt->execute();
        $get_result = $get_stmt->get_result();

        if ($get_result->num_rows > 0) {
            $student = $get_result->fetch_assoc();

            scls_mysqli()->begin_transaction();
            $update_params = [
                $fields["new_reference"], // Learner Reference Number
                ucwords($fields["given_name"]), // Given Name (capitalized first letter of every word)
                ucwords($fields["initial_name"]), // Initial Name (capitalized first letter of every word)
                ucwords($fields["family_name"]), // Family Name (capitalized first letter of every word)
                $fields["grade_level"], // Grade Level
                $fields["grade_section"], // Grade Section
                $fields["reference"] // Learner Reference Number (old)
            ];
            $update_query = "UPDATE students
                                    SET
                                        reference = ?,
                                        given_name = ?,
                                        initial_name = ?,
                                        family_name = ?,
                                        grade_level = ?,
                                        grade_section = ?
                                    WHERE reference = ?";
            $update_stmt = scls_mysqli()->prepare($update_query);
            $update_result = $update_stmt->execute($update_params);
            scls_mysqli()->commit();

            if ($update_result) {
                bs_alerts_create(BSAlertClass::Success, "Student updated: <strong>$student[family_name], $student[given_name]</strong> ($fields[reference]) -> <strong>$update_params[3], $update_params[1]</strong> ($update_params[0])");
            } else {
                bs_alerts_create(BSAlertClass::Danger, "Failed to update the specified student, try again later.");
            }
        } else {
            bs_alerts_create(BSAlertClass::Danger, "Specified student is no longer available.");
        }
    } else {
        bs_alerts_create(BSAlertClass::Danger, "Please fill out the required fields.");
    }
}

/**
 * Removes a student from the database based on the given learner reference number.
 *
 * @param string $reference The learner reference number of the student to be removed.
 *
 * This function performs the following actions:
 * - Checks if the reference number is not empty.
 * - Retrieves the student's given name and family name from the database using the reference number.
 * - If the student exists, attempts to delete the student record from the database.
 * - Commits the transaction and provides appropriate success or failure alerts based on the result.
 * - If the student does not exist, provides an alert indicating the student is no longer available.
 * - Provides an alert for invalid or empty reference numbers.
 */
function remove_student(string $reference): void
{
    if (!empty($reference)) {
        $reference = $_POST["reference"];
        $get_query = "SELECT given_name, family_name FROM students WHERE reference = ?";
        $get_stmt = scls_mysqli()->prepare($get_query);
        $get_stmt->bind_param("s", $reference);
        $get_stmt->execute();
        $get_result = $get_stmt->get_result();

        if ($get_result->num_rows > 0) {
            $student = $get_result->fetch_assoc();

            scls_mysqli()->begin_transaction();
            $delete_query = "DELETE FROM students WHERE reference = ?";
            $delete_stmt = scls_mysqli()->prepare($delete_query);
            $delete_stmt->bind_param("s", $reference);
            $delete_result = $delete_stmt->execute();
            scls_mysqli()->commit();

            if ($delete_result) {
                bs_alerts_create(BSAlertClass::Success, "Student removed: <strong>$student[family_name], $student[given_name]</strong> ($reference)");
            } else {
                bs_alerts_create(BSAlertClass::Danger, "Failed to remove the specified student, try again later.");
            }
        } else {
            bs_alerts_create(BSAlertClass::Danger, "Specified student is no longer available.");
        }
    } else {
        bs_alerts_create(BSAlertClass::Danger, "Invalid student LRN.");
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
    <title>Students &mdash; SCLS (Admin)</title>
</head>

<body>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) {
        $action = strtolower($_POST["action"]);

        switch ($action) {
            case "add":
                add_student([
                    "reference" => htmlspecialchars($_POST["reference"] ?? ""),
                    "given_name" => htmlspecialchars($_POST["given_name"] ?? ""),
                    "initial_name" => htmlspecialchars($_POST["initial_name"] ?? ""),
                    "family_name" => htmlspecialchars($_POST["family_name"] ?? ""),
                    "grade_level" => htmlspecialchars($_POST["grade_level"] ?? ""),
                    "grade_section" => htmlspecialchars($_POST["grade_section"] ?? "TBA")
                ]);
                break;
            case "edit":
                edit_student([
                    "reference" => htmlspecialchars($_POST["reference"] ?? ""),
                    "new_reference" => htmlspecialchars($_POST["new_reference"] ?? ""),
                    "given_name" => htmlspecialchars($_POST["given_name"] ?? ""),
                    "initial_name" => htmlspecialchars($_POST["initial_name"] ?? ""),
                    "family_name" => htmlspecialchars($_POST["family_name"] ?? ""),
                    "grade_level" => htmlspecialchars($_POST["grade_level"] ?? ""),
                    "grade_section" => htmlspecialchars($_POST["grade_section"] ?? "TBA")
                ]);
                break;
            case "remove":
                remove_student(intval($_POST["reference"] ?? -1));
                break;
            default:
                bs_alerts_create(BSAlertClass::Warning, "Action invalid or not recognized.");
        }
    }

    $search = htmlspecialchars($_GET["search"] ?? "");
    $statement = "SELECT
                        *,
                        CASE
                            WHEN s.user IS NULL
                            THEN 'No'
                            ELSE 'Yes'
                        END AS registered
                    FROM students s
                    WHERE
                        s.reference LIKE '%{$search}%' OR
                        s.given_name LIKE '%{$search}%' OR
                        s.initial_name LIKE '%{$search}%' OR
                        s.family_name LIKE '%{$search}%' OR
                        s.grade_level LIKE '%{$search}%' OR
                        s.grade_section LIKE '%{$search}%'
                    ORDER BY registered DESC";
    $query = scls_mysqli()->prepare($statement);
    $query->execute();
    $result = $query->get_result();
    ?>
    <div class="container content">
        <header><?php require("components/navbar/admin.php"); ?></header>
        <div class="page-header">
            <h1>
                Students
                <?php if ($result->num_rows > 0): ?>
                    <small>(<?php echo $result->num_rows; ?>)</small>
                <?php endif; ?>
            </h1>
        </div>
        <div class="row">
            <!-- Add Student -->
            <div class="col-md-9">
                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#scls-student-modal-add">
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add New
                </button>
                <?php require("components/modals/student/add.php"); ?>
            </div>
            <!-- Search Input -->
            <div class="col-md-3">
                <form method="get" role="search">
                    <div class="form-group input-group">
                        <input type="search" class="form-control" name="search" placeholder="Search for specific student" value="<?php echo $search; ?>">
                        <span class="input-group-btn">
                            <?php if (!empty($search)): ?>
                                <a class="btn btn-default" href="admin_students.php"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                        </span>
                    </div>
                </form>
            </div>
        </div>
        <br class="visible-xs-block">
        <!-- Alert -->
        <?php bs_alerts_display(); ?>
        <!-- Table -->
        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th>Learner Reference Number</th>
                        <th>Given Name</th>
                        <th>Initial Name</th>
                        <th>Family Name</th>
                        <th>Grade Level</th>
                        <th>Grade Section</th>
                        <th>Actions</th>
                    </tr>
                    <?php while ($student = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $student["reference"]; ?></td>
                            <td><?php echo $student["given_name"]; ?></td>
                            <td><?php echo $student["initial_name"]; ?></td>
                            <td><?php echo $student["family_name"]; ?></td>
                            <td><?php echo $student["grade_level"]; ?></td>
                            <td class="<?php echo ($student["grade_section"] == "TBA") ? "danger" : ""; ?>"><?php echo $student["grade_section"]; ?></td>
                            <td style="width: 10%;">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#scls-student-modal-edit-<?php echo $student["reference"]; ?>">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                    </button>
                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#scls-student-modal-remove-<?php echo $student["reference"]; ?>">
                                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                    </button>
                                </div>
                                <?php require("components/modals/student/edit.php"); ?>
                                <?php require("components/modals/student/remove.php"); ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        <?php elseif (!empty($list_search)): ?>
            <p class="text-muted">The system couldn't find any results from the search query.</p>
        <?php else: ?>
            <p class="text-muted">Hmm, there aren't any students.</p>
        <?php endif; ?>
    </div>
    <?php require("components/footer.php"); ?>
</body>

</html>