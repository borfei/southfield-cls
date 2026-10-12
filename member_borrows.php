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
 * Removes a transaction if it is returned.
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
        bs_alerts_create(BSAlertClass::Success, "Transaction (#$id) removed: <strong>$transaction[book_title]</strong> by <strong>$transaction[book_author]</strong>");
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
    <title>Borrows &mdash; SCLS</title>
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
            <h1>Borrows</h1>
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
                                SUM(CASE WHEN status = 'Borrowed' THEN 1 ELSE 0 END) AS borrows,
                                SUM(CASE WHEN status = 'Returned' THEN 1 ELSE 0 END) AS returns
                            FROM transactions 
                            WHERE user = ?";
            $query = scls_mysqli()->prepare($statement);
            $query->bind_param("s", $user["name"]);
            $query->execute();
            $result = $query->get_result();
            $counts = $result->fetch_assoc();

            $result->close(); // i guess no longer neeeded??

            if (!empty($counts["borrows"])) {
                if ($counts["borrows"] > 1) {
                    $borrows = "$counts[borrows] books";
                } else {
                    $borrows = "$counts[borrows] book";
                }
            } else {
                $borrows = "N/A";
            }
            if (!empty($counts["returns"])) {
                if ($counts["returns"] > 1) {
                    $returns = "$counts[returns] books";
                } else {
                    $returns = "$counts[returns] book";
                }
            } else {
                $returns = "N/A";
            }
            ?>
            <!-- Borrow Counts -->
            <div class="col-sm-4 col-md-6">
                <div class="thumbnail">
                    <div class="caption">
                        <small>Borrowed Books</small>
                        <h2><?php echo $borrows; ?></h2>
                    </div>
                </div>
            </div>
            <!-- Return Counts -->
            <div class="col-sm-5 col-md-6">
                <div class="thumbnail">
                    <div class="caption bg-success">
                        <small>Returned Books</small>
                        <h2><?php echo $returns; ?></h2>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <!-- List -->
        <?php
        $statement = "SELECT
                            t.id,
                            t.borrow_date,
                            t.due_date,
                            t.return_date,
                            t.status,
                            b.id AS book_id,
                            b.title AS book_title,
                            b.author AS book_author,
                            b.year AS book_year
                        FROM transactions t
                        LEFT JOIN books b ON t.book = b.id
                        WHERE
                            t.user = ? AND
                            t.status IN ('Borrowed', 'Returned') AND
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
                    <li class="list-group-item list-group-item-<?php echo ($transaction["status"] == "Returned") ? "success" : ""; ?>">
                        <div class="media">
                            <div class="media-left">
                                <img class="media-object img-thumbnail" width="128px" height="128px" src="<?php echo $image; ?>" alt="<?php echo $transaction["book_title"]; ?>">
                            </div>
                            <div class="media-body media-middle">
                                <h4 class="media-heading">
                                    <strong><?php echo $transaction["book_title"]; ?></strong>
                                    <small><?php echo $transaction["book_year"]; ?></small>
                                    <br>
                                    <small><?php echo $transaction["book_author"]; ?></small>
                                </h4>
                                <p>
                                    Borrowed on: <?php echo $transaction["borrow_date"] ?? "N/A"; ?>
                                    <br>
                                    Due on: <?php echo $transaction["due_date"] ?? "N/A"; ?>
                                    <br>
                                    Returned on: <?php echo $transaction["return_date"] ?? "N/A"; ?>
                                    <br>
                                    Status: <?php echo $transaction["status"]; ?>
                                </p>
                            </div>
                            <?php if ($transaction["status"] == "Returned"): ?>
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
            <p class="text-muted">You haven't borrowed any book yet. Once a transaction is considered "borrowed", it will be displayed here.</p>
            <div class="btn-group" role="group">
                <a class="btn btn-default" href="member_transactions.php">View Transactions</a>
                <a class="btn btn-default" href="member_books.php">Browse for Books</a>
            </div>
            <br><br><br><br><br> <!-- some trick -->
        <?php endif; ?>
    </div>
    <?php require("components/footer.php"); ?>
</body>

</html>