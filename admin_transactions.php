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
 * Accepts a pending transaction by updating its status to 'Accepted'.
 *
 * @param int $id The ID of the transaction to accept.
 *
 * This function checks if the transaction with the given ID exists and is still pending.
 * If the transaction is pending, it updates the status to 'Accepted'. Appropriate alerts
 * are created based on the success or failure of the operation. If the transaction is
 * no longer available or not pending, an alert is issued.
 */
function accept_transaction(int $id): void
{
    if ($id == -1) {
        bs_alerts_create(BSAlertClass::Danger, "Invalid transaction ID.");
        return;
    }

    // CHECK: Transaction and it's status

    $statement = "SELECT
                        t.status AS status,
                        b.title AS book_title,
                        b.year AS book_year,
                        b.author AS book_author
                    FROM transactions t
                    JOIN books b ON t.book = b.id
                    WHERE t.id = ?";
    $query = scls_mysqli()->prepare($statement);
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows < 1) {
        bs_alerts_create(BSAlertClass::Danger, "Specified transaction is no longer available.");
        return;
    }

    $transaction = $result->fetch_assoc(); // store transaction data for later use
    $result->close(); // needed for later use

    if ($transaction["status"] != "Pending") {
        bs_alerts_create(BSAlertClass::Danger, "Transaction (#$id) is no longer pending: <strong>$transaction[book_title]</strong> ($transaction[book_year]) by <strong>$transaction[book_author]</strong>");
        return;
    }

    // TRANSACTION: Accept by updating status

    scls_mysqli()->begin_transaction();
    $query = scls_mysqli()->prepare("UPDATE transactions SET status = 'Accepted' WHERE id = ?");
    $query->bind_param("i", $id);
    $result = $query->execute();
    scls_mysqli()->commit();

    if ($result) {
        bs_alerts_create(BSAlertClass::Success, "Transaction (#$id) accepted: <strong>$transaction[book_title]</strong> ($transaction[book_year]) by <strong>$transaction[book_author]</strong>");
    } else {
        bs_alerts_create(BSAlertClass::Danger, "Failed to accept the following transaction: #$id <strong>$transaction[book_title]</strong> ($transaction[book_year]) by <strong>$transaction[book_author]</strong>");
    }
}

/**
 * Marks an accepted transaction as borrowed by updating its status, borrow date, and due date.
 *
 * @param int $id The ID of the transaction to mark as borrowed.
 * @param string $due_date The due date for the borrowed book.
 *
 * This function checks if the transaction with the given ID exists and is accepted.
 * If the transaction is accepted, it updates the status to 'Borrowed', sets the borrow date to the current date,
 * and updates the due date. Appropriate alerts are created based on the success or failure of the operation.
 * If the transaction is no longer available or not accepted, an alert is issued.
 */

function mark_borrow_transaction(int $id, string $due_date): void
{
    if ($id == -1) {
        bs_alerts_create(BSAlertClass::Danger, "Invalid transaction ID.");
        return;
    }

    // CHECK: Transaction and it's status

    $statement = "SELECT
                        t.status AS status,
                        b.title AS book_title,
                        b.year AS book_year,
                        b.author AS book_author
                    FROM transactions t
                    JOIN books b ON t.book = b.id
                    WHERE t.id = ?";
    $query = scls_mysqli()->prepare($statement);
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows < 1) {
        bs_alerts_create(BSAlertClass::Danger, "Specified transaction is no longer available.");
        return;
    }

    $transaction = $result->fetch_assoc(); // store transaction data for later use
    $result->close(); // needed for later use

    if ($transaction["status"] != "Accepted") {
        bs_alerts_create(BSAlertClass::Danger, "Transaction (#$id) hasn't been accepted: <strong>$transaction[book_title]</strong> ($transaction[book_year]) by <strong>$transaction[book_author]</strong>");
        return;
    }

    // TRANSACTION: Borrow by updating status and borrow date

    scls_mysqli()->begin_transaction();
    $statement = "UPDATE transactions SET
                                        status = 'Borrowed',
                                        borrow_date = NOW(),
                                        due_date = ?
                    WHERE id = ?";
    $query = scls_mysqli()->prepare($statement);
    $query->bind_param("si", $due_date, $id);
    $result = $query->execute();
    scls_mysqli()->commit();

    if ($result) {
        bs_alerts_create(BSAlertClass::Success, "Transaction (#$id) marked as borrowed: <strong>$transaction[book_title]</strong> ($transaction[book_year]) by <strong>$transaction[book_author]</strong>");
    } else {
        bs_alerts_create(BSAlertClass::Danger, "Failed to mark the following transaction as borrowed: #$id <strong>$transaction[book_title]</strong> ($transaction[book_year]) by <strong>$transaction[book_author]</strong>");
    }
}

