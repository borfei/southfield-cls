<?php
require("components/bootstrap/alerts.php");
require("components/scls/document.php");
require("components/scls/mysqli.php");
require("components/scls/session.php");
require("components/sync.php");

// Forbid access if the user is not a member
if (scls_session_user()["role"] != "member") {
    scls_document_navigate("error.php?status=403", true);
}

/**
 * Removes a transaction if it is pending, or declined.
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
    <title>Transactions &mdash; SCLS</title>
</head>

<body>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) {
        $action = strtolower($_POST["action"]);

        switch ($action) {
            case "remove":
                remove_transaction(intval($_POST["id"] ?? -1));
                break;
            default:
                bs_alerts_create(BSAlertClass::Warning, "Action invalid or not recognized.");
        }
    }

    $search = htmlspecialchars($_GET["search"] ?? "");
    ?>
    <header><?php require("components/navbar/member.php"); ?></header>
    <div class="jumbotron">
        <div class="container">
            <h1>Transactions</h1>
        </div>
    </div>
    <div class="container content">
        <?php $user = scls_session_user(); ?>
        <!-- Alert -->
        <?php bs_alerts_display(); ?>
        <!-- Counts -->
        <div class="row">
            <?php
            $statement = "SELECT 
                                SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS pendings,
                                SUM(CASE WHEN status = 'Accepted' THEN 1 ELSE 0 END) AS accepts,
                                SUM(CASE WHEN status = 'Declined' THEN 1 ELSE 0 END) AS declines
                            FROM transactions 
                            WHERE user = ?";
            $query = scls_mysqli()->prepare($statement);
            $query->bind_param("s", $user["name"]);
            $query->execute();
            $result = $query->get_result();
            $counts = $result->fetch_assoc();

            $result->close(); // i guess no longer neeeded??

            if (!empty($counts["pendings"])) {
                if ($counts["pendings"] > 1) {
                    $pendings = "$counts[pendings] transactions";
                } else {
                    $pendings = "$counts[pendings] transaction";
                }
            } else {
                $pendings = "N/A";
            }
            if (!empty($counts["accepts"])) {
                if ($counts["accepts"] > 1) {
                    $accepts = "$counts[accepts] transactions";
                } else {
                    $accepts = "$counts[accepts] transaction";
                }
            } else {
                $accepts = "N/A";
            }
            if (!empty($counts["declines"])) {
                if ($counts["declines"] > 1) {
                    $declines = "$counts[declines] transactions";
                } else {
                    $declines = "$counts[declines] transaction";
                }
            } else {
                $declines = "N/A";
            }
            ?>
            <!-- Pending Counts -->
            <div class="col-sm-4 col-md-4">
                <div class="thumbnail">
                    <div class="caption bg-warning">
                        <small>Pending Transactions</small>
                        <h2><?php echo $pendings; ?></h2>
                    </div>
                </div>
            </div>
            <!-- Accepted Counts -->
            <div class="col-sm-5 col-md-4">
                <div class="thumbnail">
                    <div class="caption bg-success">
                        <small>Accepted Transactions</small>
                        <h2><?php echo $accepts; ?></h2>
                    </div>
                </div>
            </div>
            <!-- Declined Counts -->
            <div class="col-sm-5 col-md-4">
                <div class="thumbnail">
                    <div class="caption bg-danger">
                        <small>Declined Transactions</small>
                        <h2><?php echo $declines; ?></h2>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <!-- List -->
        <?php
        $statement = "SELECT
                            t.id,
                            t.status,
                            b.id AS book_id,
                            b.title AS book_title,
                            b.author AS book_author,
                            b.year AS book_year
                        FROM transactions t
                        LEFT JOIN books b ON t.book = b.id
                        WHERE
                            t.user = ? AND
                            t.status IN ('Pending', 'Accepted', 'Declined') AND
                            (
                                b.title LIKE '%{$search}%'OR
                                b.author LIKE '%{$search}%' OR
                                b.year LIKE '%{$search}%'
                            )
                        ORDER BY t.status DESC";
        $query = scls_mysqli()->prepare($statement);
        $query->bind_param("s", $user["name"]);
        $query->execute();
        $result = $query->get_result();
        ?>
        <?php if ($result->num_rows > 0): ?>
            <!-- Search Input -->
            <form method="get" role="search">
                <div class="form-group input-group">
                    <input type="search" class="form-control" placeholder="Search" name="search" value="<?php echo $search; ?>">
                    <span class="input-group-btn">
                        <?php if (!empty($search)): ?>
                            <a class="btn btn-default" href="<?php echo basename($_SERVER["SCRIPT_FILENAME"]); ?>"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                    </span>
                </div>
            </form>
            <?php if (!empty($search)): ?>
                <?php $returned_plural = ($result->num_rows > 1) ? "results" : "result"; ?>
                <p class="text-muted">The search query returned matching <?php echo "$result->num_rows $returned_plural"; ?>:</p>
            <?php endif; ?>
            <ul class="list-group">
                <?php while ($transaction = $result->fetch_assoc()): ?>
                    <?php
                    if (is_readable("public/books/$transaction[book_id].png")) {
                        $image = "public/books/$transaction[book_id].png";
                    } else {
                        $image = "assets/book-placeholder.png";
                    }
                    ?>
                    <li class="list-group-item list-group-item-<?php echo ($transaction["status"] == "Pending") ? "warning" : (($transaction["status"] == "Accepted") ? "success" : ($transaction["status"] == "Declined" ? "danger" : "")); ?>">
                        <div class="media">
                            <div class="media-left">
                                <img class="media-object img-thumbnail" width="64px" height="64px" src="<?php echo $image; ?>" alt="<?php echo $transaction["book_title"]; ?>">
                            </div>
                            <div class="media-body media-middle">
                                <h4 class="media-heading">
                                    <strong><?php echo $transaction["book_title"]; ?></strong>
                                    <small><?php echo $transaction["book_year"]; ?></small>
                                    <br>
                                    <small><?php echo $transaction["book_author"]; ?></small>
                                    <br>
                                    <small><?php echo $transaction["status"]; ?></small>
                                </h4>
                            </div>
                            <?php if ($transaction["status"] == "Pending" || $transaction["status"] == "Declined"): ?>
                                <div class="media-right media-middle">
                                    <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#scls-transaction-modal-remove-<?php echo $transaction["id"]; ?>">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </li>
                    <?php require("components/modals/transaction/remove.php"); ?>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p class="text-muted">You don't have any transactions. Transactions will appear here once you borrow books.</p>
            <a class="btn btn-default" href="member_books.php">Browse for Books</a>
            <br><br><br><br><br> <!-- some trick -->
        <?php endif; ?>
    </div>
    <?php require("components/footer.php"); ?>
</body>

</html>