/**
 * Marks a transaction as returned by updating its status to 'Returned' and setting the return date to the current date.
 *
 * @param int $id The ID of the transaction to mark as returned.
 *
 * This function checks if the transaction with the given ID exists and is borrowed.
 * If the transaction is borrowed, it updates the status to 'Returned', sets the return date to the current date.
 * Appropriate alerts are created based on the success or failure of the operation.
 * If the transaction is no longer available or not borrowed, an alert is issued.
 */
function mark_return_transaction(int $id): void
{
    if ($id == -1) {
        bs_alerts_create(BSAlertClass::Danger, "Invalid transaction ID.");
        return;
    }

    // CHECK: Transaction and it's status

    $statement = "SELECT
                        t.status AS status,
                        b.title AS book_title,
                        b.year AS book_year,
                        b.author AS book_author
                    FROM transactions t
                    JOIN books b ON t.book = b.id
                    WHERE t.id = ?";
    $query = scls_mysqli()->prepare($statement);
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows < 1) {
        bs_alerts_create(BSAlertClass::Danger, "Specified transaction is no longer available.");
        return;
    }

    $transaction = $result->fetch_assoc(); // store transaction data for later use
    $result->close(); // needed for later use

    if ($transaction["status"] != "Borrowed") {
        bs_alerts_create(BSAlertClass::Danger, "Transaction (#$id) hasn't been borrowed: <strong>$transaction[book_title]</strong> ($transaction[book_year]) by <strong>$transaction[book_author]</strong>");
        return;
    }

    // TRANSACTION: Borrow by updating status and return date

    scls_mysqli()->begin_transaction();
    $query = scls_mysqli()->prepare("UPDATE transactions SET status = 'Returned', return_date = NOW() WHERE id = ?");
    $query->bind_param("i", $id);
    $result = $query->execute();
    scls_mysqli()->commit();

    if ($result) {
        bs_alerts_create(BSAlertClass::Success, "Transaction (#$id) marked as returned: <strong>$transaction[book_title]</strong> ($transaction[book_year]) by <strong>$transaction[book_author]</strong>");
    } else {
        bs_alerts_create(BSAlertClass::Danger, "Failed to mark the following transaction as returned: #$id <strong>$transaction[book_title]</strong> ($transaction[book_year]) by <strong>$transaction[book_author]</strong>");
    }
}

/**
 * Declines a pending transaction by updating its status to 'Declined'.
 *
 * @param int $id The ID of the transaction to decline.
 *
 * This function checks if the transaction with the given ID exists and is still pending.
 * If the transaction is pending, it updates the status to 'Declined'. Appropriate alerts
 * are created based on the success or failure of the operation. If the transaction is
 * no longer available or not pending, an alert is issued.
 */
function decline_transaction(int $id): void
{
    if ($id == -1) {
        bs_alerts_create(BSAlertClass::Danger, "Invalid transaction ID.");
        return;
    }

    // CHECK: Transaction and it's status

    $statement = "SELECT
                        t.status AS status,
                        b.title AS book_title,
                        b.year AS book_year,
                        b.author AS book_author
                    FROM transactions t
                    JOIN books b ON t.book = b.id
                    WHERE t.id = ?";
    $query = scls_mysqli()->prepare($statement);
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows < 1) {
        bs_alerts_create(BSAlertClass::Danger, "Specified transaction is no longer available.");
        return;
    }

    $transaction = $result->fetch_assoc(); // store transaction data for later use
    $result->close(); // needed for later use

    if ($transaction["status"] != "Pending") {
        bs_alerts_create(BSAlertClass::Danger, "Transaction (#$id) is no longer pending: <strong>$transaction[book_title]</strong> ($transaction[book_year]) by <strong>$transaction[book_author]</strong>");
        return;
    }

    // TRANSACTION: Decline by updating status

    scls_mysqli()->begin_transaction();
    $query = scls_mysqli()->prepare("UPDATE transactions SET status = 'Declined' WHERE id = ?");
    $query->bind_param("i", $id);
    $result = $query->execute();
    scls_mysqli()->commit();

    if ($result) {
        bs_alerts_create(BSAlertClass::Success, "Transaction (#$id) declined: <strong>$transaction[book_title]</strong> ($transaction[book_year]) by <strong>$transaction[book_author]</strong>");
    } else {
        bs_alerts_create(BSAlertClass::Danger, "Failed to decline the following transaction: #$id <strong>$transaction[book_title]</strong> ($transaction[book_year]) by <strong>$transaction[book_author]</strong>");
    }
}

/**
 * Removes a transaction if it is pending, declined, or returned.
 *
 * @param int $id The ID of the transaction to remove.
 */
function remove_transaction(int $id): void
{
    if ($id == -1) {
        bs_alerts_create(BSAlertClass::Danger, "Invalid transaction ID.");
        return;
    }

    // CHECK: Book Transaction

    $statement = "SELECT
                        b.id AS book_id,
                        b.title AS book_title,
                        b.year AS book_year,
                        b.author AS book_author
                    FROM transactions t
                    JOIN books b ON t.book = b.id
                    WHERE t.id = ?";
    $query = scls_mysqli()->prepare($statement);
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows < 1) {
        bs_alerts_create(BSAlertClass::Warning, "Transaction (#$id) does not exist.");
        return;
    }

    $transaction = $result->fetch_assoc(); // store the transaction data for later use
    $result->close(); // needed for later queries

    // TRANSACTION: Delete Book Transaction

    scls_mysqli()->begin_transaction();
    $query = scls_mysqli()->prepare("DELETE FROM transactions WHERE id = ?");
    $query->bind_param("i", $id);
    $result = $query->execute();
    scls_mysqli()->commit();

    if ($result) {
        bs_alerts_create(BSAlertClass::Success, "Transaction (#$id) removed: <strong>$transaction[book_title]</strong> ($transaction[book_year]) by <strong>$transaction[book_author]</strong>");
    } else {
        bs_alerts_create(BSAlertClass::Danger, "Failed to remove the following transaction: #$id <strong>$transaction[book_title]</strong> ($transaction[book_year]) by <strong>$transaction[book_author]</strong>");
        return;
    }

    // TRANSACTION: Reset AUTO_INCREMENT Value

    scls_mysqli()->begin_transaction();
    $query = scls_mysqli()->prepare("ALTER TABLE transactions AUTO_INCREMENT = 1");
    $result = $query->execute();
    scls_mysqli()->commit();

    if (!$result) {
        bs_alerts_create(BSAlertClass::Warning, "Couldn't reset auto-increment value.");
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
    <title>Transactions &mdash; SCLS (Admin)</title>
</head>

<body>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) {
        $action = strtolower($_POST["action"]);

        switch ($action) {
            case "accept":
                accept_transaction(intval($_POST["id"] ?? -1));
                break;
            case "borrow":
                mark_borrow_transaction(intval($_POST["id"] ?? -1), $_POST["due_date"] ?? "");
                break;
            case "decline":
                decline_transaction(intval($_POST["id"] ?? -1));
                break;
            case "remove":
                remove_transaction(intval($_POST["id"] ?? -1));
                break;
            case "return":
                mark_return_transaction(intval($_POST["id"] ?? -1));
                break;
            default:
                bs_alerts_create(BSAlertClass::Warning, "Action invalid or not recognized.");
        }
    }

    $search = htmlspecialchars($_GET["search"] ?? "");
    $statement = "SELECT
                        t.id,
                        u.name as user_name,
                        s.given_name as user_given_name,
                        s.family_name as user_family_name,
                        b.id as book_id,
                        b.title as book_title,
                        b.author as book_author,
                        b.year as book_year,
                        t.borrow_date,
                        t.due_date,
                        t.return_date,
                        t.status
                    FROM transactions t
                    LEFT JOIN users u ON t.user = u.name
                    LEFT JOIN books b ON t.book = b.id
                    LEFT JOIN students s ON t.user = s.user
                    WHERE
                        t.status LIKE '%{$search}%' OR
                        b.title LIKE '%{$search}%' OR
                        b.author LIKE '%{$search}%' OR
                        b.category LIKE '%{$search}%' OR
                        s.given_name LIKE '%{$search}%' OR
                        s.family_name LIKE '%{$search}%'
                    ORDER BY
                        CASE t.status
                            WHEN 'Pending' THEN 1
                            WHEN 'Accepted' THEN 2
                            WHEN 'Declined' THEN 3
                        END
                    DESC";
    $query = scls_mysqli()->prepare($statement);
    $query->execute();
    $result = $query->get_result();
    ?>
    <div class="container content">

        <header><?php require("components/navbar/admin.php"); ?></header>
        <div class="page-header">
            <h1>
                Transactions
                <?php if ($result->num_rows > 0): ?>
                    <small>(<?php echo $result->num_rows; ?>)</small>
                <?php endif; ?>
            </h1>
        </div>
        <!-- Alert -->
        <?php bs_alerts_display(); ?>
        <!-- Table -->
        <?php if ($result->num_rows > 0): ?>
            <!-- Search Input -->
            <form method="get" role="search">
                <div class="form-group input-group">
                    <input type="search" class="form-control" placeholder="Search" name="search" value="<?php echo $search; ?>">
                    <span class="input-group-btn">
                        <?php if (!empty($search)): ?>
                            <a class="btn btn-default" href="admin_transactions.php"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                    </span>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th>ID</th>
                        <th>Book</th>
                        <th>User</th>
                        <th>Borrow Date</th>
                        <th>Due Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    <?php while ($transaction = $result->fetch_assoc()): ?>
                        <?php
                        if (is_readable("public/books/$transaction[book_id].png")) {
                            $image = "public/books/$transaction[book_id].png";
                        } else {
                            $image = "assets/book-placeholder.png";
                        }
                        switch ($transaction["status"]) {
                            case "Pending":
                                $class = "warning";
                                break;
                            case "Accepted":
                                $class = "success";
                                break;
                            case "Declined":
                                $class = "danger";
                                break;
                            case "Borrowed":
                                $now = date("Y-m-d");

                                if ($transaction["due_date"] > date("Y-m-d")) {
                                    $class = "active";
                                } else {
                                    $class = "danger";
                                }

                                break;
                            default:
                                $class = "";
                        }
                        ?>
                        <tr class="<?php echo $class; ?>">
                            <td><?php echo $transaction["id"]; ?></td>
                            <td>
                                <a href="admin_books.php?search=<?php echo $transaction["book_id"]; ?>">
                                    <strong><?php echo $transaction["book_title"]; ?></strong> (<?php echo $transaction["book_year"]; ?>)
                                    <br>
                                    <small><?php echo $transaction["book_author"]; ?></small>
                                </a>
                            </td>
                            <td>
                                <a href="admin_users.php?type=member&search=<?php echo $transaction["user_name"]; ?>">
                                    <strong><?php echo "$transaction[user_family_name], $transaction[user_given_name]"; ?></strong>
                                    <br>
                                    <small><?php echo $transaction["user_name"]; ?></small>
                                </a>
                            </td>
                            <td><?php echo $transaction["borrow_date"] ?? "N/A"; ?></td>
                            <td><?php echo $transaction["due_date"] ?? "N/A"; ?></td>
                            <td><?php echo $transaction["return_date"] ?? "N/A"; ?></td>
                            <td>
                                <?php echo $transaction["status"]; ?>
                                <br>
                                <?php if ($transaction["return_date"] > $transaction["due_date"]): ?>
                                    <span class="text-danger">(Overdue)</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($transaction["status"] == "Pending"): ?>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#scls-transaction-modal-accept-<?php echo $transaction["id"]; ?>">
                                            <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                                        </button>
                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#scls-transaction-modal-decline-<?php echo $transaction["id"]; ?>">
                                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                    <?php require("components/modals/transaction/admin_accept.php"); ?>
                                    <?php require("components/modals/transaction/admin_decline.php");
                                    ?>
                                <?php elseif ($transaction["status"] == "Accepted"): ?>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#scls-transaction-modal-borrow-<?php echo $transaction["id"]; ?>">
                                            <span class="glyphicon glyphicon-time" aria-hidden="true"></span>
                                        </button>
                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#scls-transaction-modal-remove-<?php echo $transaction["id"]; ?>">
                                            <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                    <?php require("components/modals/transaction/admin_borrow.php"); ?>
                                    <?php require("components/modals/transaction/remove.php"); ?>
                                <?php elseif ($transaction["status"] == "Borrowed"): ?>
                                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#scls-transaction-modal-return-<?php echo $transaction["id"]; ?>">
                                        <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                                    </button>
                                    <?php require("components/modals/transaction/admin_return.php"); ?>
                                <?php elseif ($transaction["status"] == "Declined" || $transaction["status"] == "Returned"): ?>
                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#scls-transaction-modal-remove-<?php echo $transaction["id"]; ?>">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                    </button>
                                    <?php require("components/modals/transaction/remove.php"); ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        <?php elseif (!empty($search)): ?>
            <p class="text-muted">The system couldn't find any results from the search query.</p>
        <?php else: ?>
            <p class="text-muted">No transactions have been created yet.</p>
        <?php endif; ?>
    </div>
    <?php require("components/footer.php"); ?>
</body>

</html>